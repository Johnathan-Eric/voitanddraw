<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('post', 'display', 'delete', 'bargain_list', 'bargain_logs');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

mload()->model('plugin');
$store_info = $_W['wn_storex']['store_info'];
$storeid = intval($store_info['id']);
if (in_array($op, array('post', 'display'))) {
	$base_goods = pdo_getall('storex_goods', array('weid' => $_W['uniacid'], 'store_base_id' => $storeid, 'status' => 1), array(), 'id');
	if (is_array($base_goods)) {
		$goodsids = array_keys($base_goods);
		$goods_list = pdo_getall('storex_spec_goods', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'goodsid' => $goodsids), array(), 'id');
	}
	if (!empty($base_goods) && is_array($base_goods)) {
		foreach ($base_goods as &$val) {
			$val['thumb'] = tomedia($val['thumb']);
			$val['is_spec'] = 2;
		}
		unset($val);
	}
	if (!empty($goods_list) && is_array($goods_list)) {
		foreach ($goods_list as &$goods) {
			$fake_ids[] = $goods['goodsid'];
			$goods['thumb'] = tomedia($goods['thumb']);
			$goods['goods_val'] = iunserializer($goods['goods_val']);
			$goods['goods_val_title'] = implode('/', $goods['goods_val']);
			$goods['is_spec'] = 1;
		}
		unset($goods);
	} else {
		$goods_list	= $base_goods;
	}
	$not_have_spec_ids = @array_diff($goodsids, $fake_ids);
	if (!empty($not_have_spec_ids) && is_array($not_have_spec_ids)) {
		foreach ($not_have_spec_ids as $key => $value) {
			$goods_list[] = $base_goods[$value];
		}
	}
	$bargains = pdo_getall('storex_plugin_bargain', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'status' => 1), array('id', 'goodsid', 'is_spec'), 'id');
	if (!empty($bargains) && is_array($bargains)) {
		foreach ($bargains as $k => $bargain) {
		    if ($bargain['endtime'] < TIMESTAMP || $bargain['stock'] <= 0) {
                continue;
            }
			if ($bargain['is_spec'] == 1) {
				$spec_ids[$k] = $bargain['goodsid'];
			} elseif ($bargain['is_spec'] == 2) {
				$not_spec_ids[$k] = $bargain['goodsid'];
			}
		}
	}
	$available_list = $goods_list;
	if (!empty($available_list) && is_array($available_list)) {
		foreach ($available_list as $key => $goods) {
			if ($goods['is_spec'] == 1) {
				if (is_array($spec_ids) && in_array($goods['id'], $spec_ids)) {
					unset($available_list[$key]);
				}
			} elseif ($goods['is_spec'] == 2) {
				if (is_array($not_spec_ids) && in_array($goods['id'], $not_spec_ids)) {
					unset($available_list[$key]);
				}
			}
		}
	}
}

if ($op == 'display') {
	$plugin_list = plugin_list();
	if (empty($plugin_list['wn_storex_plugin_bargain'])) {
//		message('插件未安装', '', 'error');
	}
	
	$bargain_condition_str = " uniacid = " . $_W['uniacid'] . " AND storeid = " . $storeid;
	//砍价成功数量
	$bargain_over_num = plugin_day_statis($bargain_condition_str . " AND status = 1", 0, 'bargain');
	//砍价进行中数量
	$bargain_unover_num = plugin_day_statis($bargain_condition_str . " AND status = 0", 0, 'bargain');
	//砍价下单成功数量
	$bargain_success_num = plugin_day_statis($bargain_condition_str . " AND status = 2", 0, 'bargain');
	//砍价失败数量
	$bargain_faile_num = plugin_day_statis($bargain_condition_str . " AND status = 3", 0, 'bargain');
	
	$order_condition_str = " weid = " . $_W['uniacid'] . " AND hotelid = " . $storeid . " AND bargainid != '' AND status = 3";
	$order_today = plugin_day_statis($order_condition_str, 1);
	$order_yestoday = plugin_day_statis($order_condition_str, -1);
	$order_seven = plugin_day_statis($order_condition_str, 7);
	$order_month = plugin_day_statis($order_condition_str, 30);
	
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$bargain_list = pdo_getall('storex_plugin_bargain', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'status' => 1), array(), '', 'id DESC', array($pindex, $psize));
	if (!empty($bargain_list) && is_array($bargain_list)) {
		foreach ($bargain_list as $key => &$value) {
			if ($value['is_spec'] == 1) {
				$value['edit_thumb'] = $goods_list[$value['goodsid']]['thumb'];
				$value['edit_title'] = $goods_list[$value['goodsid']]['title'];
				$value['edit_goods_val'] = $goods_list[$value['goodsid']]['goods_val_title'];
				$value['cprice'] = $goods_list[$value['goodsid']]['cprice'];
			} else {
				$value['edit_thumb'] = $base_goods[$value['goodsid']]['thumb'];
				$value['edit_title'] = $base_goods[$value['goodsid']]['title'];
				$value['cprice'] = $base_goods[$value['goodsid']]['cprice'];
			}
		}
		unset($value);
	}
	$params = array();
	$params[':uniacid'] = $_W['uniacid'];
	$params[':storeid'] = $storeid;
	$params[':status'] = 1;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_plugin_bargain') . " WHERE uniacid = :uniacid AND storeid = :storeid AND status = :status");
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'post') {
	$id = intval($_GPC['id']);

	$current_bargain = pdo_get('storex_plugin_bargain', array('id' => $id));
	if (empty($current_bargain)) {
		$current_bargain['starttime'] = time();
		$current_bargain['endtime'] = time();
	}
	if ($current_bargain['is_spec'] == 1) {
		$current_bargain['edit_thumb'] = $goods_list[$current_bargain['goodsid']]['thumb'];
		$current_bargain['edit_title'] = $goods_list[$current_bargain['goodsid']]['title'];
		$current_bargain['edit_goods_val'] = $goods_list[$current_bargain['goodsid']]['goods_val_title'];
		$current_bargain['cprice'] = $goods_list[$current_bargain['goodsid']]['cprice'];
	} else {
		$current_bargain['edit_thumb'] = $base_goods[$current_bargain['goodsid']]['thumb'];
		$current_bargain['edit_title'] = $base_goods[$current_bargain['goodsid']]['title'];
		$current_bargain['cprice'] = $base_goods[$current_bargain['goodsid']]['cprice'];
	}
	$current_bargain['rand'] = iunserializer($current_bargain['rand']);
	if (checksubmit('submit')) {
		if (strtotime($_GPC['starttime']) >= strtotime($_GPC['endtime'])) {
			itoast('开始时间不能大于结束时间', '', 'error');
		}
		$rand_list = $_GPC['rand'];
		if (!empty($rand_list) && is_array($rand_list)) {
			foreach ($rand_list as $key => $value) {
				if ((empty($value['left']) && $value['left'] != 0) || (empty($value['right']) && $value['right'] != 0) || empty($value['percent'])) {
					itoast('请完善单次砍价概率', '', 'error');
				}
				if ($value['left'] > $value['right']) {
					itoast('最大金额应大于最小金额', '', 'error');
				}
				$percent_total += $value['percent'];
			}
			if ($percent_total != 100) {
				itoast('概率相加应等于100%', '', 'error');
			}
		} else {
			itoast('请填写单次砍价概率', '', 'error');
		}
		if (empty($_GPC['totaltime']) || $_GPC['totaltime'] < 1) {
			itoast('请填写砍价次数', '', 'error');
		}
		if (empty($_GPC['stock']) || $_GPC['stock'] < 1) {
			itoast('请填写发起次数限制', '', 'error');
		}
		$bargain_data = array(
			'goodsid' => intval($_GPC['goodsid']),
			'is_spec' => intval($_GPC['is_spec']),
			'endprice' => trim($_GPC['endprice']),
			'totaltime' => trim($_GPC['totaltime']),
			'starttime' => strtotime($_GPC['starttime']),
			'endtime' => strtotime($_GPC['endtime']),
			'stock' => trim($_GPC['stock']),
			'rand' => iserializer($_GPC['rand']),
			'status' => 1
		);
		if (!empty($id)) {
			itoast('砍价功能未知错误', $this->createWebUrl('shop_plugin_bargain', array('op' => 'display', 'storeid' => $storeid)), 'error');
		} else {
			$bargain_data['uniacid'] = $_W['uniacid'];
			$bargain_data['storeid'] = $storeid;
			pdo_insert('storex_plugin_bargain', $bargain_data);
			$id = pdo_insertid();
		}
		itoast('设置成功', $this->createWebUrl('shop_plugin_bargain', array('op' => 'display', 'storeid' => $storeid)), 'success');
	}
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	$bargain_info = pdo_get('storex_plugin_bargain', array('id' => $id, 'uniacid' => $_W['uniacid']));
	if (empty($bargain_info)) {
		itoast('活动信息不存在', '', 'error');
	}
	pdo_update('storex_plugin_bargain', array('status' => 2), array('id' => $id, 'uniacid' => $_W['uniacid']));
	itoast('删除成功', '', 'success');
}

if ($op == 'bargain_list') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$bargain_id = intval($_GPC['id']);
	$bargain_list = pdo_getall('storex_plugin_bargain_list', array('bargainid' => $bargain_id), array(), '', 'time ASC', array($pindex, $psize));
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_plugin_bargain_list') . " WHERE bargainid = :bargain_id", array(':bargain_id' => $bargain_id));
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'bargain_logs') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$bargainid = intval($_GPC['bargainid']);
	$bargain_list_id = intval($_GPC['id']);
	$bargain_logs = pdo_getall('storex_plugin_bargain_logs', array('bargain_list_id' => $bargain_list_id), array(), '', 'time ASC', array($pindex, $psize));
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_plugin_bargain_logs') . " WHERE bargain_list_id = :bargain_list_id", array(':bargain_list_id' => $bargain_list_id));
	$pager = pagination($total, $pindex, $psize);
}
include $this->template('store/shop_plugin_bargain');