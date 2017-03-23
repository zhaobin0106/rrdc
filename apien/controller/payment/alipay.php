<?php

/**
 * 支付宝支付成功回调地址
 */
class ControllerPaymentAlipay extends Controller {
    public function notify() {
        $success = 'success';
        $failure = 'fail';
//        $this->log->write(json_encode($this->request->post));
        $pdr_sn = $this->request->post['out_trade_no'];
        $trade_no = $this->request->post['trade_no'];
        $config = $this->getConfig();
        $noticeObj = new \payment\alipay\AlipayNotify($config);
        $this->load->library('sys_model/deposit', true);
        $recharge_info = $this->sys_model_deposit->getRechargeInfo(array('pdr_sn' => $pdr_sn));
        if (empty($recharge_info)) {
            exit('no result');
        }
        if ($recharge_info['pdr_payment_state'] == 1) {
            exit($success);
        }
        $verify = $noticeObj->verifyNotify();
        if (!$verify) {
            exit($failure);
        }
        $payment_info = array(
            'payment_code' => 'alipay',
            'payment_name' => $this->language->get('text_alipay_payment')
        );

        $result = $this->sys_model_deposit->updateDepositChargeOrder($trade_no, $pdr_sn, $payment_info, $recharge_info);
        exit($result['state'] ? $success : $failure);
    }

    private function getConfig() {
        $config = array();
        $config['partner'] = $this->config->get('config_alipay_partner');
        $config['seller_id'] = $this->config->get('config_alipay_seller_id');
        $config['cacert'] = getcwd() . '\\cacert.pem';
        $config['private_key_path'] = 'key/rsa_private_key.pem';
        $config['ali_public_key_path'] = 'key/alipay_public_key.pem';

        $config['key'] = $this->config->get('config_alipay_key');
        $config['notify_url'] = "";
        $config['sign_type'] = strtoupper('MD5');
        $config['input_charset'] = strtolower('utf-8');
        $config['transport'] = 'http';
        $config['payment_type'] = "1";
        $config['service'] = "mobile.securitypay.pay";

        return $config;
    }
}