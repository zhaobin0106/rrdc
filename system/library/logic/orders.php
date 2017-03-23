<?php
namespace Logic;

use Tool\Distance;

class Orders {
    private $registry;
    public function __construct($registry) {
        $this->registry = $registry;
        $this->sys_model_orders = new \Sys_Model\Orders($registry);
        $this->sys_model_bicycle = new \Sys_Model\Bicycle($registry);
        $this->sys_model_region = new \Sys_Model\Region($registry);
    }

    /**
     * 预约单车
     * @param $data
     * @return array
     */
    public function addOrders($data) {
        $lock_sn = $data['lock_sn'];
        $order_info = $this->sys_model_orders->getOrdersInfo(array('lock_sn' => $lock_sn, 'order_state' => array('lt', 2)));
        $condition = array(
            'lock_sn' => $lock_sn
        );
        $bicycle_info = $this->sys_model_bicycle->getBicycleInfo($condition);
        if (empty($bicycle_info)) {
            return callback(false, 'error_bicycle_sn_nonexistence');
        }
        $condition = array(
            'region_id' => $bicycle_info['region_id']
        );
        $region = $this->sys_model_region->getRegionInfo($condition);
        if (is_array($region) && !empty($region)) {
            $region['area_code'] = sprintf('%03d%02d', $region['region_city_code'], $region['region_city_ranking']);
        }

        if (!empty($order_info) && ($order_info['add_time'] >= time() - BOOK_EFFECT_TIME)) {
            if ($data['user_id'] == $order_info['user_id']) {
                return callback(false, 'error_repeat_book_bicycle');
            } else {
                return callback(false, 'error_bicycle_has_book');
            }
        }

        $arr = array (
            'order_sn' => $this->make_sn($data['user_id']),
            'user_id' => $data['user_id'],
            'lock_sn' => $data['lock_sn'],
            'bicycle_id' => $bicycle_info['bicycle_id'],
            'bicycle_sn' => $bicycle_info['bicycle_sn'],
            'user_name' => $data['user_name'],
            'region_id' => $region['region_id'],
            'area_code' => $region['area_code'],
            'region_name' => $region['region_name'],
            'add_time' => time(),
        );

        $order_id = $this->sys_model_orders->addOrders($arr);
        if ($order_id) {
            return callback(true, 'success_build_order', array('add_time' => $arr['add_time'], 'bicycle_sn' => $arr['bicycle_sn'], 'lock_sn' => $arr['lock_sn'], 'order_sn' => $arr['order_sn'], 'keep_time' => (isset($data['keep_time']) && !empty($data['keep_time'])) ? $data['keep_time'] : BOOK_EFFECT_TIME));
        } else {
            return callback(false, 'error_database_operation_failure');
        }
    }

    public function existsOrder($where) {
        $order_info = $this->sys_model_orders->getOrdersInfo($where);
        return $order_info;
    }

    /**
     * 回调使订单生效
     * @param $data
     * @return array $data
     */
    public function effectOrders($data) {
        $device_id = $data['device_id'];
        $cmd = $data['cmd'];
        if (strtolower($cmd) == 'open' && strtolower($data['result']) == 'ok') {
            $order_info = $this->sys_model_orders->getOrdersInfo(array('lock_sn' => $device_id, 'order_state' => 0, 'add_time' => $data['serialnum']));
            if (!empty($order_info)) {
                $arr = array(
                    'start_time' => time(),
                    'order_state' => 1
                );
                $model_location = new \Sys_Model\Location_Records($this->registry);
                $location_info = $model_location->findLastLocation($device_id);
                if ($location_info) {
                    $arr['start_lng'] = $location_info['lng'];
                    $arr['start_lat'] = $location_info['lat'];
                }
                $update = $this->sys_model_orders->updateOrders(array('order_id' => $order_info['order_id']), $arr);

                if (!$update) {
                    return callback(false, 'error_update_order_state_failure');
                }
                $line_data = array(
                    'user_id' => $order_info['user_id'],
                    'order_id' => $order_info['order_id'],
                    'lng' => $location_info['lng'],
                    'lat' => $location_info['lat'],
                    'add_time' => time(),
                );
                $insert = $this->sys_model_orders->addOrderLine($line_data);

                $output = array(
                    'order_sn' => $order_info['order_sn'],
                    'cmd' => $cmd,
                    'user_id' => $order_info['user_id'],
                    'device_id' => $device_id
                );
                return callback(true, '', $output);
            }
            return callback(false, 'error_lock_order_nonexistence');
        }
        return callback(false, 'error_send_instruction');
    }

    /**
     * 关锁订单完成
     * @param $data
     * @return array
     */
    public function finishOrders($data) {
        $device_id = $data['device_id'];
        $cmd = $data['cmd'];

        if (strtolower($cmd) == 'close') {
            $order_info = $this->sys_model_orders->getOrdersInfo(array('lock_sn' => $device_id, 'order_state' => 1));
            if (!empty($order_info)) {
                $arr = array(
                    'end_time' => time(),
                    'order_state' => 2
                );

                try {
                    $this->sys_model_orders->begin();
                    $start_time = $order_info['start_time'];
                    $end_time = $arr['end_time'];
                    $riding_time = $end_time - $start_time; //骑行时间
                    $unit = ceil($riding_time / TIME_CHARGE_UNIT);//计费单元
                    $amount = $unit * PRICE_UNIT; //骑行费用
                    $sys_model_deposit = new \Sys_Model\Deposit($this->registry);
                    $sys_model_user = new \Sys_Model\User($this->registry);

                    $arr_data = array(
                        'user_id' => $order_info['user_id'],
                        'user_name' => $order_info['user_name'],
                        'amount' => $amount,
                        'order_sn' => $order_info['order_sn'],
                        'end_lat' => $data['lat'],
                        'end_lng' => $data['lng']
                    );

                    $arr['order_amount'] = $amount;
                    $arr['pay_amount'] = $amount;
                    $arr['end_lat'] = $data['lat'];
                    $arr['end_lng'] = $data['lng'];
                    $user_info = $sys_model_user->getUserInfo(array('user_id' => $order_info['user_id']));
                    if (empty($user_info)) {
                        throw new \Exception('error_user_info');
                    }
                    //扣费金额大于骑行的费用
                    if ($user_info['available_deposit'] < $amount) {
                        $change_type = 'order_freeze';
                    } else {
                        $change_type = 'order_pay';
                    }

                    $sys_model_coupon = new \Sys_Model\Coupon($this->registry);
                    $coupon_info = $sys_model_coupon->getRightCoupon(array('user_id' => $order_info['user_id']));
                    if (!empty($coupon_info)) {
                        if ($coupon_info['coupon_type'] != 3) {

                        } else {
                            $arr_data['pay_amount'] = $arr_data['pay_amount'] - $coupon_info['number'];
                        }
//                        $insert_id = $sys_model_deposit->changeDeposit($change_type, $arr_data);
//                        if (!$insert_id) {
//                            throw new \Exception('写入金额明细失败');
//                        }
                        //更新优惠券的信息
                        $update = $sys_model_coupon->dealCoupon($coupon_info);
                        if ($update) {
                            $arr['coupon_id'] = $coupon_info['coupon_id'];
                        }
                    } else {
                        $insert_id = $sys_model_deposit->changeDeposit($change_type, $arr_data);
                        if (!$insert_id) {
                            throw new \Exception('error_insert_order_amount');
                        }
                    }

                    $line_data = array(
                        'user_id' => $order_info['user_id'],
                        'order_id' => $order_info['order_id'],
                        'lng' => $data['lng'],
                        'lat' => $data['lat'],
                        'add_time' => time(),
                    );

                    $this->sys_model_orders->addOrderLine($line_data);

                    $order_lines = $this->sys_model_orders->getOrderLine(array('order_id' => $order_info['order_id']));
                    $tool_distance = new Distance();
                    $distance = $tool_distance->sumDistance($order_lines);
                    $distance = round($distance * 1000, -1);

                    $arr['distance'] = $distance;

                    //更新订单状态
                    $update = $this->sys_model_orders->updateOrders(array('order_id' => $order_info['order_id']), $arr);
                    if (!$update) {
                        throw new \Exception('error_update_order_state_failure');
                    }

                    $this->sys_model_orders->commit();

                    $data = array(
                        'cmd' => 'close',
                        'order_sn' => $order_info['order_sn'],
                        'user_id' => $order_info['user_id'],
                        'device_id' => $device_id
                    );
                } catch (\Exception $e) {
                    $this->sys_model_orders->rollback();
                    return callback(false, $e->getMessage());
                }

                // 增加信用分
                $this->registry->get('load')->library('logic/credit', true);
                $this->registry->get('logic_credit')->addCreditPointOnFinishCycling($order_info['user_id']);

                return callback(true, '', $data);
            }
        }
        return callback(false);
    }

    public function recordLine($data) {
        return $this->addOrderLine($data);
    }

    public function addOrderLine($data) {
        $arr = array(
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'lng' => $data['lng'],
            'lat' => $data['lat'],
            'add_time' => time(),
            'status' => $data['status']
        );
        return $this->sys_model_orders->addOrderLine($data);
    }

    public function make_sn($user_id) {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (float) microtime() * 1000) . sprintf('%03d', (int) $user_id % 1000);
    }

    public function getOrdersByUserId($user_id, $page) {
        $limit = (empty($page) || $page<1) ? 10 : (10 * ($page-1) . ', 10');
        $orders =  $this->sys_model_orders->getOrdersList(array('user_id'=>$user_id, 'order_state'=> 2), 'add_time DESC', $limit);
        foreach ($orders as &$order) {
            $order['duration'] = $this->_getOrderDuration($order);
            $order['distance'] = round($order['distance'] / 1000.0, 2);
        }
        return $orders;
    }

    private function _getOrderDuration(&$order) {
        //修正预约有效期内没有取消预约的订单的数据
        if($order['order_state']==0 && $order['start_time']==0 && $order['end_time']==0 && time()>($order['add_time']+BOOK_EFFECT_TIME)) {
            $order['order_state']= -1;
            $order['end_time'] = $order['add_time']+BOOK_EFFECT_TIME;
        }

        //计算的结束时间
        $end_time = ($order['order_state'] == 2 || $order['order_state'] == -1) ? $order['end_time'] : time();
        $duration = $end_time - ($order['order_state'] <= 0 ? $order['add_time'] : $order['start_time']);
        return ceil($duration / 60.0);
    }

    public function getOrdersCountByUserId($user_id) {
        return $this->sys_model_orders->getTotalOrders(array('user_id'=>$user_id));
    }

    public function getOrderDetail($order_id) {
        $order_info = $this->sys_model_orders->getOrdersInfo(array('order_id'=>$order_id));
        $order_info['duration'] = ceil(($order_info['end_time'] - $order_info['start_time'])/60.0);
        $order_info['distance'] = round($order_info['distance'] / 1000.0, 2);
        $order_info['calorie'] = round(60 * $order_info['distance'] * 1.036, 2);
        $order_info['emission'] = $order_info['distance'] ? round($order_info['distance'] * 0.275 * 1000) : 0;

        $coupon_info = array();

        if ($order_info['coupon_id']) {
            $sys_model_coupon = new \Sys_Model\Coupon($this->registry);
            $coupon_info = $sys_model_coupon->getCouponInfo(array('coupon_id' => $order_info['coupon_id']), 'coupon_id,number,failure_time,coupon_type');
            if ($coupon_info['coupon_type'] == 1) {
                $coupon_info['number'] = ($coupon_info['number'] % 30 == 0) ? ($coupon_info['number'] / 60) : $coupon_info['number'];//半小时取整
                $coupon_info['unit'] = ($coupon_info['number'] % 30 == 0) ? $this->language->get('text_hour') : $this->language->get('text_minute');
            } elseif ($coupon_info['coupon_type'] == 2) {
                $coupon_info['unit'] = $this->language->get('text_time_unit');
            } elseif ($coupon_info['coupon_type'] == 3) {
                $coupon_info['unit'] = $this->language->get('text_money_unit');
            } elseif ($coupon_info['coupon_type'] == 4) {
                $coupon_info['unit'] = $this->language->get('text_discount_unit');
            }

            if (!empty($coupon_info)) {
                $coupon_info = array($coupon_info);
            }
        }

        $order_info['coupon_info'] = $coupon_info;
        $locations = $this->sys_model_orders->getOrderLine(array('order_id'=>$order_id));
        return array(
            'order_info' => $order_info,
            'locations' => $locations
        );
    }

    public function getLastSql() {
        return $this->sys_model_orders->getLastSql();
    }
}