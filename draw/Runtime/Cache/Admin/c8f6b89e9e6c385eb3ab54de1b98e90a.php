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

<style>
        <!--.mainnav_title{ display:none;}
        h1 { height:30px;line-height:30px;font-size:14px;padding-left:15px;background:#EEE;border-bottom:1px solid #ddd;border-right:1px solid #ddd;overflow:hidden;zoom:1;margin-bottom:4px;}
        h1 b {color:rgba(26, 188, 156, 1);}
        h1 span {color:#ccc;font-size:10px;margin-left:10px;}
        #Profile{ width:48%; height:192px; float:left; margin:5px 15px 10px 0;}
        #system {width:48%; height:192px; float:left; margin:5px 15px 10px 0;}
        .list ul{ border:1px #ddd solid;  overflow:hidden;border-bottom:none;}
        .list ul li{ border-bottom:1px #ddd solid; height:30px;  overflow:hidden;zoom:1; line-height:30px; color:#777;padding-left:5px;}
        .list ul li span{ display:block; text-align:right; margin-right:5px; float:left; color:#777;width:100px;}

        #sitestats {width:48%; min-height:192px; float:left;margin:5px 15px 0 0;overflow:hidden;}
        #sitestats div {_width:99.5%;border:1px solid #ddd;overflow:hidden;zoom:1;}
        #sitestats ul {zoom:1;width:102%;padding:2px 0 0 2px;_padding:1px 0 0 1px;min-height:132px;}
        #sitestats ul li {float:left; margin-bottom:5px; height:50px; float:left; width:16.1%;_width:16.3%;text-align:center;border-right:1px solid #fff;border-bottom:none;}
        #sitestats ul li b,#sitestats ul li span{ display: block;width:100%; height:25px;line-height:25px;}
        #sitestats ul li b {background:#EFEFEF;color:#777;font-weight:normal;}
        #sitestats ul li span {color:#3792d1;background:#F8F8F8; margin-top: 1px;}

        #dagenews {width:48%;height:192px; float:left;margin:5px 15px 0 0;}

        #tabs {margin:0px auto;overflow:hidden;border:1px solid #ccc; height:214px;}
        #tabs .title {overflow:hidden;background:url(/Public/Admin/Images/tab_bottom_line_1.jpg) repeat-x 0 26px;height:27px;}
        #tabs .title ul li {float:left;margin-left:-1px;height:26px;line-height:26px;background:#EAEEF4;padding:0px 10px;border:1px solid #ccc;border-top:0;border-bottom:0;}
        #tabs .title ul li.on {background:#FFF;height:27px;}
        #tabs .content_1 { overflow:hidden;border-top:none;}
        #tabs .tabbox {display:none;padding:10px;}

        #tabs .tabbox ul li {background:url(/Public/Admin/Images/ico_1.gif) no-repeat 4px 11px;padding-left:13px;border-bottom:1px #ddd dashed; height:27px;  line-height:26px;color:#333 }
        #tabs .tabbox ul li a {color:#333}
        #tabs .tabbox ul li a:hover {color:#FB0000;}
        #tabs .tabbox ul li span.date {float:right;color:#777}
        #dage_license {font-weight:normal;color:blue;}
        #dage_license a {color:#FB0000;}
        ul, ol {list-style: none outside none;}
		border-width: 1px;
			border-style: solid;
			border-color: rgba(228, 228, 228, 1);
			background-color: rgba(255, 255, 255, 1);
		-->
		body {background-color: rgba(249, 249, 249, 1);background-image: none;position: relative;left: -0px;font-family: '微软雅黑';font-weight: 400;font-style: normal;}
		.nav_title {
			border-width: 0px;
			width: 100%;
			height: 50px;
			overflow: hidden;
			background-color:rgba(243, 243, 243, 1);
			position: relative;
			border: 1px solid #eee;
		}
		.nav_title .panel_title {position: absolute;left: 0px;top: 16px;width: 188px;height: 18px;font-family: '微软雅黑';font-weight: 400;font-style: normal;color: #999999;text-align: left;}
		.p_div {
			border-width: 0px;
			position: absolute;
			left: 35px;
			top: 0px;
			width: 188px;
			height: 18px;
			background: inherit;
			background-color: rgba(255, 255, 255, 0);
			box-sizing: border-box;
			border-width: 5px;
			border-style: solid;
			border-color: rgba(26, 188, 156, 1);
			border-top: 0px;
			border-right: 0px;
			border-bottom: 0px;
			border-radius: 0px;
			border-top-left-radius: 0px;
			border-top-right-radius: 0px;
			border-bottom-right-radius: 0px;
			border-bottom-left-radius: 0px;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			font-family: '微软雅黑';
			font-weight: 400;
			font-style: normal;
			color: #999999;
			text-align: left;
		}
		.p_text {
			border-width: 0px;
			position: absolute;
			left: 10px;
			top: -1px;
			width: 176px;
			word-wrap: break-word;
		}
		p {line-height:18px;}
		.f_l {float:left;}
		.p_d {width:100%;height:120px}
		.p_d_all {width:92%;min-width:1000px;height:100px;margin:20px auto;}
		.pd_child {
			width:23.5%;
			min-width: 200px;
			height:100px;
			background-color: rgba(255, 255, 255, 1);
			margin-right:2%;
			box-sizing: border-box;
			border-radius: 0px;
			-moz-box-shadow: none;
			-webkit-box-shadow: none;
			box-shadow: none;
			font-family: '微软雅黑';
			font-weight: 400;
			font-style: normal;
			text-align: left;
			position: relative;
		}
		.pd_child_end {padding:0;margin:0}
		.pd_child_div {height:100%;border-width: 1px;border-style: solid;border-color: rgba(228, 228, 228, 1);}
		.img_div {
			border-width: 0px;
			position: absolute;
			left: 20px;
			top: 24px;
			width: 46px;
			height: 48px;
			font-family: '微软雅黑 Bold', '微软雅黑';
			font-style: normal;
			font-size: 16px;
			color: #1ABC9C;
		}
		.img_div img {
			border-width: 0px;
			position: absolute;
			left: 0px;
			top: 0px;
			width: 46px;
			height: 48px;
		}
		.pd_child .div_text {
			border-width: 0px;
			position: absolute;
			left: 80px;
			top: 24px;
			width: 149px;
			word-wrap: break-word;
		}
		.panel_pend {width:100%;height:182px;}
		.panel_entrance {width:100%;height:200px;margin-bottom:20px;}
		.panel_pandect {width:100%;height:200px;margin-bottom:20px;}
		.pend_title {
			border-width: 0px;
			position: relative;
			width: 100%;
			height: 45px;
			font-family: '微软雅黑 Bold', '微软雅黑';
			font-weight: 700;
			font-style: normal;
			text-align: left;
			background-color: rgba(243, 243, 243, 1);
			box-sizing: border-box;
			border-bottom: 1px solid rgba(228, 228, 228, 1);
			border-radius: 0px;
		}
		.pand_text {
			border-width: 0px;
			position: absolute;
			left: 20px;
			top: 3px;
			height: 36px;
			line-height: 36px;
			word-wrap: break-word;
		}
		.pend_menu {margin:0 20px;height:115px;}
		.pend_menu ul {margin: 0;padding-top:11px;height:104px;}
		.pend_menu li {width:32%;height:40px;margin-right:2%;border-bottom:1px solid rgba(242, 242, 242, 1);overflow:hidden;}
		.pend_menu li.r_end {margin:0}
		.pend_menu li a{display:inline-block;width:100%;height:36px;margin:2px 0;position:relative;word-wrap: break-word;font-family: '微软雅黑';font-weight: 410;font-style: normal;color:#666666;text-align: left;cursor:pointer;}
		.pend_a_text {position:absolute;left:18px;}
		.pend_menu li a:hover {color: rgb(26, 188, 156);}
		.span_peng_text {display:inline-block;height:36px;line-height:36px;margin:0;}
		.pend_count {position:absolute;right:10px;margin:0}
		.entrance_menu {
			width: 12%;
			height: 155px;
			font-family: '微软雅黑';
			font-weight: 400;
			font-style: normal;
			position: relative;
		}
		.pendect_text {
			width: 33%;
			height: 155px;
			font-family: '微软雅黑';
			font-weight: 400;
			font-style: normal;
			position: relative;
		}
		.entrance_menu a {display:block;cursor:pointer;}
		.entrance_menu a:hover {color:rgb(26, 188, 156);text-decoration:none;background-color:rgba(249,249,249,1);border-width:1px;border-style:solid;border-color:rgba(228,228,228,1);border-bottom:0;border-top:0;}
		a.entrance_menu_a_start:hover {border-left:0;}
		.entrance_menu_img {text-align:center;padding-top:40px;padding-bottom:24px;}
		.entrance_menu_img img {width:48px;height:48px;}
		.pandect_menu {width:100%;height:100%;margin-right:2%;}
		.pandect_menu_end {margin:0;}
		.panel_pandect_menu {width:100%;height:100%;border-width: 1px;border-style: solid;border-color: rgba(228, 228, 228, 1);margin-top:10px;}
		.div_pendect_text {padding-top:10px;}
                .lay-boder{
                    border-color: #e6e6e6;
                    border-style: solid;
                    border-width: 1px;
                    height: 100px;
                    background: #fff none repeat scroll 0 0;
                    min-width: 247px;
                    text-align: center;
                    overflow: hidden;
                }
                .lay-click{
                    width: 230px;
                    height: 100px;
                }
                .a_day{
                    margin: auto 10px;
                    cursor: pointer;
                }
	</style>

<div class="main-body">
    <div class="nav_title" style="left: 0; width: 100%;">
        <div class="panel_title">
            <div class="p_div">
                <div class="p_text" style="visibility: inherit; top: 0px; transform-origin: 84px 9px 0px;">
                    <p><span><?php echo ($page_title); ?></span></p>
                </div>
            </div>
        </div>
        <div class="refresh_div">
            <button class="layui-btn layui-btn-small layui-btn-primary" onclick="javascript:location.reload();">
                <i class="layui-icon">&#x1002;</i>
            </button>
        </div>
    </div>
    <div class="lay-body p_d_all">
        <div style="margin-top: 0;min-width: 1130px;">
            <div class="layui-row layui-col-space10">
                <?php if( 2 != $request['dtype']): ?><div class="layui-col-md3">
                        <div class="layui-box lay-boder">
                            <div class="lay-click" style="position: relative; left: 32px;">
                                <div class="lay-icon" style="clear:both;position:absolute; top:20px;">
                                    <i class="layui-icon layui-icon-cart" style="font-size:60px;color: #999999;">&#xe698;</i>
                                </div>
                                <div style="clear: both;position: absolute; left: 75px;top: 25px;">
                                    <ul>
                                       <li style="color:#999999;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;">我的分店</li>
                                       <li><b style="font-size:16px;"><?php echo ((isset($d_count) && ($d_count !== ""))?($d_count):0); ?>个</b></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div><?php endif; ?>
                <div class="layui-col-md3">
                    <div class="layui-box lay-boder">
                        <div class="lay-click" style="position: relative; left: 32px;">
                            <div class="lay-icon" style="clear:both;position:absolute; top:20px;">
                                <i class="layui-icon layui-icon-rmb" style="font-size:60px;color: #999999;">&#xe65e;</i>
                            </div>
                            <div style="clear: both;position: absolute; left: 75px;top: 25px;">
                                <ul>
                                   <li style="color:#999999;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;">今日销售总额</li>
                                   <li><b style="font-size:16px;"><?php echo ((isset($c_data["daySumMoney"]) && ($c_data["daySumMoney"] !== ""))?($c_data["daySumMoney"]):0); ?>元</b></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-box lay-boder">
                        <div class="lay-click" style="position: relative; left: 32px;">
                            <div class="lay-icon" style="clear:both;position:absolute; top:20px;">
                                <i class="layui-icon layui-icon-dollar" style="font-size:60px;color: #999999;">&#xe659;</i>
                            </div>
                            <div style="clear: both;position: absolute; left: 75px;top: 25px;">
                                <ul>
                                   <li style="color:#999999;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;">昨日销售总额</li>
                                   <li><b style="font-size:16px;"><?php echo ((isset($c_data["yesterdaySumMoney"]) && ($c_data["yesterdaySumMoney"] !== ""))?($c_data["yesterdaySumMoney"]):0); ?>元</b></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md3">
                    <div class="layui-box lay-boder">
                        <div class="lay-click" style="position: relative; left: 32px;">
                            <div class="lay-icon" style="clear:both;position:absolute; top:20px;">
                                <i class="layui-icon layui-icon-chart" style="font-size:60px;color: #999999;">&#xe62c;</i>
                            </div>
                            <div style="clear: both;position: absolute; left: 75px;top: 25px;">
                                <ul>
                                   <li style="color:#999999;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;">近7天销售总额</li>
                                   <li><b style="font-size:16px;"><?php echo ((isset($c_data["weekSumMoney"]) && ($c_data["weekSumMoney"] !== ""))?($c_data["weekSumMoney"]):0); ?>元</b></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lay-search" style="margin-top: 10px;min-width: 1130px;">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><span>待处理事务</span></p>
                    </div>
                </div>
            </div>
            <div class="layui-row layui-col-space10" style="height:60px;">
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="<?php echo U('Orders/index/','order_search=1');?>" target="main" >待付款订单 (<?php echo ((isset($c_data["status0_num_count"]) && ($c_data["status0_num_count"] !== ""))?($c_data["status0_num_count"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="<?php echo U('Orders/index/','order_search=4');?>" target="main" >已完成订单 (<?php echo ((isset($c_data["status3_num_count"]) && ($c_data["status3_num_count"] !== ""))?($c_data["status3_num_count"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="<?php echo U('Orders/index/','order_search=2');?>" target="main" >待发货订单 (<?php echo ((isset($c_data["status1_num_count"]) && ($c_data["status1_num_count"] !== ""))?($c_data["status1_num_count"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                <!--
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="/Admin/Property/index.html" target="main" >待确认退货订单 (<?php echo ((isset($c_data["status5_num_count"]) && ($c_data["status5_num_count"] !== ""))?($c_data["status5_num_count"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="/Admin/Property/index.html" target="main" >待处理退货订单 (<?php echo ((isset($c_data["weekSumMoney"]) && ($c_data["weekSumMoney"] !== ""))?($c_data["weekSumMoney"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                <div class="layui-col-md4" style="text-align:center;height: 40px;line-height: 30px;font-size: 18px;cursor: pointer;margin-top: 8px;">
                    <a class="childCli" uri="/Admin/Property/index.html" target="main" >待处理退款申请 (<?php echo ((isset($c_data["paystatus2_num_count"]) && ($c_data["paystatus2_num_count"] !== ""))?($c_data["paystatus2_num_count"]):0); ?>)</a>
                    <hr style="width: 80%;margin:auto;">
                </div>
                -->
            </div>
        </div>
        <div class="lay-search" style="margin-top: 10px;min-width: 1130px;height: 180px;">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><span>运营快捷入口</span></p>
                    </div>
                </div>
            </div>
            <div class="layui-row layui-col-space10">
                <div class="layui-col-md2" style="text-align: center;height: 150px;">
                    <a class="childCli" uri="<?php echo U('Books/add_books');?>" target="main" style="cursor: pointer;">
                        <ul style="margin-top: 25px;">
                            <li><i class="layui-icon layui-icon-add-1" style="font-size:60px;color: #999999;">&#xe654;</i></li>
                            <li>添加商品</li>
                        </ul>
                    </a>
                </div>
                <div class="layui-col-md2" style="text-align: center;height: 150px;">
                    <a class="childCli" uri="<?php echo U('Member/index');?>" target="main" style="cursor: pointer;">
                        <ul style="margin-top: 25px;">
                            <li><i class="layui-icon layui-icon-friends" style="font-size:60px;color: #999999;">&#xe612;</i></li>
                            <li>用户中心</li>
                        </ul>
                    </a>
                </div>
                <div class="layui-col-md2" style="text-align: center;height: 150px;">
                    <a class="childCli" uri="<?php echo U('Advert/index/');?>" target="main" style="cursor: pointer;">
                        <ul style="margin-top: 25px;">
                            <li><i class="layui-icon layui-icon-release" style="font-size:60px;color: #999999;">&#xe609;</i></li>
                            <li>广告管理</li>
                        </ul>
                    </a>
                </div>
                <div class="layui-col-md2" style="text-align: center;height: 150px;">
                    <a class="childCli" uri="<?php echo U('Books/index/');?>" target="main" style="cursor: pointer;">
                        <ul style="margin-top: 25px;">
                            <li><i class="layui-icon layui-icon-list" style="font-size:60px;color: #999999;">&#xe60a;</i></li>
                            <li>商品管理</li>
                        </ul>
                    </a>
                </div>
                <div class="layui-col-md2" style="text-align: center;height: 150px;">
                    <a class="childCli" uri="<?php echo U('/Admin/Admin/user_index');?>" target="main" style="cursor: pointer;">
                        <ul style="margin-top: 25px;">
                            <li><i class="layui-icon layui-icon-user" style="font-size:60px;color: #999999;">&#xe770;</i></li>
                            <li>员工管理</li>
                        </ul>
                    </a>
                </div>
            </div>
        </div>
        <div class="layui-row layui-col-space5" style="margin-top: 10px;min-width: 1130px;">
            <div class="layui-col-md6">
                <div class="lay-search">
                    <div class="nav_header">
                        <div class="p-search">
                            <div class="p_text">
                                <p><span>商品总览</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row layui-col-space20" style="height: 100px;margin-top: 25px;">
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($b_data["sold_out"]) && ($b_data["sold_out"] !== ""))?($b_data["sold_out"]):0); ?></li>
                                <li><b style="font-size:16px;">已下架</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($b_data["putaway"]) && ($b_data["putaway"] !== ""))?($b_data["putaway"]):0); ?></li>
                                <li><b style="font-size:16px;">已上架</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($b_data["warning"]) && ($b_data["warning"] !== ""))?($b_data["warning"]):0); ?></li>
                                <li><b style="font-size:16px;">库存紧张</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($b_data["all"]) && ($b_data["all"] !== ""))?($b_data["all"]):0); ?></li>
                                <li><b style="font-size:16px;">全部商品</b></li>
                             </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
                <div class="lay-search">
                    <div class="nav_header">
                        <div class="p-search">
                            <div class="p_text">
                                <p><span>会员总览</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row layui-col-space20" style="height: 100px;margin-top: 25px;">
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($m_data["day"]) && ($m_data["day"] !== ""))?($m_data["day"]):0); ?></li>
                                <li><b style="font-size:16px;">今日新增</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($m_data["yesterday"]) && ($m_data["yesterday"] !== ""))?($m_data["yesterday"]):0); ?></li>
                                <li><b style="font-size:16px;">昨日新增</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($m_data["month"]) && ($m_data["month"] !== ""))?($m_data["month"]):0); ?></li>
                                <li><b style="font-size:16px;">本月新增</b></li>
                             </ul>
                        </div>
                        <div class="layui-col-md3" style="text-align: center;">
                            <ul>
                                <li style="color:#FB0000;font-size: 20px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ((isset($m_data["all"]) && ($m_data["all"] !== ""))?($m_data["all"]):0); ?></li>
                                <li><b style="font-size:16px;">会员总数</b></li>
                             </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lay-search" style="margin-top: 83px;min-width: 1130px;">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><span>订单统计</span></p>
                    </div>
                </div>
            </div>
            <div style="height: 700px;margin-top: 25px;">
                <div style="text-align: center;width: 25%;float: left;">
                    <ul>
                        <li>
                            <a class="a_day or" myDate="day">今日</a>
                            <a class="a_day or" myDate="week">本周</a>
                            <a class="a_day or" myDate="month">本月</a>
                        </li>
                        <li>
                            <div class="layui-inline" style="width: 215px;margin-top: 10px;">
                                <input type="text" autoComplete='off' class="layui-input laydate-icon layui-icon-date" id="orderLine" placeholder=" ~ "> 
                            </div>
                        </li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li style="color:#999999;font-size: 14px;font-family:'Microsoft YaHei';font-weight:lighter;">本月订单总数</li>
                        <li style="color:#333;font-size: 18px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ($t_data["order_num"]["num30"]["val"]); ?></li>
                        <li style="font-family:'Microsoft YaHei';font-weight:lighter;">
                            <!--<i class="layui-icon layui-icon-up" style="font-size:18px;color: #999999;">&#xe619;</i> -->
                            <span style="color:#FB0000;font-size: 14px;"><?php echo ($t_data["order_num"]["num30"]["rate"]); ?></span>
                            <span style="color:#999999;font-size: 14px;">同比上周个周期</span>
                        </li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li style="color:#999999;font-size: 14px;font-family:'Microsoft YaHei';font-weight:lighter;">本周订单总数</li>
                        <li style="color:#333;font-size: 18px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ($t_data["order_num"]["num7"]["val"]); ?></li>
                        <li style="font-family:'Microsoft YaHei';font-weight:lighter;">
                            <!--<i class="layui-icon layui-icon-down" style="font-size:18px;color: #999999;">&#xe61a;</i>-->
                            <span style="color:#FB0000;font-size: 14px;"><?php echo ($t_data["order_num"]["num7"]["rate"]); ?></span>
                            <span style="color:#999999;font-size: 14px;">同比上周个周期</span>
                        </li>
                    </ul>
                </div>
                <div style="text-align: center;width: 75%;float: left;">
                    <canvas id="orderChart" style="height: 500px;"></canvas>
                </div>
            </div>
        </div>
        <div class="lay-search" style="margin-top: 13px;min-width: 1130px;">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><span>销售统计</span></p>
                    </div>
                </div>
            </div>
            <div style="height: 700px;margin-top: 25px;">
                <div style="text-align: center;width: 25%;float: left;">
                    <ul>
                        <li>
                            <a class="a_day sell" myDate="day">今日</a>
                            <a class="a_day sell" myDate="week">本周</a>
                            <a class="a_day sell" myDate="month">本月</a>
                        </li>
                        <li>
                            <div class="layui-inline" style="width: 215px;margin-top: 10px;">
                                <input type="text" autoComplete='off' class="layui-input" id="sellLine" placeholder=" ~ "> 
                            </div>
                        </li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li style="color:#999999;font-size: 14px;font-family:'Microsoft YaHei';font-weight:lighter;">本月订单总数</li>
                        <li style="color:#333;font-size: 18px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ($t_data["order_money"]["money30"]["val"]); ?></li>
                        <li style="font-family:'Microsoft YaHei';font-weight:lighter;">
                            <!--<i class="layui-icon layui-icon-up" style="font-size:18px;color: #999999;">&#xe619;</i>-->
                            <span style="color:#FB0000;font-size: 14px;"><?php echo ($t_data["order_money"]["money30"]["rate"]); ?></span>
                            <span style="color:#999999;font-size: 14px;">同比上周个周期</span>
                        </li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li><br/></li>
                        <li style="color:#999999;font-size: 14px;font-family:'Microsoft YaHei';font-weight:lighter;">本周订单总数</li>
                        <li style="color:#333;font-size: 18px;font-family:'Microsoft YaHei';font-weight:lighter;"><?php echo ($t_data["order_money"]["money7"]["val"]); ?></li>
                        <li style="font-family:'Microsoft YaHei';font-weight:lighter;">
                            <!--<i class="layui-icon layui-icon-down" style="font-size:18px;color: #999999;">&#xe61a;</i>-->
                            <span style="color:#FB0000;font-size: 14px;"><?php echo ($t_data["order_money"]["money7"]["rate"]); ?></span>
                            <span style="color:#999999;font-size: 14px;">同比上周个周期</span>
                        </li>
                    </ul>
                </div>
                <div style="text-align: center;width: 75%;float: left;">
                    <canvas id="sellChart" style="height: 500px;"></canvas>
                </div>
            </div>
        </div>
    </div>    
</div>
    <script type="text/javascript" src="/Public/new/js/Chart.min.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript">
    
    var allTotal = <?php echo ((isset($_total) && ($_total !== ""))?($_total):0); ?>;
    var curr = <?php echo ((isset($_curr) && ($_curr !== ""))?($_curr):1); ?>;
    var limit = <?php echo ((isset($_limit) && ($_limit !== ""))?($_limit):10); ?>;
    layui.use(['laypage', 'layer'], function(){
        var laypage = layui.laypage
        ,layer = layui.layer;
        laypage.render({
            elem: 'layPage'
            ,count: allTotal
            ,limit: limit
            ,curr : curr
            ,layout: ['count', 'prev', 'page', 'next', 'skip']
            ,jump: function(obj,first){
                if(!first){
                    jsPost('', {curr: obj.curr}); 
                }
            }
        });
    });
    $(function(){
        var windowHeigth = $(document).height();
        $(".main-body").height(windowHeigth);
    });
    
    $(".childCli").on('click',function(){
        parent.main.location.href = $(this).attr('uri');
    });
    
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        laydate.render({
            elem: '#orderLine'
            ,range: '~'
            ,done: function(value, date, endDate){
                setOrderLine(value);
            }
        });
    });
    
    $(document).ready(function(){
        setOrderDate('day',1);
        setOrderDate('day',2); 
    });
    
    $(document).on('click','.or',function(){
        var myDate = $(this).attr('myDate');
        setOrderDate(myDate,1);
    });
    
    $(document).on('click','.sell',function(){
        var myDate = $(this).attr('myDate');
        setOrderDate(myDate,2);
    });
    function setOrderDate(myDate,type){
        var now = new Date(); //当前日期
        var nowDayOfWeek = now.getDay(); //今天本周的第几天
        var nowDay = now.getDate(); //当前日
        var nowMonth = now.getMonth(); //当前月
        var nowYear = now.getFullYear(); //当前年
        switch(myDate){
            case 'day':
                newDate = new Date(nowYear, nowMonth, nowDay + 1); //明天
                date = now.format("isoDate") + " ~ " + newDate.format("yyyy-mm-dd");
                break;
            case 'week':
                newDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + 1); //周一
                date = newDate.format("isoDate") + " ~ " + now.format("isoDate");
                break;
            case 'month':
                newDate = new Date(nowYear, nowMonth, 1); //本月1号
                date = newDate.format("isoDate") + " ~ " + now.format("isoDate");
                break;
            default:
                newDate = new Date(nowYear, nowMonth, nowDay + 1); //明天
                date = now.format("isoDate") + " ~ " + newDate.format("isoDate");
                break;
        }
        if(type == 1){
            $("#orderLine").val(date);
            setOrderLine(date);
        }else{
            $("#sellLine").val(date);
            setSellLine(date);
        }
    }
    
    function setOrderLine(date){
        var octx = document.getElementById("orderChart").getContext('2d');
        var lod_index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post('/Admin/Ajax/ajaxOrderLine', {date: date},function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.status == 1){
                var myChart = new Chart(octx, {
                    type: 'line',
                    data: {
                        labels: parsedJson.data.timeDate,
                        datasets: [{
                            label: '订单数',
                            data: parsedJson.data.valDate,
                            backgroundColor: [
                                    'rgba(53, 91, 247, 0.2)',
                            ],
                            borderColor: [
                                    'rgba(53, 91, 247,1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }else{
                layer.msg(parsedJson.msg);
            } 
        });
    }
    
    function setSellLine(date){
        var octx = document.getElementById("sellChart").getContext('2d');
        var lod_index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post('/Admin/Ajax/ajaxSellLine', {date: date},function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.status == 1){
                var myChart = new Chart(octx, {
                    type: 'line',
                    data: {
                        labels: parsedJson.data.timeDate,
                        datasets: [{
                            label: '销售金额',
                            data: parsedJson.data.valDate,
                            backgroundColor: [
                                    'rgba(53, 91, 247, 0.2)',
                            ],
                            borderColor: [
                                    'rgba(53, 91, 247,1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }else{
                layer.msg(parsedJson.msg);
            } 
        });
    }
    
    var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};
 
	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;
 
		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}
 
		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");
 
		mask = String(dF.masks[mask] || mask || dF.masks["default"]);
 
		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}
 
		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};
 
		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();
 
// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};
 
// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};
 
// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};
    
</script>
</body>
</html>