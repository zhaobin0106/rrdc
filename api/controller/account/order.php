<?php
class ControllerAccountOrder extends Controller {
    private $direct_output = false;

    /**
     * 订单预约，已在startup/deposit和startup/order里面做特殊处理
     */
//    public function book() {}

    /**
     * 获取订单详情
     */
    public function getOrderInfo() {
        if (!isset($this->request->post['order_sn']) || empty($this->request->post['order_sn'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $order_sn = $this->request->post['order_sn'];
        $this->load->library('sys_model/orders');
        $order_info = $this->sys_model_orders->getOrdersInfo(array('order_sn' => $order_sn));

        if (empty($order_info)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }

        $user_id = $this->startup_user->userId();
        $user_info = $this->startup_user->getUserInfo();
        if ($order_info['user_id'] != $user_id) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $order_info['available_deposit'] = $user_info['available_deposit'];
        $order_info = $this->format($order_info);
        $this->response->showSuccessResult($order_info, $this->language->get('success_loading'));
    }

    /**
     * 获取订单轨迹，此方法可以作为接口，也可以直接返回（事先设置$this->direct_output为true）
     * @return array
     */
    public function getOrderLine() {
        $order_sn = $this->request->post['order_sn'];
        $user_id = $this->startup_user->userId();
        $this->load->library('sys_model/orders');
        $order_info = $this->sys_model_orders->getOrdersInfo(array('order_sn' => $order_sn, 'user_id' => $user_id));
        if (empty($order_info)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $order_line = $this->sys_model_orders->getOrderLine(array('order_id' => $order_info['order_id']));
        $line_data = array();
        foreach ($order_line as $line) {
            $line_data[] = array('lat' => $line['lat'], 'lng' => $line['lng']);
        }
        $this->load->library('tool/distance');
        $distance = $this->tool_distance->sumDistance($line_data);
        $distance = round($distance, 2);
        $calorie = round(60 * $distance * 1.036, 2);
        $emission = $distance ? round($distance * 0.275 * 1000.0) : 0;
        if ($this->direct_output) {
            return array('line_data' => $line_data, 'distance' => $distance, 'calorie' => $calorie, 'emission' => $emission);
        }
        $this->response->showSuccessResult(array('distance' => $distance, 'line_list' => $line_data, 'order_sn' => $order_sn));
    }

    /**
     * 取消订单
     */
    public function cancelOrder() {
        $order_sn = $this->request->post['order_sn'];
        $user_id = $this->startup_user->userId();
        $this->load->library('sys_model/orders');
        $order_info = $this->sys_model_orders->getOrdersInfo(array('order_sn' => $order_sn, 'user_id' => $user_id, 'order_state' => '0', 'add_time' => array('egt', time() - BOOK_EFFECT_TIME)));
        if (empty($order_info)) {
            $this->response->showErrorResult($this->language->get('error_invalid_order'), 135);
        }
        $order_id = $order_info['order_id'];
        $update = $this->sys_model_orders->updateOrders(array('order_id' => $order_id), array('order_state' => '-1', 'end_time'=>time()));
        if (!$update) {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
        }
        $this->response->showSuccessResult(array('order_sn' => $order_sn), $this->language->get('success_cancel'));
    }

    /**
     * 获取用户当前订单状况
     */
    public function current() {
        $user_id = $this->startup_user->userId();
        $this->load->library('sys_model/orders');
        $t = time() - BOOK_EFFECT_TIME;
        $order_info = $this->sys_model_orders->getOrdersInfo("`user_id`={$user_id} AND (`order_state`=1 OR (`order_state`=0 AND `add_time`>={$t}))");
        if (empty($order_info)) {
            $this->response->showSuccessResult(array('has_order' => false), $this->language->get('error_no_order'));
        }
        else {
            $user_info = $this->startup_user->getUserInfo();
            $order_info['available_deposit'] = $user_info['available_deposit'];
            $this->request->post['order_sn'] = $order_info['order_sn'];
            if($order_info['order_state']==0) {
                $this->load->library('sys_model/lock');
                $lock_info = $this->sys_model_lock->getLockInfo(array('lock_sn'=>$order_info['lock_sn']));
                $order_info['end_lat'] = $lock_info['lat'];
                $order_info['end_lng'] = $lock_info['lng'];
            }
            $this->response->showSuccessResult(array('has_order' => true, 'current_order' => $this->format($order_info)), $this->language->get('error_no_order'));
        }
    }

    /**
     * 格式化订单数据
     * @param $data
     * @return array
     */
    private function format($data) {
        $this->direct_output = true;
        $arr = $this->getOrderLine();

        //修正预约有效期内没有取消预约的订单的数据
        if($data['order_state']==0 && time()>($data['add_time']+BOOK_EFFECT_TIME) && $data['start_time']==0 && $data['end_time']==0) {
            $data['order_state']= -1;
            $data['end_time'] = $data['add_time']+BOOK_EFFECT_TIME;
        }

        //计算的结束时间
        $time = ($data['order_state'] == 2 || $data['order_state'] == -1) ? $data['end_time'] : time();
        $riding_time = $time - ($data['order_state'] <= 0 ? $data['add_time'] : $data['start_time']);

        $hours = floor($riding_time / (60 * 60));
        $min = floor(($riding_time - ($hours * 60 * 60)) / 60);
        $data['time'] = array(
            'hours' => $hours,
            'min' => $min
        );

        if($data['order_state']==0) {
            $data['keep_time'] = BOOK_EFFECT_TIME - (time() - $data['add_time']);
        } else {
            $data['keep_time'] = 0;
        }

        $unit = ceil($riding_time / TIME_CHARGE_UNIT);//计费单元
        $amount = $unit * PRICE_UNIT; //骑行费用
        if ($data['order_state'] == 1) {
            $data['order_amount'] = $amount;
        }

        $data = array_merge($data, $arr);
        return $data;
    }

//蓝牙关锁
    public function changeOrder(){
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = file_get_contents("php://input");
            if (isset($post['time'])) {
                $post['time'] = strtotime($post['time']);
            }

            $data = array(
                'userid' => $post['user_id'],
                'cmd' => isset($post['cmd'])?strtolower($post['cmd']):'close',
                'order_sn' => isset($post['order_sn'])?$post['order_sn']:'',
                'battery' => isset($post['battery'])?$post['battery']:'',
                'lock_status' => $post['lockstatus'],
                'lng' => $post['lng'],
                'lat' => $post['lat'],
                'time' => $post['time'],
                'serialnum' => $post['serialnum'],
            );
            if(empty($data['userid']) || empty($data['order_sn']) || empty($data['battery']) || empty($data['lock_status']) || empty($data['lng']) || empty($data['lat']) || empty($data['serialnum'])){
                $this->response->showErrorResult('缺少参数', 400);
            }
            $lock_data = array(
                'battery' => $data['battery'],
                'lng' => $data['lng'],
                'lat' => $data['lat'],
                'lock_status' => $data['lock_status'],
                'system_time' => time(),
                'device_time' => $data['time'],
            );

            if ($post['cmd'] == 'open') {
                $lock_data['open_nums'] = array('exp', 'open_nums+1');
            }

            $line_data = array(
                'lat' => $data['lat'],
                'lng' => $data['lng'],
            );

            $this->load->library('sys_model/location_records', true);
            $this->load->library('logic/orders', true);
            $this->load->library('sys_model/orders', true);
            $this->load->library('sys_model/lock', true);

            $price_unit = $this->config->get('config_price_unit') ? $this->config->get('config_price_unit') : 1;
            $time_recharge_unit = $this->config->get('config_time_charge_unit') ? $this->config->get('config_time_charge_unit') : 30 * 60;
            define('PRICE_UNIT', $price_unit); //价格单元
            define('TIME_CHARGE_UNIT', $time_recharge_unit);//计费单位 
            switch ($post['cmd']) {
                case 'open' :
                    break;
                case 'close':
                    $order_info = $this->sys_model_orders->getOrdersInfo(array('order_sn' => $data['order_sn'], 'user_id' => $data['userid']));
                    if(empty($order_info)){
                        $this->response->showErrorResult('订单不存在', 400);
                        exit();
                    }
                    if($order_info['order_state'] != 1){
                        $this->response->showErrorResult('订单已结束', 400);
                        exit();                        
                    }
                    $data['device_id'] = $order_info['lock_sn'];
                    $result = $this->logic_orders->finishOrders($data);
                    $i = 0;
                    if ($result['state'] == true) {
                        if ($i == 0) {
                            $arr = $this->response->_error['success'];
                            $arr['data'] = $result['data'];
                            $this->load->library('JPush/JPush', true);
                            $this->JPush_JPush->message($result['data']['user_id'], json_encode($arr));
                        }
                        $i++;
                    } else {
                        file_put_contents('close_order_error.log', json_encode($result) . "\n" , 8);
                    }
                    break;
                case 'normal':
                break;
            }    

            //更新锁的相关信息
            $lock_info = $this->sys_model_lock->getLockInfo(array('order_sn' => $data['order_sn']));
            if ($lock_info) {
                $this->sys_model_lock->updateLock(array('lock_sn' => $data['device_id']), $lock_data);
            }

            if ($lock_info) {
                library('queue/queue_client');
                library('queue/queue_db');
                library('queue/queue_server');
                if ($this->config->get('config_start_queue')) {
                    $result = \Queue\Queue_Client::push('addLocation', $data, $this->registry);
                } else {
                    $result = $this->sys_model_location_records->addLogs($data);
                }

                if (!$result) {
                    $this->response->showErrorResult('数据写入失败');
                }
                $this->response->showSuccessResult(array('request' => 'ok'));
            }
            $this->response->showErrorResult('不存在此锁');
        } else {
            $this->response->showErrorResult('Request Method Error');
        }      
    }
}
