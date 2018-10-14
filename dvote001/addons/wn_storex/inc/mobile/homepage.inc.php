<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$ops = array('display', 'notice');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

mload()->model('plugin');
check_params();
$is_wxapp = 2;
if (!empty($_GPC['v'])) {
	$is_wxapp = 1;
}
$storeid = intval($_GPC['id']);
$store_info = get_store_info($storeid);
if ($op == 'display') {
	$default_module = array(
		array(
			'type' => 'search',
			'items' => array()
		),
		array(
			'type' => 'slide',
			'items' => array()
		),
		array(
			'type' => 'notice',
			'items' => array()
		),
		array(
			'type' => 'nav',
			'items' => array()
		),
		array(
			'type' => 'cube',
			'items' => array()
		),
		array(
			'type' => 'adv',
			'items' => array()
		),
		array(
			'type' => 'activity_seckill',
			'items' => array()
		),
		array(
			'type' => 'activity_limited',
			'items' => array()
		),
	);
	if (empty($store_info['store_type'])) {
		$default_module[] = array(
			'type' => 'activity_group',
			'items' => array(),
		);
	}
	$homepage_list = pdo_getall('storex_homepage', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'is_wxapp' => $is_wxapp), array(), 'displayorder', 'displayorder ASC');
	$activity_list = pdo_getall('storex_goods_activity', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'endtime >' => TIMESTAMP), array(), 'id');
    $spec_ids = array();
    $not_spec_ids = array();
	if (!empty($activity_list) && is_array($activity_list)) {
		foreach ($activity_list as $key => $value) {
			if ($value['is_spec'] == 1) {
				$spec_ids[$key] = $value['specid'];
			} else {
				$not_spec_ids[$key] = $value['goodsid'];
			}
			if ($value['type'] == 1) {
				$activity_seckill[] = $value;
			} elseif ($value['type'] == 2) {
				$activity_limited[] = $value;
			}
		}
	}
	$spec_goods = pdo_getall('storex_spec_goods', array('id' => $spec_ids), array('id', 'goodsid', 'title', 'goods_val', 'oprice', 'cprice', 'stock', 'thumb'), 'id');
	$not_spec_goods = pdo_getall('storex_goods', array('id' => $not_spec_ids), array('id', 'title', 'oprice', 'cprice', 'stock', 'thumb'), 'id');
	if (!empty($spec_goods) && is_array($spec_goods)) {
		foreach ($spec_goods as &$goods) {
			$goods['thumb'] = tomedia($goods['thumb']);
			$goods['goods_val'] = iunserializer($goods['goods_val']);
			$goods['is_spec'] = 1;
		}
		unset($goods);
	}
	if (!empty($not_spec_goods) && is_array($not_spec_goods)) {
		foreach ($not_spec_goods as &$val) {
			$val['goodsid'] = $val['id'];
			$val['thumb'] = tomedia($val['thumb']);
			$val['is_spec'] = 2;
		}
		unset($val);
	}
	if (!empty($activity_seckill) && is_array($activity_seckill)) {
		foreach ($activity_seckill as $list) {
			if ($list['is_spec'] == 1) {
				$goods_val = '';
				if (!empty($spec_goods[$list['specid']]['goods_val'])) {
					$goods_val = implode(' ', $spec_goods[$list['specid']]['goods_val']);
				}
				$spec_goods[$list['specid']]['cprice'] = $list['price'];
				$spec_goods[$list['specid']]['nums'] = $list['nums'] - $list['sell_nums'];
				$spec_goods[$list['specid']]['title'] .= ' ' . $goods_val;
				$spec_goods[$list['specid']]['specid'] = $list['specid'];
				$seckill_list[] = $spec_goods[$list['specid']];
			} else {
				$not_spec_goods[$list['goodsid']]['nums'] = $list['nums'] - $list['sell_nums'];
				$seckill_goods = $not_spec_goods[$list['goodsid']];
				$seckill_goods['cprice'] = $list['price'];
				$seckill_list[] = $seckill_goods;
			}
		}
	}
	if (!empty($activity_limited) && is_array($activity_limited)) {
		foreach ($activity_limited as $list) {
			if ($list['is_spec'] == 1) {
				$goods_val = '';
				if (!empty($spec_goods[$list['specid']]['goods_val'])) {
					$goods_val = implode(' ', $spec_goods[$list['specid']]['goods_val']);
				}
				$spec_goods[$list['specid']]['cprice'] = $list['price'];
				$spec_goods[$list['specid']]['nums'] = $list['nums'];
				$spec_goods[$list['specid']]['title'] .= ' ' . $goods_val;
				$spec_goods[$list['specid']]['specid'] = $list['specid'];
				$limited_list[] = $spec_goods[$list['specid']];
			} else {
				$limited_goods = $not_spec_goods[$list['goodsid']];
				$limited_goods['cprice'] = $list['price'];
				$limited_list[] = $limited_goods;
			}
		}
	}
	
	//拼团活动
	$plugin_list = plugin_list();
	$activity = array();
	if (check_ims_version() && !empty($plugin_list['wn_storex_plugin_group'])) {
		$activity_group = pdo_getall('storex_plugin_group_activity', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'endtime >' => TIMESTAMP), array(), '', 'starttime ASC');
		if (!empty($activity_group) && is_array($activity_group)) {
			foreach ($activity_group as $activity) {
				$activity_goods = pdo_getall('storex_plugin_activity_goods', array('group_activity' => $activity['id']));
				if (!empty($activity_goods)) {
					foreach ($activity_goods as &$good) {
						$storex_goods = pdo_get('storex_goods', array('id' => $good['goods_id']), array('id', 'title', 'cprice', 'thumb'));
						$good['spec_cprice'] = iunserializer($good['spec_cprice']);
						$good['oprice'] = $storex_goods['cprice'];
						if ($good['is_spec'] == 1) {
							foreach ($good['spec_cprice'] as $specid => $price) {
								$good['cprice'] = $price;
								$good['specid'] = $specid;
							}
						} else {
							$good['cprice'] = $good['spec_cprice'][$good['goods_id']];
						}
						$good['thumb'] = tomedia($storex_goods['thumb']);
					}
				}
				$activity['starttime'] = date('Y/m/d H:i:s', $activity_group['starttime']);
				$activity['endtime'] = date('Y/m/d H:i:s', $activity_group['endtime']);
				$activity['goods'] = $activity_goods;
				if (!empty($activity_goods)) {
					break;
				}
			}
		}
	}
	if (!empty($homepage_list) && is_array($homepage_list)) {
		$tablaname = gettablebytype($store_info['store_type']);
		mload()->model('goods');
		$discount = goods_member_get_discount($storeid);
		foreach ($homepage_list as $k => &$v) {
			unset($v['displayorder'], $v['uniacid'], $v['storeid']);
            $v['items'] = !empty($v['items']) ? iunserializer($v['items']) : '';
			if ($v['type'] == 'recommend') {
				if ($store_info['store_type'] != STORE_TYPE_HOTEL) {
					if (!empty($v['items']) && is_array($v['items'])) {
						$fields = array('id', 'title', 'thumb', 'cprice', 'store_type', 'sub_title', 'oprice', 'sold_num');
						if ($store_info['store_type'] == 1) {
							$fields[] = 'is_house';
						}
						$goodslist = pdo_getall($tablaname, array('id' => array_values($v['items'])), $fields, 'id');
						foreach ($v['items'] as $key => &$value) {
							if (!empty($value)) {
								$value = $goodslist[$value];
								$value['thumb'] = tomedia($value['thumb']);
								$value['type'] = 2;
								if ($value['store_type'] == 1 && isset($value['is_house']) && $value['is_house'] == 1) {
									$value['type'] = 1;
								}
								$value['cprice'] = sprintf("%.2f", $discount * $value['cprice']);
							} else {
								unset($v['items'][$key]);
							}
						}
					}
				} else {
					unset($homepage_list[$k]);
				}
			}
			if ($v['type'] == 'footer') {
				unset($homepage_list[$k]);
			}
			if ($v['type'] == 'activity_seckill') {
                $v['items'] = $seckill_list;
			}
			if ($v['type'] == 'activity_limited') {
                $v['items'] = $limited_list;
			}
			if ($v['type'] == 'activity_group') {
                $v['items'] = $activity;
			}
		}
	} else {
		$homepage_list = $default_module;
	}
	$share_data = array(
		'title' => $store_info['title'],
		'desc' => $store_info['title'],
		'link' => murl('entry', array('do' => 'display', 'id' => $storeid, 'm' => 'wn_storex', 'type' => 'storeindex'), true, true),
		'imgUrl' => tomedia($store_info['thumb'])
	);
	$share_data = get_share_data('homepage', array('storeid' => $storeid), $share_data);
	wmessage(error(0, $homepage_list), $share_data, 'ajax');
}

if ($op == 'notice') {
	$noticeid = $_GPC['noticeid'];
	$noticekey = $_GPC['noticekey'];
	if ($noticekey == 'article') {
		$article = pdo_get('storex_article', array('id' => $noticeid));
		if (!empty($article)) {
			$article['thumb'] = tomedia($article['thumb']);
			wmessage(error(0, $article), '', 'ajax');
		}
	} else {
		$notice_info = pdo_get('storex_homepage', array('id' => $noticeid, 'uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'type' => 'notice', 'is_wxapp' => $is_wxapp));
		if (!empty($notice_info) && !empty($notice_info['items'])) {
			$notice_info['items'] = iunserializer($notice_info['items']);
			if (!empty($notice_info['items'][$noticekey])) {
				$article = pdo_get('storex_article', array('id' => $notice_info['items'][$noticekey]['id']));
				if (!empty($article)) {
					$article['thumb'] = tomedia($article['thumb']);
					wmessage(error(0, $article), '', 'ajax');
				}
			}
		}
	}
	wmessage(error(0, array()), '', 'ajax');
}