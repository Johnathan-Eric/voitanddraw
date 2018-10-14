<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
load()->model('module');
load()->model('permission');

$ops = array('profile');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'profile';

$actions_permit = check_user_permit(array('wn_storex_menu_storeprofile'));
$days = empty($_GPC['days']) ? 'today' : trim($_GPC['days']);

$day = array(
	'today' => array(
		'start' => strtotime(date('Y-m-d', TIMESTAMP)),
		'end' => strtotime(date('Y-m-d', TIMESTAMP)) + 86399,
		'day' => 1,	
	),
	'yesterday' => array(
		'start' => strtotime(date('Y-m-d', TIMESTAMP)) - 86400,
		'end' => strtotime(date('Y-m-d', TIMESTAMP)) - 1,
		'day' => -1,
	),
	'sevendays' =>  array(
		'start' => strtotime(date('Y-m-d', TIMESTAMP)) - 6 * 86400,
		'end' => strtotime(date('Y-m-d', TIMESTAMP)) + 86399,
		'day' => 7,
	),
	'month' => array(
		'start' => mktime(0,0,0,date('m'),1,date('Y')),
		'end' => mktime(23,59,59,date('m'),date('t'),date('Y')),
		'day' => date('t', strtotime(date('Y-m', TIMESTAMP))),
	),
);
if ($op == 'profile') {
	$order_confirm_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " WHERE weid = :uniacid AND status = :status", array(':uniacid' => $_W['uniacid'], ':status' => 0));
	$order_send_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " WHERE weid = :uniacid AND status = :status AND mode_distribute = :mode_distribute AND goods_status = :goods_status", array(':uniacid' => $_W['uniacid'], ':status' => 1, ':mode_distribute' => 2, ':goods_status' => 1));
	$order_checked_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " WHERE weid = :uniacid AND status = :status AND goods_status = :goods_status", array(':uniacid' => $_W['uniacid'], ':status' => 1, ':goods_status' => 4));
	$storeprofile = array();
	if (!empty($days) && isset($day[$days])) {
		for ($i = 0; $i < $day[$days]['day']; $i++) {
			$time = $i * 86400 + $day[$days]['start'];
			$key = date('Ymd', $time);
			$label[] = $key;
			$storeprofile['money'][$key] = 0;
			$storeprofile['number'][$key] = 0;
		}
		if ($day[$days]['day'] == -1) {
			$key = date('Ymd', TIMESTAMP - 86400);
			$label[] = $key;
			$storeprofile['money'][$key] = 0;
			$storeprofile['number'][$key] = 0;
		}
		$order_list = pdo_getall('storex_order', array('status' => 3, 'weid' => $_W['uniacid'], 'time >=' => $day[$days]['start'], 'time <' => $day[$days]['end']), array('time', 'sum_price'));
		$total_money = 0;
		$total_num = 0;
		if (!empty($order_list) && is_array($order_list)) {
			foreach ($order_list as $order) {
				$date = date('Ymd', $order['time']);
				$storeprofile['money'][$date] += $order['sum_price'];
				$storeprofile['number'][$date] ++;
				$total_money += $order['sum_price'];
				$total_num ++;
			}
		}
		$chart_data['label'] = $label;
		$chart_data['series_data']['money'] = array_values($storeprofile['money']);
		$chart_data['series_data']['number'] = array_values($storeprofile['number']);
		$avg_money = 0;
		if (!empty($total_num)) {
			$avg_money = sprintf("%.2f", $total_money / $total_num);
		}
	}
	include $this->template('storeprofile');
}