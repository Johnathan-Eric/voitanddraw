/*表单提交功能*/
$(function(){
	$(".cancel").click(function(){
		var url = $(this).attr("uri");
		if(url){
			location.href = url;
		}else{
			history.back(-1);	
		}
	})
	$(".apply").click(function(){
		var that = $(this);
		var type = $(that).data("type");
		var form = $(that).parents("form");
		if(eval("typeof(chk_"+type+")") == "undefined"){
			if(!eval("chk_default(form)")){return false;}	
			}
		else{
			if(!eval("chk_"+type+"(form)")){return false;}
		}
		$.ui.dialog.loading({text:"正在提交数据…"});	
		//$(that).attr("disabled",true);
		$.ajax({
			url:$(form).attr("action"),
			type:"POST",
			dataType:"json",
			data:$(form).serialize(),
			success: function(data){
				$.ui.dialog.close();
				if(data.res == 1){
					$.ui.dialog.ok({text:data.msg});
					if(data.url){
						location.href=data.url;	
					}else{
						location.reload();	
					}
				}else{
					$.ui.dialog.alert({tip:data.msg});
				}
			}
		});
	});	
});

//默认检查
function chk_default(that){
	var str = $(that).find("input[name='data[name]']").val();
	if(!iscn_username(str)){
		$.ui.dialog.alert({tip:"请填正确填写您的姓名"});
		return false;	
	}
	var str = $(that).find("input[name='data[mobile]']").val();
	if(!istelphone(str)){
		$.ui.dialog.alert({tip:"请输入正确的手机号码"});
		return false;	
	}
	var str = $(that).find("input[name='data[qq]']").val();
	if(str && !isqq(str)){
		$.ui.dialog.alert({tip:"请输入正确的QQ号码"});
		return false;	
	}
	return true;
}