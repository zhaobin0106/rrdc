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
        $min = ceil(($riding_time - ($hours * 60 * 60)) / 60);
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
}