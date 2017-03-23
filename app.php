<?php
/**
 * 东莞市亦强软件有限公司
 * Author: 罗剑波
 * Time: 2017/2/23 20:31
 */

define('WEIXIN_URL', 'http://bike.e-stronger.com/bike/wechat');
define('ANDROID_DOWNLOAD_URL', 'http://a.app.qq.com/o/simple.jsp?pkgname=cn.estronger.bike');
define('IOS_DOWNLOAD_URL', 'https://itunes.apple.com/cn/app/xiao-qiang-dan-che/id1196263366');

$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if($ua != '' && preg_match("/MicroMessenger/i", $ua)) {
    header('Location: ' . WEIXIN_URL, true, 302);
}
elseif($ua != '' && preg_match("/(iphone|ipad)/i", $ua)){
    header('Location: ' . IOS_DOWNLOAD_URL, true, 302);
}
else {
    header('Location: ' . ANDROID_DOWNLOAD_URL, true, 302);
}