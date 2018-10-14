<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('datamemory', 'datagain');
$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'datagain';

if (empty($_W['openid'])) {
	wmessage(error(-1, 'openid为空'), '', 'ajax');
}
$userdata = pdo_get('storex_wxapp_userdata', array('openid' => $_W['openid']));
if ($op == 'datamemory') {
	$key = trim($_GPC['key']);
	$data = $_GPC['data'];
	if (empty($key) || empty($data)) {
		wmessage(error(-1, '存储参数错误'), '', 'ajax');
	}
	if (!empty($userdata)) {
		$userdata['data'] = iunserializer($userdata['data']);
		if (!empty($userdata['data']) && is_array($userdata['data'])) {
			foreach ($userdata['data'] as $k => $v) {
				if ((TIMESTAMP - $v['time']) > 7200) {
					unset($userdata['data'][$k]);
				}
			}
		}
		$userdata['data'][$key] = array('time' => TIMESTAMP, 'data' => $data);
		pdo_update('storex_wxapp_userdata', array('data' => iserializer($userdata['data'])), array('id' => $userdata['id']));
	} else {
		$insert = array(
		    'openid' => $_W['openid'],
		);
		$wxappdata[$key] = array('time' => TIMESTAMP, 'data' => $data);
		$insert['data'] = iserializer($wxappdata);
		pdo_insert('storex_wxapp_userdata', $insert);
	}
	wmessage(error(0, '存储成功'), '', 'ajax');
}

if ($op == 'datagain') {
	$key = $_GPC['key'];
	if (!empty($userdata)) {
		$userdata['data'] = iunserializer($userdata['data']);
		if (!empty($userdata['data'][$key])) {
			$userdata['data'][$key]['data'] = htmlspecialchars_decode($userdata['data'][$key]['data']);
			$data = $userdata['data'][$key];
			unset($userdata['data'][$key]);
			$userdata['data'] = iserializer($userdata['data']);
			pdo_update('storex_wxapp_userdata', array('data' => $userdata['data']), array('id' => $userdata['id']));
			wmessage(error(0, $data), '', 'ajax');
		} else {
			wmessage(error(-1, '数据不存在'), '', 'ajax');
		}
	} else {
		wmessage(error(-1, '数据不存在'), '', 'ajax');
	}
}