<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('display');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

mload()->model('plugin');
$store_info = $_W['wn_storex']['store_info'];
$storeid = intval($store_info['id']);
if ($op == 'display') {
	$plugin_list = plugin_list();
	if (empty($plugin_list['wn_storex_plugin_printer'])) {
//		message('插件未安装', '', 'error');
	}
	$printer_list = store_printers($storeid);

	$mode_info = pdo_get('storex_plugin_printer_mode', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));
	$mode_info['print_mode'] = !empty($mode_info['print_mode']) ? iunserializer($mode_info['print_mode']) : array();
	if (checksubmit()) {
		$mode = array(
			'defaultid' => intval($_GPC['defaultid']),
			'print_mode' => iserializer($_GPC['print_mode'])
		);
		$mode_info = pdo_get('storex_plugin_printer_mode', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid), array('id'));
		if (empty($mode_info)) {
			$mode['uniacid'] = $_W['uniacid'];
			$mode['storeid'] = $storeid;
			pdo_insert('storex_plugin_printer_mode', $mode);
		} else {
			pdo_update('storex_plugin_printer_mode', $mode, array('id' => $mode_info['id']));
		}
		itoast('编辑成功', referer(), 'success');
	}
}

include $this->template('store/shop_plugin_printer_setting');