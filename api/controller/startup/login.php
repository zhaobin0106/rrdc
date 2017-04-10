<?php

/**
 * 判断是否登录，ignore为忽略列表
 * Class ControllerStartupLogin
 */
class ControllerStartupLogin extends Controller {
    public function index() {
        $route = isset($this->request->get['route']) ? strtolower(trim($this->request->get['route'])) : '';
        $ignore = array(
            'account/account/sendregistercode',
            'account/account/register',
            'account/account/login',
            'account/account/sendsharecode',//发送分享验证码
            'account/coupon/getcouponbysharetrip',//行程分享获取优惠券
            'account/coupon/getcouponfrontpage',//首页分享获取优惠券
            'account/account/getuserinfobyencrypt',
            'account/account/getorderdetailbyencrypt',
            'payment/alipay/notify',
            'payment/wxpay/notify',
            'payment/napas/notify',
            'payment/napas/qiantai',
            'location/location/getbicyclelocation',
            'location/location/getlocalprice',
            'system/common/wechat_jssdk',
            'system/common/wechat',
            'system/common/wechatapp',
            'article/index',
            'system/test',
            'system/common/contact',
            'system/common/version',
			'wechat/mp',
        );

        if (!in_array($route, $ignore)) {
            if (!isset($this->request->get['user_id']) || !isset($this->request->get['sign'])) {
                $this->response->showErrorResult('缺少登录参数', 98);
            }

            $this->load->library('logic/user', true);
            $user_id = $this->request->get['user_id'];
            $sign = $this->request->get['sign'];
            $result = $this->logic_user->checkUserSign(array('user_id' => $user_id), $sign);
            if ($result['state']) {
                $this->registry->set('startup_user', $this->logic_user);
            } else {
                $this->response->showErrorResult('您的账号已在其他设备登录', 99);
            }
        }
    }
}