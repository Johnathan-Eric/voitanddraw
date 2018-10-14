<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
if (!check_ims_version()) {
	message('系统版本太低，请升级系统到1.0以上版本', '', 'error');
}

load()->model('mc');
load()->model('module');
mload()->classs('customservice');
$ops = array('display', 'delete', 'add_service');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$storeid = intval($_W['wn_storex']['store_info']['id']);
$service_api = new customservice();
$kflist = $service_api->GetKflist();
if (!empty($kflist['kf_list']) && is_array($kflist['kf_list'])) {
	foreach ($kflist['kf_list'] as $key => $value) {
		$service_list[$value['kf_id']] = $value;
	}
}

$storex_kflist = pdo_getall('storex_customservice', array(), array(), 'kf_id');
if (!empty($storex_kflist) && is_array($storex_kflist)) {
	foreach ($storex_kflist as $key => $value) {
		unset($service_list[$key]);
	}
}
if ($op == 'display') {
	$sql = "";
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = pdo_getall('storex_customservice', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid), array(), '', 'id DESC', ($pindex - 1) * $psize . ',' . $psize);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_customservice') . " WHERE `uniacid` = :uniacid AND `storeid` = :storeid $sql", array(':uniacid' => $_W['uniacid'], ':storeid' => $storeid));
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete('storex_customservice', array('id' => $id));
	message('删除成功', referer(), 'success');
}
if ($op == 'add_service') {
	$kf_id = intval($_GPC['kf_id']);
	if (empty($service_list[$kf_id])) {
		message(error(-1, '该客服不存在'), '', 'ajax');
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'storeid' => $storeid,
		'kf_account' => $service_list[$kf_id]['kf_account'],
		'kf_nick' => $service_list[$kf_id]['kf_nick'],
		'kf_headimgurl' => $service_list[$kf_id]['kf_headimgurl'],
		'kf_wx' => $service_list[$kf_id]['kf_wx'],
		'kf_id' => $service_list[$kf_id]['kf_id'],
	);
	pdo_insert('storex_customservice', $data);
	message(error(-1, $data), '', 'ajax');
}

include $this->template('store/shop_service');