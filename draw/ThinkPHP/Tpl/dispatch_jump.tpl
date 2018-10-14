<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message .jump{ padding-top: 10px; font-size:14px;}
.system-message .jump a{ color: #333;}
.system-message .msg{ line-height: 2em; font-size: 16px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
body{margin:50px auto;}
</style>
</head>
<body>
<table align="center" border="0" cellspacing="0" cellpadding="10" style="border:1px solid #ccc; background:#efefef; min-width:400px; max-width:750px" class="system-message">
  <tr>
    <td width="30" align="center" valign="top">
    <present name="message">
        <img src="__PUBLIC__/images/ok.png" />
    <else/>
        <img src="__PUBLIC__/images/error.png" />
    </present>
    </td>
    <td>
    	<div class="msg">
        <present name="message">
            <?php echo($message); ?>
        <else/>
            <?php echo($error); ?>
        </present>
        </div>
        <p class="jump">
        页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>秒!
        </p>
    </td>
  </tr>
</table>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
    var time = --wait.innerHTML;
    if(time <= 0) {
            top.location.href = href;
            clearInterval(interval);
    };
}, 1000);
})();
</script>
</body>
</html>
