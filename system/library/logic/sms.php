<?php
/**
 * Created by PhpStorm.
 * User: estronger
 * Date: 2016/12/9
 * Time: 10:25
 */
namespace Logic;
class Sms {
    public function __construct($registry)
    {
        $this->sysmodel_sms = new \Sys_Model\Sms($registry);
        $this->tool_phone_code = new \Tool\Phone_code();
    }

    /**
     * 生成验证码
     * @return string
     */
    public function createVerifyCode() {
        return token(4, 'int');
    }

    /**
     * 发送短信，type为类型，注册或者其他
     * @param $mobile
     * @param $code
     * @param string $type
     * @return bool
     */
    public function sendSms($mobile, $code, $type = 'register') {
        if (empty($code)) {
            return false;
        }
        //第二个参数是数组
        $result = $this->tool_phone_code->sendSMS($mobile, array($code, SMS_TIMEOUT / 60));

        if (!$result) {
            return false;
        }

        $data = array(
            'mobile' => $mobile,
            'code' => $code,
            'type' => $type,
            'add_time' => TIMESTAMP,
            'ip' => getIP()
        );

        $result = $this->sysmodel_sms->addSms($data);
        return $result;
    }

    /**
     * 改变短信的状态
     * @param $mobile
     * @param $code
     * @param $state
     * @param string $type
     * @return bool
     */
    public function changeSmsStatus($mobile, $code, $state, $type = 'register') {
        if (empty($code)) return false;
        return $this->sysmodel_sms->updateSmsStatus(array('mobile' => $mobile, 'code' => $code, 'type' => $type), $state);
    }

    public function enValidated($mobile, $code, $type = 'register') {
        if (empty($code)) return false;
        $result = $this->sysmodel_sms->getSmsInfo(array('mobile' => $mobile,'code' => $code, 'type' => $type));
        if ($result['state'] == 2) {
            return false;
        } elseif ($result['state'] == 1) {
            return true;
        }
        return $this->changeSmsStatus($mobile, $code, 1, $type);
    }

    /**
     * 注册最后一步检测短信是否验证过，并且没有使用
     * @param $mobile
     * @param $code
     * @param string $type
     * @return bool
     */
    public function disableInvalid($mobile, $code, $type = 'register') {
        if (empty($mobile) || empty($code)) return false;
        if($mobile=='15088159005' || $mobile=='18145873506') return true; //苹果测试人员的特别通道。
        $result = $this->sysmodel_sms->getSmsInfo(array('mobile' => $mobile, 'code' => $code, 'type' => $type));
        if (!$result) {
            return false;
        }
        if ($result['state'] != 0) {
            return false;
        }
        return true;
    }

    public function enInvalid($mobile, $code, $type = 'register') {
        if($mobile=='15088159005' || $mobile=='18145873506') return true; //苹果测试人员的特别通道。
        if (empty($code)) return false;
        $result = $this->sysmodel_sms->getSmsInfo(array('mobile' => $mobile, 'code' => $code, 'type' => $type));
        if ($result['state'] != 0) {
            return false;
        }
        return $this->changeSmsStatus($mobile, $code, 1, $type);
    }

    public function validateCode($code, $type = 'register') {
        return $this->_dealCode($code, $type, 'enValidated');
    }

    public function submitCode($code, $type = 'register') {
        return $this->_dealCode($code, $type, 'enInvalid');
    }

    public function _dealCode($code, $type = 'register', $method = 'enValidated') {
        if (empty($code)) return false;
        $where = array(
            'code' => $code,
            'type' => $type
        );
        $result = $this->sysmodel_sms->getSmsInfo($where);
        if ($result) {
            if ($result['add_time'] > TIMESTAMP - SMS_TIMEOUT) {
                $update = $this->$method($code, $type);
                if ($update) {
                    return $update;
                }
            }
        }
        return false;
    }
}
