var site_url = "http://"+document.domain;
var wxshare = {};
var query_arg = {};
var zy = zy || {};

zy.tips=function(msg,time){
    var zytips=$(".zytips");
    if(zytips.length==0){
      zytips=$('<div class="zytips"></div>').appendTo("body");
    }
    zytips.html("<div>"+msg+"</div>");
    zytips.fadeIn();
    time = time || 2;
    var tt = window.setTimeout(function(){
	    $(".zytips").fadeOut('fast');
	 },time*1000);
     //window.setTimeout(function(){
        //$(document).one("touchstart",function(e){
	    //    if(e.target.className != "order_submit"){
        			//$(".zytips").fadeOut("fast");
        			//clearTimeout(tt);
	     //   }
     	//});
     // },300);
}


zy.msg = function(msg,icon){
	return $.tips({
	        content:msg,
	        stayTime:1500,
	        type:icon||'info'
	   });
	};
zy.alert = function(msg,btn){
	return $.dialog({
		content:msg,
		button:[btn || '确定'],
	})
}
zy.loading = function(msg){
	var ld = $.loading({content:msg||'正在加载中...'});
		setTimeout(function(){
			ld.loading('hide');
		},5000);
	return ld;
}
var get_val = function(name,type){
		type = type ? type : "input";
		return $(type+"[name='"+name+"']").val();
	}
//图片延时加载
var zooyoo_load=function(a,d){var b=function(f){if(typeof(f.style)!="undefined"&&((typeof(f.style.display)!="undefined"&&f.style.display=="none")||(typeof(f.style.visibility)!="undefined"&&f.style.visibility=="hidden"))){return false}else{if(typeof(f.parentNode)!="undefined"&&f.parentNode!=null&&f.parentNode!=f){return b(f.parentNode)}}return true};var c=$("img[_src]").not(".zz").filter(function(f){return b(this)});this.checkImg=function(i){var h=$(window).height();var g=$(window).scrollTop();c.each(function(){var k=this;var m=k.height;var j=$(k).offset().top;if(m<=1){m=d;j=j-m}a=(a<=m)?m:a;var l=parseInt(m/10);if((((j+a)-i)>0&&(j-i)<h)||((j+m>=g)&&(g+h>=j+l))){if(k.getAttribute("_src")){k.src=k.getAttribute("_src");k.removeAttribute("_src");$(k).fadeIn();}}});var f=$("img[_src]");if(f.length==0){window.onresize=null;window.onscroll=null}};this.init=function(){this.checkImg(document.documentElement.scrollTop)};this.loadDefImg=function(f){for(var g=0;g<c.length;g++){var h=c[g];h.src=f}}};
//记录分享日志
var zy_share_log=function(share_type,share_page,share_data){
	$.ajax({type:'POST',
			url:'/index.php/Ajax/sharelog',
			data:{type:share_type,action:share_page,data:share_data}
		});
	return;
}
//获取当前url
var get_now_url=function(skip,puid){
	url = site_url + window.location.pathname;
	var skips = ",code,state,isappinstalled,from," + (skip ? skip+',':'');
	var schurl = window.location.search;
	var qeury="";
	if(puid>0) qeury="puid="+puid;
	if(schurl){
	    schurl = schurl.substr(1).split("&");
		for(var i=0;i<schurl.length;i++){
		   t = schurl[i].split("=");
		   if(skips.indexOf(','+t[0].toLowerCase()+',')==-1 && t[1]){
			   qeury += (qeury == "" ? "":"&")+t[0]+"="+t[1];
			}
		}	
	}
	if(qeury !="") qeury = "?" + qeury;
	return url+qeury;
 }
var zyscroll=function(st, time) {
	var sf = parseInt($(window).scrollTop()),i=0,runEvery=5;
	st = parseInt(st);
	time /= runEvery;
	var interval=setInterval(function(){
		i++;$(window).scrollTop((st-sf)/time*i+sf);
		if(i>=time){clearInterval(interval);}
	}, runEvery);
}
//显示团购结束
var show_group_end = function(obj){
	$(obj).html("已结束");
}
//剩余时间倒计时
var show_group_time = 	function (time,obj,period,fun){
		if(time < 1 ){
			if(typeof fun == 'function'){
				fun.apply(obj);
			}else{
				show_group_end(obj);
			}
			return;
		}
		var ss = 0, ms = 1;
		if(period == 100){
			var ss = time % 10;
			ms = 10;
		}
		var second = Math.floor(time / ms % 60);
		var minute = Math.floor(time / 60 /ms % 60);
		var hour   = Math.floor(time / 3600/ms);
		str= '<em>'+hour+'</em>:<em>'+(minute<10?"0":"")+minute+'</em>:<em class="ss">'+(second < 10 ? "0":"")+ second + (ms == 10 ? ('.<i>'+ss+'</i>'):'')+'</em>';
		$(obj).html(str);
		if($(obj).size() >0 ){
			setTimeout(function(){
				show_group_time(--time,obj,period,fun)
			},period);
		}
	}


$(function(){
	var a = function (e) { e.preventDefault();};
	var zy_load = new zooyoo_load(200, 200);
	zy_load.init();
	window.onscroll = function(){zy_load.init();}
	//window.onresize = function(){zy_load.init();}
	$("a.back,a.getback").click(function(){
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
	$(document).on("click",".ui-icon-close",function(){
		$(this).parent().find('input,textarea').val("");
	})
	$(document).on("click",'.buliding',function(){
		var msg = $(this).data('msg');
		$.dialog({
			title:'温馨提示',
			content: msg || 'Sorry,此功能正在建议中!',
		})
	})
	//左侧分类展开JS	
	$("#subfield").click(function(){
		if($("#m-bg").is(":hidden")){
			$("#m-bg").show();
			$(".nav-list").removeClass("slideOut").addClass("slidIn");
			document.addEventListener('touchmove',a);
		};
	});
	//分类层级展示
	$("#scroller ul li").click(function(){
		if($(this).find("i").hasClass("up")){
			$(this).find("i").removeClass("up");
			$(this).find("div.sub").fadeOut();
		}else{
			$(this).find("i").addClass("up");
			$(this).find("div.sub").fadeIn();
			}
	});
	/*商品按钮*/
	$(".sousuo").click(function(){goods_search()})
	$("#keyword").keydown(function(){if(event.keyCode == 13)goods_search()})
	/*商品搜索*/
	function goods_search(){
		var keyword = $.trim($("#keyword").val());
		if(keyword == "") return;
		location.href="/search.html?keyword="+decodeURIComponent(keyword);
	}
	function navClose(){
		if($(".nav-list").hasClass("slidIn")){
			$(".nav-list").removeClass("slidIn").addClass("slideOut");
			$("#m-bg").hide();	
			document.removeEventListener('touchmove',a);
		};
	};
	$("#m-bg").on("touchend",function(){
		 navClose();
		 event.preventDefault();
	});
	$("#navClose").click(function(){ navClose() });
	$(window).scroll(function(){
		if($(window).scrollTop()> 150){
			$(".top-btn").show();
		}else{
			$(".top-btn").hide();
		}
	});
	$(".top-btn").find("a").click(function(){
		zyscroll('0', 100);
	});
	//搜索js
	$("#search").children("a").click(function(){
		var sText=$(".search-text");
		if(sText.is(":hidden") == true){
			sText.show();
			$(this).children("i").css("color","#fcd605");
		}else{
			sText.hide();
			$(this).children("i").css("color","#fff")
		};
	});
	//用户中心订阅状态
	$("#cfg_msg").click(function(){
		location.href = $(this).data("uri");
	});
	$(".my_subscribe span").click(function(){
		$(this).toggleClass("on");
		var cfg_val = $(this).hasClass("on") ? 1 : 0;
		var cfg_name = $(this).data("name");
		$.ajax({type:'POST',url:'/index.php/Member/subscribe',data:{cfg_name:cfg_name,status:cfg_val},success:function(res){}})	
	});
	//头部固定
	var topHead=$(".layout").offset()?$(".layout").offset().top:0;
    $(window).scroll(function(){
    		var ww = $(window).width()*0.618;
		if($(".top-fix").size() > 0){
			if($(window).scrollTop() < ww){
				$(".top-fix").addClass("top-fix-on");
			}else{
				$(".top-fix").removeClass("top-fix-on");
			};
		};
		if($("body").find(".start-fix").size() > 0){
			if($(window).scrollTop() < ww){
				$(".start-fix").addClass("start-fix-on");
			}else{
				$(".start-fix").removeClass("start-fix-on");
			};
		};
	});
	/*列表排列切换*/
	$(".showtype").click(function(){
		if($(".list-item").hasClass("list-body")){
			$(".list-item").removeClass("list-body");
		}else{
			$(".list-item").addClass("list-body");
		}
	});	
	$(document).on("click",".ui-icon-close",function(){
		$(this).parent().find('input,textarea').val("");
	})
	$(document).on('click','.show_detail',function(){
		title = $(this).data("title") || "";
		$.dialog({title:title,content:$(this).html()})
	})
//显示微信分享
	$(document).on("click",".wx_share",function(){
		if($('.weixin_share').size()==0){
			$('body').append('<section class="show-bg weixin_share"><img src="/Public/images/fenxiang_01.png"/></section>');
			$('.weixin_share').click(function(){$(this).hide()});
		}
		$('.weixin_share').show();
	});
	//加载子页面
	$(document).on("click",".ajax_page",function(){
		var uri = $(this).data('uri');
		if(!uri) return;
		if($('#ajax_container').size()==0){
			$('body').append('<div id="ajax_container" class="self-window"></div>');
		}
		var ld = $.loading({content:'加载中...'});
		data = $(this).data();
		delete data.uri;
		$.ajax({
			type:"get",
			url:uri,
			data:data,
			success:function(html){
				ld.loading('hide');
				$("#ajax_container").html(html).addClass("on");
				location.href = "#ajax_page";
			},
			error:function(){
				ld.loading('hide');
				$.tips({content:'页面加载失败'});
			}
		});
	});
	
	$(document).on("click","#ajax_container .back",function(){
		$("#ajax_container").removeClass("on");
		location.href = "#";
	})
	$(document).on("click",".go2uri",function(){
		var uri  = $(this).data("uri");
		if(uri) location.href = uri;
	})
});
window.onhashchange =function(){
	if(location.href.indexOf("#ajax_page")==-1){
		$("#ajax_container,.win-show").removeClass("on");
	}
}