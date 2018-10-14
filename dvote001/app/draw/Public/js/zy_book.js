var get_region = function(type, obj, sdata){
		url = "/index.php/Ajax/get_region";
		if(typeof(obj) != "object") obj=$("#"+obj);
		sdata.region_id = sdata.region_id ? sdata.region_id : $(obj).val();

		if(type == 0 && sdata.region_id == 0 && sdata.region_name == 'city'){
			get_region(1,'province',{'next':'city','next2':'county'});
			return;	
		}
		if(type == 0 && sdata.region_id == 0 && sdata.region_name == 'county'){
			get_region(1,"city",{'next':'county'});	
			return;	
		}
		$.getJSON(url,{id:sdata.region_id,type:type},
			function(data){
				if(type == 0){
					$(obj).children().remove()
					$.each(data,function(idx,item){
						$("<option value="+item.region_id+">"+item.region_name+"</option>").appendTo($(obj));
					});	
					$(obj).find("option[value='"+(sdata.region_id)+"']").attr("selected", true);
				}else{
					nobj = $("#"+sdata.next);
					if(sdata.next2) $("#"+sdata.next2).children().not($("#"+sdata.next2).children()[0]).remove();
					if(sdata.region_id == 0){
						$(nobj).children().not($(nobj).children()[0]).remove();
					}else{
						if(data) $(nobj).children().remove();
							$.each(data,function(idx,item){
							$("<option value="+item.region_id+">"+item.region_name+"</option>").appendTo(nobj);
						});
					}
					$("input[name='region_id']").val($(obj).val());
				}
			}
		);
	}

$(function(){
	$("textarea[name='ecard_content']").keyup(function(){
		var len = $(this).val().length;
		if(len>200){
			$(this).val($(this).val().substr(0,200));
			len = 200;
		}
		$(this).next("i").html(200-len);
	});
		
	$(".order-other").children("dl").find("dt").click(function() {
			if($(this).next("dd").is(":hidden")) {
				$(this).next("dd").show();
				$(this).find("input").attr("checked", true);
			} else {
				$(this).next("dd").hide();
				$(this).find("input").removeAttr("checked")
			}
			order_settle();
		})
	
	$(document).on('click','ul.add_list li',function(){
		$(this).addClass("on").siblings().removeClass("on");
	});
	
	//单选
	$(".select_radio a").click(function(){
		$(this).addClass('active').siblings().removeClass("active");
		$(this).parent().find("input").val($(this).data('val'));
	});
	//最多多选
	$(".check_max input").click(function(){
		var max = $(this).parents('.check_max').data('num');
		if(!max || max*1 < 1) return;
		if($(".check_max input:checked").size()>=max){
			$(".check_max input").not(":checked").attr("disabled",true);
		}else{
			$(".check_max input").removeAttr("disabled");
		}
	});
	
	$(document).on("click",".del_address" ,function(){
		var that = $(this);
		var da = $.dialog({
			content:'确定要删除地址吗?',
			button:['确定','取消']
		});
		da.on("dialog:action",function(e){
			if(e.index != 0) return;
			$.get($(that).data("uri")).then(function(res){
				zy.tips(res.info);
				if(res.status == "1") $(that).parents("li").remove();
			});
		});
	});
	
})