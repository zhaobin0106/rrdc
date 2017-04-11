<!DOCTYPE html>
<html>
  <head>
    <title>returnApp.html</title>
	
    <meta http-equiv="keywords" content="keyword1,keyword2,keyword3">
    <meta http-equiv="description" content="this is my page">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <script type="text/javascript">
    	      //window.location="oytrtapp://chooseType:3";
            if (/android/i.test(navigator.userAgent)){
              window.location="com.cn.rrdc.app://db.app/openwith?<?php echo 'type='.$type.'&dingdanhao='.$out_trade_no;?>";
            }

            if (/ipad|iphone|mac/i.test(navigator.userAgent)){
              window.location="openAlipayH5.com.bxsapp.rrdc://<?php echo $type.','.$out_trade_no;?>";
            }
    </script>

  </head>
  
  <body>
    success
  </body>
</html>
