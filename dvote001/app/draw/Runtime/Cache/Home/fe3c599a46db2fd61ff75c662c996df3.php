<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>抽奖</title>
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<link rel="stylesheet" type="text/css" href="/app/draw/Public/css/base-style.css">
</head>
<body>
	<div class="top"><img src="/app/draw/Public/images/bgx.png" /></div>
<div class="top-title"><img src="/app/draw/Public/images/top.png" /></div>
<div class="Rankingsall">
	<div class="rank-top">抽奖</div>
	<div class="line"></div>
</div>
<div class="draw" id="lottery">
	<ul>
		<?php if(is_array($data['awList'])): $i = 0; $__LIST__ = $data['awList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="item lottery-unit lottery-unit-<?php echo ($key); ?>">
				<div class="img"> <img src="<?php echo ($vo["thumb"]); ?>" alt="<?php echo ($vo["name"]); ?>"> </div>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php $__FOR_START_1370893296__=$data['awTotal'];$__FOR_END_1370893296__=9;for($i=$__FOR_START_1370893296__;$i < $__FOR_END_1370893296__;$i+=1){ ?><li class="item lottery-unit lottery-unit-<?php echo ($i); ?>">
				<div class="img"> <img src="/app/draw/Public/images/thanks.jpeg" alt="谢谢参与"> </div>
			</li><?php } ?>
	</ul>
</div>
<div class="loterycont">剩余抽奖机会：0</div>
<div class="lottery-bottom">
	<ul>
		<li>抽奖1次</li>
		<li>抽奖10次</li>
		<li>抽奖100次</li>
	</ul>
</div>
<div class="loterycont-start">
	<div class="leftlotery"><a href="#">我的中奖记录>></a></div>
	<div class="rightlotery"><a href="#">继续投票>></a></div>
</div>
<div class="Rankingsall">
	<div class="rank-top">中奖名单</div>
	<div class="line"></div>
</div>
<div class="Rankings">
	<ul>
		<li>J** 谢谢参与×1 09-03 17:50</li>
		<li>o** 谢谢参与×1 09-03 17:51</li>
		<li>2** 谢谢参与×1 09-03 17:51</li>
		<li>i** 谢谢参与×1 09-03 17:51</li>
		<li>e** 谢谢参与×1 09-03 17:51</li>
		<li>a** 谢谢参与×1 09-03 17:51</li>
		<li>z** 谢谢参与×1 09-03 17:51</li>
		<li>d** 谢谢参与×1 09-03 17:51</li>
		<li>O** 谢谢参与×1 09-03 17:50</li>
		<li>J** 谢谢参与×1 09-03 17:50</li>
	</ul>
</div>
<div> </div>

<script type="text/javascript" src="/app/draw/Public/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
var lottery = {
	index: -1,    //当前转动到哪个位置，起点位置
	count: 0,     //总共有多少个位置
	timer: 0,     //setTimeout的ID，用clearTimeout清除
	speed: 20,    //初始转动速度
	times: 0,     //转动次数
	cycle: 50,    //转动基本次数：即至少需要转动多少次再进入抽奖环节
	prize: -1,    //中奖位置
	init: function(id) {
		if ($('#' + id).find('.lottery-unit').length > 0) {
			$lottery = $('#' + id);
			$units = $lottery.find('.lottery-unit');
			this.obj = $lottery;
			this.count = $units.length;
			$lottery.find('.lottery-unit.lottery-unit-' + this.index).addClass('active');
		};
	},
	roll: function() {
		var index = this.index;
		var count = this.count;
		var lottery = this.obj;
		$(lottery).find('.lottery-unit.lottery-unit-' + index).removeClass('active');
		index += 1;
		if (index > count - 1) {
			index = 0;
		};
		$(lottery).find('.lottery-unit.lottery-unit-' + index).addClass('active');
		this.index = index;
		return false;
	},
	stop: function(index) {
		this.prize = index;
		return false;
	}
};

function roll() {
	lottery.times += 1;
	lottery.roll(); //转动过程调用的是lottery的roll方法，这里是第一次调用初始化
	if (lottery.times > lottery.cycle + 10 && lottery.prize == lottery.index) {
		clearTimeout(lottery.timer);
		lottery.prize = -1;
		lottery.times = 0;
		click = false;
	} else {
		if (lottery.times < lottery.cycle) {
			lottery.speed -= 10;
		} else if (lottery.times == lottery.cycle) {
			var index = Math.random() * (lottery.count) | 0; //静态演示，随机产生一个奖品序号，实际需请求接口产生
			lottery.prize = index;		
		} else {
			if (lottery.times > lottery.cycle + 10 && ((lottery.prize == 0 && lottery.index == 7) || lottery.prize == lottery.index + 1)) {
				lottery.speed += 110;
			} else {
				lottery.speed += 20;
			}
		}
		if (lottery.speed < 40) {
			lottery.speed = 40;
		};
		lottery.timer = setTimeout(roll, lottery.speed); //循环调用
	}
	return false;
}

var click = false;
window.onload = function(){
	lottery.init('lottery');
	$('.lottery-bottom ul li').click(function() {
		if (click) { //click控制一次抽奖过程中不能重复点击抽奖按钮，后面的点击不响应
			return false;
		} else {
			lottery.speed = 200;
			roll(); //转圈过程不响应click事件，会将click置为false
			click = true; //一次抽奖完成后，设置click为true，可继续抽奖
			return false;
		}
	});
};
</script>
</body>
</html>