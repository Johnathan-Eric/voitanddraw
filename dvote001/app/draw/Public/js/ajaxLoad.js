var page 	= 1,
order_by	="default",
order_way	="",
isLoading	=false,
hasMore		=true,
listId		="#goods_list",
moreId 		="#wx_loading",
loaderr		= 0;
moreURL  = document.location.href;
function load_more(url,data){
	query_arg=data;
	if(isLoading || !hasMore) return;
	isLoading = true;
	if(data.p==1){$(listId).html("")}
	$(moreId).show();
	$.ajax({url:url,dataType:"json",data:data,type:"POST",error: function(){
			isLoading = false;
			loaderr++;
			if(loaderr>3){
				var dia=$.dialog({
			        title:'哎呀，怎么了？',
			        content:'加载失败，我们重来一次？',
			        button:["确认","取消"]
			    }).on("dialog:action",function(e){
			        if(e.index == 0){
			        		loaderr=0;load_more(url,data);
			        }
			    }).on("dialog:hide",function(e){});
			}else{
				load_more(url,data);
			}
		},success: function(res){
		if(res.status){
			page++;
			loaderr=0;
			if(res.map.total == 0){
				$(listId).html(res.content);	
			}else{
				if(res.map.page == 1){
					$(listId).html(res.content);	
				}else{
					$(res.content).appendTo($(listId));	
				}
			}
			if(res.map.page>=res.map.totalpage){
				hasMore = false;
				}
			else{
				hasMore = true
			}
			$(moreId).hide();
			isLoading = false;
			var zy_load = new zooyoo_load(200, 200);
			zy_load.init()
			window.onscroll = function(){zy_load.init();}
			//window.onresize = function(){zy_load.init();}
		}
	}});
}
