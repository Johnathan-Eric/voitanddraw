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
	if (empty($plugin_list['wn_storex_plugin_bargain'])) {
//		message('插件未安装', '', 'error');
	}
	$setting_info = pdo_get('storex_plugin_bargain_setting', array('storeid' => $storeid, 'uniacid' => $_W['uniacid']));
	$setting_info['thumbs'] = !empty($setting_info['thumbs']) ? iunserializer($setting_info['thumbs']) : array();
	$setting_info['rules'] = !empty($setting_info['rules']) ? $setting_info['rules'] : '<div><span style="color: #222;">1.砍价有效期</span></div><p><span style="color: #999;">砍价有效期以商家设置开始时间至结束时间为准。</span></p><div><span style="color: #222;">2.砍价成功</span></div><p><span style="color: #999;">活动有效期内，砍至底价下单购买，则砍价成功，商家进入发货流程。</span></p><div><span style="color: #222;">3.砍价失败</span></div><p><span style="color: #999;">活动有效期内，未砍至底价或库存为0，则为砍价失败。</span></p>';
	if (checksubmit()) {
		$setting = array(
			'thumbs' => iserializer($_GPC['thumbs']),
			'rules' => htmlspecialchars_decode($_GPC['rules'], ENT_QUOTES),
		);
		$setting_info = pdo_get('storex_plugin_bargain_setting', array('storeid' => $storeid, 'uniacid' => $_W['uniacid']));
		if (empty($setting_info['id'])) {
			$setting['uniacid'] = $_W['uniacid'];
			$setting['storeid'] = $storeid;
			pdo_insert('storex_plugin_bargain_setting', $setting);
		} else {
			pdo_update('storex_plugin_bargain_setting', $setting, array('id' => $setting_info['id']));
		}
		itoast('编辑成功', referer(), 'success');
	}
}

include $this->template('store/shop_plugin_bargain_setting');