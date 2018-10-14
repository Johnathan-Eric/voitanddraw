$(function(){
	var pre_url = "/index.php/Activity/";
	//点赞
	$("#game-digg").click(function(){
		var code = $(this).data("code");
		var uid  = $(this).data("uid");
		var that = $(this);
		if($(that).hasClass('digg-success')){
			//G.ui.dialog.alert({tip:$(that).html(),btn:{val:"确定",handler:function(t){}}});	
			return;
		}
		$.ajax({
			type:'POST',
			url:pre_url+code,
			data:{act:'digg',uid:uid},
			success:function(res){
				if(res.status){
					$(that).html(res.msg);
					$(that).addClass("digg-success");
					$.ui.dialog.alert({tip:res.msg,
							icon:{
							url: '/Public/images/i-right.png',
							width: '35px',
							height: '36px'
							},btn:{val:"确定",handler:function(t){
								load_more(moreURL,{p:1});	
							}}
						});
				}else{
					G.ui.dialog.alert({tip:res.msg,btn:{val:"确定",handler:function(t){}}});	
				}
		}})
		
	})
	
	//加入游戏
	$(".game-join").click(function(){
		var code = $(this).data("code");
		var that = $(this);
		if($(that).data("uri")){
			location.href = $(that).data("uri");
			return;	
		}
		$.ajax({
			type:'POST',
			url:pre_url+code,
			data:{act:'join'},
			success:function(res){
				if(res.status == 1){
					$(that).html("参与活动成功");
					$(that).attr("data-uri",res.url).addClass('join_success');
					$.ui.dialog.confirm({
						text: res.msg,
						cancel: {
							val: '关闭',
							handler: function(e){
								
							}
						},
						icon:{
							url: '/Public/images/i-right.png',
							width: '35px',
							height: '36px'
							},
						confirm: {
							val: '去分享',
							handler: function(e){
								location.href = res.url;
							}
						}
					})	
				}else if(res.status == 2){
					$.ui.dialog.confirm({
						text: res.msg,
						cancel: {
							val: '关闭',
							handler: function(e){
								
							}
						},
						confirm: {
							val: '去关注',
							handler: function(e){
								location.href = res.url;
							}
						}
					})
				}
				else{
					G.ui.dialog.alert({tip:res.msg,btn:{val:"确定",handler:function(t){}}});		
				}
		}})
		
	});
	
	//显示分享
	$(".share").click(function(){
		$("#m-bg").show();
		$(".howShare").show();
	});
	
	$(".claseShare").click(function(){
		$(".howShare").hide();
		$("#m-bg").hide();
	});
	
})