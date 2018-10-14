//商品详情购买
function skuClose(){
	$("#skuCont").removeClass("attribute_wrap_show").css("z-index","-1");
}

$(function(){
	$("#pageload").hide();	
	$(".showload").click(function(){$.ui.dialog.loading({text:"正在加载中…"});return;});
	$(".nav-buttom a").click(function(){$.ui.dialog.loading({text:"正在加载中…"});return;});
	$("a.back,a.getback").tap(function(){
		if($(this).attr("uri")){
			location.href = $(this).attr("uri");	
		}else{
			if(history.length>1){
				history.back(-1);
			}else{
				var uri = $(this).data("uri");
				if(uri){
					location.href = uri;
				}
				else{
					location.href = "/";
				}
			}
		}
	});
	
	//用户删除收藏
	$(document).on("click",".fav-del",function(){
		var id = $(this).data("id");
		var obj = $(this).parents("li");
		$.ui.dialog.confirm({
			text: '确定要删除吗？',
			cancel: {
				val: '取消',
				handler: function(e){
					
				}
			},
			confirm: {
				val: '确定',
				handler: function(e){
					$.ui.dialog.loading({text:"正在处理中…"})
					$.ajax({
						type:"POST",
						url:"/index.php/ajax/delfav",
						data:{id:id},
						dataType:"json",
						success: function(data){
							$.ui.dialog.close();
							if(data.result){
								//$.ui.dialog.tip({text:data.msg});
								$(obj).remove();
								if($("#fav_list").find("li").size()==0){
									location.reload();
								}
							}else{
								$.ui.dialog.alert({tip:data.msg});
							}						
							return;
						}
					})
				}
			}
		})
	})
	//会员中心 收缩JS
	var myList=$(".my-list").find("li");
	myList.each(function(){
		var myLi=$(this).children(".xiangqing");
		if(myLi.length > 0){
			$(this).find("a").tap(function(){
				if( myLi.is(":hidden")){
					myLi.show();
					$(this).prev("i").css({"-moz-transform":"rotate(90deg)","-webkit-transform":"rotate(90deg)","-o-transform":"rotate(90deg)","transform":"rotate(90deg)","top":"11px"});
				}else{
					myLi.hide();
					$(this).prev("i").css({"-moz-transform":"rotate(0deg)","-webkit-transform":"rotate(0deg)","-o-transform":"rotate(0deg)","transform":"rotate(0deg)","top":"6px"});
				}
			});
		};
	});
	//地址选择
	var radio=$(".radio");
	var radioA=radio.children(".choose").find("a.types");
	radio.children(".choose").tap(function(){
		var radioA=$(this).find("a.types");
		if( radioA.attr("class") == "types"){
			$(this).find("a.types").addClass("on");
			$(this).siblings(".choose").find("a.types").removeClass("on");
		}else{
			$(this).find("a.types").removeClass("on")
		};
	});
	//添加到购物车
	$(".attr_btns_buy").click(function(){
		var obj 		= $(this);
		var url 		= obj.attr("url");
		var toUrl		= obj.attr("toUrl");
		var goods_id 	= obj.attr("goods_id");
		var spec_cat_id	= obj.attr("spec_cat_id");
		var number		= $(".buy_number:eq(0)").val();
		if(spec_cat_id == '-1' || $(obj).hasClass("buy-gray")){
			$.ui.dialog.alert({tip:'请选择商品规格！'});
			return false;
		}
		if(typeof(tejia) !== "undefined" && tejia == "1" && typeof(tmaxbuy) !== "undefined" && tmaxbuy > 0 && number > tmaxbuy){
			$.ui.dialog.tip({
				text:"此特价商品限购"+tmaxbuy+"件",
				time:2*1000,
			});
			return false;
		}
		//$.ui.dialog.loading({text:"正在加载中…"});
		$.ajax({
			type:"POST",
			url:url,
			data:{goods_id:goods_id, number:number, spec_cat_id:spec_cat_id},
			dataType:"json",
			success: function(data){
				$.ui.dialog.close();
				if (data.status){
					if(toUrl && $(obj).attr("data-type")=='buy'){
						$.ui.dialog.flatLoading({text:"数据加载中…"});	
						window.location.href=toUrl;
					}else{
						skuClose()
						$.ui.dialog.confirm({
							text: data.msg,
							icon:{
								url: '/Public/images/i-right.png',
								width: '35px',
								height: '36px'
								},
							cancel: {
								val: '关闭'
							},
							confirm: {
								val: '去结算',
								handler: function(e){
									$.ui.dialog.flatLoading({text:"数据加载中…"});	
									window.location.href=toUrl;
								}
							}
						})
					}					
				}else{
					$.ui.dialog.alert({tip:data.msg});
				}
				if(data.total && data.total.goods_num > 0){
					$(".showload").find("i").show().html(data.total.total);
					$(".cart-num span").show().html(data.total.total);	
				}
				return;
			},
			error:function(){
				$.ui.dialog.alert({tip:'加入失败，请重试！'});
			}
			
		})		
	})
	
	//购物车
	var shopCart=$(".shopcart");
	shopCart.find("li").each(function(){
		$(this).find(".check-icon").tap(function(){
			if($(this).attr("class") == "check-icon"){
				$(this).addClass("no-check");
				$(".money").hide();
			}else{
				$(this).removeClass("no-check");
				$(".money").show();
			}
		});
		$(this).find(".del").tap(function(){
			var id	= $(this).attr("id");
			var url	= $(this).attr("url");
			
			$.ui.dialog.confirm({
				text: '确定要删除吗？',
				cancel: {
					val: '取消',
					handler: function(e){
						
					}
				},
				confirm: {
					val: '确定',
					handler: function(e){
						$.ui.dialog.loading({text:"正在处理中…"})
						$.ajax({
							type:"POST",
							url:url,
							data:{id:id},
							dataType:"json",
							success: function(data){
								$.ui.dialog.close();
								if(data.status){
									$.ui.dialog.ok({text:data.msg});
									$(this).parents("li").remove();
									location.reload();
								}else{
									$.ui.dialog.alert({tip:data.msg});
								}						
								return;
							}
						})
					}
				}
			})
			
		});
	});
	
	//页面跳转
	$(".jump_url").tap(function(){
		var obj 		= $(this);
		url 			= obj.attr("url");
		$.ui.dialog.loading({text:"正在加载中…"})
		window.location.href=url;
	})

	$(".shopcart-ment").find(".clear").children("a").tap(function(){
		var shopLi=shopCart.find("li").size();
		if(shopLi> 0){
			var url			= $(".clear").attr("url");
			$.ui.dialog.confirm({
				text: '确定要清空购物车吗？',
				cancel: {
					val: '取消',
					handler: function(e){
						
					}
				},
				confirm: {
					val: '确定',
					handler: function(e){
						$.ui.dialog.loading({text:"正在处理中…"})
						$.ajax({
							type:"POST",
							url:url,
							dataType:"json",
							success: function(data){
								$.ui.dialog.close();								
								if(data.status){
									shopCart.find("li").remove();
									location.reload();
								}else{
									$.ui.dialog.alert({tip:data.msg});
								}								
								return;
							}
						})
					}
				}
			})
		}
	});	
	var shopPn=$(".items-opt");
	shopPn.children(".btn-del").tap(function(){
		var that = $(this);
		var shopNum=$(that).next(".fm-txt");
		if(parseInt(shopNum.val()) > 1){
			var num = parseInt(shopNum.val())-1;
			if($(this).attr("action") == "update_car"){
				update_car(num,shopNum,that);
			}else{
				shopNum.val(num);
				$(".buy_number").val(num);
			}
			if(num==1) $(this).addClass("unselect");
		}else{
			$(that).addClass("unselect");
		};
		$(that).parent().find(".btn-add").removeClass("unselect");
	});
	shopPn.children(".btn-add").tap(function(){
		var that = $(this);
		var shopNum=$(this).prev(".fm-txt");
		$(that).parent().find(".btn-del").removeClass("unselect");
		var num = parseInt(shopNum.val()) + 1;
		
		if(typeof(tejia) !== "undefined" && tejia == "1" && typeof(tmaxbuy) !== "undefined" && tmaxbuy > 0 && num > tmaxbuy){
			num = tmaxbuy;
			$.ui.dialog.tip({
				text:"此特价商品限购"+tmaxbuy+"件",
				time:2*1000,
			});
		}
		
		if($(this).attr("action") == "update_car"){
			update_car(num,shopNum,that);
		}else{
			var stock = $("#goods_stock").text() * 1;
			if(num > stock){
				$(that).addClass("unselect");
				return false;
			}else{
				shopNum.val(num);
				$(".buy_number").val(num);
				if(num==stock) $(that).addClass("unselect");	
			}
		}
	});
		
	//更新购物车
	function update_car(num,shopNum,that){
		$.ui.dialog.loading({text:"正在加载中…"})
		$.ajax({
			type:'POST',
			url:$(that).attr('uri'),
			data:{'car_id':$(that).attr("car_id"), 'number':num},
			dataType:"json",
			success: function(res){	
				$.ui.dialog.close();		
				if(res.status == false){
					if(res.msg) $.ui.dialog.alert({tip:res.msg});
				}
				if(typeof(res.total) !== "undefined"){
					$("#oamount").html(res.total.oamount);
					$("#discount").html(res.total.discount);
					$("#amount").html(res.total.amount);
					$("#num").html(res.total.total);
					$(".shpcart i").show().html(res.total.total);
				}
				if(res.number){
					shopNum.val(res.number);	
				}
				
				if(res.callback) eval(res.callback);
			}
		})	
	}
	
	//确认收货
	$(".confirm").tap(function(){
		var order_id	= $(this).attr('order_id');
		var url			= $(this).attr('url');
		$.ui.dialog.confirm({
				text: '确定您已收货吗？',
				cancel: {
					val: '取消',
					handler: function(e){
						
					}
				},
				confirm: {
					val: '确定',
					handler: function(e){
						$.ui.dialog.loading({text:"正在处理中…"});			
						$.ajax({
							type:"POST",
							url:url,
							data:{order_id:order_id},
							dataType:"json",
							success: function(data){
								if (data.status){
									$.ui.dialog.ok({text:'操作成功'});
									window.location.reload();					
								}else{
									$.ui.dialog.alert({tip:data.msg});
								}
								return;
							},
							error:function(){
								$.ui.dialog.alert({title:'确认收货',tip:'操作失败，请重试！'});
							}
						})
					}
				}
			})	
	})
});