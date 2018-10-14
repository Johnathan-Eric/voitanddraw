var cart_show = function() {
	$(".meal-choose a.on").each(function() {
		if($(".car_list li#m" + $(this).data('mid')).size() == 0) {
			var a = $(this).data();
			var html = '<li class="select" id="m' + a.mid + '"><span>' + a.name + '<br/><small>(套餐内容：' + a.ename + ')</small></span>\
					<span><em>' + a.price + '</em>积分</span>\
					<span><div class="num">\
							<a href="javascript:;" class="move">-</a>\
							<input type="text" name="mid[' + a.mid + ']" data-mid="' + a.mid + '" data-price="' + a.price + '" class="text" value="1" />\
							<a href="javascript:;" class="add">+</a>\
						</div></span></li>';
			$(".car_list").append(html);
		}
	});
	if($(".car_list").find('li').size() == 1) {
		$('.car_list li.no-data').show();
		$('a.btn').addClass('disabled');
	} else {
		$('.car_list li.no-data').hide();
		$('a.btn').removeClass('disabled');
	}
	cart_settle();
}
var cart_settle = function() {
	var amount = 0;
	$(".car_list input").each(function() {
		amount += $(this).data('price') * 1 * $(this).val();
	});
	$('.buy-info em').html(amount);
}
var show_date = function() {
	$('.choose_time').html($("input[name='date']").val() + ' ' + $("input[name='time']").val())
}
$(function() {
	$(document).on('tap', '.car_list .num a', function() {
		var that = $(this).parent().find('input');
		var num = $(that).val() * 1;
		if($(this).hasClass("add")) {
			num++;
		} else {
			num--;
		}
		if(num < 1) num = 1;
		$(that).val(num);
		$(that).parents('li').find("em").html($(that).data('price') * num);
		cart_settle();
	});

	$(".meal-choose").find("a").click(function() {
		if($(this).hasClass("on")) {
			$(this).removeClass("on");
			$(this).find("small").hide();
			$(".car_list li#m" + $(this).data('mid')).remove();
		} else {
			$(this).addClass("on");
			$(this).find("small").show();
			$(this).siblings("a").find("small").hide()
		}
		cart_show();
	});
	//日期选择
	$(document).on('tap', '.date-select li', function() {
		if($(this).hasClass('off')) return;
		$(this).addClass("on").siblings().removeClass('on');
	});
	$(document).on('tap', '.date-select-ok', function() {
		if($('.date-select li.on').size() == 0) {
			zy.msg('请先选择预约日期');
			return;
		}
		var sday = $('.date-select li.on').data('date');
		$(".ajax_date").data('date', sday);
		$("input[name='date']").val(sday);
		ld = zy.loading();
		$.ajax({
			type: "get",
			url: "{:U('choose_time')}",
			data: {
				sid: sid,
				tid: tid,
				date: sday,
				time: $("input[name='time']").val()
			},
			success: function(html) {
				ld.loading('hide');
				$("#ajax_container").html(html).addClass("on");
				location.href = "#ajax_page";
			},
			error: function() {
				ld.loading('hide');
				$.tips({
					content: '页面加载失败'
				});
			}
		});
		show_date();
	});
	//时间选择
	$(document).on('tap', '.time-select-ok', function() {
		if($('.time-select li.on').size() == 0) {
			zy.msg('请选择要预约时间');
			return;
		}
		var stime = $('.time-select li.on').data('time');
		$(".ajax_date").data('time', stime);
		$("input[name='time']").val(stime);
		$("input[name='date']").val($(this).data('date'));
		$(".ajax_date").data('date', $(this).data('date'));
		$("#ajax_container,.ajax_page").removeClass("on");
		show_date();
	});
})