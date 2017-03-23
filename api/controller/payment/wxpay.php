<?php
class ControllerPaymentWxpay extends Controller {
    public function notify() {
        $file_content = file_get_contents("php://input");
        if (!$file_content) {
            exit('no result');
        }

        $xml_result = simplexml_load_string($file_content);
        $new_xml = $xml_result->asXML();
        $arr = $this->from_xml($new_xml);
        $success = 'success';
        $failure = 'fail';
        $pay_sn = $trade_no = $arr['out_trade_no'];
        $this->load->library('sys_model/deposit', true);
        $recharge_info = $this->sys_model_deposit->getRechargeInfo(array('pdr_sn' => $pay_sn));
        if (empty($recharge_info)) {
            exit('no result');
        }
        if ($recharge_info['pdr_payment_state'] == 1) {
            exit($success);
        }

        $payment_info = array(
            'payment_code' => 'wxpay',
            'payment_name' => $this->language->get('text_weixin_payment')
        );

        $result = $this->sys_model_deposit->updateDepositChargeOrder($trade_no, $pay_sn, $payment_info, $recharge_info);

        exit($result['state'] ? $success : $failure);
    }

    public function from_xml($xml) {
        if (!$xml) {
            echo json_encode(array('code' => false, 'msg' => $this->language->get('error_xml_data')));
            exit;
        }
        //禁止引用外部XML实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }
}