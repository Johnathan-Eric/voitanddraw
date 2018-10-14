<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML>
<HEAD>
<TITLE>404页面不存在</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<meta http-equiv='refresh' content='5; url=<?php echo U('Admin/index');?>'>
<!--[if (lt IE 8.0)]>
	<link href="/Public/css/404.css" type=text/css rel=stylesheet>
<![endif]-->
<!--[if (!IE)|(gte IE 8.0)]><!-->
	<link href="/Public/css/404_2.css" type=text/css rel=stylesheet>
<!--<![endif]-->
<STYLE type=text/css>
body{font-size:12px;line-height:1.8em; background:#fff;}
.mod-notfound {
	BORDER-RIGHT: #e1e1e1 1px solid;
	BORDER-TOP: #e1e1e1 1px solid;
	MARGIN-TOP: 10px;
	BACKGROUND: #fff;
	BORDER-LEFT: #e1e1e1 1px solid;
	BORDER-BOTTOM: #e1e1e1 1px solid;
	HEIGHT: 500px;
	WIDTH:600px;
	TEXT-ALIGN: center;
	border-radius: 10px;
	margin:20px auto;
	margin-top:50px;
	
}
#footer{
	WIDTH:600px;
	margin:0 auto;
	text-align:center;
}
</STYLE>
</HEAD>
<BODY>
	<SECTION class=mod-page-body>
<DIV class="mod-page-main wordwrap clearfix">
<DIV class=x-page-container>
<DIV class="mod-notfound grid-98">
				<IMG class=img-notfound height=320 src="/Public/images/notfound.gif">
				<P style="FONT-SIZE: 24px; LINE-HEIGHT:1.5em">您访问的页面不存在或已删除！</P>
<P style="FONT-SIZE: 14px; LINE-HEIGHT: 2em">您可以点击[<A href="<?php echo U('Admin/Index');?>">转到首页</A>]或[<a href="javascript:history.back()">返回上一页</a>]!
<br>5秒钟后自动返回到网站首页!
</P>
			</DIV>
		</DIV>
	</DIV>
</SECTION>
<div id="footer">
</div>
</BODY>
</HTML>