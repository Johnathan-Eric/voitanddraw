<?php
/**
 * 万能小店砍价模块微站定义
 *
 * @author 万能君
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Wn_storex_plugin_bargainModuleSite extends WeModuleSite {
	public function doWebStorexbargain() {
		global $_W, $_GPC;
		$storex_bases = pdo_getall('storex_bases', array('weid' => $_W['uniacid'], 'store_type' => 0, 'status' => 1), array('id', 'title', 'thumb'));
		if (!empty($storex_bases)) {
			foreach ($storex_bases as &$store) {
				$store['thumb'] = tomedia($store['thumb']);
				$store['link'] = url('site/entry', array('do' => 'shop_plugin_bargain', 'op' => 'display', 'storeid' => $store['id'], 'm' => 'wn_storex'));
			}
		}
		include $this->template('storexbargain');
	}
}