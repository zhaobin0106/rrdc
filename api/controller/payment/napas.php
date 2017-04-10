<?php

/**
 * 支付宝支付成功回调地址
 */
class ControllerPaymentNapas extends Controller {
    public function notify() {
        $success = 'success';
        $failure = 'fail';
        $config = $this->getNapasConfig();
        $SECURE_SECRET = $config['SecureHash']['val'];
        $this->load->library('sys_model/deposit', true);
        $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
        unset($_GET["vpc_SecureHash"]);

        $errorExists = false;
        file_put_contents('napas1.txt', var_export($_POST,true));
        file_put_contents('napas2.txt', var_export($_GET,true));
        if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_ResponseCode"] != "No Value Returned") {

            $md5HashData = $SECURE_SECRET;

            foreach($_GET as $key => $value) {
                if ($key != "vpc_SecureHash" or strlen($value) > 0) {
                    $md5HashData .= $value;
                }
            }
            file_put_contents('napas.txt', $md5HashData.'||'.$vpc_Txn_Secure_Hash);
            if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData))) {
                $hashValidated = "CORRECT";
            } else {
                $hashValidated = "INVALID HASH";
                $errorExists = true;
            }
        } else {
            $hashValidated = "Not Calculated - No 'SECURE_SECRET' present.";
            $errorExists = true;
        }

        if($errorExists){
            file_put_contents("napaspayERROR.txt","------------INVALID QUERY---------\r\n",FILE_APPEND);
            file_put_contents("napaspayERROR.txt","ERROR：".date('Y-m-d H:i:s', time())."\r\n",FILE_APPEND);
            file_put_contents("napaspayERROR.txt","ERROR CODE：".$hashValidated."\r\n",FILE_APPEND);
            file_put_contents("napaspayERROR.txt","------------INVALID QUERY---------\r\n",FILE_APPEND);
            file_put_contents("napaspayERROR.txt","\r\n",FILE_APPEND);
            echo fail;exit;
        }else{
            // Standard Receipt Data
//            $version         = null2unknown($_GET["vpc_Version"]);              //版本号
//            $locale          = null2unknown($_GET["vpc_Locale"]);               //
//            $command         = null2unknown($_GET["vpc_Command"]);
//            $merchantID      = null2unknown($_GET["vpc_Merchant"]);
            $merchTxnRef     = null2unknown($_GET["vpc_MerchTxnRef"]);          //订单号
            $amount          = null2unknown($_GET["vpc_Amount"]);               //订单金额
//            $currencyCode    = null2unknown($_GET["vpc_CurrencyCode"]);
//            $orderInfo       = null2unknown($_GET["vpc_OrderInfo"]);
            $txnResponseCode = null2unknown($_GET["vpc_ResponseCode"]);         //回调状态码
           $transactionNo   = null2unknown($_GET["vpc_TransactionNo"]);        //交易流水号
//            $additionData    = null2unknown($_GET["vpc_AdditionData"]);
//            $batchNo         = null2unknown($_GET["vpc_BatchNo"]);              //批号
//            $acqResponseCode = null2unknown($_GET["vpc_AcqResponseCode"]);
//            $message         = null2unknown($_GET["vpc_Message"]);

            $errorTxt = "";
            if ($txnResponseCode != "0" || $txnResponseCode == "No Value Returned" || $errorExists) {
                $errorTxt = getResponseDescription($txnResponseCode);
                echo fail;exit;
            }

            $total_fee_t = $amount/100;
            $out_trade_no = $merchTxnRef;
            $recharge_info = $this->sys_model_deposit->getRechargeInfo(array('pdr_sn' => $out_trade_no));
            if (empty($recharge_info)) {
                echo fail;exit;
            }
            if ($recharge_info['pdr_payment_state'] == 1) {
                echo success;exit;
            }
            $payment_info = array(
                'payment_code' => 'napas',
                'payment_name' => 'NAPAS'
            );

        $result = $this->sys_model_deposit->updateDepositChargeOrder($transactionNo, $out_trade_no, $payment_info, $recharge_info);
        exit($result['state'] ? $success : $failure);
    }
}
    public function qiantai(){
        //sleep(2);
//        $out_trade_no = $_POST['out_trade_no'];   //商户订单号
        $this->load->library('sys_model/deposit', true);
        $out_trade_no=$_GET['vpc_MerchTxnRef'];
        $recharge_info = $this->sys_model_deposit->getRechargeInfo(array('pdr_sn' => $out_trade_no));
        if(!$recharge_info || $recharge_info['pdr_payment_state'] != 1){
            //_message("支付失败", WEB_PATH."/member/cart/paysuccess");
            $type = 400;
        }else{
            $type = 200;
        }
        $this->assign('type', $type);
        $this->assign('out_trade_no', $out_trade_no);
        $this->response->setOutput($this->load->view('api/payment', $this->output));
    }
    private function getNapasConfig(){
        $config = $this->config->get('config_napas');
        return unserialize($config);       
    }
}
function getResponseDescription($responseCode) {

    switch ($responseCode) {
        case "0" : $result = "Giao dich thanh cong"; break;                                 //交易成功
        case "1" : $result = "Ngan hang tu choi thanh toan: the/tai khoan bi khoa"; break;  //银行拒绝支付：银行卡/账号被锁
        case "2" : $result = "Loi so 2"; break;                                             //第二个错误
        case "3" : $result = "The het han"; break;                                          //银行卡过期
        case "4" : $result = "Qua so lan giao dich cho phep. (Sai OTP, qua han muc trong ngay)"; break; //超过允许的交易次数（OTP错误，超过每日的限额）
        case "5" : $result = "Khong co tra loi tu Ngan hang"; break;                        //没有收到银行的回复
        case "6" : $result = "Loi giao tiep voi Ngan hang"; break;                          //银行的联系有误
        case "7" : $result = "Tai khoan khong du tien"; break;                              //账号余额不足
        case "8" : $result = "Loi du lieu truyen"; break;                                   //数据有误
        case "9" : $result = "Kieu giao dich khong duoc ho tro"; break;                     //没有支持的交易
        default  : $result = "Loi khong xac dinh";                                          //未确定的错误
    }
    return $result;
}

function null2unknown($data) {
    if ($data == "") {
        return "No Value Returned";
    } else {
        return $data;
    }
}
