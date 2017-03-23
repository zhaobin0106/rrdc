<?php
namespace Logic;
class User {
    private $sys_model_user;
    public $_user_info = array();
    public $_user_id;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->sys_model_user = new \Sys_Model\User($registry);
    }

    public function getUserInfo($where = array()) {
        if (!empty($this->_user_info) && empty($where)) {
            return $this->_user_info;
        }
        if (!$this->_user_id) {
            return false;
        }
        $where = !empty($where) ? $where : array('user_id' => $this->_user_id);
        $this->_user_info = $this->sys_model_user->getUserInfo($where);
        return $this->_user_info;
    }

    public function setUserId($user_id) {
        $this->_user_id = $user_id;
    }

    public function userId() {
        if ($this->_user_id) {
            return $this->_user_id;
        }
        return null;
    }

    public function checkUserSign($data, $sign) {
        $user_id = $data['user_id'];
        $this->_user_id = $user_id;
        $user_info = $this->getUserInfo();
        if(empty($user_info['uuid'])) {
            return callback(false, 'success_not_logged');
        }

        $user_sign = md5($user_id . $user_info['uuid']);
        if ($user_sign == $sign) {
            return callback(true, 'success_auth_verification');
        }
        return callback(false, 'error_auth_verification');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function register($data) {
        $arr = array();
        $arr['nickname'] = $arr['mobile'] = $data['mobile'];
        $arr['uuid'] = $data['uuid'];
        $arr['login_time'] = time();
        $arr['ip'] = getIP();
        $arr['user_sn'] = $this->make_sn();
        $arr['add_time'] = TIMESTAMP;
        $arr['update_time'] = TIMESTAMP;
        if ($this->sys_model_user->existsMobile($data['mobile'])) {
            return callback(false, 'error_already_register');
        }
        $insert_id = $this->sys_model_user->addUser($arr);
        if ($insert_id) {
            return callback(true, 'success_register', array('user_id' => $insert_id, 'user_sn' => $arr['user_sn']));
        } else {
            return callback(false, 'error_database_operation_failure');
        }
    }

    /**
     * 写入实名认证
     * @param $user_id
     * @param $data
     * @return mixed
     */
    public function verify_identity($user_id, $data) {
        $where = array('user_id' => $user_id);

        $arr['real_name'] = $data['real_name'];
        $arr['identification'] = $data['identity'];
        $arr['credit_point'] = CREDIT_POINT; //信用分数
        $arr['cert_time'] = TIMESTAMP;
        $arr['verify_state'] = '1';
        return $this->sys_model_user->updateUser($where, $arr);
    }

    /**
     * 检测是否已注册
     * @param $mobile
     * @return array
     */
    public function existMobile($mobile) {
        $result = $this->sys_model_user->existsMobile($mobile);
        if ($result) {
            return callback(true, '', $result);
        }
        return callback(false);
    }

    public function make_sn() {
        return mt_rand(10, 99)
            . sprintf('%010', time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (float) microtime() * 1000);
    }

    /**
     * 登录
     * @param $mobile
     * @param $device_id
     * @return array
     */
    public function login($mobile, $device_id) {
        $result = $this->sys_model_user->getUserInfo(array('mobile' => $mobile));
        if (!$result) {
            return callback(false, 'error_user_nonexistence');
        }
        $update = $this->sys_model_user->updateUser(array('mobile' => $mobile), array('ip' => getIP(), 'uuid' => $device_id, 'login_time' => time()));
        if (!$update) {
            return callback(false, 'error_update_user_info');
        }

        $info = array(
            'user_id' => $result['user_id'],
            'user_sn' => $result['user_sn'],
            'mobile' => $result['mobile'],
            'nickname' => $result['nickname'],
            'avatar' => $result['avatar'],
            'deposit' => $result['deposit'],
            'deposit_state' => $result['deposit_state'],
            'available_deposit' => $result['available_deposit'],
            'freeze_deposit' => $result['freeze_deposit'],
            'freeze_recharge' => $result['freeze_recharge'],
            'credit_point' => $result['credit_point'],
            'real_name' => $result['real_name'],
            'identification' => $result['identification'],
            'verify_state' => $result['verify_state'],
            'available_state' => $result['available_state'],
            'recommend_num' => $result['recommend_num']
        );
        return callback(true, 'success_login', $info);
    }

    /**
     * 检测是否可交押金
     * @param $user_id
     * @return array
     */
    public function checkDeposit($user_id) {
        $result = $this->sys_model_user->getUserInfo(array('user_id' => $user_id), 'deposit, deposit_state');
        if (empty($result)) {
            return callback(false, 'error_missing_parameter');
        }
        if ($result['deposit_state'] == 1) {
            return callback(false, 'error_repeat_pay_deposit');
        }
        return callback(true);
    }

    /**
     * 检测是否可退押金
     * @param $user_id
     * @return array
     */
    public function checkCashDeposit($user_id) {
        $result = $this->sys_model_user->getUserInfo(array('user_id' => $user_id), 'deposit,deposit_state,available_deposit,freeze_recharge,freeze_deposit');
        if (!$result) {
            return callback(false, 'error_missing_parameter');
        }

        if ($result['deposit_state'] != 1) {
            return callback(false, 'error_no_pay_deposit');
        }

        if ($result['freeze_recharge'] > 0) {
            return callback(false, 'error_arrears_can_not_refund_deposit');
        }

        return callback(true);
    }

    /**
     * 更新用户信息
     * @param $user_id
     * @param $data
     * @return array
     */
    public function updateUserInfo($user_id, $data) {
        $data['update_time'] = TIMESTAMP;
        $update = $this->sys_model_user->updateUser(array('user_id'=>$user_id), $data);
        return $update ? callback(true) : callback(false);
    }

    public function logout($user_id) {
        return $this->updateUserInfo($user_id, array('uuid' => ''));
    }
}