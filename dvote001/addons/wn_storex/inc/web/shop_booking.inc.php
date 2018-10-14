<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$ops = array('booking_set', 'booking_list', 'delete', 'deleteall','status');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'booking_set';

$store = $_W['wn_storex']['store_info'];
$storeid = $store['id'];
$booking_set = pdo_get('storex_booking_set', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));

if (!empty($booking_set)) {
	$booking_set['text'] = iunserializer($booking_set['text']);
	$booking_set['value'] = iunserializer($booking_set['value']);
	if (!empty($booking_set['value']) && is_array($booking_set['value'])) {
		foreach ($booking_set['value'] as &$value) {
			$value = urldecode($value);
		}
	}
}
$types = array(
	'text' => '文本',
	'date' => '日期',
	'time' => '时间',
	'number' => '数字',
	'radio' => '单选',
	'checkbox' => '多选'
);
if ($op == 'booking_set') {
	if (checksubmit('submit')) {
		if (!empty($_GPC['text'])) {
			foreach ($_GPC['text'] as $text) {
				if (isset($text['status']) && $text['status'] == 1 && empty($text['title'])) {
					itoast('启用后文本框必填', '', 'error');
				}
			}
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'storeid' => $storeid,
			'text' => iserializer($_GPC['text']),
			'value' => iserializer($_GPC['value']),
			'prompt' => trim($_GPC['prompt']),
		);
		if (!empty($booking_set)) {
			pdo_update('storex_booking_set', $data, array('id' => $booking_set['id']));
		} else {
			pdo_insert('storex_booking_set', $data);
		}
		itoast('预约设置成功！', $this->createWebUrl('shop_booking', array('op' => 'booking_set', 'storeid' => $storeid)), 'success');
	}
}

if ($op == 'booking_list') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$booking_list = pdo_getall('storex_booking_list', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid), array(), '', array('booking_time DESC'), array($pindex, $psize));
	$params = array(':uniacid' => $_W['uniacid'], ':storeid' => $storeid);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_booking_list') . " WHERE uniacid = :uniacid AND storeid = :storeid", $params);
	if (!empty($booking_list)) {
		foreach ($booking_list as &$booking) {
			$booking['text'] = iunserializer($booking['text']);
		}
		unset($booking);
	}
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete('storex_booking_list', array('id' => $id));
	itoast('删除成功', $this->createWebUrl('shop_booking', array('op' => 'booking_list', 'storeid' => $storeid)), 'success');
}

if ($op == 'deleteall') {
	$ids = intval($_GPC['idArr']);
	pdo_delete('storex_booking_list', array('id' => $ids));
	itoast('删除成功', $this->createWebUrl('shop_booking', array('op' => 'booking_list', 'storeid' => $storeid)), 'success');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('storex_booking_list', array('status' => $status), array('id' => $id));
	itoast('状态更新成功', $this->createWebUrl('shop_booking', array('op' => 'booking_list', 'storeid' => $storeid)), 'success');
}
include $this->template('store/shop_booking');