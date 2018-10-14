function selectall(name,chkbox) {
	check_box = chkbox || "#check_box";
	if ($(check_box).attr("checked")) {
		$("input[name='"+name+"']").each(function() {
			this.checked=true;
		});
	} else {
		$("input[name='"+name+"']").each(function() {
			this.checked=false;
		});
	}
}

function selectall2() {
	if ($("#check_box").attr("checked")) {
		$(":checkbox").each(function(){
			this.checked = true;
		})
	}else{
		$(":checkbox").each(function(){
			this.checked = false;
		})
	}
}



//TAB切换
function Tabs(id,title,content,box,on,action){
	if(action){
		  $(id+' '+title).click(function(){
			  $(this).addClass(on).siblings().removeClass(on);
			  $(content+" > "+box).eq($(id+' '+title).index(this)).show().siblings().hide();
		  });
	  }else{
		  $(id+' '+title).mouseover(function(){
			  $(this).addClass(on).siblings().removeClass(on);
			  $(content+" > "+box).eq($(id+' '+title).index(this)).show().siblings().hide();
		  });
	  }
}

function openwin(id,url,title,width,height,lock,yesdo,topurl){ 
		art.dialog.open(url, {
		id:id,
		title: title,
		lock:  lock,
		width: width,
		height: height,
		cancel: true,
		ok: function(){
			var iframeWin = this.iframe.contentWindow;
    		var topWin = art.dialog.top;
				if(yesdo || topurl){
					if(yesdo){
					    yesdo.call(this,iframeWin, topWin); 
					}else{
						art.dialog.close();
					    topWin.location.href=topurl;
					}
				}else{
					var form = iframeWin.document.getElementById('dosubmit');form.click();
				}
				return false;
			}
		});
}


function resetVerifyCode(){
	var timenow = new Date().getTime();
	document.getElementById('verifyImage').src='./index.php?g=Home&m=Index&a=verify#'+timenow;
}

function showpicbox(url){
	art.dialog({
		padding: 2,
		title: 'Image',
		content: '<img src="'+url+'" />',
		lock: true
	});
}
$(function(){
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
})