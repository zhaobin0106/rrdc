<?php
class ControllerTaskMinutes extends Controller {
    public function index() {
        $this->_order_timeout_cancel();
    }

    /**
     * 预约超过15（变量）分钟，系统自动取消
     */
    private function _order_timeout_cancel() {
        $_break = false;
        $this->load->library('sys_model/orders');
        $condition = array();
        $condition['order_state'] = '0';
        $condition['add_time'] = array('lt', time() - BOOK_EFFECT_TIME);
        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) break;
            $order_list = $this->sys_model_orders->getOrdersList($condition, '', 100);
            if (empty($order_list)) break;
            foreach ($order_list as $order_info) {
                $update = $this->sys_model_orders->updateOrders(array('order_id' => $order_info['order_id']), array('order_state' => '-1'));
                if (!$update) {
                    //更新失败写入日志
                }
            }
        }
    }

    /**
     * 优惠券过期处理
     */
    private function _coupon_timeout_expire() {

    }
}