$(function(){
//	初始化
	var wHeight = $(window).height();
	$("#content-mark").height(wHeight-$("#header").height());
	$("#content-mark-fr").height(wHeight-85);
	$("#content-mark-fl").height(wHeight-85);
	$("#content-address").height(wHeight-100);
	var $dqNote;
	init();
//	加保留两位
	$("#food .add").click(function(){
		$(this).prev().show();
		var $number = $(this).siblings(".none").children(".number");
		var number = parseInt($number.html());
		
		$number.html(++number);
		$("#bottom .number").html(parseInt($("#bottom .number").html())+1);
		$("#bottom .rental").html(
			Math.round(
				(
					parseFloat($("#bottom .rental").html())+
					parseFloat($(this).parent().siblings(".pic").find(".money").html())
				)*100)/100);
		 
	})
	
//	减
	$("#food .subtract").click(function(){
		var $number = $(this).siblings(".number");
		var number = parseInt($number.html());
		number -=1;
		if(number<=0){
			$number.html(number);
			$(this).parent().hide()
			
		}else{
			$number.html(number);
		}
		$("#bottom .number").html(parseInt($("#bottom .number").html())-1);
		$("#bottom .rental").html(
			Math.round(
				(
					parseFloat($("#bottom .rental").html())-
					parseFloat($(this).parent().parent().siblings(".pic").find(".money").html())
				)*100
			)/100
		);
	})
//	详细加
	$("#cover .shop-number .add").click(function(){
		var $number = $(this).parent().find(".number");
		var number = parseInt($(this).parent().find(".number").html())+1;
		$(this).parent().find(".number").html(number);
		
	})
//	详细减
	$("#cover .shop-number .subtract").click(function(){
		var number = parseInt($(this).siblings(".number").html())-1;
		if(number>=0){
			$(this).siblings(".number").html(number);
		}
	})
//	加入购物车
	$("#cover .shop-number .addshop").click(function(){
		$("#bottom .rental").html(
			Math.round(
				(
					parseFloat($("#bottom .rental").html())+
					(parseFloat($("#cover .pic .money").html())*(parseInt($(this).parents("#shop-number").find(".number").html())))
				)*100
			)/100
		);
		var $dq = $dqNote.parent().find(".number")
		$dq.html(parseInt($("#cover #shop-number .number").html()));
		$("#cover").hide();
		init();
		return false;
	});
	$(".cover").click(function(){
		$dqNote.parent().find(".number").html($("#cover #shop-number .number").html())
		$(this).hide()
		return false;
	})
	$("#cut").click(function(){
		return false;
	})
//	变色
	$(".content-mark-fl li a").click(function(){
		$("#header").find(".title").html($(this).html());
		$(".content-mark-fl li").removeClass("hover");
		$(this).parents("li").addClass("hover");
	})
	
	$("#standard>.number>ul>li").click(function(){
		$("#standard>.number>ul>li").removeClass("hover");
		$(this).addClass("hover");
	})
	
	
//	初始化
	function init(){
		var $fodLi = $("#food ul li")
		var sum = 0;
		var sumPic = 0;
		for(var i =0; i<$fodLi.length;i++){
			var number = parseInt($($($fodLi)[i]).find(".number").html())
			var pic =  parseFloat($($($fodLi)[i]).find(".money").html());
			sumPic += pic*number;
			if(number>0){		
				sum += number; 
				$($($fodLi)[i]).find(".number").parent().show();
			}else{
				$($($fodLi)[i]).find(".number").parent().hide();
			}
			
			$("#bottom .number").html(sum);
			$("#bottom .rental").html(Math.round(sumPic*100)/100);
		}
	}

//	点击进入详情
	$("#food>ul>li a").click(function(){
		$("#cover").show(0);
		$dqNote = $(this);
		$("#cover #shop-number .number").html($(this).parent().find(".number").html());
		$("#cover  .money").html($(this).parent().find(".money").html());
		$("#cover  .title").html($(this).parent().find(".title").html());
		$("#cover  .number>ul>li").removeClass("hover")
		$("#cover  .number>ul>li").eq(0).addClass("hover");
		$("#cover .pic .xl").html($dqNote.parent().find(".sales .xl").html());
	})
})

window.onload=function(){
	var foodHeight = $("#foodImg").width();
	$("#foodImg").css({"height":foodHeight,"line-height":foodHeight})
	
}
