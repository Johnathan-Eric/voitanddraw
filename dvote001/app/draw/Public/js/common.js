var __img__ = __img__ ? __img__ : "/Public/images";
var __js__ = __js__ ? __js__ : "/Public/Js";
var __css__ = __css__ ? __css__ : "/Public/css";
var __dir__ = __dir__ ? __dir__ : "/";
var rootpath = "/index.php/"

$.extend({
	//  获取对象的长度，需要指定上下文 this
	Object:     {
		count: function( p ) {
			p = p || false;
			return $.map( this, function(o) {
				if( !p ) return o;
				return true;
			} ).length;
		}
	}
});
//省市区联动
$(document).on('change','select[event="loadRegion"]',function(){
  var url 	= $(this).prevAll("input[event='region_id']").attr("uri");
  var hidden_id = $(this).prevAll("input[name='hidden_id']").val();
  var type 	= 1;
  var id 	= $(this).val();
  $(this).nextAll().children("option[value!=0]").remove();
  if(id == 0){
	  var region_id = $(this).prev("select[event='loadRegion']").val();
	  $(this).prevAll("input[event='region_id']").val(region_id);
	  return;
  }
  $(this).prevAll("input[event='region_id']").val(id);
  var _this = $(this);
  $(this).find("option[value='"+id+"']").attr("selected", true);
  $.getJSON(url,{id:id,type:type,hidden_id:hidden_id},
     function(data){
		   if(data) $(_this).next("select[event='loadRegion']").children().remove();
           $.each(data,function(idx,item){
                $("<option value="+item.region_id+">"+item.region_name+"</option>").appendTo($(_this).next("select[event='loadRegion']"));
           });
      }
  );
});

$(function(){
	
	//确认收货
	$(".confirm").click(function(){
		var order_id	= $(this).attr('order_id');
		var url			= $(this).attr('url');
		$.ajax({
			type:"POST",
			url:url,
			data:{order_id:order_id},
			dataType:"json",
			success: function(data){
				if (data.status){
					alert('已确认收货');
					 window.location.reload();
					
				}else{
					alert(data.msg);
				}
				return;
			},
			error:function(){
				alert('确认失败！');
			}
		})
		 
			
	})
	//立即抢购
$(".add_cart").click(function(){
		var obj 		= $(this);
		url 			= obj.attr("url");
		toUrl			= obj.attr("toUrl");
		goods_id 		= obj.attr("goods_id");
		number			= $("#number").val();
		$.ajax({
			type:"POST",
			url:url,
			data:{goods_id:goods_id, number:number},
			dataType:"json",
			success: function(data){
				if (data.status){
					if(toUrl){
						window.location.href=toUrl;
					}else{
						alert(data.msg);
					}
					
				}else{
					alert(data.msg);
				}
				return;
			},
			error:function(){
				alert('加入购物车失败,请重新加入！');
			}
			
		})		
	})
	
	//页面跳转
	$(".jump_url").click(function(){
		var obj 		= $(this);
		url 			= obj.attr("url");
		window.location.href=url;
	})

	//显示大图
	$("*[event='showbimg']").click(function(){
		var imgurl = $(this).attr("src")?$(this).attr("src"):$(this).attr("href")?$(this).attr("href"):"";
		if(imgurl){
			var title = $(this).attr("alt")?$(this).attr("alt"):$(this).attr("title")?$(this).attr("title"):"大图显示"; 
			art.dialog({
				title: title,
				content: '<img src="'+imgurl+'" style="max-width:640px;">',
				padding:0
			});
			return false;	
		}	
	});
	/*中文转英文*/
	$("*[event='cntoeng']").click(function(){
			var data = $(this).attr("data");
			if(!data) return;
			data = $.parseJSON(data);
			var cnstr = $("#"+data.id).val();
			data.keyword = cnstr;
			if(cnstr == ""){
				alert("请选择输入内容");
				$("#"+data.id).focus();
				return;	
			}
			$.ajax({
				type:'POST',
				dataType:"JSON",
				url:rootpath+'ajax/cntoeng',
				data:data,
				success:function(res){
					if(res.result){
						$("#"+data.engid).val(res.data);
					}
				},
				error:function(){
					alert("fuck");
				}
			});
			
		});
	
	/*重置搜索表单*/
	$("input.reset").click(function(){
		var form = $(this).parents("form");
		if(form){
			$(form).find("input[type='text']").val('');
			$(form).find(".textbox-value").val("");
			$(form).find("select").find("option:first").attr("selected",true);	
		}
	});
	
	$(".money_format").keyup(function(){
		 var v		= $(this).val();
		 var v2 	= v.replace(/[^0-9.]/g,'');
		 var len	= v2.length;
		 var no1	= v2.substr(0,1);
		 var no2	= v2.substr(1,1);
		 var point	= v2.indexOf(".");
		 
		 if(no1=='.'){
			 v2 = '';
		 }
		 if(len>=2 && v2*1==0 && no2!='.'){
			 v2 = '';
		 }
		 if(len>=2 && no1==0 && no2!='.'){
			v2 = v2.substr(1);	 
		 }
	 	if(point>0){
	 		v2 = v2.substr(0,point+3);
	 	}
			
		 $(this).val(v2);
	});

	
	/*重置搜索表单*/
	$("input.reset").click(function(){
		var form = $(this).parents("form");
		if(form){
			$(form).find("input[type='text']").val('');
			$(form).find("select").find("option:first").attr("selected",true);	
		}
	});


	$(document).on('click',"*[event='tips']",function(){
		var data = $(this).attr("data");
		if(!data) return;
		art.dialog({
			title: '详情',
			padding: 0,
			lock:true,
			ok: true,
			okVal:'关闭',
			content:"<div style='padding:20px;'>"+data+"</div>",
		});
	})
	/* 异步移除数据*/
	$("*[event='remove']").click(function(){
		var icon = $(this);
		var uri = ($(this).attr('uri')) ? $(this).attr('uri') : "" ;
		var data = $(this).attr("data");
		if(data && confirm('\u60a8\u786e\u5b9a\u5220\u9664\u8be5\u5206\u7c7b\u5417\uff1f')) {
			data = eval("("+data+")");
	
			if(!data.act) data.act = 'remove_field';
	
			$.ajax({
				url :uri + "ajax_" + data.act,
				type:"POST",
				//async:false,
				data:"field=" + data.field + "&id=" +data.id,
				dataType:"json",
				success: function(res){
					if (res.message)
					{
						alert(res.message);
						window.location.reload();
					}
					if (res.error == 0)
					{
						window.location.reload();
					}
				}
			});
		}
	});
	/* 切换状态*/
	$("*[event='toggle']").click(function(){
		var icon = $(this);
		var src = $(this).attr('src');
		var uri = ($(this).attr('uri')) ? $(this).attr('uri') : "" ;
		var val = (src.match(/yes.gif/i)) ? 0 : 1;
		var data = $(this).attr("data");
		if(data) {
			data = eval("("+data+")");
	
			if(!data.act) data.act = 'update_field';
			//if(!data.act) data.prototype.act='update_field';
	
			$.ajax({
			url :uri + "ajax_" + data.act,
				type:"POST",
				//async:false,
				data:"field=" + data.field +"&val=" + val + "&id=" +data.id,
				success: function(msg){
					res = $.parseJSON(msg);
					if (res.error == 0)
					{
						var img_url;
						img_url = (val == 0) ? src.replace('yes.gif','no.gif') : src.replace('no.gif','yes.gif');
						//var img_url = (res.content > 0) ? 'images/yes.gif' : 'images/no.gif';
						icon.attr('src',img_url);
					}else{
						alert(res.message);
					}
				}
			});
		}
	});

	
	//省市区联动
	$('select[event="loadRegion"]').change(function() {
	  var url 	= $(this).prevAll("input[event='region_id']").attr("uri");
	  var type 	= 1;
	  var id 	= $(this).val();
	  $(this).nextAll().children("option[value!=0]").remove();
	  if(id == 0){
		  var region_id = $(this).prev("select[event='loadRegion']").val();
		  $(this).prevAll("input[event='region_id']").val(region_id);
		  return;
	  }
	  $(this).prevAll("input[event='region_id']").val(id);
	  var _this = $(this);
	  $(this).find("option[value='"+id+"']").attr("selected", true);
	  $.getJSON(url,{id:id,type:type},
		 function(data){
			   if(data) $(_this).next("select[event='loadRegion']").children().remove();
			   $.each(data,function(idx,item){
					$("<option value="+item.region_id+">"+item.region_name+"</option>").appendTo($(_this).next("select[event='loadRegion']"));
			   });
		  }
	  );
	});

	
	$('select[event="loadRegion"]').mouseover(function(){
		if ($(this).find("option").size() > 1) {
			return;
		}
		var url 	= $(this).prevAll("input[event='region_id']").attr("uri");
		var type 	= 0;
		var id 		= $(this).val();
		if($(this).prev().attr("event") == "loadRegion" && $(this).val() == 0){
			id = $(this).prev().val();
			if(id == 0) return;
			type = 1;
		}
		var _self = this;
		$.getJSON(url,{id:id,type:type},
		 function(data){
				  if(data.num!=0){
					  $(_self).children().remove();
					  $.each(data,function(idx,item){
						  $(_self).append("<option value="+item.region_id+">"+item.region_name+"</option>");
					  });
					  if (id != 0) {
						$(_self).find("option[value='"+id+"']").attr("selected", true);
					  }
				  }
			  }
		  );
		  
	
	});

})


/*列表数据鼠标移动变色事件 和点击变色事件*/
function table_select(){
	$("#list-table tr").hover(
	function(){$(this).addClass("trover");},
	function(){$(this).removeClass("trover");}
	).click(function(){
	$("#list-table .trselect").removeClass("trselect");
	$(this).addClass("trselect");
	});
}
/*菜单栏 全部展开和收起*/
function allClicked(obj){
	if($(obj).attr('src').indexOf('menu_minus.gif') > 0){
		$("#list-table tbody tr.2_explode,tr.3_explode").hide();
		var img_url = obj.src.replace('minus.gif','plus.gif');
		$(obj).attr('src',img_url);
		$("#list-table tbody tr img.menu_img").attr('src',img_url)
	}else{
		$("#list-table tbody tr.2_explode,tr.3_explode").show();
		var img_url = obj.src.replace('plus.gif','minus.gif');
		$(obj).attr('src',img_url);
		$("#list-table tbody tr img.menu_img").attr('src',img_url);
	}
}

/**
 * 折叠分类列表
 */
function rowClicked(obj)
{
	var imgPlus = new Image();
	imgPlus.src = __img__ + "/icon/menu_plus.gif";
	// 当前图像
	img = obj;
	// 取得上二级tr>td>img对象
	obj = obj.parentNode.parentNode;
	// 整个分类列表表格
	var tbl = document.getElementById("list-table");
	// 当前分类级别
	var lvl = parseInt(obj.className);
	// 是否找到元素
	var fnd = false;
	var sub_display = img.src.indexOf('menu_minus.gif') > 0 ? 'none' : (Browser.isIE) ? 'block' : 'table-row' ;
	// 遍历所有的分类
	for (i = 0; i < tbl.rows.length; i++)
	{
	    var row = tbl.rows[i];
	    if (row == obj)
	    {
	        // 找到当前行
	        fnd = true;
	        //document.getElementById('result').innerHTML += 'Find row at ' + i +"<br/>";
	    }
	    else
	    {
	        if (fnd == true)
	        {
	            var cur = parseInt(row.className);
	            var icon = 'icon_' + row.id;
	            if (cur > lvl)
	            {
	                row.style.display = sub_display;
	                if (sub_display != 'none')
	                {
	                    var iconimg = document.getElementById(icon);
	                    iconimg.src = iconimg.src.replace('plus.gif', 'minus.gif');
	                }
	            }
	            else
	            {
	                fnd = false;
	                break;
	            }
	        }
	    }
  }

  for (i = 0; i < obj.cells[1].childNodes.length; i++)
  {
      var imgObj = obj.cells[1].childNodes[i];
      if (imgObj.tagName == "IMG" && imgObj.src != __img__ + '/icon/menu_arrow.gif')
      {
          imgObj.src = (imgObj.src == imgPlus.src) ? __img__+ '/icon/menu_minus.gif' : imgPlus.src;
      }
  }
}

/*
* ajax加载页
* obj为当前连接对象
* di为加载成功后，数据返回的窗口ID
* 方法<a href='javascript:void(0)' onclick="ajax_load(this, contet)" uri='网址'>
*/

function ajax_load(obj,id){
	//art.dialog.tips("正在加载中...",0.5);
	$.ajax({
		url:$(obj).attr("uri"),
		async:false,
		success: function(html){
			$("#"+id).html(html);
			},
		error:function(XMLHttpRequest, textStatus){                       
			alert("加载出错了,请稍后重试!");
		}
		});
}

/*
* ajax读取表单数据
* obj为当前连接对象
* di为加载成功后，数据返回的窗口ID
* 方法<form onsubmit=ajax_form_load(this,obj)"">
*/
 function ajax_form_load(_this,obj){
	var url 	= $(_this).attr("action");
	var method	= ($(_this).attr("method") ? $(_this).attr("method") : "get").toUpperCase();
	var _data 	= $(_this).serialize();
	var obj		= obj ? "#" + obj : ".aui_content";
	if(method == "GET"){
		url += ((url.indexOf("?") == -1) ? "?" : "&") + _data;
		_data = ""; 
	}
	//art.dialog.tips("正在加载中...",0.5);
	$.ajax({
		type:method,
		url :url,
		data:_data,
		success:function(html){
			$(obj).html(html);
		},
		error:function(XMLHttpRequest, textStatus){                       
			alert("加载出错了,请稍后重试!");
		}
	});
	return false;
}
//显示错误信息
function art_show_error(errmsg){
		var timer;
		art.dialog({
			icon: 'error',
			content: errmsg,
			lock:true,
			ok:true,
			init: function () {
				var that = this, i = 3;
				var fn = function () {
					that.title(i + '秒后关闭');
					!i && that.close();
					i --;
				};
				timer = setInterval(fn, 1000);
				fn();
			},
			close: function () {
				clearInterval(timer);
			}
		}).show();
}

//ajax加载
function art_ajax(title, url, config){
		art.dialog.load(url, {
			title: title,
			width: (config.width  != 'undefined' ? config.width : "auto"),
			height: (config.height != 'undefined' ? config.height : "auto"),
			ok:(config.ok == false ? false:true),
			okVal: (config.okVal != 'undefined' ? config.okVal : "关闭"),
			padding: (config.padding != 'undefined' ? config.padding : 5),
			lock:(config.lock != 'undefined' ? config.lock:false),
		}, false);
}

/*****
* 审核资料
* @param sting id 标识打开的框架
* @param bool is_refresh 关闭窗口后是否刷新页面
* @param string title 弹出框的名字
* @param string url 请求的url
* @author Jeffreyzhu.cn@gmail.com
*****/
function dialog_open(id, is_refresh, title, url)
{
	art.dialog.open(url, {
		id: id,
		title: title,
		lock: 'true',
		window: 'top',
		width: 1200,
		height: 700,
		cancel: function(){
			if(is_refresh){
				window.location.reload();
			}
		},
		ok: false
		
	});
} 

