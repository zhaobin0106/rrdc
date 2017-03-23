<?php
namespace Logic;

class Deposit {
    public function __construct($registry) {
        $this->sys_model_deposit = new \Sys_Model\Deposit($registry);
    }

    /**
     * 生成充值订单
     * @param $data
     * @return array
     */
    public function addRecharge($data) {
        $recharge_sn = $this->sys_model_deposit->makeSn($data['user_id']);
        $insert = array(
            'pdr_sn' => $recharge_sn,
            'pdr_user_id' => $data['user_id'],
            'pdr_user_name' => $data['user_name'],
            'pdr_amount' => $data['amount'],
            'pdr_type' => isset($data['type']) ? $data['type'] : 0,
            'pdr_trade_sn' => $recharge_sn,
            'pdr_add_time' => time()
        );
        $insert_id = $this->sys_model_deposit->addRecharge($insert);
        if ($insert_id) {
            $data = array('pdr_id' => $insert_id, 'pdr_sn' => $recharge_sn);
            return callback(true, 'success_deposit_checkout', $data);
        } else {
            return callback(false, 'error_database_operation_failure');
        }
    }

    /**
     * 获取用户的钱包明细
     * @param $user_id
     * @param $page
     * @return array
     */
    public function getDepositLogByUserId($user_id, $page = 1) {
        $page = intval($page);
        $page = $page<1 ? 1 : $page;
        $limit = (empty($page) || $page<1) ? 10 : (10 * ($page-1) . ', 10');
        $data = $this->sys_model_deposit->getDepositLogList(array('pdl_user_id'=>$user_id), '*', 'pdl_add_time DESC', $limit);
        foreach ($data as &$item) {
            $item['deposit_type'] = $this->_getFriendlyDepositType($item['pdl_type']);
        }
        return callback(true, 'success_operation', $data);
    }

    /**
     * 获取用户的钱包明细条目数
     * @param $user_id
     * @return integer
     */
    public function getDepositLogCountByUserId($user_id) {
        return $this->sys_model_deposit->getDepositLogCount(array('pdl_user_id'=>$user_id));
    }

    private function _getFriendlyDepositType($type) {
        static $DEPOSIT_TYPE = array(
            'deposit' => 'success_pay_deposit',
            'recharge' => 'success_recharged',
            'order_pay' => 'success_pay',
            'order_freeze' => 'text_fund_freezing',
            'cash_apply' => 'success_apply_cash',
            'cash_pay' => 'success_cash',
            'cash_cancel' => 'text_cancel_cash'
        );
        return isset($DEPOSIT_TYPE[$type]) ? $DEPOSIT_TYPE[$type] : 'UnknownType';
    }
}