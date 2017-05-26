<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2016/12/7
 * Time: 15:24
 */
namespace Sys_Model;

class Deposit {
    private $registry;
    public $db;
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $registry->get('db');
    }

    public function makeSn($user_id) {
        return mt_rand(10, 99)
            . sprintf('%010d', time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (int) $user_id % 1000);
    }

    public function getRechargeList($condition = array(), $field = '*', $order = '', $limit = '') {
        $on = 'dr.pdr_user_id=u.user_id';
        return $this->db->table('deposit_recharge as dr,user as u')->where($condition)->field($field)->order($order)->limit($limit)->join('left')->on($on)->select();
    }

    public function addRecharge($data) {
        return $this->db->table('deposit_recharge')->insert($data);
    }

    public function updateRecharge($where, $data) {
        return $this->db->table('deposit_recharge')->where($where)->update($data);
    }

    public function getRechargeInfo($where, $fields = '*') {
        $on = 'dr.pdr_user_id=u.user_id';
        return $this->db->table('deposit_recharge as dr,user as u')->field($fields)->join('left')->on($on)->where($where)->limit(1)->find();
    }

    public function getOneRecharge($where, $field = '*', $order = '') {
        return $this->db->table('deposit_recharge')->field($field)->where($where)->order($order)->find();
    }

    public function getRechargeCount($where) {
        $on = 'dr.pdr_user_id=u.user_id';
        return $this->db->table('deposit_recharge as dr,user as u')->where($where)->join('left')->on($on)->count();
    }

    public function getDepositCashCount($where) {
        return $this->db->table('deposit_cash')->where($where)->count();
    }

    public function getDepositLogCount($where) {
        return $this->db->table('deposit_log')->where($where)->count();
    }

    public function getDepositLogList($where = array(), $field = '*', $order = '', $limit = '') {
        return $this->db->table('deposit_log')->where($where)->field($field)->order($order)->limit($limit)->select();
    }

    /**
     * @param $change_type
     * @param array $data
     * @return mixed
     */
    public function changeDeposit($change_type, $data = array()) {
        $data_log = array();
        $data_pd = array();
        $data_msg = array();

        $data_log['pdl_user_id'] = $data['user_id'];
        $data_log['pdl_user_name'] = $data['user_name'];
        $data_log['pdl_add_time'] = TIMESTAMP;
        $data_log['pdl_type'] = $change_type;

        $data_msg['time'] = date('Y-m-d H:i:s');
        //$data_msg['pd_url'] =

        switch ($change_type) {
            case 'order_pay' :
                $data_log['pdl_available_amount'] = -$data['amount'];
                $data_log['pdl_desc'] = '单车骑行，扣除支付预存款，订单号：' . $data['order_sn'];
                $data_log['pdl_payment_code'] = 'deposit';
                $data_log['pdl_payment_name'] = '余额支付';
                $data_log['pdl_sn'] = $data['order_sn'];
                $data_pd['available_deposit'] = array('exp', 'available_deposit-' . $data['amount']);
                //消息推送
                break;
            case 'order_freeze':
                $data_log['pdl_available_amount'] = -$data['amount'];
                $data_log['pdl_freeze_amount'] = $data['amount'];
                $data_log['pdl_desc'] = '单车骑行，费用不足，冻结预存款，订单号：' . $data['order_sn'];

                $data_log['pdl_payment_code'] = 'deposit';
                $data_log['pdl_payment_name'] = '余额支付';
                $data_log['pdl_sn'] = $data['order_sn'];

                $data_pd['freeze_recharge'] = array('exp', 'freeze_recharge+' . $data['amount']);
                $data_pd['available_deposit'] = '0';
                $data_pd['available_state'] = '0';
                break;
            case 'recharge' :
                $data_log['pdl_payment_code'] = isset($data['payment_code']) ? $data['payment_code'] : '';
                $data_log['pdl_payment_name'] = isset($data['payment_name']) ? $data['payment_name'] : '';
                $data_log['pdl_available_amount'] = $data['amount'];
                $data_log['pdl_desc'] = '充值，充值单号：' . $data['pdr_sn'];
                $data_log['pdl_sn'] = $data['pdr_sn'];
                $data_log['pdl_admin_name'] = $data['admin_name'];

                $user_info = $this->db->table('user')->field('user_id,available_deposit,freeze_recharge,available_state')->where(array('user_id' => $data['user_id']))->find();
                if (!empty($user_info) && $user_info['freeze_recharge'] > 0) {
                    if($data['amount'] - $user_info['freeze_recharge'] > 0){
                        $data_pd['freeze_recharge'] = 0;
                    }else{
                        $data_pd['freeze_recharge'] = array('exp', 'freeze_recharge-' . $data['amount']);
                    }
                    $data['amount'] = $data['amount'] - $user_info['freeze_recharge'];
                }

                if ($data['amount'] > 0) {
                    $data_pd['available_deposit'] = array('exp', 'available_deposit+' . $data['amount']);
                    if ($user_info['available_state'] == 0) {
                        $data_pd['available_state'] = '1';
                    }
                } else {
                    if (intval($user_info['available_state']) == 1) {
                        $data_pd['available_state'] = '0';
                    }
                }
                break;
            case 'deposit' :
                $data_log['pdl_payment_code'] = isset($data['payment_code']) ? $data['payment_code'] : '';
                $data_log['pdl_payment_name'] = isset($data['payment_name']) ? $data['payment_name'] : '';
                $data_log['pdl_available_amount'] = $data['amount'];
                $data_log['pdl_desc'] = '充值押金，充值单号：' . $data['pdr_sn'];
                $data_log['pdl_admin_name'] = $data['admin_name'];
                $data_log['pdl_sn'] = $data['pdr_sn'];

                $data_pd['deposit'] = array('exp', 'deposit+' . $data['amount']);
                $data_pd['deposit_state'] = '1';
                break;
            //押金申请提现
            case 'cash_apply' :
                $data_log['pdl_available_amount'] = -$data['amount'];
                $data_log['pdl_freeze_amount'] = $data['amount'];
                $data_log['pdl_desc'] = '申请提现，冻结预存款，提现单号：' . $data['pdc_sn'];

                $data_log['pdl_sn'] = $data['pdc_sn'];
                $data_log['pdl_payment_code'] = 'deposit';
                $data_log['pdl_payment_name'] = '余额支付';
                $data_pd['deposit'] = array('exp', 'deposit-' . $data['amount']);
                $data_pd['freeze_deposit'] = array('exp', 'freeze_deposit+' . $data['amount']);
                $data_pd['deposit_state'] = '0';
                $data_pd['available_state'] = '0';

                break;

            case 'cash_pay':
                $data_log['pdl_freeze_amount'] = -$data['amount'];
                $data_log['pdl_desc'] = '提现成功，提现单号：' . $data['pdr_sn'];
                $data_log['pdl_admin_name'] = $data['admin_name'];

                $data_log['pdl_payment_code'] = 'deposit';
                $data_log['pdl_payment_name'] = '余额支付';

                $data_pd['freeze_deposit'] = array('exp', array('freeze_deposit-' . $data['amount']));
                break;
            //取消申请
            case 'cash_cancel':
                $data_log['pdl_available_amount'] = $data['amount'];
                $data_log['pdl_freeze_amount'] = -$data['amount'];
                $data_log['pdl_desc'] = '取消提现申请，解冻预存款，提现单号：' . $data['amount'];
                $data_log['pdl_admin_name'] = $data['admin_name'];

                $data_log['pdl_payment_code'] = 'deposit';
                $data_log['pdl_payment_name'] = '余额支付';

                $data_pd['freeze_deposit'] = array('exp', 'freeze_deposit-' . $data['amount']);
                $data_pd['deposit'] = array('exp', 'deposit+' . $data['amount']);
                $data_pd['deposit_state'] = '1';

                break;

            default:
                throw new \Exception('参数错误');
        }
        //更新金额
        if ($change_type == 'deposit') {
            //充值押金时，检测是否再次充值押金，如果是已实名并且有余额则更新状态
            $user_info = $this->db->table('user')->where(array('user_id' => $data['user_id']))->find();
            if ($user_info['verify_state'] == 1 && $user_info['available_deposit'] > 0) {
                $data_pd['available_state'] = 1;
            }
        }

        $update = $this->db->table('user')->where(array('user_id' => $data['user_id']))->update($data_pd);
        if (!$update) {
            throw new \Exception('更新用户金额失败');
        }
        //写入记录
        $insert = $this->db->table('deposit_log')->insert($data_log);
        if (!$insert) {
            throw new \Exception('操作失败');
        }

        return $insert;
    }

    public function deleteRecharge($condition) {
        return $this->db->table('deposit_recharge')->where($condition)->delete();
    }

    public function addDepositCash($data) {
        return $this->db->table('deposit_cash')->insert($data);
    }

    public function getDepositCashInfo($where, $fields = '*') {
        return $this->db->table('deposit_cash')->field($fields)->where($where)->find();
    }

    public function getDepositCashList($where = array(), $limit = '', $order = '', $field = '*') {
        return $this->db->table('deposit_cash')->where($where)->limit($limit)->order($order)->field($field)->select();
    }

    public function getDepositCashTotal($where = array()) {
        return $this->db->table('deposit_cash')->where($where)->count();
    }

    public function deleteDepositCash($where) {
        return $this->db->table('deposit_cash')->where($where)->delete();
    }

    public function updateDepositCash($where, $data) {
        return $this->db->table('deposit_cash')->where($where)->update($data);
    }

    /**
     * 验证充值金额
     * @param $amount
     * @return array
     */
    public function validateRecharge($amount) {
        $amount = floatval($amount);
        $min = floatval(MIN_RECHARGE);
        $max = floatval(MAX_RECHARGE);
        if ($amount < $min) {
            return callback(false, '充值金额不可小于' . $min);
        } elseif ($amount > $max) {
            return callback(false, '充值金额不可大于' . $max);
        }
        return callback(true);
    }

    public function updateDepositChargeOrder($out_trade_no, $trade_no, $payment_info, $recharge_info) {
        $condition = array();
        $condition['pdr_sn'] = $recharge_info['pdr_sn'];
        $condition['pdr_payment_state'] = 0;
        $update = array();
        $update['pdr_payment_state'] = 1;
        $update['pdr_payment_time'] = time();
        $update['pdr_payment_code'] = $payment_info['payment_code'];
        $update['pdr_payment_name'] = $payment_info['payment_name'];
        $update['pdr_trade_sn'] = $trade_no;
        $update['pdr_admin'] = 'admin';
        $update['trace_no'] = $out_trade_no;

        try {
            $this->db->begin();
            $info = $this->db->table('deposit_recharge')->field('pdr_sn')->where(array('pdr_sn' => $recharge_info['pdr_sn'], 'pdr_payment_state' => 1))->find();
            if ($info) {
                throw new \Exception('订单已处理');
            }
            $state = $this->db->table('deposit_recharge')->where($condition)->update($update);
            if (!$state) {
                throw new \Exception('更新充值状态失败');
            }
            $data = array();
            $data['user_id'] = $recharge_info['pdr_user_id'];
            $data['user_name'] = $recharge_info['pdr_user_name'];
            $data['amount'] = $recharge_info['pdr_amount'];
            $data['pdr_sn'] = $recharge_info['pdr_sn'];
            $data['admin_name'] = $recharge_info['pdr_admin'];
            $data = array_merge($data, $payment_info);
            $type = $recharge_info['pdr_type'] ? 'deposit' : 'recharge';

            $this->changeDeposit($type, $data);
            $this->db->commit();
            return callback(true);
        } catch (\Exception $e) {
            $this->db->rollback();
            return callback(false, $e->getMessage());
        }
    }

    /**
     * 申请提现
     * @param $pdr_info
     * @return mixed
     */
    public function cashApply($pdr_info) {
        $data['pdc_sn'] = $this->makeSn($pdr_info['pdr_user_id']);
        $data['user_id'] = $pdr_info['pdr_user_id'];
        $data['user_name'] = $pdr_info['pdr_user_name'];
        $data['amount'] = $pdr_info['pdr_amount'];
        $data['admin_name'] = '';

        $insert_arr['pdc_sn'] = $data['pdc_sn'];
        $insert_arr['pdc_user_id'] = $data['user_id'];
        $insert_arr['pdc_user_name'] = $data['user_name'];
        $insert_arr['pdc_amount'] = $data['amount'];
        $insert_arr['pdc_payment_name'] = $pdr_info['pdr_payment_name'];
        $insert_arr['pdc_payment_code'] = $pdr_info['pdr_payment_code'];
        $insert_arr['pdc_add_time'] = time();
        $insert_arr['pdr_sn'] = $pdr_info['pdr_sn'];
        $insert_arr['trace_no'] = $pdr_info['trace_no'];

        try {
            $this->db->begin();
            $insert = $this->db->table('deposit_cash')->insert($insert_arr);
            if (!$insert) {
                throw new \Exception('写入申请库失败');
            }
            $this->changeDeposit('cash_apply', $data);
            $this->db->commit();
            return callback(true);
        } catch (\Exception $e) {
            $this->db->rollback();
            return callback(false, $e->getMessage());
        }
    }

    public function cashCancel($pdc_info) {
        $data['user_id'] = $pdc_info['pdc_user_id'];
        $data['user_name'] = $pdc_info['pdc_user_name'];
        $data['amount'] = $pdc_info['pdc_amount'];
        $data['admin_name'] = 'admin';

        try {
            $this->db->begin();
            $this->changeDeposit('cash_cancel', $data);
            $effect = $this->deleteDepositCash(array('pdc_id' => $pdc_info['pdc_id']));
            if (!$effect) {
                throw new \Exception('删除申请记录失败');
            }
            $this->db->commit();
            return callback(true);
        } catch (\Exception $e) {
            $this->db->rollback();
            return callback(false, $e->getMessage());
        }
    }

    public function aliPayRefund($cash_info) {
        $config = $this->registry->get('config');
        $alipay_config = array();
        $alipay_config['key'] = $config->get('config_alipay_key');
        $alipay_config['partner'] = $config->get('config_alipay_partner');
        $alipay_config['seller_id'] = $config->get('config_alipay_seller_id');
        $alipay_config['sign_type'] = strtoupper('md5');
        $alipay_config['input_charset'] = strtolower('utf-8');

        if (!empty($cash_info) && $cash_info['pdc_payment_state'] == 0 && $cash_info['pdc_payment_code'] == 'alipay') {
            $aliPaySubmit = new \payment\alipay\alipaySubmit($alipay_config);
            $parameter = $this->getPara($alipay_config);
            $refund_amount = $cash_info['pdc_amount'];
            $batch_no = $cash_info['pdc_batch_no'];
            if (empty($batch_no)) {
                $batch_no = date('YmdHis') . $cash_info['pdc_id'];
                $this->db->table('deposit_cash')->where(array('pdc_id' => $cash_info['pdc_id']))->update(array('pdc_batch_no' => $batch_no));
            } else {
                $date = substr($batch_no, 0, 8);
                if ($date != date('Ymd')) {
                    $batch_no = date('Ymd') . substr($batch_no, 8);
                    $this->db->table('deposit_cash')->where(array('pdc_id' => $cash_info['pdc_id']))->update(array('pdc_batch_no' => $batch_no));
                }
            }
            $parameter['batch_no'] = $batch_no;
            $parameter['detail_data'] = $cash_info['trace_no'] . '^' . $refund_amount . '^协商退款';

            $pay_url = $aliPaySubmit->buildRequestParaToString($parameter);
            $pay_url = $aliPaySubmit->alipay_gateway_new . $pay_url;

//            return $pay_url;
            @header("Location: " . $pay_url);
            exit;
        }
        return '';
    }

    public function getPara($alipay_config) {
        $parameter = array(
            'service' => 'refund_fastpay_by_platform_pwd',
            'partner' => trim($alipay_config['partner']),
            '_input_charset' => strtolower('utf-8'),
            'sign_type' => strtoupper('MD5'),
            'notify_url' => 'http://121.42.254.23/admin/payment/alipay.php',
            'seller_email' => trim($alipay_config['seller_id']),
            'refund_date' => date('Y-m-d H:i:s'),
            'batch_no' => '',
            'batch_num' => '1',
            'detail_data' => '',
        );
        return $parameter;
    }

    public function wxPayRefund($cash_info) {
        if (!empty($cash_info) && $cash_info['pdc_payment_state'] == 0 && $cash_info['pdc_payment_code'] == 'wxpay') {
            $config = $this->registry->get('config');
            $wx_app_id = $config->get('config_wxpay_appid');
            $wx_mch_id = $config->get('config_wxpay_mchid');
            $wx_app_secert_id = $config->get('config_wxpay_appsecert');
            $wx_key = $config->get('config_wxpay_key');

            $refund_amount = $cash_info['pdc_amount'];
            if ($refund_amount > 0) {
                $total_fee = $cash_info['pdc_amount'] * 100;
                $refund_fee = $refund_amount * 100;
                //退款批次号，支付宝要求当天日期
                $batch_no = $cash_info['pdc_batch_no'];
                if (empty($batch_no)) {
                    $batch_no = date('YmdHis') . $cash_info['pdc_id'];
                    $this->db->table('deposit_cash')->where(array('pdc_id' => $cash_info['pdc_id']))->update(array('pdc_batch_no' => $batch_no));
                } else {
                    //还需判断流水号是否今天，如非，前面八位要替换成当天的日期
                }

                define('WXPAY_APPID', $wx_app_id);
                define('WXPAY_MCHID', $wx_mch_id);
                define('WXPAY_KEY', $wx_key);
                define('WXPAY_APPSECRET', $wx_app_secert_id); //jdk支付会使用到

                library('payment/wechat/wxpayconfig');
                library('payment/wechat/wxpaydata');

                $input = new \Payment\WeChat\WxPayRefund();
                //$input->SetTransaction_id($cash_info['pdr_sn']);
                $input->SetOut_trade_no($cash_info['pdr_sn']);
                $input->SetTotal_fee($total_fee);
                $input->SetRefund_fee($refund_fee);
                $input->SetOut_refund_no($batch_no);
                $input->SetOp_user_id(\Payment\WeChat\WxPayConfig::MCHID);

                $data = \Payment\WeChat\WxPayApi::refund($input);

                //微信同步产生结果
                if (!empty($data) && $data['return_code'] == 'SUCCESS') {
                    if ($data['result_code'] == 'SUCCESS') {
                        $pdc_data = array();
                        $pdc_data['pdc_payment_time'] = time();
                        $pdc_data['pdc_payment_admin'] = 'admin';
                        $pdc_data['pdc_payment_state'] = '1';
                        try {
                            $this->db->begin();
                            $update = $this->updateDepositCash(array('pdc_id' => $cash_info['pdc_id']), $pdc_data);
                            if ($update) {
                                $arr['user_id'] = $cash_info['pdc_user_id'];
                                $arr['user_name'] = $cash_info['pdc_user_name'];
                                $arr['amount'] = $cash_info['pdc_amount'];
                                $arr['pdr_sn'] = $cash_info['pdc_sn'];
                                $arr['admin_name'] = 'admin';
                                $arr['payment_code'] = $cash_info['pdc_payment_code'];
                                $arr['payment_name'] = $cash_info['pdc_payment_name'];

                                $this->changeDeposit('cash_pay', $arr);
                                $update = $this->updateRecharge(array('pdr_sn' => $cash_info['pdr_sn']), array('pdr_payment_state' => -1));
                                if (!$update) {
                                    throw new \Exception('更新充值单号失败');
                                }
                            }
                            $this->db->commit();
                            return callback(true);
                        } catch (\Exception $e) {
                            $this->db->rollback();
                            return callback(false, $e->getMessage());
                        }
                    }
                }
                return callback(false, $data['err_code_des']);
            }
        }
        return callback(false, '参数错误');
    }


}