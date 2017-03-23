<?php
/**
 * Created by PhpStorm.
 * User: estronger
 * Date: 2016/12/8
 * Time: 13:19
 */
class ControllerTransferHome extends Controller {
    public function receiptData() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = file_get_contents("php://input");
            file_put_contents('/data/wwwroot/default/bike/transfer/controller/transfer/receive.log', date('Y-m-d H:i:s ') . $post . "\n", FILE_APPEND);
            $post = json_decode($post, true);

            if (isset($post['time'])) {
                $post['time'] = strtotime($post['time']);
            }

            $data = array(
                //'userid' => $post['userid'],
                'cmd' => strtolower($post['cmd']),
                'device_id' => $post['deviceid'],
                'battery' => $post['battery'],
                'location_type' => $post['bike'],
                'lock_status' => $post['lockstatus'],
                'lng' => $post['lng'],
                'lat' => $post['lat'],
                'gx' => $post['gx'],
                'gy' => $post['gy'],
                'gz' => $post['gz'],
                'time' => $post['time'],
                'serialnum' => $post['serialnum'],
            );

            $lock_data = array(
                'battery' => $data['battery'],
                'lng' => $data['lng'],
                'lat' => $data['lat'],
                'gx' => $data['gx'],
                'gy' => $data['gy'],
                'gz' => $data['gz'],
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
                    if($data['lock_status']==1) { //开锁指令执行之后开锁成功
                        // 利用开锁之后锁立刻上传的定位信息来改变订单状态
                        // 先准备数据
                        $order_info = $this->sys_model_orders->getOrdersList(array('order_state' => 0, 'lock_sn' => $data['device_id']), '`order_id` DESC', '1');
                        if(empty($order_info)) {
                            break;
                        }
                        $data['result'] = 'ok';
                        $data['cmd'] = 'open';
                        $data['serialnum'] = $order_info[0]['add_time'];

                        $result = $this->logic_orders->effectOrders($data);
                        if ($result['state'] == true) {
                            $arr = $this->response->_error['success'];
                            $arr['data'] = $result['data'];
                            $this->load->library('JPush/JPush', true);
                            $send_result = $this->JPush_JPush->message($result['data']['user_id'], json_encode($arr));
                        }
                    }
                    else { // 开锁指令之后开锁失败

                    }
                    break;
                case 'close':
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
                        $this->log->write(json_encode($result));
                    }
                    break;
                case 'normal':
                    $this->load->library('sys_model/orders');
                    $order_info = $this->sys_model_orders->getOrdersInfo(array('order_state' => 1, 'lock_sn' => $data['device_id']));
                    if ($order_info) {
                        $line_data['order_id'] = $order_info['order_id'];
                        $line_data['user_id'] = $order_info['user_id'];
                        $line_data['add_time'] = time();
                        $line_data['status'] = 1;
                        $this->logic_orders->recordLine($line_data);


                        if ($data['lock_status'] == 0) {
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
                                file_put_contents('transfer_error.txt', date('Y-m-d H:i:s') . ' - ' . print_r($result, true) . "\n", FILE_APPEND);
                            }
                        }
                    }
                    break;
            }

            //更新锁的相关信息
            $lock_info = $this->sys_model_lock->getLockInfo(array('lock_sn' => $data['device_id']));
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
                $this->response->showSuccessResult();
            }
            $this->response->showErrorResult('不存在此锁');
        } else {
            $this->response->showErrorResult('Request Method Error');
        }
    }
}