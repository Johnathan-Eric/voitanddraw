<?php defined('IN_IA') or exit('Access Denied');?>		<?php  if(empty($footer_off)) { ?>
			<div class="text-center footer" style="display:none;">
				<?php  if(!empty($_W['page']['footer'])) { ?>
					<?php  echo $_W['page']['footer'];?>
				<?php  } else { ?>
					
				<?php  } ?>
				&nbsp;&nbsp;<?php  echo $_W['setting']['copyright']['statcode'];?>
			</div>
		<?php  } ?>
	
		
	</div>
	<!-- 提示弹窗start -->



<div id="dialog2" style="opacity: 1;display:none;">
        <div class="weui-mask"></div>
        <div class="weui-dialog">
            <div class="weui-dialog__bd">返回错误！</div>
            <div class="weui-dialog__ft">
                <a href="javascript:hidemod('dialog2');" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
            </div>
        </div>
</div> 

<div id="toast" style="opacity: 1; display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-icon-success-no-circle weui-icon_toast"></i>
            <p class="weui-toast__content" style="color:#fff">已完成</p>
        </div>
</div>
<div id="loadingToast" style="opacity: 1; display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-loading weui-icon_toast"></i>
            <p class="weui-toast__content" style="color:#fff">数据加载中</p>
        </div>
</div>
<!-- 提示弹窗start -->
<?php
	$_share['title'] = !empty($_share['title']) ? $_share['title'] : $_W['account']['name'];
	$_share['imgUrl'] = !empty($_share['imgUrl']) ? $_share['imgUrl'] : '';
	if(isset($_share['content'])){
		$_share['desc'] = $_share['content'];
		unset($_share['content']);
	}
	$_share['desc'] = !empty($_share['desc']) ? $_share['desc'] : '';
	$_share['desc'] = preg_replace('/\s/i', '', str_replace('	', '', cutstr(str_replace('&nbsp;', '', ihtmlspecialchars(strip_tags($_share['desc']))), 60)));
	if(empty($_share['link'])) {
		$_share['link'] = '';
		$query_string = $_SERVER['QUERY_STRING'];
		if(!empty($query_string)) {
			parse_str($query_string, $query_arr);
			$query_arr['u'] = $_W['member']['uid'];
			$query_string = http_build_query($query_arr);
			$_share['link'] = $_W['siteroot'].'app/index.php?'. $query_string;
		}
	}
?>

	<script type="text/javascript">


$.getJSON("<?php  echo $this->createMobileUrl('Count',array('ty'=>'pv','rid'=>$_GPC['rid'],'id'=>$_GPC['id']))?>", function(result) {
	});
	wx.config(jssdkconfig);
	
	var $_share = <?php  echo json_encode($_share);?>;
	
	if(typeof sharedata == 'undefined'){
		sharedata = $_share;
	} else {
		sharedata['title'] = sharedata['title'] || $_share['title'];
		sharedata['desc'] = sharedata['desc'] || $_share['desc'];
		sharedata['link'] = sharedata['link'] || $_share['link'];
		sharedata['imgUrl'] = sharedata['imgUrl'] || $_share['imgUrl'];
	}

	function tomedia(src) {
		if(typeof src != 'string')
			return '';
		if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0) {
			return src;
		} else if(src.indexOf('../addons') == 0 || src.indexOf('../attachment') == 0) {
			src=src.substr(3);
			return window.sysinfo.siteroot + src;
		} else if(src.indexOf('./resource') == 0) {
			src=src.substr(2);
			return window.sysinfo.siteroot + 'app/' + src;
		} else if(src.indexOf('images/') == 0) {
			return window.sysinfo.attachurl+ src;
		}
	} 
	

	
	if(sharedata.desc == ''){
		var _share_content = _removeHTMLTag($('body').html());
		if(typeof _share_content == 'string'){
			sharedata.desc = _share_content.replace($_share['title'], '')
		}
	}
	wx.ready(function () {
		wx.onMenuShareAppMessage(sharedata);
		wx.onMenuShareTimeline(sharedata);
		wx.onMenuShareQQ(sharedata);
		wx.onMenuShareWeibo(sharedata);
	});
	sharedata.success = function(){
			var url = "<?php  echo $this->createMobileUrl('Count',array('ty'=>'share','rid'=>$_GPC['rid'],'id'=>$_GPC['id']))?>";
			$.post(url, function(dat){});
	}
	$(function(){
		if($('.js-quickmenu')!=null && $('.js-quickmenu')!=''){
			var h = $('.js-quickmenu').height()+'px';
			$('body').css("padding-bottom",h);
		}else{
			$('body').css("padding-bottom", "0");
		}
		
		if($('.js-quickmenu')!=null && $('.js-quickmenu')!=''){
			var h = $('.js-quickmenu').height()+'px';
			$('body').css("padding-bottom",h);
		}
	});

	
	
	</script>
	<?php  echo m('tpl')->tpl_footer();?>


<script>;</script><script type="text/javascript" src="http://inkhome.net/app/index.php?i=6&c=utility&a=visit&do=showjs&m=tyzm_diamondvote"></script></body>
</html>
