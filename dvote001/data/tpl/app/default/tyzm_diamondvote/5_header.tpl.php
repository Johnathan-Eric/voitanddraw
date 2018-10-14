<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php  if(!empty($_W['page']['sitename'])) { ?><?php  echo $_W['page']['sitename'];?><?php  } else { ?><?php  echo $_W['account']['name'];?><?php  } ?><?php  if(!empty($title)) { ?><?php  echo $title;?> <?php  } ?></title>
	<meta name="format-detection" content="telephone=no, address=no">
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreen -->
	<meta name="apple-touch-fullscreen" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="keywords" content="<?php  if(empty($_W['page']['keywords'])) { ?><?php  } else { ?><?php  echo $_W['page']['keywords'];?><?php  } ?>" />
	<meta name="description" content="<?php  if(empty($_W['page']['description'])) { ?><?php  } else { ?><?php  echo $_W['page']['description'];?><?php  } ?>" />
	<link rel="shortcut icon" href="<?php  echo $_W['siteroot'];?><?php  echo $_W['config']['upload']['attachdir'];?>/<?php  if(!empty($_W['setting']['copyright']['icon'])) { ?><?php  echo $_W['setting']['copyright']['icon'];?><?php  } else { ?>images/global/wechat.jpg<?php  } ?>" />  
	<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>app/resource/js/app/util.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>app/resource/js/require.js"></script>
	<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>app/resource/js/lib/jquery-1.11.1.min.js?v=20160906"></script>
	<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>app/resource/js/lib/mui.min.js?v=20160906"></script>
	<script type="text/javascript" src="<?php  echo $_W['siteroot'];?>app/resource/js/app/common.js?v=20160906"></script>
	<link href="<?php  echo $_W['siteroot'];?>app/resource/css/bootstrap.min.css?v=20160906" rel="stylesheet">
	<link href="<?php  echo $_W['siteroot'];?>app/resource/css/common.min.css?v=20160906" rel="stylesheet">
	<link href="<?php echo MODULE_URL;?>/template/static/css/weui1.0.1.min.css" rel="stylesheet">
	<link href="<?php echo MODULE_URL;?>/template/static/css/index.css?a=11" rel="stylesheet"> 
	<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}
	window.sysinfo = {
		<?php  if(!empty($_W['uniacid'])) { ?>'uniacid': '<?php  echo $_W['uniacid'];?>',<?php  } ?>
		<?php  if(!empty($_W['acid'])) { ?>'acid': '<?php  echo $_W['acid'];?>',<?php  } ?><?php  if(!empty($_W['openid'])) { ?>'openid': '<?php  echo $_W['openid'];?>',<?php  } ?>
		<?php  if(!empty($_W['uid'])) { ?>'uid': '<?php  echo $_W['uid'];?>',<?php  } ?>
		'siteroot': '<?php  echo $_W['siteroot'];?>',
		'siteurl': '<?php  echo $_W['siteurl'];?>',
		'attachurl': '<?php  echo $_W['attachurl'];?>',
		'attachurl_local': '<?php  echo $_W['attachurl_local'];?>',
		'attachurl_remote': '<?php  echo $_W['attachurl_remote'];?>',
		<?php  if(defined('MODULE_URL')) { ?>'MODULE_URL': '<?php echo MODULE_URL;?>',<?php  } ?>
		'cookie' : {'pre': '<?php  echo $_W['config']['cookie']['pre'];?>'}
	};
	// jssdk config 对象
	jssdkconfig = <?php  echo json_encode($_W['account']['jssdkconfig']);?> || {};
	// 是否启用调试
	jssdkconfig.debug = false;
	jssdkconfig.jsApiList = [
		'checkJsApi',
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
		'onMenuShareQQ',
		'onMenuShareWeibo',
		'hideMenuItems',
		'showMenuItems',
		'hideAllNonBaseMenuItem',
		'showAllNonBaseMenuItem',
		'translateVoice',
		'startRecord',
		'stopRecord',
		'onRecordEnd',
		'playVoice',
		'pauseVoice',
		'stopVoice',
		'uploadVoice',
		'downloadVoice',
		'chooseImage',
		'previewImage',
		'uploadImage',
		'downloadImage',
		'getNetworkType',
		'openLocation',
		'getLocation',
		'hideOptionMenu',
		'showOptionMenu',
		'closeWindow',
		'scanQRCode',
		'chooseWXPay',
		'openProductSpecificView',
		'addCard',
		'chooseCard',
		'openCard'
	];
	</script>
</head>
<body>
<div class="container container-fill" <?php  if($reply['notice']) { ?> style="padding-top:36px;" <?php  } ?>>
<?php  if($reply['notice']) { ?>
<div class="notice"><marquee style="WIDTH: 100%; HEIGHT: 18px" scrollamount="6" Align="Middle"  hspace="0" direction="Left" ><?php  echo $reply['notice'];?></marquee></div>
<?php  } ?>
<?php  if($reply['status']==0 || $reply['status']=="" || $reply['endtime']< TIMESTAMP ) { ?>
<div id="replystatus" >
<div class="alert alert-danger alert-dismissible" style="text-align:center;position: fixed;top:40%;z-index: 2; width:80%;margin:0 10%;z-index:9000000" role="alert">
  <button type="button" class="close"  onclick="hidemod('replystatus')" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>活动已结束！</strong>
  
</div>
<div class="weui-mask"></div>
</div>
<?php  } ?>


