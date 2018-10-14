<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel='stylesheet' type='text/css' href='/Public/Admin/Css/style.css?v=<?php echo C('jsversion');?>'>
        <script type="text/javascript" src="/Public/js/jquery.min.js?v=<?php echo C('jsversion');?>"></script>
        <script type="text/javascript" src="/Public/Admin/Js/common.js?v=<?php echo C('jsversion');?>"></script>
        <title><?php echo C('site_title');?>-后台管理</title>
    </head>
    <body style="background:#E2E9EA" style="padding:0; margin:0;">
        <div id="header" class="header">
            <div class="logo">
            	<a href="/Admin" target="_blank"><span class='xitong'><?php echo C('site_title');?></span></a></div>
        <div class="nav">
                <div class="panel_state">
                        <div class="user_info">
                                <img class="user_info_img" src="/Public/Admin/Images/u2632.png">
                                <span style="width:100%">
                                        <?php echo ($user["role_name"]); ?>&nbsp
                                        <?php echo ((isset($user["username"]) && ($user["username"] !== ""))?($user["username"]):''); ?>
                                </span>
                                <span class="pulldown_span"></span>
                                <div class="user_detail">
                                    <div style="width: 100%;position: relative;">
                                            <div class="my_xcx">
                                                    <p><span>我的账户</span></p>
                                            </div>
                                            <div class="my_xcx" style="background-color: #000;">
                                                <p><span style="color: #FFF;"><?php echo ($user["uname"]); ?></span></p>
                                            </div>
<!--                                             <div class="my_xcx">
                                                    <p><span>更多</span></p>
                                            </div> -->
                                            <div class="my_xcx" style="height: 41px;border: 0;">
                                                    <p><span style="font-size: 16px;line-height:35px;width: 130px;">
                                                            <img width="12px" height="12px" src="/Public/Admin/Images/add.png" />
                                                            <a href="<?php echo U('Index/profile');?>" target="main"><span style="padding: 0;height: 41px;color: #8a8a8a;">修改密码</span></a>
                                                    </span>
                                                    </p>
                                            </div>
                                    </div>
                                </div>
                        </div>
                        <div class="nav_logout">
                                <img class="u_img" src="/Public/Admin/Images/u2645.png" tabindex="0">
                        </div>
                        <div class="panel_logout">
                            <a href="<?php echo U('User/logout');?>" target="_top" onClick="return confirm('确定要退系统吗？');"><img class="u_pic" src="/Public/Admin/Images/u2651.png"></a>
                        </div>
                </div>
        </div>
        <div class="topmenu">
        <ul>
        <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($k % 2 );++$k;?><li id="menu_<?php echo ($k); ?>">
            <span>
                <a href="javascript:void(0);" onClick="sethighlight(<?php echo ($k); ?>);">
                    <span class="topmenu_img_span"></span>
                    <span class="topmenu_text"><?php echo ($tag["auth_name"]); ?></span>
                </a>
            </span>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        </div>
        <div class="header_footer"></div>
        </div>
        <div id="Main_content">
        <div id="MainBox" >
        <div class="main_box">
        <iframe name="main" id="Main" src='<?php echo U("Index/main");?>' frameborder="false" scrolling="auto"  width="100%" height="auto" allowtransparency="true" style="background-color:#FFF;"></iframe>
        </div>
        </div>
        <div id="leftMenuBox">
            <div id="leftMenu">
                <div style="position: relative;">
                        <?php if(is_array($menu)): $ke = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($ke % 2 );++$ke;?><dl id="nav_<?php echo ($ke); ?>">
                                <div class="menu_title">
                                        <div style="height:100%;padding-left:0;">
                                                <p style="margin:0;line-height:49px;font-size:16px;"><span style="display:inline-block;width:100%;text-align: center;"><?php echo ($tag["name"]); ?></span></p>
                                        </div>
                                </div>
                                <div class="panel_menu_button">
                                        <ul>
                                        <?php if(is_array($tag['child'])): $kk = 0; $__LIST__ = $tag['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$r): $mod = ($kk % 2 );++$kk;?><li>
                                                <a uri="<?php echo U('/Admin/'.$r['auth_path']);?>" target="main" class="childCli">
                                                    <p style="margin:0;padding-left:38px;line-height:40px;font-size:14px;"><span><?php echo ($r["auth_name"]); ?></span></p>
                                                </a>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul>
                                </div>
                            </dl><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
        </div>
        <div id="footer" class="footer" ><!--Powered by <a href="http://www.zooyoo.cc" target="_blank">zooyoo.cc</a>&nbsp;南昌卓云科技&nbsp;--> <span id="run">为了更好的体验请使用<a href="https://www.baidu.com/s?wd=%20chrome" target="_blank">歌谷</a>或者<a href="http://www.firefox.com.cn/" target="_blank">火狐</a>浏览器</span></div>
        <script language="JavaScript">
                var now_id;
	               if (!Array.prototype.map)
                    Array.prototype.map = function(fn, scope) {
                        var result = [], ri = 0;
                        for (var i = 0, n = this.length; i < n; i++) {
                            if (i in this) {
                                result[ri++] = fn.call(scope, this[i], i, this);
                            }
                        }
                        return result;
                    };
                var getWindowWH = function() {
                    return ["Height", "Width"].map(function(name) {
                        return window["inner" + name] || document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
                    });
                }
                window.onload = function() {
                    if (!+"\v1" && !document.querySelector) { //IE6 IE7
                        document.body.onresize = resize;
                    } else {
                        window.onresize = resize;
                    }
                    function resize() {
                        wSize();
                        return false;
                    }
                }
                function wSize() {
                    var str = getWindowWH();
                    var strs = new Array();
                    strs = str.toString().split(","); //字符串分割
                    var h = strs[0] - 80;
                    $('#leftMenu').height(h);
                    $('#Main').height(h);
                }
                wSize();
				
                function set_now_menu(id){
                        if(id){
                                now_id = id	;
                        }else{
                                return now_id;	
                        }
                }

                function sethighlight(n) {
                    $('.topmenu li span').removeClass('current');
                    $('#menu_' + n + ' span').addClass('current');
                    $('#leftMenu dl').hide();
                    $('#nav_' + n).show();
                    $('#leftMenu div dl li a').removeClass('current');
                    $('#nav_' + n + ' li a').eq(0).addClass('current');
                    url = $('#nav_' + n + ' li a').eq(0).attr('uri');
                    if(url) window.main.location.href = url;
                }
                $(".childCli").on('click',function(){
                    $('#leftMenu div dl li a').removeClass('current');
                    $(this).addClass('current');
                    window.main.location.href = $(this).attr('uri');
                });

                /* function toggleMenu(doit) {
                    if (doit == 1) {
                        $('#Main_drop a.on').hide();
                        $('#Main_drop a.off').show();
                        $('#MainBox .main_box').css('margin-left', '24px');
                        $('#leftMenu').hide();
                    } else {
                        $('#Main_drop a.off').hide();
                        $('#Main_drop a.on').show();
                        $('#leftMenu').show();
                        $('#MainBox .main_box').css('margin-left', '224px');
                    }
                } */
                $(function(){
                	var light = $(".topmenu li:first").find("a").attr("onClick");
                	if(light){eval(light)}else{sethighlight(1)}
         
                })
               
               	$(function(){
               		var isShow = true;
               		$(".user_info").click(function(){
               			if(isShow == true) {
               				$(".user_detail").show();
               				$("span.pulldown_span").css("background","url(/Public/Admin/Images/pulltop.png)");
               				isShow = false;
               			} else {
               				$(".user_detail").hide();
               				$("span.pulldown_span").css("background","url(/Public/Admin/Images/pulldown.png)");
               				isShow = true;
               			}
               		});
               	});
            </script>
    </body>
</html>