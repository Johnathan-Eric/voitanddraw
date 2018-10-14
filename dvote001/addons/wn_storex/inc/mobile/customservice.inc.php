<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$ops = array('service_list', 'create', 'close');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

check_params();
load()->model('mc');
mload()->classs('customservice');
$storeid = intval($_GPC['storeid']);
$uid = mc_openid2uid($_W['openid']);
$service_api = new customservice();

if ($op == 'service_list') {
	$service_list = pdo_getall('storex_customservice', array('storeid' => $storeid, 'uniacid' => $_W['uniacid']), '', 'kf_id');
	if (!empty($service_list) && is_array($service_list)) {
		foreach ($service_list as &$val) {
			$val['status'] = 0;
		}
	}
	$onlinekflist = $service_api->GetOnlineKflist();
	if (!empty($onlinekflist['kf_online_list']) && is_array($onlinekflist['kf_online_list'])) {
		foreach ($onlinekflist['kf_online_list'] as $key => $value) {
			if (!empty($service_list[$value['kf_id']])) {
				$service_list[$value['kf_id']]['status'] = $value['status'];
				$service_list[$value['kf_id']]['accepted_case'] = $value['accepted_case'];
			}
		}
	}
	wmessage(error(0, array_values($service_list)), '', 'ajax');
}

if ($op == 'create') {
	$data = array(
		'kf_account' => trim($_GPC['kf_account']),
		'openid' => $_W['openid']
	);
	$result = $service_api->KfsessionCreate($data);
	if (is_error($result)) {
		wmessage($result, '', 'ajax');
	}
	wmessage(error(0, ''), '', 'ajax');
}

if ($op == 'close') {
	$data = array(
		'kf_account' => trim($_GPC['kf_account']),
		'openid' => $_W['openid']
	);
	$result = $service_api->KfsessionClose($data);
	if (is_error($result)) {
		wmessage($result, '', 'ajax');
	}
	wmessage(error(0, ''), '', 'ajax');
}