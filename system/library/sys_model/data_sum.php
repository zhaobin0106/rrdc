<?php
namespace Sys_Model;
/**
 * 统计首页
 * User: 温海滔
 * Date: 2017/3/2
 * Time: 10:14
 */
class Data_sum {
    public function __construct($registry) {
        $this->db = $registry->get('db');
    }

    public function getRegisterSum($day = 7) {
        $total_arr = $this->db->table('user')->where(array())->field('count(user_id) as total')->find();

        $result = $this->db->getRows('select count(user_id) user_count, FROM_UNIXTIME(add_time, \'%Y-%m-%d\') days from rich_user where FROM_UNIXTIME(add_time, \'%Y-%m-%d\')>DATE_SUB(CURDATE(), INTERVAL '.$day.' DAY) group by days');

        return array('total'=>$total_arr['total'],'list'=>$this->matchingDate($result,$day));
    }

    public function getBicycleSum($day = 7) {
        $total_arr = $this->db->table('bicycle')->where(array())->field('count(bicycle_id) as total')->find();

        $result = $this->db->getRows('select count(bicycle_id) bicycle_count, FROM_UNIXTIME(add_time, \'%Y-%m-%d\') days from rich_bicycle where FROM_UNIXTIME(add_time, \'%Y-%m-%d\')>DATE_SUB(CURDATE(), INTERVAL '.$day.' DAY) group by days');

        return array('total'=>$total_arr['total'],'list'=>$this->matchingDate($result,$day));
    }

    public function getUsedBicycleSum($day = 7) {
        $total_arr = $this->db->table('bicycle')->where(array('is_using' => 2))->field('count(bicycle_id) as total')->find();

        $result = $this->db->getRows('select count(order_id) order_count, FROM_UNIXTIME(add_time, \'%Y-%m-%d\') days from rich_orders where order_state=2 and FROM_UNIXTIME(add_time, \'%Y-%m-%d\')>DATE_SUB(CURDATE(), INTERVAL '.$day.' DAY) group by days');

        return array('total'=>$total_arr['total'],'list'=>$this->matchingDate($result,$day));
    }

    public function getFaultBicycleSum($day = 7) {
        $total_arr = $this->db->getRow('select COUNT(DISTINCT bicycle_sn) total from rich_fault');

        $result = $this->db->getRows('select count(distinct bicycle_sn) fault_count, FROM_UNIXTIME(add_time, \'%Y-%m-%d\') days from rich_fault where FROM_UNIXTIME(add_time, \'%Y-%m-%d\')>DATE_SUB(CURDATE(), INTERVAL '.$day.' DAY) group by days');

        return array('total'=>$total_arr['total'],'list'=>$this->matchingDate($result,$day));
    }

    public function getRechargeSum() {
        $total_arr = $this->db->table('orders')->where(array('order_state' => 2))->field('sum(pay_amount) as total')->find();

        $result = $this->db->getRows('select sum(pay_amount) total,CURDATE() date from rich_orders where FROM_UNIXTIME(add_time, \'%Y-%m-%d\')=CURDATE()');

        return array('total'=>$total_arr['total'],'list'=>$result);
    }

    public function getDepositSum() {
        $total_arr = $this->db->table('deposit_recharge')->where(array('pdr_payment_state' => 1,'pdr_type'=>1))->field('sum(pdr_amount) as total')->find();

        $recharge = $this->db->getRows('select sum(pdr_amount) total,CURDATE() date from rich_deposit_recharge where pdr_payment_state=1 and pdr_type =1 and FROM_UNIXTIME(pdr_add_time, \'%Y-%m-%d\')=CURDATE()');

        $cash = $this->db->getRows('select sum(pdc_amount) total,CURDATE() date from rich_deposit_cash where pdc_payment_state=1 and FROM_UNIXTIME(pdc_add_time, \'%Y-%m-%d\')=CURDATE()');

        return array('total'=>$total_arr['total'],'recharge'=>$recharge,'cash'=>$cash);
    }

    public function getCouponSum() {
        $total_arr = $this->db->table('coupon')->where(array('used' => 0))->field('count(coupon_id) as total')->find();

        $result = $this->db->getRows('select count(coupon_id) total,CURDATE() date from rich_coupon where used = 1 and FROM_UNIXTIME(used_time, \'%Y-%m-%d\')=CURDATE()');

        return array('total'=>$total_arr['total'],'list'=>$result);
    }

    private function _dealData($total_arr, $result, $day) {
        $items = array();
        foreach ($result as $value) {
            $items[$value['days']] = $value;
        }

        for ($j = $day; $j > 0; $j--) {
            $day_index = date('Y-m-d', strtotime('-' . $day . ' day'));
            if (!isset($day_index)) {
                $items[$day_index] = array('user_count' => 0, 'days' => $day_index);
            }
        }

        $result = array_values($items);
        $arr = array('list' => $result);
        return array_merge($total_arr, $arr);
    }

    private function matchingDate($data, $days){
        $arr = array();
        for($i = $days; $i >= 0; $i--){
            foreach ($data as $v){
                if(strtotime($v['days']) == (strtotime(date('Y-m-d',time())) - $i * 86400)) {
                    $arr[$days-$i]['days'] = date('Y-m-d',(strtotime(date('Y-m-d',time())) - $i * 86400));
                    $arr[$days-$i]['count'] = array_values($v)[0];
                }
                if(empty($arr[$days-$i])){
                    $arr[$days-$i]['days'] = date('Y-m-d',(strtotime(date('Y-m-d',time())) - $i * 86400));
                    $arr[$days-$i]['count'] = 0;
                }
            }
            if(empty($data)){
                $arr[$days-$i]['days'] = 0;
                $arr[$days-$i]['count'] = 0;
            }
        }
        return $arr;
    }
}