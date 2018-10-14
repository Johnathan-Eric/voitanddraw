$(function(){
	
	if($.cookie('piclist')==1){
		$("#piclist").prop("checked",true);
		$(".logqr").show().find("img").each(function(){
			$(this).attr("src",$(this).attr("sri"))
			});	
	}
	
	if($.cookie('new_window')==1){
		$('.wxedit').attr("target","_blank");	
		$('#new_window').prop("checked",true);
	}
	
	$("#piclist").click(function(){
		if($(this).prop("checked")){
			$.cookie("piclist",1);
			$(".logqr").show().find("img").each(function(){
			$(this).attr("src",$(this).attr("sri"))
			});	
		}else{
		$.cookie("piclist",null);
			$(".logqr").hide();	
		}
	});

	$(document).on('click','.audit',function(){
		if($("input[name='ids[]']:checked").size() == 0){
			art.dialog({
				lock:true,
				content: "请选择要审核的数据",
				icon:'error',
				ok: function () {
					return true;
				},
			});
			return false;
		}
		url = rootpath + 'Admin/Weixin/audit';
		ids = "";
		$("input[name='ids[]']:checked").each(function() {
            ids += (ids == "" ? "" : ",") + $(this).val();
        });
		url += (url.indexOf("?") != -1 ? "&":"?") + "ids="+ids;
		art_ajax("设置微信属性",url,{width:400,padding:0});
		
	})

	var ajaxurl = rootpath + 'Admin/Weixin/weight';
	$("input[name='weight']").blur(function(){
		if($(this).val() != $(this).attr("old")){
				$.ajax({type:'POST',url:ajaxurl,data:{'type':$(this).attr('wtype'),'id':$(this).attr('data'),'typeid':$(this).attr('typeid'),'weight':$(this).val()},success:''})
			}
		})	

	$(".selectsingle").click(function(){
		t = $(this).attr("checked");
		if(t=='checked'){
			$(this).next("input").attr("checked","checked");
		}
		
	})	
	
	$("*[event='wxedit']").click(function(){
		
		var url = $(this).attr('act');	
		var id	= $(this).parents("tr").attr("id");
		url += (url.indexOf("?") > 0 ? "&":"?") + "id="+id;
		art_ajax("设置微信",url,{width:400,padding:0,ok:false});
	});
	
	
		
})
//状态切换
function toggle(obj){
	var url, id, field, fieldval;
	url 	= obj.attr("url");
	id 		= obj.attr("id");
	field	= obj.attr("field");
	fieldval= obj.attr("fieldval");
	
	$.ajax({
		type:"POST",
		url:url,
		data:{id:id, field:field, fieldval:fieldval},
		dataType:"json",
		success: function(data){
			if (data.res === 1){
				if(fieldval == "1"){
					obj.attr("fieldval", "2").attr("is_confirm", "0");
					obj.children("font").css("color", "green").text("正常");
				} else if (fieldval == "2"){
					obj.attr("fieldval", "1").attr("is_confirm", "0");;
					obj.children("font").css("color", "red").text("锁定");
				}else{
					art.dialog("操作失败");
				}
			}else{
				art.dialog(data.msg);
			}
			return;
		}
	})
}