<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>抽奖</title>
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<link rel="stylesheet" type="text/css" href="/Public/css/base-style.css?1">
<script type="text/javascript" src="/Public/js/jquery-2.1.0.min.js"></script>
</head>
<body>
	<div id="awdiv" class="zz" style="display: none;"></div>
<div id="awdiv2" class="jl-tk" style="display: none;"> <img onclick="hidediv()" src="/Public/images/close.png" class="cjgz-c">
	<div class="jlalert">
		<h3>中奖记录</h3>
		<div id="awData" class="dra"></div>
	</div>
</div>
<div class="top"><img src="/Public/images/bgx.png" /></div>
<div class="top-title"><img src="/Public/images/top.png" /></div>
<div class="Rankingsall">
	<div class="rank-top">抽奖</div>
	<div class="line"></div>
</div>
<div class="draw" id="lottery">
	<ul>
		<?php if(is_array($data['awList'])): $i = 0; $__LIST__ = $data['awList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="item lottery-unit lottery-unit-<?php echo ($key); ?>">
				<div class="img">
					<?php if($vo["hurl"] != ''): ?><a href="<?php echo ($vo["hurl"]); ?>" target="_blank"><img style="padding-top: 25px;" src="<?php echo ($vo["thumb"]); ?>" alt="<?php echo ($vo["name"]); ?>"></a>
					<?php else: ?>
						<img src="<?php echo ($vo["thumb"]); ?>" onclick="showInfo()" alt="<?php echo ($vo["name"]); ?>"><?php endif; ?>
				</div>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>
<div class="loterycont">剩余抽奖机会：<span id="totalNum"><?php echo ($totalNum); ?></span><input type="hidden" id="hTotal" value="<?php echo ($totalNum); ?>" /></div>
<div class="lottery-bottom">
	<ul>
		<?php if($totalNum >= 1): ?><li class="doAward_1" onclick="doAward(1)" style="cursor: pointer;">抽奖1次</li>
			<?php else: ?>
			<li class="doAward_1" style="background: #C0C0C0;">抽奖1次</li><?php endif; ?>
		
		<?php if($totalNum >= 10): ?><li class="doAward_10" onclick="doAward(10)" style="cursor: pointer;">抽奖10次</li>
			<?php else: ?>
			<li class="doAward_10" style="background: #C0C0C0;">抽奖10次</li><?php endif; ?>

		<?php if($totalNum >= 100): ?><li class="doAward_100" onclick="doAward(100)" style="cursor: pointer;">抽奖100次</li>
			<?php else: ?>
			<li class="doAward_100" style="background: #C0C0C0;">抽奖100次</li><?php endif; ?>
	</ul>
</div>
<div class="loterycont-start" style="margin-top: 25px;">
	<!--<div class="rightlotery"><a href="/Home/Index/awardLog/uid/<?php echo ($uid); ?>/actid/<?php echo ($actid); ?>">我的中奖记录 >></a></div>-->
	<!-- <div class="rightlotery"><a href="#">继续投票>></a></div> -->
</div>
<div class="Rankingsall">
	<div class="rank-top">中奖名单</div>
	<div class="line"></div>
</div>
<div class="Rankings" style="height: 300px;">
	<marquee behavior="scroll" direction="down">
	<ul>
		<?php if(is_array($data["logs"])): $i = 0; $__LIST__ = $data["logs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lvo): $mod = ($i % 2 );++$i;?><li>
				<?php echo ($lvo["uname"]); ?> <?php echo ($lvo["aname"]); ?> <?php echo (date('m-d H:m:s',$lvo["addtime"])); ?>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	</marquee>
</div>
<div> </div>
<div class="footertop"></div>
<div class="allgrains1">
	<a href="<?php echo ($homeUrl); ?>" class="<?php echo ($homeClass); ?>"><img src="/Public/images/<?php echo ($homePng); ?>.png" /><p>抽奖</p></a>
	<a href="<?php echo ($myUrl); ?>" class="<?php echo ($myClass); ?>"><img src="/Public/images/<?php echo ($myPng); ?>.png" /><p>我的</p></a>
</div>

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

		// 弹出中奖弹窗
        $("#awdiv").show();
        $("#awdiv2").show();

		lottery.prize = -1;
		lottery.times = 0;
		click = false;
	} else {
		if (lottery.times < lottery.cycle) {
			lottery.speed -= 10;
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
var uid = "<?php echo ($uid); ?>";
var actid = "<?php echo ($actid); ?>";
var uniacid = "<?php echo ($uniacid); ?>";
var nickname = "<?php echo ($nickname); ?>";

// 抽奖
function doAward(num) {
    if (click) { //click控制一次抽奖过程中不能重复点击抽奖按钮，后面的点击不响应
        return false;
    } else {
        var hTotal = $("#hTotal").val();
        $.ajax({
            type:"POST",
            url:"/Home/Index/doAward",
            data:{uid:uid, actid:actid, num:num, uniacid:uniacid, nickname:nickname},
            dataType:"json",
            success:function(data) {
                if (data.code == 200) {
                    // 执行抽奖转盘旋转
                    lottery.init('lottery');
                    lottery.speed = 200;
                    roll(); //转圈过程不响应click事件，会将click置为false
                    setTimeout(function() {
						lottery.stop(data.rid);
						$("#awData").empty();
						$("#awData").append(data.data);
                        var totalNum = parseInt(hTotal);
						if (data.onceNum > 0) {
                            totalNum += parseInt(data.onceNum) - parseInt(num);
						} else {
                            totalNum -= parseInt(num);
						}
                        $("#totalNum").empty();
                        $("#totalNum").html(totalNum);
                        $("#hTotal").val(totalNum);

                        // 修改抽奖状态
						if (data.lastNum < 1) {
						    $('.doAward_1').attr('onclick', '');
						    $('.doAward_1').attr('style', 'background: #C0C0C0;');
                            $('.doAward_10').attr('onclick', '');
                            $('.doAward_10').attr('style', 'background: #C0C0C0;');
                            $('.doAward_100').attr('onclick', '');
                            $('.doAward_100').attr('style', 'background: #C0C0C0;');
						} else if (data.lastNum < 10 && data.lastNum >= 1) {
                            $('.doAward_10').attr('onclick', '');
                            $('.doAward_10').attr('style', 'background: #C0C0C0;');
                            $('.doAward_100').attr('onclick', '');
                            $('.doAward_100').attr('style', 'background: #C0C0C0;');
						} else {
                            $('.doAward_100').attr('onclick', '');
                            $('.doAward_100').attr('style', 'background: #C0C0C0;');
						}

					},5000);
                } else {
                    alert(data.msg);return;
				}
            }
        });

        click = true; //一次抽奖完成后，设置click为true，可继续抽奖
        return false;
    }
}

// 关闭中奖弹窗
function hidediv() {
    $("#awdiv").hide();
    $("#awdiv2").hide();
}

// 显示奖品弹窗信息
function showInfo() {
	alert('奖品信息');
}
</script>
</body>
</html>