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

//显示成功信息
function zy_ok(msg){
	top.layer.open({title: '成功提示',content: msg, icon:1});  
}
//报错信息
function zy_error(msg){
	top.layer.open({title: '错误提示',content: msg, icon:7});  
}

//弹窗默认的回调整，会执行子窗口的 api_back()
function win_callback(frame,index,that){
	if(typeof frame.api_back != 'function') return true;
	return frame.api_back(index);
}
function reload_me(){
	location.reload();
}
//重新加载子首页主要内容窗口
function reload_main_frame(){
	try{top.window.main.location.reload()}catch(e){
		console.log(e)
	}
}
//刷新当前页面
function reload(){
	location.reload();
}
//移除数据行
function remove_data_line(that,index){
	if($(that).data('parent')){
		$(that).parents($(that).data('parent')).remove();
	}else{
		$(that).parent().remove();
	}
}
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
	//获取地址位置 [百度]
	$("*[event='bdgps']").click(function(){
        var address	  = $('select[event="loadRegion"]:eq(1)').find("option:selected").text() + ' ' + $('select[event="loadRegion"]:eq(2)').find("option:selected").text() + " " + $("input[name='data[address]']").val();
		var url = rootpath + "map/getgps.html";
		var lng = $($(this).data("lng")).val();
		var lat = $($(this).data("lat")).val();
        url += (url.indexOf("?") != -1 ? "&":"?") + "address="+address + "&lng="+lng+"&lat="+lat;
		art_ajax("获取GPS数据",url,{width:600,height:500,padding:0,ok:false});
	});
		
	//选择字体
	$("*[event='iconfont']").click(function(){
		var a = $(this).data();
		var uri = a.uri || "/index.php/Public/icon.html";
		top.layer.open({type:2,title:a.title||'选择图标',content:uri,
			        	area:['730px','500px'],
			        	btn: ['确定', '取消'],
			        	maxmin:1,
			        	yes: function(index, layero){
			        			var iframeWin = top.window[layero.find('iframe')[0]['name']];
			        			var icon = iframeWin.dosumit();
			        			if(icon==""){
			        				top.layer.msg("请先选择图标");
			        			}else{
			        				$(a.input).val(icon);
			        				$(a.show).html(icon);
			        				top.layer.close(index);
			        			}
					  },btn2: function(index, layero){
					  	top.layer.close(index);
					  }
        		});
		
	});
	
	//弹出窗口
	$("*[event='window']").click(function(){
		var url = $(this).data("uri");
		var title = $(this).data("title") ? $(this).data("title") : $(this).attr("title");
		art_ajax(title?title:"详情",url,{padding:0,ok:true,lock:true});		
	});
	
	//弹出窗口2
	$("*[event='window2']").click(function(){
		var o = $(this);
		var a = $(o).data();
		var c = {type:2,title:a.title||a.btn||'详情',content:a.uri,
		area:[(a.width||600)+'px',(a.height||350)+'px'],maxmin:a.maxmin||false,success:function(e,i){
			if(a.auto){top.layer.iframeAuto(i)}
		}};
		if(a.btn){
			   if(a.btn == "关闭"){
				 	c.btn = [a.btn];
				 	c.yes = function(index, layero){
						  	top.layer.close(index);
						  }
				 }else{
				 	c.btn = [a.btn, '关闭'];
				 	c.yes = function(index, layero){
				        			var iframeWin = top.window[layero.find('iframe')[0]['name']];
				        			if(a.callback){
				        				eval(a.callback+"(iframeWin,index)");
				        			}else{
				        				if(win_callback(iframeWin, index, o)){
				        					top.layer.close(index);
				        				}
				        			}
						  };
					 c.btn2 = function(index, layero){
						  	top.layer.close(index);
						  }
				 }
		}
		top.layer.open(c);
	});

//更新值
	$(document).on("blur","input[event='update']",function(){
		var that = $(this);
		var data = $(that).data("data");
		var oval = $(that).data("val");
		var val	 = $(that).val();
		if(val == oval) return;
		if(data) data = eval("("+data+")");
		data.val = val;
		$.ajax({
			type:"post",
			url:$(that).data("uri"),
			data:data,
			success:function(res){
				if(res.status == 1){
					$(that).data("val", data.val);
				}else{
					$(that).val(oval);
				}
			}
		});
	})
	
	//页面跳转
	$(".jump_url").click(function(){
		var obj 		= $(this);
		url 			= obj.attr("url");
		window.location.href=url;
	})
	
	//显示大图
	$(document).on('click',"*[event='showbimg']",function(){
		var imgurl = $(this).data('uri') || $(this).attr("src") || $(this).attr("href") || "";
		if(imgurl){
			var title = $(this).attr("alt")?$(this).attr("alt"):$(this).attr("title")?$(this).attr("title"):"大图显示"; 
			top.layer.open({
				type:1,
				title: "大图显示",
				content: '<img src="'+imgurl+'">',
				offset: 'lt',
				maxmin:true,
			});
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
					alert('网络连接失败');
				}
			});
			
		});
	/*提示*/
	$(".tips").click(function(){
		var a = $(this).data();
		if(a.msg) layer.msg(a.msg)
	});
	
	$(document).on("click",".show_img",function(){
		var imgurl = $(this).prev("input").val();
		if(imgurl){
			top.layer.open({
				type:1,
				title: "图片预览",
				content: '<img src="'+imgurl+'">',
			});
		}	
	})
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
	/*确认操作*/
	$(document).on("click","*[event='confirm']",function(){
			var that = $(this);
			var a 	= $(that).data();
			top.layer.confirm(a.msg||"确定要操作吗？", {icon: a.icon||7, title:a.title||'系统提示'}, function(index){
			  if(a.uri){
			  	  if(a.ajax){
			  	  		$.ajax({
			  	  			type:"get",
			  	  			url:a.uri,
			  	  			async:false,
			  	  			success:function(res){
			  	  				top.layer.msg(res.info,{time:1000});
			  	  				if(res.status == 1 && a.callback) eval(a.callback+"(that,index)");
			  	  			},
			  	  			error:function(res){
			  	  				zy_error('网络请求失败');
			  	  			}
			  	  		});
			  	  }else{location.href=a.uri}
			  }else if(a.callback){eval("a.callback(that,index)");}
			  top.layer.close(index);
			});
	})
	
	//ajax删除操作
	$(document).on("click","*[event='remove2']",function(){
			var a = $(this).data(),that = $(this);
			var msg 	 	 = a.msg || "确定要删除吗?";	//提示内容
			var parent   = a.parent || false;	//移动父级
			var callback = a.callback || false;
			top.layer.confirm(a.msg||"确定要操作吗？", {icon: a.icon||7, title:a.title||'系统提示'}, function(index){
			  	top.layer.close(index);
			  	//无URL则不需要ajax请求
			    	if(!a.uri){
			    		if(parent) $(that).parents(parent).remove();
			    		if(callback) try{eval(callback)}catch(e){}
			    		return;
			    	}
			   var index = top.layer.load(2, {time: 10*1000});
			    	$.ajax({type: a.post ? "post":"get",
						url:a.uri,
						success:function(res){
							top.layer.close(index);
							if(res.status){
									zy_ok(res.info);
								    	if(parent){
										$(that).parents(parent).remove();
										if(callback) try{eval(callback)}catch(e){}
								    	}else{
										location.reload();
									}
							}else{
									zy_error(res.info);
							}
						}
			    });
			});
	});
	//ajax删除操作end
	
	/* 异步移除数据*/
	$("*[event='remove']").click(function(){
		var icon = $(this);
		var uri = ($(this).attr('uri')) ? $(this).attr('uri') : "" ;
		var data = $(this).attr("data");
		if(data && confirm('你确定要删除吗？\n\n删除后不可恢复！')) {
			data = eval("("+data+")");
	
			if(!data.act) data.act = 'remove_field';
	
			$.ajax({
				url :uri + "ajax_" + data.act,
				type:"POST",
				//async:false,
				data:data,
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
	
	//切换状态
	$(document).on("click","*[event='toggle2']",function(){
		var that = $(this);
		var data = $(that).data("data");
		var type = $(that).data("type");
		var val  = $(that).data("val");
		if(type == "img"){
			var vd = Array("/Public/images/icon/no.gif","/Public/images/icon/yes.gif","/Public/images/icon/loading.gif");
		}else{
			var vd = Array("否","是","<img src='/Public/images/icon/loading.gif'>");
		}
		if(data) data = eval("("+data+")");
		data.val = 1-val;
		if(type == 'img'){
			$(that).attr("src", vd[2]);
		}else{
			$(that).html(vd[2]);
		}
		$.ajax({
			type:"post",
			url:$(that).data("uri"),
			data:data,
			success:function(res){
				if(res.status == 1){
					$(that).data("val", data.val);
					if(type == 'img'){
						$(that).attr("src", vd[data.val]);
					}else{
						$(that).html(vd[data.val]);
					}
				}else{
					if(type == 'img'){
						$(that).attr("src", vd[val]);
					}else{
						$(that).html(vd[val]);
					}
				}
				if(res.url) location.href = res.url;
			}
		});
	})
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
	
	//规格设置
	if($("input[event='spec_chk']").size() > 0){
		if($("input[event='spec_chk']:checked").size() >=2){
			$("input[event='spec_chk']").not(":checked").attr("disabled",true);
		}
	}
	$("input[event='spec_chk']").click(function(){
		if($("input[event='spec_chk']:checked").size() >=2){
			$("input[event='spec_chk']").not(":checked").attr("disabled",true);
		}else{
			$("input[event='spec_chk']").not(":checked").attr("disabled",false);
		}
	})
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

function upload_ok(data){
			if(typeof(data) != "object") return;
			var html = "";
			switch(data.filetype){
				case "image":
					if(data.ext == "jpg" || data.ext == "jpeg" || data.ext == "png" || data.ext == "gif"){
						if(data.did){
							$("input[name='"+data.did+"']").val(data.uri);
						}else{
							$("#"+data.utype).val(data.uri);
						}
					}else{
						alert('无效的图片');
					}
					break;
				default:
					if(data.did){
						$("input[name='"+data.did+"']").val(data.uri);
					}else{
						$("#"+data.utype).val(data.uri);
					}
				break;
			}
		}