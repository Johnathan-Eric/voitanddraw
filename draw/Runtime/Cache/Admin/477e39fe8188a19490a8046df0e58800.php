<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo C('site_title');?>-<?php echo ($page_title); ?></title>
<link rel="stylesheet" type="text/css" href="/Public/new/css/public.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/new/css/jquery.step.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/layui/css/layui.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/layer/layui/css/modules/layer/default/layer.css?v=<?php echo C('jsversion');?>" />
<script type="text/javascript" src="/Public/js/jquery-2.1.0.min.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/layui/layui.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/layui/lay/modules/layer.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/jquery.step.min.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/public.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/base64.js?v=<?php echo C('jsversion');?>"></script>
</head>
<body width="100%">
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>平台管理登陆</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" type="text/css" href="/Public/layui/css/layui.css?v=<?php echo C('jsversion');?>" />
    <link rel="stylesheet" href="/Public/Admin/newStyle/login/css/control.css">
</head>
<body>
	<div class="page_container">
		<div class="bgPic">
			<img src="/Public/Admin/newStyle/login/img/bg.png">
		</div>
		<form action="" method="post">
			<div class="body">
				<div class="body_head"></div>
				<div class="middle">
					<!-- <img class="logoPic" src="/Public/Admin/newStyle/login/img/logo.png"> -->
					<div class="title" style="margin-top: 80px;"><?php echo C('site_title');?>登陆</div>
					<div class="nameInput">
						<input type="text" name="uname" class="Musername" placeholder="请输入用户名称">
						<img class="inconPerson" src="/Public/Admin/newStyle/login/img/person.png">
					</div>
					<div class="pwInput">
						<input type="password" name="password" class="Mpassword" placeholder="请输入登录密码">
						<img class="inconSuo" src="/Public/Admin/newStyle/login/img/suo.png">
					</div>
					<button type="submit">登陆</button>
				</div>
			</div>
		</form>
	</div>

	 <script src="/Public/Admin/newStyle/login/js/jquery-1.8.2.min.js"></script>
     <script src="/Public/Admin/newStyle/login/js/supersized.3.2.7.min.js"></script>
     <script type="text/javascript" src="/Public/layui/layui.js?v=<?php echo C('jsversion');?>"></script>
     <script type="text/javascript">
     	jQuery(document).ready(function() {
			$('.page_container form').submit(function(){
				var username = $(this).find('.Musername').val();
				var password = $(this).find('.Mpassword').val();
				if(username == '') {
					$('.Musername').css('border-color','red');
					return false;
				}
				if(password == '') {
					$('.Mpassword').css('border-color','red');
					return false;
				}
				var date = {uname:username,password:password};
				setOper(date);
				return false;
			});

                $('.Musername').keyup(function(){
                    $(this).css('border-color','#ccc');
                });

                $('.Mpassword').keyup(function(){
                    $(this).css('border-color','#ccc');
                });


            });
            function setOper(date)
            {
                layui.use('layer', function(){ 
                    var $ = layui.jquery,
                    layer = layui.layer; 
                    var lod_index = layer.load(1, {
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                    });
                    var url = window.location.href;
                    $.post(url, date ,function(a){
                        layer.close(lod_index);
                        var jsonDate = jQuery.parseJSON(a);
                        if(jsonDate.status == 1){
                            layer.msg(jsonDate.info,{
                                time:1000,
                                end:function(){
                                    window.location.href = jsonDate.url;
                                }
                            });
                        }else{
                            layer.msg(jsonDate.info);
                        }
                    });
                });
            }
     </script>
</body>
</html>
</body>
</html>