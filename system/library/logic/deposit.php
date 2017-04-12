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
            return callback(true, '生成充值押金订单成功', $data);
        } else {
            return callback(false, '数据库操作失败，生成订单失败');
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
        return callback(true, '操作成功', $data);
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
            'deposit' => '交押金成功',
            'recharge' => '充值成功',
            'order_pay' => '支付成功',
            'order_freeze' => '资金冻结',
            'cash_apply' => '申请提现',
            'cash_pay' => '提现成功',
            'cash_cancel' => '取消提现申请'
        );
        return isset($DEPOSIT_TYPE[$type]) ? $DEPOSIT_TYPE[$type] : 'UnknownType';
    }
}