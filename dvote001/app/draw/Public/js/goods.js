$(function(){
	//微信API
	wx.ready(function(){
		var site_url = "http://"+document.domain;
		var gallery = Array();
		$(".goodsgallery img").each(function(){
			gallery.push(site_url+($(this).attr("_src")?$(this).attr("_src"):$(this).attr("src")));	
		})
		$(".goodsgallery img").click(function(){
			wx.previewImage({
				current: site_url+($(this).attr("_src")?$(this).attr("_src"):$(this).attr("src")),
				urls: gallery
			});	
		})
		var conimg = Array();
		$(".goods_content img").each(function(){
			imgurl = $(this).attr("_src")?$(this).attr("_src"):$(this).attr("src");
			imgurl = (imgurl.indexOf("http://")==-1? site_url:"")+imgurl;
			conimg.push(imgurl);	
		})
		$(".goods_content img").click(function(){
			imgurl = $(this).attr("_src")?$(this).attr("_src"):$(this).attr("src");
			imgurl = (imgurl.indexOf("http://")==-1? site_url:"")+imgurl;
			wx.previewImage({
				current: imgurl,
				urls: conimg
			});	
		})
	})

	
	//商品收藏操作
	$(".collect").click(function(){
		var that 	= $(this);
		var id 		= $(this).data("id");
		$.ajax({
			url:"/index.php/ajax/fav",
			data:{id:id},
			type:"POST",
			dataType:"json",
			success: function(res){
				if(res.result){
					if(res.status == 1){
						$(that).addClass("on").find("p").html("已收藏");
						$(".fav_num").html($(".fav_num").html()*1+1);
					}else{
						$(that).removeClass("on").find("p").html("收藏");
						$(".fav_num").html($(".fav_num").html()*1-1);
					}
				}
				else{
					$.ui.dialog.tip({text:res.msg,time:1.5*1000});	
				}
			},
			error: function(){
				$.ui.dialog.tip({text:"收藏失败,请重试",time:1.5*1000});
			}	
		})
	})
	/*商品分享*/
	$(".share").click(function(){
		$(".howShare,#m-bg").show();
		$.ajax({url:$(this).data("uri"),success:function(res){
			if(res.status){
				$(".howShare").find(".qrcode").attr("src",res.data.uri);	
			}
		}});
	});
	$(".claseShare").click(function(){
		$(".howShare,#m-bg").hide();	
	});
	/*规格选择*/
	$(".goods_sepc_1 li,.goods_sepc_2 li").click(function(){
		if($(this).hasClass("select")||$(this).hasClass("unselect")) return;
		var spec_check = function(type){
			spec_stock = 0;spec_price.min=spec_price.max=0;
			$(".goods_sepc_"+type+" li").each(function(){
				var spec_id1 = $(this).attr("data");
				var spec_id2 = spec_ids[type==1?2:1];
				var sku = Math.min(spec_id1,spec_id2)+','+Math.max(spec_id1,spec_id2);
				if(!spec_data[sku] || spec_data[sku]['spec_stock'] == 0){
					$(this).removeClass("select").addClass("unselect");
				}else{
					var _sku = ","+sku+",";
					if(_sku.indexOf(","+spec_ids[type==1?2:1]+",") > -1){
						spec_stock +=spec_data[sku]['spec_stock']*1;
						_price = spec_data[sku]['spec_price']*1;
						spec_price.min = (spec_price.min == 0) ? _price : Math.min(spec_price.min,_price);
						spec_price.max = Math.max(spec_price.max,_price);
					}
					$(this).removeClass("unselect");
				}
			});
		}
		var type = $(this).attr("type");
		spec_ids[type] = $(this).attr("data");
		$(this).parent().find("li").removeClass("select");
		$(this).addClass("select");		
		if(spec_count>1) spec_check(type==1?2:1);
		if(spec_ids[1] && spec_ids[2]){
			var spec_cat_id = Math.min(spec_ids[1],spec_ids[2])+','+Math.max(spec_ids[1],spec_ids[2]);
			$("#goods_stock").html(spec_data[spec_cat_id]['spec_stock']);
			$("price").html(spec_data[spec_cat_id]['spec_price']);
		}else{
			if(spec_count == 1){
				 spec_stock = spec_data[spec_ids[1]]['spec_stock'];
				 spec_price.min=spec_price.max = spec_data[spec_ids[1]]['spec_price'];
				 spec_cat_id = spec_ids[1]
			}else{
				spec_cat_id = '-1';
			}
			$("#goods_stock").html(spec_stock);
			if(spec_price.min==spec_price.max){
				$("price").html(spec_price.min);
			}else{
				$("price").html(spec_price.min.toFixed(2)+"～"+spec_price.max.toFixed(2));
			}
		}
		if(typeof(tejia) !== "undefined" && tejia == "1" && typeof(tprice) !== "undefined"){
			$("price").html(tprice.toFixed(2));
		}
		$(".attr_btns_buy").attr('spec_cat_id', spec_cat_id);
		if(spec_cat_id!=-1) $(".attr_btns_buy").removeClass("buy-gray");
	});	
	
	if(spec_count == 1){
		$.each(spec_data,function(key,obj){
			if(obj.spec_stock == 0) $("li[type='1'][data='"+key+"']").addClass("unselect");	
		})	
	}
	
	$(".buy-sku").bind("click",function(){
		$(".attr_btns_buy").attr("data-type",$(this).hasClass('prt-buy')?"buy":"cart");
		$("#skuCont").addClass("attribute_wrap_show").css("z-index","102");
		$("#skuCont").find("img[_src]").each(function(){
			$(this).attr("src",$(this).attr("_src")).removeAttr("_src");        
    	});
	});
	$("#sku_item_bg").bind("click",function(){
		skuClose()
	});
	$("#sku_close").bind("click",function(){
		skuClose()
	});
})