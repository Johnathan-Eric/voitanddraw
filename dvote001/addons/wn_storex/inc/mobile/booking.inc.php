
<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('booking_setinfo', 'booking', 'booking_list');

$op = in_array($_GPC['op'], $ops) ? trim($_GPC['op']) : 'booking_setinfo';
check_params();
$storeid = intval($_GPC['storeid']);
$booking_set = pdo_get('storex_booking_set', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));
if (empty($booking_set)) {
	wmessage(error(-1, '预约表单不存在！'), '', 'ajax');
}

$booking_set['text'] = iunserializer($booking_set['text']);
$booking_set['value'] = iunserializer($booking_set['value']);
if (!empty($booking_set['text']) && is_array($booking_set['text'])) {
	foreach ($booking_set['text'] as $key => &$value) {
		$value['value_list'] = $booking_set['value'][$key];
		$value['value_list'] = explode(',', $value['value_list']);
	}
}
if ($op == 'booking_setinfo') {
	wmessage(error(0, $booking_set), '', 'ajax');
}

if ($op == 'booking') {
    $texts = array();
	if (!empty($booking_set['text'])) {
		foreach ($booking_set['text'] as $text) {
			if (isset($text['write']) && $text['write'] == 1 && empty($_GPC['text'][$text['order']])) {
				wmessage(error(-1, '必填项不能为空'), '', 'ajax');
			}
			$text['value'] = $_GPC['text'][$text['order']];
			$texts[] = $text;
		}
	}
	$booking_data = array(
		'uniacid' => $_W['uniacid'],
		'storeid' => $storeid,
		'openid' => $_W['openid'],
		'text' => iserializer($texts),
		'booking_time' => TIMESTAMP,
		'status' => 0,
	);
	pdo_insert('storex_booking_list', $booking_data);
	if (pdo_insertid()) {
		wmessage(error(0, !empty($booking_set['prompt']) ? $booking_set['prompt'] : '成功提交预约'), '', 'ajax');
	} else {
		wmessage(error(-1, '预约失败'), '', 'ajax');
	}
	
}

if ($op == 'booking_list') {
	$booking_list = pdo_getall('storex_booking_list', array('openid' => $_W['openid'], 'storeid' => $storeid), array('id', 'storeid', 'text', 'booking_time', 'status'));
	if (!empty($booking_list)) {
		foreach ($booking_list as &$list) {
			$list['text'] = iunserializer($list['text']);
			$list['booking_time'] = date('Y-m-d H:i');
			if ($list['status'] == 0) {
				$list['status_cn'] = '预约提交';
			} elseif ($list['status'] == 1) {
				$list['status_cn'] = '预约确认';
			} elseif ($list['status'] == 2) {
				$list['status_cn'] = '预约已关闭';
			}
		}
		unset($list);
	}
	wmessage(error(0, $booking_list), '', 'ajax');
}