<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>抽奖</title>
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<link rel="stylesheet" type="text/css" href="/Public/css/base-style.css?1">
<script type="text/javascript" src="/Public/js/jquery-2.1.0.min.js"></script>
</head>
<body>
	<div class="top"><img src="/Public/images/bgx.png" /></div>
<div class="top-title"><img src="/Public/images/top.png" /></div>
<div class="Rankingsall">
	<div class="rank-top">我的奖品</div>
	<div class="line"></div>
	<div class="loterycont-start" style="margin-top: 25px;">
		<div class="leftlotery">&nbsp;</div>
		<div class="rightlotery"><a href="javascript:void(0);" onclick="showAddress()">填写邮寄地址 >></a></div>
	</div>
	<div id="addData" style="text-align: center;display: none;">
		<div style="margin: 5px 0;">
			<font color="red">*</font>手机号码：<input style="height: 30px;border-radius:6px;width: 200px;padding-left: 3px;" type="text" id="tel" name="tel" value="<?php echo ($addInfo["tel"]); ?>"/>
		</div>
		<div style="margin: 5px 0;">
			<font color="red">*</font>收件人：&nbsp;&nbsp;&nbsp;<input style="height: 30px;border-radius:6px;width: 200px;padding-left: 3px;" type="text" id="name" name="name" value="<?php echo ($addInfo["name"]); ?>"/>
		</div>
		<div style="margin: 5px 0;">
			<font color="red">*</font>收件地址：<input style="height: 30px;border-radius:6px;width: 200px;padding-left: 3px;" type="text" id="address" name="address" value="<?php echo ($addInfo["address"]); ?>"/>
		</div>
		<div style="margin: 5px 0;">&nbsp;邮政编码：<input style="height: 30px;border-radius:6px;width: 200px;padding-left: 3px;" type="text" id="postcode" name="postcode" value="<?php echo ($addInfo["postcode"]); ?>"/></div>
		<div style="margin: 5px 0;"><button onclick="saveData()" style="height: 30px;border-radius:6px;width: 60px;">保存</button></div>
		<input type="hidden" id="uid" value="<?php echo ($uid); ?>"/>
	</div>
</div>
<div class="Rankings">
	<ul>
		<?php if(is_array($logs)): $i = 0; $__LIST__ = $logs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
				<img width="60" src="<?php echo ($vo["thumb"]); ?>" /> <?php echo ($vo["aname"]); ?>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>
<!--<div style="margin-bottom: 60px;">&nbsp;</div>-->
<div class="footertop"></div>
<div class="allgrains1">
	<a href="<?php echo ($homeUrl); ?>" class="<?php echo ($homeClass); ?>"><img src="/Public/images/<?php echo ($homePng); ?>.png" /><p>抽奖</p></a>
	<a href="<?php echo ($myUrl); ?>" class="<?php echo ($myClass); ?>"><img src="/Public/images/<?php echo ($myPng); ?>.png" /><p>我的</p></a>
</div>
<script type="text/javascript">
	// 是否显示地址
	function showAddress() {
		$("#addData").toggle();
	}

	// 保存数据
	function saveData() {
		var tel = $("#tel").val();
		if (!tel) {
			alert('手机号码不能为空！');
			return false;
		}
		// 验证手机号码格式
		var isTrue = isPhoneNo(tel);
		if (!isTrue) {
			alert('手机号码格式错误！');
			return false;
		}

		var name = $("#name").val();
		if (!name) {
			alert('收件人不能为空！');
			return false;
		}
		var address = $("#address").val();
		if (!address) {
			alert('收件地址不能为空！');
			return false;
		}
		var postcode = $("#postcode").val();
		if (postcode) {
			var isPtrue = is_postcode(postcode);
			if (!isPtrue) {
				alert('邮政编码格式错误！');
				return false;
			}
		}
		var uid = $("#uid").val();

		$.ajax({
			type:"POST",
			url:"/Home/Index/saveAddress",
			data:{uid:uid,tel:tel,name:name,address:address,postcode:postcode},
			dataType:"json",
			success:function(data) {
				alert(data.msg);
			}
		});
	}

	// 验证手机号
	function isPhoneNo(phone) {
	    var pattern = /^1[345789]\d{9}$/;
	    return pattern.test(phone);
	}

	// 验证邮政编码
	function is_postcode(postcode) {
		if ( postcode == "") {
			return false;
		} else {
			if (! /^[0-9]{6}$/.test(postcode)) {
				return false;
			}
		}
		return true;
	}

	
</script>
</body>
</html>