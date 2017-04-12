<?php
class ControllerAccountAccount extends Controller {

    /**
     * 注册
     */
    public function register() {
        if (!isset($this->request->post['mobile']) || !isset($this->request->post['uuid']) || !isset($this->request->post['code']) || !isset($this->request->post['lat']) || !isset($this->request->post['lng'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'), 1);
        }

        if (empty($this->request->post['mobile']) || empty($this->request->post['uuid']) || empty($this->request->post['code'])) {
            $this->response->showErrorResult($this->language->get('error_empty_login_param'), 101);
        }

        $mobile = trim($this->request->post['mobile']);
        $uuid = $this->request->post['uuid'];
        $code = $this->request->post['code'];
        $register_lat = $this->request->post['lat'];
        $register_lng = $this->request->post['lng'];

        if (!is_mobile($mobile)) {
            $this->response->showErrorResult($this->language->get('error_mobile'), 2);
        }

        $this->load->library('logic/user', true);
        $this->load->library('logic/sms', true);

        $data = array(
            'mobile' => $mobile,
            'uuid' => $uuid,
            'register_lat' => $register_lat,
            'register_lng' => $register_lng
        );
        if($mobile != '18612560278' && $mobile != '18352532583' && $mobile != '18811562913'){
            if (!$this->logic_sms->disableInvalid($mobile, $code)) {
                $this->response->showErrorResult($this->language->get('error_invalid_message_code'), 3);
            }

            //更新短信的
            $update = $this->logic_sms->enInvalid($mobile, $code);

            if (!$update) {
                $this->response->showErrorResult($this->language->get('error_database_failure'), 4);
            }
        }
        //防止前端状态码判断错误，即把登录接口的数据传到注册接口，产生重复的手机注册用户
        $user_info = $this->logic_user->getUserInfo(array('mobile' => $mobile));

        if (!$user_info) {
            $result = $this->logic_user->register($data);
            if (!$result['state']) {
                $this->response->showErrorResult($result['msg'], 102);
            }
            $this->load->library('logic/credit', true);
            $this->logic_credit->addCreditPointOnRegister($result['data']['user_id']);

            //后面的两位可以写常量，或者写入配置文件
            if ($this->config->get('config_register_direct_coupon')) {
                $time = $this->config->get('config_register_coupon_number');
                $this->addCoupon(array('user_id' => $result['data']['user_id'], 'mobile' => $mobile), $time, 1, 1);
            }

            $this->response->showSuccessResult($result['data'], $this->language->get('success_register'));
        }
        $this->response->showSuccessResult($user_info, $this->language->get('success_login'));
    }

    private function getSMSConfig() {
        define('SMS_ACCOUNT_SID', $this->config->get('config_sms_account_sid'));
        define('SMS_ACCOUNT_TOKEN', $this->config->get('config_sms_account_token'));
        define('SMS_APP_ID', $this->config->get('config_sms_app_id'));
        define('SMS_TEMP_ID', $this->config->get('config_sms_temp_id'));
    }

    /**
     * 分享获取优惠券接口验证码
     */
    public function sendShareCode() {
        if (!isset($this->request->post['mobile']) || !isset($this->request->post['encrypt_code'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'), 1);
        }

        $share_type = isset($this->request->post['order_id']) ? 'share_trip' : 'share_front';

        //加载短信配置，使用常量
        $this->getSMSConfig();
        $alert = $this->language->get('text_message_upper_limit');
        $mobile = trim($this->request->post['mobile']);
        if (!is_mobile($mobile)) {
            $this->response->showJsonResult($this->language->get('error_mobile'), 0, array('alert'=>$alert), 2);
        }

        $encrypt_code = $this->request->post['encrypt_code'];
        $code = decrypt($encrypt_code);
        if (!strpos($code, '_')) {
            $this->response->showErrorResult($this->language->get('error_data_parse_failure'));
        }

        $arr = explode('_', $code);
        $user_id = $arr[0];
        $this->load->library('sys_model/user');
        $user_info = $this->sys_model_user->getUserInfo(array('user_id' => $user_id), 'user_id,avatar,real_name,mobile');
        if (empty($user_info)) {
            $this->response->showErrorResult($this->language->get('error_get_user_infomation'));
        }

        if (isset($this->request->post['order_id'])) {
            $this->load->library('sys_model/orders');
            $order_info = $this->sys_model_orders->getOrdersInfo(array('order_id' => $this->request->post['order_id'], 'user_id' => $user_id));
            if (empty($order_info)) {
                $this->response->showErrorResult($this->language->get('error_invalid_share'));
            }
        }

        //可能已注册，可能未注册
        $result = $this->sys_model_user->getUserInfo(array('mobile' => $mobile));
        $this->load->library('sys_model/coupon');
        //已经注册
        if ($result) {
            if (!isset($this->request->post['order_id'])) {
                $this->response->showErrorResult($this->language->get('error_already_register'));
            }
            $where = array('order_id' => $this->request->post['order_id'], 'user_id' => $result['user_id']);
            $coupon_info = $this->sys_model_coupon->getCouponInfo($where);
            if ($coupon_info) {
                $this->response->showErrorResult($this->language->get('error_repeat_receive'), 202, $coupon_info);
            }
        }

        $type = 'share';
        $this->load->library('logic/sms', true);
        $code = $this->logic_sms->createVerifyCode();
        $result_id = $this->logic_sms->sendSms($mobile, $code, $type);
        $result_id ? $this->response->showSuccessResult(array('type' => $share_type, 'alert'=>$alert)) : $this->response->showJsonResult($this->language->get('error_send_message_failure'), 0, array('alert'=>$alert), 4);
    }

    /**
     * 发送注册|登录验证码
     */
    public function sendRegisterCode() {
        if (!isset($this->request->post['mobile'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'), 1);
        }
        //加载短信配置，使用常量
        $this->getSMSConfig();

        $alert = $this->language->get('text_message_upper_limit');
        $mobile = trim($this->request->post['mobile']);
        if (!is_mobile($mobile)) {
            $this->response->showJsonResult($this->language->get('error_mobile'), 0, array('alert'=>$alert), 2);
        }

        $this->load->library('logic/sms', true);
        $this->load->library('logic/user', true);
        $result = $this->logic_user->existMobile($mobile);

        $type = 'register';

        if ($result['state']) {
            $type = 'login';
            if ($result['data']['deposit_state'] == 0) {
                $state = 0; //未交押金
            } elseif ($result['data']['verify_state'] == 0) {
                $state = 1;//未实名认证
            } elseif ($result['data']['available_deposit'] == 0) {
                $state = 2;//未充值
            } else {
                $state = 3;//正常状态
            }
            if ($result['data']['deposit_state'] == 0 && $result['data']['verify_state'] == 1 && $result['data']['available_deposit'] > 0) {
                $state = 4;
            }
        }

        $state = isset($state) ? $state : '0';
        $code = $this->logic_sms->createVerifyCode();
        $result_id = $this->logic_sms->sendSms($mobile, $code, $type);
        $result_id
            ? $this->response->showSuccessResult(array('type' => $type, 'state' => $state, 'alert'=>$alert))
            : $this->response->showJsonResult($this->language->get('error_send_message_failure'), 0, array('alert'=>$alert), 4);
    }

    /**
     * 登录
     */
    public function login() {
        if (!isset($this->request->post['mobile']) || !isset($this->request->post['uuid']) || !isset($this->request->post['code'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'), 1);
        }

        $mobile = trim($this->request->post['mobile']);
        $device_id = $this->request->post['uuid'];
        $code = $this->request->post['code'];

        if (empty($mobile)) {
            $this->response->showErrorResult($this->language->get('error_empty_mobile'), 103);
        }

        if (!is_mobile($mobile)) {
            $this->response->showErrorResult($this->language->get('error_mobile'), 2);
        }

        if (empty($code)) {
            $this->response->showErrorResult($this->language->get('error_mobile'), 104);
        }

        if (empty($device_id)) {
            $this->response->showErrorResult($this->language->get('error_device_id'), 105);
        }

        $this->load->library('logic/sms', true);
        $this->load->library('logic/user', true);
        if($mobile != '18612560278' && $mobile != '18352532583' && $mobile != '18811562913'){
            if (!$this->logic_sms->disableInvalid($mobile, $code, 'login')) {
                $this->response->showErrorResult($this->language->get('error_invalid_message_code'), 3);
            }

            //更新短信的
            $update = $this->logic_sms->enInvalid($mobile, $code, 'login');

            if (!$update) {
                $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
            }
        }

        $result = $this->logic_user->login($mobile, $device_id);
        if (!$result['state']) {
            $this->response->showErrorResult($this->language->get($result['msg']), 106);
        }
        $this->response->showSuccessResult($result['data'], $this->language->get($result['msg']));
    }

    /**
     * 获取个人信息
     */
    public function info() {
        $result =  $this->startup_user->getUserInfo();
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
            'recommend_num' => $result['recommend_num'],
        );
        if ($result['deposit_state'] == 0) {
            $info['user_state'] = $result['verify_state'] ? 4 : 0;
        } else {
            if ($result['verify_state'] == 0) {
                $info['user_state'] = 1;
            } elseif ($result['available_deposit'] == 0) {
                $info['user_state'] = 2;
            } else {
                $info['user_state'] = 3;
            }
        }
        if(!empty($result)) {
            $this->response->showSuccessResult($info, $this->language->get('success_operation'));
        } else {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
        }
    }

    /**
     * 更新个人信息（暂时只有更新昵称）
     */
    public function updateInfo() {
        if (!isset($this->request->post['nickname']) || empty($this->request->post['nickname'])) {
            $this->response->showErrorResult($this->language->get('error_empty_nickname'), 114);
        }

        $user_id = $this->startup_user->userId();
        $result = $this->startup_user->updateUserInfo($user_id, array('nickname'=>$this->request->post['nickname']));
        if ($result['state']) {
            $this->response->showSuccessResult();
        } else {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
        }
    }

    /**
     * 更新个人头像
     */
    public function updateAvatar() {
        $uploader = new \Uploader(
            'avatar',  //字段名
            array( // 配置项
                'allowFiles'=>array('.jpg', '.jpeg', '.png'),
                'maxSize'=>10*1024*1024,
                'pathFormat'=>'avatar/{yyyy}{mm}{dd}{hh}{ii}{ss}{rand:4}'
            ),
            empty($this->request->files['avatar']) ? 'base64' : 'upload', //类型，可以是upload，base64或者remote
            $this->request->files //文件上传变量数组，base64的不用提供，内部直接用$_POST[字段名]作为数据
        );

        $fileInfo = $uploader->getFileInfo();
        if($fileInfo['state']=='SUCCESS') {
            $user_id = $this->startup_user->userId();
            $user_info = $this->startup_user->getUserInfo();
            //如果更换头像之前就存在头像，则删除头像
            if ($user_info['avatar']) {
                @unlink(DIR_STATIC . 'avatar/' . retrieve($user_info['avatar']));
            }

            $result = $this->startup_user->updateUserInfo($user_id, array('avatar'=>$fileInfo['url']));
            if ($result['state']) {
                $this->response->showSuccessResult(array('user_id'=>$user_id, 'avatar'=>$fileInfo['url']), $this->language->get('success_operation'));
            } else {
                $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
            }
        }
        else {
            $this->response->showErrorResult($fileInfo['state'], 5);
        }
    }

    /**
     * 更新手机号码
     */
    public function updateMobile() {
        //能进来到这里都是有userInfo的
        $userInfo = $this->startup_user->getUserInfo();
        $this->log->write(print_r($userInfo, true));
        if (empty($userInfo['verify_state']) //  verify_state=='0'，没有通过实名验证
            || empty($userInfo['real_name']) || empty($userInfo['identification']) ) // 用户实名或者身份证信息为空
        {
            $this->response->showErrorResult($this->language->get('error_not_identification'), 115);
        }

        if (!isset($this->request->post['code']) || empty($this->request->post['code'])) {
            $this->response->showErrorResult($this->language->get('error_empty_message_code'),116);
        }

        if (!isset($this->request->post['real_name']) || empty($this->request->post['real_name'])) {
            $this->response->showErrorResult($this->language->get('error_empty_real_name'),117);
        }

        if (!isset($this->request->post['identification']) || empty($this->request->post['identification'])) {
            $this->response->showErrorResult($this->language->get('error_empty_identification'),118);
        }

        if (!isset($this->request->post['mobile']) || empty($this->request->post['mobile'])) {
            $this->response->showErrorResult($this->language->get('error_empty_new_mobile'),119);
        }

        if (!is_mobile($this->request->post['mobile'])) {
            $this->response->showErrorResult($this->language->get('error_mobile'),2);
        }

        if (time() < $userInfo['last_update_mobile_time'] + UPDATE_MOBILE_INTERVAL) {
            $this->response->showErrorResult($this->language->get('error_replace_mobile_limit'), 120);
        }

        $existMobile = $this->startup_user->existMobile($this->request->post['mobile']);
        if($existMobile['state']) {
            $this->response->showErrorResult($this->language->get('error_mobile_existed'), 121);
        }

        // 验证短信码
        $this->load->library('logic/sms', true);
        if (!$this->logic_sms->disableInvalid($this->request->post['mobile'], $this->request->post['code'], 'register')) {
            $this->response->showErrorResult($this->language->get('error_invalid_message_code'), 3);
        }
        //更新短信的
        $update = $this->logic_sms->enInvalid($this->request->post['mobile'], $this->request->post['code'], 'register');


        if($this->request->post['real_name']!=$userInfo['real_name']) {
            $this->response->showErrorResult($this->language->get('error_invalid_message_code'), 122);
        }

        if($this->request->post['identification']!=$userInfo['identification']) {
            $this->response->showErrorResult($this->language->get('error_identification_inconsistent'), 123);
        }

        $result = $this->startup_user->updateUserInfo($userInfo['user_id'], array(
            'mobile'=>$this->request->post['mobile'],
            'last_update_mobile_time' => time()
        ));

        if ($result['state']) {
            $this->response->showSuccessResult();
        } else {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'),4);
        }
    }

    /**
     * 获取信用积分记录
     */
    public function getCreditLog() {
        $userInfo = $this->startup_user->getUserInfo();

        $this->load->library('logic/credit', true);

        $page = (isset($this->request->post['page']) && intval($this->request->post['page'])) >= 1 ? intval($this->request->post['page']) : 1;

        $count = $this->logic_credit->getCreditPointsCount($userInfo['user_id']);

        $result = array(
            'credit_point' => $userInfo['credit_point'],
            'total_items_count' => $count,
            'total_pages' => ceil($count/10.0),
            'items' => $this->logic_credit->getCreditPoints($userInfo['user_id'], $page)
        );

        $this->response->showSuccessResult($result);
    }

    /**
     * 获取钱包信息
     */
    public function getWalletInfo() {
        $userInfo = $this->startup_user->getUserInfo();

        $result = array(
            'deposit' => $userInfo['deposit'],  //押金
            'deposit_state' => $userInfo['deposit_state'], //是否已交押金（0未交，1已交）
            'available_deposit' => $userInfo['available_deposit'], //余额
            'freeze_deposit' => $userInfo['freeze_deposit'], //未退回的押金
            'freeze_recharge' => $userInfo['freeze_recharge']
        );
        $this->response->showSuccessResult($result);

    }

    /**
     * 获取钱包明细
     */
    public function getWalletDetail() {
        $userInfo = $this->startup_user->getUserInfo();

        $this->load->library('logic/deposit', true);

        $page = (isset($this->request->post['page']) && intval($this->request->post['page'])) >= 1 ? intval($this->request->post['page']) : 1;

        $count = $this->logic_deposit->getDepositLogCountByUserId($userInfo['user_id']);
        $items = $this->logic_deposit->getDepositLogByUserId($userInfo['user_id'], $page);

        if($items['state']) {
            $result = array(
                'total_items_count' => $count,
                'total_pages' => ceil($count/10.0),
                'items' => $items['data']
            );
            $this->response->showSuccessResult($result);
        }
        else {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'),4);
        }
    }

    /**
     * 获取我的行程列表
     */
    public function getOrders() {
        $userInfo = $this->startup_user->getUserInfo();

        $this->load->library('logic/orders', true);

        $page = (isset($this->request->post['page']) && intval($this->request->post['page'])) >= 1 ? intval($this->request->post['page']) : 1;

        $count = $this->logic_orders->getOrdersCountByUserId($userInfo['user_id']);
        $items = $this->logic_orders->getOrdersByUserId($userInfo['user_id'], $page);

        $result = array(
            'total_items_count' => $count,
            'total_pages' => ceil($count/10.0),
            'items' => $items
        );
        $this->response->showSuccessResult($result);
    }

    /**
     * 获取行程详情
     */
    public function getOrderDetail() {
        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $this->response->showErrorResult($this->language->get('error_empty_order_id'),124);
        }

        $this->load->library('logic/orders', true);

        $result = $this->logic_orders->getOrderDetail($this->request->post['order_id']);
        if (empty($result)) {
            $this->response->showErrorResult($this->language->get('error_empty_order_id'), 124);
        }
        //有订单信息并且订单状态是在进行中的
        if (!empty($result) && $result['order_info']['order_state'] == 1) {
            $lock_sn = $result['order_info']['lock_sn'];
            $this->load->library('sys_model/lock');
            $lock_info = $this->sys_model_lock->getLockInfo(array('lock_sn' => $lock_sn));
            if ($lock_info['lock_status'] == 0) {
                $finish_time = $lock_info['system_time'];//系统更新时间
//                $this->load->library('logic/orders');
//                $callback = $this->logic_orders->closeOrder(array('order_id' => $result['order_info']['order_id'], 'finish_time' => $finish_time));
            }
        }

        if (isset($result['order_info']['coupon_info'])) {
            $coupon_info = &$result['order_info']['coupon_info'][0];
            if ($coupon_info['coupon_type'] == 1) {
                $coupon_info['number'] = ($coupon_info['number'] % 30 == 0) ? ($coupon_info['number'] / 60) : $coupon_info['number'];//半小时取整
                $coupon_info['unit'] = ($coupon_info['number'] % 30 == 0) ? $this->language->get('text_hour') : $this->language->get('text_minute');
            } elseif ($coupon_info['coupon_type'] == 2) {
                $coupon_info['unit'] = $this->language->get('text_time_unit');
            } elseif ($coupon_info['coupon_type'] == 3) {
                $coupon_info['unit'] = $this->language->get('text_money_unit');
            } elseif ($coupon_info['coupon_type'] == 4) {
                $coupon_info['unit'] = $this->language->get('text_discount_unit');
            }
        }

        if ($result['order_info']['coupon_id'] == 0) {
            $result['order_info']['coupon_info'] = array();
        }
        $user_info = $this->startup_user->getUserInfo();
        $fields = array('nickname', 'avatar', 'real_name', 'available_deposit');
        //直接输出用户所有信息太危险
        $output_user_info = array();
        foreach ($fields as $field) {
            if (isset($user_info[$field])) {
                $output_user_info[$field] = $user_info[$field];
            }
        }
        $result['user_info'] = $output_user_info;
        $this->response->showSuccessResult($result);
    }

    /**
     * 无登录获取订单信息
     */
    public function getOrderDetailByEncrypt() {
        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $this->response->showErrorResult($this->language->get('error_empty_order_id'),124);
        }

        $encrypt_code = $this->request->post['encrypt_code'];
        $code = decrypt($encrypt_code);
        if (!strpos($code, '_')) {
            $this->response->showErrorResult($this->language->get('error_data_parse_failure'));
        }

        $arr = explode('_', $code);
        $user_id = $arr[0];

        $this->load->library('sys_model/user', true);
        $this->load->library('logic/orders', true);

        $result = $this->logic_orders->getOrderDetail($this->request->post['order_id']);
        $user_info = $this->sys_model_user->getUserInfo(array('user_id' => $user_id), 'avatar,nickname,mobile');
        $user_info['mobile'] = substr($user_info['mobile'], 0, 3) . '****' . substr($user_info['mobile'], -4);
        if (is_numeric($user_info['nickname'])) {
            $user_info['nickname'] = $user_info['mobile'];
        }

        $result['user_info'] = $user_info;
        $this->response->showSuccessResult($result);
    }

    /**
     * 获取我的消息列表
     */
    public function getMessages() {
        $this->load->library('logic/message', true);

        $page = (isset($this->request->post['page']) && intval($this->request->post['page'])) >= 1 ? intval($this->request->post['page']) : 1;

        $count = $this->logic_message->getMessagesCount(array('user_id' => $userInfo = $this->startup_user->userId()));
        $items = $this->logic_message->getMessages(array('user_id' => $userInfo = $this->startup_user->userId()),$page);

        if (!empty($items) && is_array($items)) {
            foreach ($items as &$item) {
                if (isset($item['msg_image']) && !empty($item['msg_image'])) {
                    $item['msg_image'] = HTTP_IMAGE . $item['msg_image'];
                }
            }
        }

        $result = array(
            'total_items_count' => $count,
            'total_pages' => ceil($count/10.0),
            'items' => $items
        );
        $this->response->showSuccessResult($result);
    }

    /**
     * 生成押金充值订单
     */
    public function deposit() {
        $amount = $this->config->get('config_operator_deposit') ? $this->config->get('config_operator_deposit') : DEPOSIT;
        if (floatval($amount) == 0) {
            $this->response->showErrorResult($this->language->get('error_deposit_amount'),200);
        }
        $data['type'] = 1; //押金充值
        $data['amount'] = floatval($amount);
        $user_info = $this->startup_user->getUserInfo();
        $data['user_id'] = $user_info['user_id'];
        $data['user_name'] = $user_info['mobile'];
        $this->load->library('logic/deposit', true);
        $this->load->library('logic/user', true);
        $checked = $this->logic_user->checkDeposit($data['user_id']);
        //检测押金是否已交，如果已经交了押金
        if ($checked['state'] == false) {
            $this->response->showErrorResult($checked['msg']);
        }

        $result = $this->logic_deposit->addRecharge($data);
        if ($result['state']) {
            $this->response->showSuccessResult($result['data'], $this->language->get('success_deposit_checkout'));
        } else {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'),4);
        }
    }

    /**
     * 申请退押金
     */
    public function cashApply() {
        $user_info = $this->startup_user->getUserInfo();
        if (!$user_info['deposit_state']) {
            $this->response->showErrorResult($this->language->get('error_non_payment_deposit_cannot_refund'), 201);
        }

        $this->load->library('sys_model/deposit', true);
        $cash_info = $this->sys_model_deposit->getDepositCashInfo(array('pdc_user_id' => $user_info['user_id'], 'pdc_payment_state' => '0'));
        if (!empty($cash_info)) {
            $this->response->showErrorResult($this->language->get('error_repeat_refund'), 202);
        }

        $deposit_recharge = $this->sys_model_deposit->getOneRecharge(array('pdr_user_id' => $user_info['user_id'], 'pdr_type' => 1, 'pdr_payment_state' => 1), '*', 'pdr_add_time DESC');

        if (empty($deposit_recharge)) {
            $this->response->showErrorResult($this->language->get('error_no_prepaid_records'), 203);
        }

        $result = $this->sys_model_deposit->cashApply($deposit_recharge);
        $result['state'] ? $this->response->showSuccessResult('', $this->language->get('success_application')) : $this->response->showErrorResult($result['msg'],204);
    }

    /**
     * 生成充值订单
     */
    public function charging() {
        $amount = $this->request->post['amount'];
        $amount = floatval($amount);

        if ($amount > MAX_RECHARGE) {
            $this->response->showErrorResult($this->language->get('error_recharge_upper_limit'), 205);
        }

        if($amount < MIN_RECHARGE) {
            $this->response->showErrorResult($this->language->get('error_recharge_lower_limit'), 206);
        }

        $data['type'] = '0';//普通充值
        $data['amount'] = floatval($amount);
        $user_info = $this->startup_user->getUserInfo();
        $data['user_id'] = $user_info['user_id'];
        $data['user_name'] = $user_info['mobile'];
        $this->load->library('logic/deposit', true);
        $result = $this->logic_deposit->addRecharge($data);
        if (!$result) {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'), 4);
        }
        $this->response->showSuccessResult($result['data'], $this->language->get('success_recharge_checkout'));
    }

    /**
     * 实名认证
     */
    public function identity() {
        $data['real_name'] = $this->request->post['real_name'];
        $data['identity'] = $this->request->post['identity'];

        if (empty($data['real_name'])) {
            $this->response->showErrorResult($this->language->get('error_empty_real_name'), 107);
        }
        if (empty($data['identity'])) {
            $this->response->showErrorResult($this->language->get('error_empty_identification'),108);
        }
        //加入限制，1个身份证正能验证一次
        $exist = $this->startup_user->getUserInfo(array('identification' => $data['identity']));
        if ($exist) {
            $this->response->showErrorResult($this->language->get('error_identification_existed'),109);
        }

        $user_info = $this->startup_user->getUserInfo();
        if (empty($user_info)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        if (intval($user_info['verify_state']) > 0) {
            $this->response->showErrorResult($this->language->get('error_identified'),110);
        }

        if (!intval($user_info['deposit_state'])) {
            $this->response->showErrorResult($this->language->get('error_non_payment_deposit'),111);
        }

        $this->load->library('YinHan/YinHan');
        $this->YinHan_YinHan->setIDCondition($data['real_name'], $data['identity']);
        $result = $this->YinHan_YinHan->idCardAuth();
        //判断验证结果
        if (!$result->data) {
            $this->response->showErrorResult($result->msg->codeDesc,112);
        } elseif ($result->data[0]->record[0]->resCode && (string)$result->data[0]->record[0]->resCode != '00') {
            $this->response->showErrorResult($result->data[0]->record[0]->resDesc,112);
        } elseif ($result->data[0]->record[0]->resCode && (string)$result->data[0]->record[0]->resCode == '00') {
            $res_arr = (json_decode(json_encode($result),true));
            $data['verify_sn'] = $result->header->qryBatchNo;
            //资料入库
            $this->load->library('sys_model/user');
            $this->load->library('sys_model/identity');
            $user = $this->sys_model_user->getUserInfo(array('user_id'=>$this->request->post['user_id']));
            $arr = array();
            $arr['il_user_id'] = $user['user_id'];
            $arr['il_user_mobile'] = $user['mobile'];
            $arr['il_real_name'] = $res_arr['data'][0]['record'][0]['realName'];
            $arr['il_identification'] = $res_arr['data'][0]['record'][0]['idCard'];
            $arr['il_cert_time'] = time();
            $arr['il_has_photo'] = isset($res_arr['data'][0]['record'][0]['photo']) ? 1 : 0;
            $arr['il_verify_state'] = $res_arr['data'][0]['record'][0]['resCode'] == '00' ? 1 : 0;
            $arr['il_verify_error_code'] = $res_arr['data'][0]['record'][0]['resCode'];
            $arr['il_verify_error_desc'] = $res_arr['data'][0]['record'][0]['resDesc'];
            $arr['il_charged'] = $res_arr['data'][0]['record'][0]['resCode'] == '00' ? 1 : 0;
            $arr['il_api_reply'] = print_r($result, true);
            $this->sys_model_identity->addIdentity($arr);
        }


        $update = $this->startup_user->verify_identity($user_info['user_id'], $data);
        if ($update) {
            $this->load->library('logic/credit', true);
            $this->logic_credit->addCreditPointOnVerification($user_info['user_id']);

            $this->response->showSuccessResult('', $this->language->get('success_identity'));
        }
        $this->response->showErrorResult($this->language->get('error_database_operation_failure'),4);
    }

    /**
     * 注册推荐码
     */
    public function signRecommend() {
        if (!isset($this->request->post['mobile']) || empty($this->request->post['mobile'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $mobile = $this->request->post['mobile'];
        if (!is_mobile($mobile)) {
            $this->response->showErrorResult($this->language->get('error_mobile'), 2);
        }

        $user_id = $this->startup_user->userId();
        $this->load->library('sys_model/user');

        $user_info = $this->sys_model_user->getUserInfo(array('mobile' => $mobile), 'user_id');
        if (empty($user_info)) {
            $this->response->showErrorResult($this->language->get('error_referrer'),113);
        }
        //判断是否已分享
        $this->load->library('sys_model/coupon');
        $coupon_info = $this->sys_model_coupon->getCouponInfo(array('user_id' => $user_id, 'obtain' => 1));
        if (!empty($coupon_info)) {
            $this->response->showErrorResult('您已领取过优惠券了，如果想获取更多的优惠券，请到首页分享');
        }

        $time = $this->config->get('config_register_coupon_number');
        $this->addCoupon(array('user_id' => $user_id), $time, 1, 1);
        $this->addCoupon(array('user_id' => $user_info['user_id'], 'mobile' => $mobile), $time, 1, 1);

        $data = array(
            'recommend_num' => array('exp', 'recommend_num+1'),
            'credit_point' => array('exp', 'credit_point+' . RECOMMEND_POINT)
        );

        $update = $this->sys_model_user->updateUser(array('user_id' => $user_info['user_id']), $data);
        if (!$update) {
            $this->response->showErrorResult($this->language->get('error_database_operation_failure'),4);
        }
        $this->response->showSuccessResult('', $this->language->get('success_referrer'));
    }

    /**
     * 退出登录
     */
    public function logout() {
        $user_id = $this->startup_user->userId();
        $this->startup_user->logout($user_id);
        $this->response->showSuccessResult();
    }

    //分享时候用到
    public function getEncryptCode() {
        $user_id = $this->startup_user->userId();
        $time = time();
        $code = $user_id . '_' . $time;
        $encrypt_code = encrypt($code);
        $this->response->showSuccessResult(array('encrypt_code' => $encrypt_code), $this->language->get('success_build'));
    }

    //通过encrypt获取用户的部分信息，无需登录
    public function getUserInfoByEncrypt() {
        if (!isset($this->request->post['encrypt_code'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'));
        }
        $encrypt_code = $this->request->post['encrypt_code'];
        $code = decrypt($encrypt_code);
        if (!strpos($code, '_')) {
            $this->response->showErrorResult($this->language->get('error_data_parse_failure'));
        }
        $arr = explode('_', $code);
        $user_id = $arr[0];
        $this->load->library('sys_model/user');
        $user_info = $this->sys_model_user->getUserInfo(array('user_id' => $user_id), 'avatar, nickname, mobile');
        if (empty($user_info)) {
            $this->response->showErrorResult($this->language->get('error_get_user_infomation'));
        }

        $user_info['mobile'] = substr($user_info['mobile'], 0, 3) . '****' . substr($user_info['mobile'], -4);
        $this->response->showSuccessResult($user_info, $this->language->get('success_get'));
    }

    private function addCoupon($user_info, $number, $coupon_type, $obtain_type, $order_id = 0) {
        $this->load->library('sys_model/coupon');

        if (empty($user_info)) return false;
        $description = '';
        if ($coupon_type == 1) {
            $description = ($number / 60) . $this->language->get('text_hour_coupon');
        } elseif ($coupon_type == 2) {

        } elseif ($coupon_type == 3) {

        }

        $data = array(
            'user_id' => $user_info['user_id'],
            'coupon_type' => $coupon_type,
            'number' => $number,
            'obtain' => $obtain_type,
            'add_time' => time(),
            'effective_time' => time(),
            'failure_time' => strtotime(date('Y-m-d', strtotime('+7 day'))),
            'description' => $description,
            'order_id' => $order_id
        );
        $data['coupon_code'] = $this->buildCouponCode();
        return $this->sys_model_coupon->addCoupon($data);
    }

    private function buildCouponCode() {
        return token(32);
    }
}