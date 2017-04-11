<?php
namespace Tool;
class Phone_code {
    public function sendSMS($to, $data, $temp_id = 1) {
        $account_sid = SMS_ACCOUNT_SID;
        $account_token = SMS_ACCOUNT_TOKEN;
        $app_id = SMS_APP_ID;
        $temp_id = SMS_TEMP_ID; //142934
        $server_ip = 'app.cloopen.com';
        $server_port = '8883';
        $soft_version = '2013-12-26';

        $rest = new \Sms\Rest($server_ip, $server_port, $soft_version);
        $rest->setAccount($account_sid, $account_token);
        $rest->setAppId($app_id);

        $result = $rest->sendTemplateSMS($to, $data, $temp_id);
        if ($result == null) {
            return false;
        }
        if ($result['state'] == false) {
            return false;
        } else {
            return true;
        }
    }
    public function newsendSMS($to, $data, $temp_id = 1){
        $mobile= array (
            'UserName' => 'chicilon',
            'PassWord' => 'chicilon132',
            'BrandName' => 'VTDD',
            'ClientURL' => 'http://210.211.109.118/apibrandname/send?wsdl',
            'Signature' => '[BangXueShi]',
          );
        $code = $data[0];
        $msg = 'Your verification code is 0000, please enter it in 5 minutes, please ignore this message if you do not operate it.';
        $content = str_ireplace('0000',$code, $msg).$mobile['Signature'];

        $USERNAME  = $mobile['UserName'];
        $PASSWORD  = $mobile['PassWord'];
        $BRANDNAME = $mobile['BrandName'];
        $MESSAGE = $content;
        // $encode = mb_detect_encoding($MESSAGE, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        // if($encode != 'GBK'){
            // $MESSAGE = mb_convert_encoding($MESSAGE,'urf-8');
        // }
        $TYPE  = 1;
        $PHONE  = '84'.ltrim($to, '0');//'84'.ltrim($config["mobile"], '0');
        $IDREQ  = time();
        $client  = new \SoapClient($mobile['ClientURL']);
        $result  = $client->send(array("USERNAME" => $USERNAME, "PASSWORD" => $PASSWORD, "BRANDNAME" => $BRANDNAME, "MESSAGE" => $MESSAGE, "TYPE" => $TYPE, "PHONE" => $PHONE, "IDREQ" => $IDREQ));
        $response  = (array)$result;
        $response  = (array)$response['return'];
//      var_dump($response);

        if($response['result'] != '0'){
            return false;
        }else{
            return true;
        }

    }
}