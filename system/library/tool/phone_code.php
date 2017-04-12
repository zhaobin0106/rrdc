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
}