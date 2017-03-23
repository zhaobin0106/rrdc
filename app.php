<?php
/**
 * 东莞市亦强软件有限公司
 * Author: 罗剑波
 * Time: 2017/2/23 20:31
 */

//define('YINGYONGBAO_URL', 'http://a.app.qq.com/o/simple.jsp?pkgname=cn.estronger.bike');
//define('ANDROID_DOWNLOAD_URL', 'http://a.app.qq.com/o/simple.jsp?pkgname=cn.estronger.bike');
//define('IOS_DOWNLOAD_URL', 'https://itunes.apple.com/cn/app/xiao-qiang-dan-che/id1196263366');
define('YINGYONGBAO_URL', 'http://www.buguyuan.com/');
define('ANDROID_DOWNLOAD_URL', 'http://www.buguyuan.com/');
define('IOS_DOWNLOAD_URL', 'http://www.buguyuan.com/');


$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if($ua != '' && preg_match("/MicroMessenger/i", $ua)) {
    header('Location: ' . YINGYONGBAO_URL, true, 302);
}
if($ua != '' && preg_match("/(iphone|ipad)/i", $ua)){
    header('Location: ' . IOS_DOWNLOAD_URL, true, 302);
}
else {
    header('Location: ' . ANDROID_DOWNLOAD_URL, true, 302);
}