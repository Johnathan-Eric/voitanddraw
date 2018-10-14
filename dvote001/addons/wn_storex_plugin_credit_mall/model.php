<?php
/**
 * 万能小店
 *
 * @author WeEngine Team & ewei
 * @url
 */
defined('IN_IA') or exit('Access Denied');

function get_wxapp_appid() {
	global $_W, $_GPC;
	if ($_W['account']['type'] == 4) {
		load()->model('wxapp');
		load()->model('account');
		$account = uni_fetch(intval($_GPC['i']));
		$account['versions'] = wxapp_get_some_lastversions($account['uniacid']);
		$v = trim($_GPC['v']);
		if (!empty($account['versions'])) {
			foreach ($account['versions'] as $version) {
				if ($version['version'] == $v) {
					$modules = iunserializer($version['modules']);
					breack;
				}
			}
		}
		if (!empty($modules) && !empty($modules['wn_storex']['uniacid'])) {
			$unisetting = uni_setting_load('', $modules['wn_storex']['uniacid']);
			$account_app = uni_fetch(intval($modules['wn_storex']['uniacid']));
			if (!empty($unisetting['jsauth_acid'])) {
				$jsauth_acid = $unisetting['jsauth_acid'];
			} else {
				if ($_W['account']['level'] < 3 && !empty($unisetting['oauth']['account'])) {
					$jsauth_acid = $unisetting['oauth']['account'];
				} else {
					$jsauth_acid = $account_app['acid'];
				}
			}
			if (!empty($jsauth_acid)) {
				$account_api = WeAccount::create($jsauth_acid);
				if (!empty($account_api)) {
					$_W['account']['jssdkconfig'] = $account_api->getJssdkConfig();
					$_W['account']['jsauth_acid'] = $jsauth_acid;
				}
			}
		}
	}
}

function wxapp_uniacid() {
	global $_W, $_GPC;
	if ($_W['account']['type'] == 4) {
		load()->model('wxapp');
		load()->model('account');
		$account = uni_fetch(intval($_GPC['i']));
		$account['versions'] = wxapp_get_some_lastversions($account['uniacid']);
		$v = trim($_GPC['v']);
		if (!empty($account['versions'])) {
			foreach ($account['versions'] as $version) {
				if ($version['version'] == $v) {
					$modules = iunserializer($version['modules']);
					breack;
				}
			}
		}
		if (!empty($modules) && !empty($modules['wn_storex']['uniacid'])) {
			$_W['uniacid'] = $modules['wn_storex']['uniacid'];
		}
	}
}
