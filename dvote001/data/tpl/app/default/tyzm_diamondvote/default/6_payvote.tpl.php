<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" href="<?php echo MODULE_URL;?>/template/static/css/donate.css?a=12">
<style type="text/css">
  <?php  if($reply['diamondmodel']==1 || $reply['isshowgift']>2) { ?>
.divbottommenu .divitem{width:33.333%;}

<?php  } ?>
</style>
<div class="donate">

    <div class="weui-panel__bd user_info">
        <a href="<?php  echo $this->createMobileUrl('view', array('rid' => $reply['rid'],'id' => $voteuser['id']))?>" class="weui-media-box weui-media-box_appmsg weui-cell_access">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="<?php  echo tomedia($voteuser['avatar']);?>" alt="<?php  echo $voteuser['name'];?>" style="border-radius: 50%;
    border: 1px solid #ffffff;box-shadow: 0px 1px 16px #c7c7c7;">
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title"><?php  echo $voteuser['name'];?></h4>
                <p class="weui-media-box__desc">给Ta送上一份礼物吧</p>
            </div>
            <span class="weui-cell__ft"></span>
        </a>
        
    </div>
<div class="divbottommenu"style="border-top: 1px dashed #d5d5d5;">
  
  <div class="divitem" >
	  <span><i class="fa fa-user fa-fw"></i>编号</span>
	  <span><?php  echo $voteuser['noid'];?></span>
  </div>
  <div class="divitem">
	  <span><i class="fa fa-ticket fa-fw"></i>票数</span>
	  <span><?php  echo $voteuser['votenum'];?></span>
  </div>
  <div class="divitem">
	  <span><i class="fa fa-fire fa-fw"></i>热度</span>
	  <span><?php  echo $pvtotal['pv_total'];?></span>
  </div>
  <?php  if($reply['diamondmodel']!=1 && $reply['isshowgift']<3) { ?>
  <div class="divitem">
	  <span><i class="fa fa-diamond fa-fw"></i>礼物</span>
	  <span><?php  echo $voteuser['giftcount']*$reply['giftscale'];?><?php  echo $reply['giftunit'];?></span>
  </div>
  <?php  } ?>
</div>
<div class="donate_money_p_p">
		<div class="donate_money_choose_p">
		
		
		
		<?php  if(is_array($giftlist)) { foreach($giftlist as $item) { ?>
			<div class="donate_money_choose donate_money_choose1">
			    <?php  if(is_array($item)) { foreach($item as $index => $rom) { ?>
					<div class="donate_money" dada-key="<?php  echo $index;?>" dada-tip="单价<?php  echo $rom['giftprice'];?>元，抵<?php  echo $rom['giftvote'];?>票！">
						<p class="donate_money_icon"><img src="<?php  echo tomedia($rom['gifticon']);?>" width="50%" /></p> 
						<span class="donate_money_title"><?php  echo $rom['gifttitle'];?></span>
						<span class="donate_money_num"><?php  echo $rom['giftprice']*$reply['giftscale'];?><?php  echo $reply['giftunit'];?></span>
					</div>
				<?php  } } ?>
			</div>
		<?php  } } ?>
		</div>
		<div class="donate_money_edit">
			<div class="donate_money_edit_text">请选择以上礼品</div>
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label for="" class="weui-label">数量：</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="count">
                        <option selected value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="520">520</option>
                        <option value="666">666</option>
                        <option value="1314">1314</option>
                    </select>
                </div>
            </div>
		</div>

	</div>  

	<div class="donate_bank_p">
        <?php  if(empty($reply['defaultpay'])) { ?>
        <a href="javascript:;" class="weui-btn weui-btn_primary js-wechat-pay">去支付</a>
		<?php  } else { ?>
        <a href="javascript:;" id="weixin" class="weui-btn weui-btn_primary">去支付</a>
        <?php  } ?>
	</div>
<!--<div class="donate_submit">确定</div>-->
</div>
<div id="qshuli" class="donate_alert_mask" onclick="document.getElementById('qshuli').style.display='none';"><div class="donate_alert"><div class="donate_alert_msg">请选择你要赠送的礼物</div><div class="donate_alert_btns"><div class="donate_alert_btn ok">确定</div></div></div></div>
<br></br>
<script>
$(document).ready(function(){
    var giftid="";
	$(".donate_money_choose .donate_money").click(function(){
		var tip = $(this).attr("dada-tip"); 
		$(".donate_money_edit_text").text(tip); 
		giftid=$(this).attr("dada-key"); 
		$(".donate_money_choose .donate_money").removeClass("active");
		$(this).addClass("active");
    });
    


<?php  if(empty($reply['defaultpay'])) { ?>
    //
    //发起微信支付，微信支付依赖于 WeixinJSBridge 组件，所以发起时应该在ready事件中进行
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
   
        $('.js-wechat-pay').removeClass('mui-disabled');
        $('.js-wechat-pay').click(function(){
            var count=$("select[name='count']").val();
        	if(giftid==""){dialog2("请选择你要赠送的礼物");return false;}
            loadingToast("奋力加载");
            $.ajax({
                type: "POST",
                url: "<?php  echo $this->createMobileUrl('pay', array('rid' => $reply['rid'],'id' => $voteuser['id'],'ty' => 1,'type'=>1))?>",
                data: {giftid:giftid,count:count},
                dataType: "json",
                success: function(str) {
                    hidemod("loadingToast");
                    if(str!=null && str!='' && str.error!=1){
                            payment = str.message.message;
                            WeixinJSBridge.invoke("getBrandWCPayRequest", {
                                    appId: payment.appId,
                                    timeStamp: payment.timeStamp,
                                    nonceStr: payment.nonceStr,
                                    "package": payment["package"],
                                    signType: payment.signType,
                                    paySign: payment.paySign
                            },
                           function(res){     
                               if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                                    toast("支付成功");
                                    setTimeout(location.href ="<?php  echo $this->createMobileUrl('view', array('rid' => $reply['rid'],'id' => $voteuser['id']))?>",3000); 
                                    
                               }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                                    dialog2("已取消支付");return false;
                               }else{
                                    dialog2(res.err_msg);return false;
                               }
                           }
                           ); 
                    }else{
                        dialog2(str.msg);
                    }
                },
                error: function(err) {
                    hidemod("loadingToast");
                    dialog2("发生错误，请刷新后重试！(1)");
                }
            });
        });
        $('.js-wechat-pay').html('微信支付');
    });

<?php  } else { ?>

    $("#weixin").click(function(){
        var count=$("select[name='count']").val();
    	if(giftid==""){dialog2("请选择你要赠送的礼物");return false;}
    	    location.href ="<?php  echo $this->createMobileUrl('gift', array('rid' => $reply['rid'],'id' => $voteuser['id'],'ty' => 1))?>&type=1&giftid="+giftid+"&count="+count;
    });

<?php  } ?>
});

</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>
