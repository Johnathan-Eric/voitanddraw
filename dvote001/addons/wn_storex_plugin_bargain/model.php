<?php
/**
 * 万能小店
 *
 * @author WeEngine Team & ewei
 * @url
 */

function wmessage($msg, $share = '', $type = '') {
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		$vars = array();
		$vars['message'] = $msg;
		$vars['share'] = $share;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
}

/**
 * @param 帮砍价的类型 $type
 * @param 砍价活动信息 $bargain
 * @param 发起砍价的列表信息 $bargain_list
 * @return array 砍价的记录
 * */
function bargain_money($type, $bargain, $bargain_list) {
	global $_W;
	$rand = iunserializer($bargain['rand']);
	if (!empty($rand) && is_array($rand)) {
		$num = rand(1, 100);
		$left = 1;
		$right = 0;
		foreach ($rand as $r) {
			$right += $r['percent'];
			if ($left <= $num && $num <= $right) {
				$use_rand = $r;
				break;
			}
		}
	}
	$money = rand($use_rand['left'] * 100, $use_rand['right'] * 100) / 100;
	$cprice = $bargain_list['cprice'] - $money;
	if ($cprice <= $bargain['endprice']) {
		$cprice = $bargain['endprice'];
		$money = sprintf('%.2f', $bargain_list['cprice'] - $bargain['endprice']);
		$list['status'] = 1;
	}
	$list['times'] = $bargain_list['times'] - 1;
	if ($list['times'] <= 0 && $cprice != $bargain_list['endprice']) {
		$list['status'] = 3;
		$list['reason'] = '砍价次数已用尽，未达到最低价';
	}
	$list['cprice'] = $cprice;

	$log = array(
		'uniacid' => $bargain_list['uniacid'],
		'storeid' => $bargain_list['storeid'],
		'bargain_list_id' => $bargain_list['id'],
		'oprice' => $bargain_list['cprice'],
		'cprice' => $cprice,
		'money' => sprintf('%.2f', $money),
		'time' => TIMESTAMP,
	);
	if ($type == 1) {
		$log['type'] = $type;
		$log['avatar'] = '../addons/wn_storex_plugin_bargain/icon.jpg';
	} else {
		$user_info = mc_fetch_one($_W['openid']);
		$log['avatar'] = $user_info['avatar'];
		$log['nick'] = $user_info['nickname'];
		$log['openid'] = $_W['openid'];
	}
	if (pdo_insert('storex_plugin_bargain_logs', $log)) {
		pdo_update('storex_plugin_bargain_list', $list, array('id' => $bargain_list['id']));
		if (!empty($list['status'])) {
			$log['bargain_status'] = $list['status'];
		} else {
			$log['bargain_status'] = $bargain_list['status'];
		}
		return $log;
	}
}

/**
 *
 * @param 砍价活动 $bargain
 * @return array 砍价的商品信息
 */
function bargain_good($bargain) {
	$table = 'storex_goods';
	if ($bargain['is_spec'] == 1) {
		$table = 'storex_spec_goods';
	}
	$good = pdo_get($table, array('id' => $bargain['goodsid']));
	if (!empty($good['thumb'])) {
		$good['thumb'] = tomedia($good['thumb']);
	}
	return $good;
}

/**
 *
 * @param 后台添加砍价的id $id
 * @return array 砍价活动信息
 */
function bargain_activity($id) {
	return pdo_get('storex_plugin_bargain', array('id' => $id));
}

/**
 * 
 * @param 店铺id $storeid
 * @return array   设置信息
 */
function bargain_setting($storeid) {
	global $_W;
	$setting = pdo_get('storex_plugin_bargain_setting', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));
	if (!empty($setting['thumbs'])) {
		$setting['thumbs']  = iunserializer($setting['thumbs']);
		foreach ($setting['thumbs'] as &$thumb) {
			$thumb = tomedia($thumb);
		}
		unset($thumb);
	}
	if (!empty($setting['rules'])) {
		$setting['rules'] = htmlspecialchars_decode($setting['rules'], ENT_QUOTES);
	}
	return $setting;
}

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