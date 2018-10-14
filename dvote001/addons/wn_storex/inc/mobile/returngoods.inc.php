<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$ops = array('application', 'return_list', 'return_detail', 'send_info');
$op = in_array($_GPC['op'], $ops) ? trim($_GPC['op']) : 'return_list';

check_params();
$uid = mc_openid2uid($_W['openid']);
$storeid = intval($_GPC['storeid']);
mload()->model('goods');
mload()->model('express');

if ($op == 'application') {
	$orderid = intval($_GPC['orderid']);
	$goodsid = intval($_GPC['goodsid']);
	$spec_id = intval($_GPC['spec_id']);
	$nums = $_GPC['nums'] ? intval($_GPC['nums']) : 1;
	$reason = trim($_GPC['reason']);
	$type = trim($_GPC['type']);
	//商品或规格id|数量|是不是规格,商品或规格id|数量|是不是规格1规格，2普通，3套餐
	$good = trim($_GPC['good']);
	$cart_good = array();
	if (!empty($good)) {
		$cart_good = explode('|', $good);
	}
	
	$field = array('id', 'weid', 'hotelid', 'roomid', 'style', 'nums', 'cprice', 'oprice', 'sum_price', 'status', 'cart', 'over_time');
	$order = pdo_get('storex_order', array('id' => $orderid, 'status' => 3, 'openid' => $_W['openid']), $field);
	
	if (empty($order)) {
		wmessage(error(-1, '订单不存在'), '', 'ajax');
	}
	//判断在不在退货的日期范围内
	$storeinfo = pdo_get('storex_bases', array('id' => $storeid), array('id', 'return_info'));
	if (!empty($storeinfo)) {
		$storeinfo['return_info'] = iunserializer($storeinfo['return_info']);
	}
	if (TIMESTAMP > ($order['over_time'] + 86400 * $storeinfo['return_info']['return_days'])) {
		wmessage(error(-1, '订单已不在退货日期范围内'), '', 'ajax');
	}
	
	if (!empty($cart_good)) {
		if (empty($order['cart'])) {
			wmessage(error(-1, '退货商品不存在'), '', 'ajax');
		}
		$order['cart'] = iunserializer($order['cart']);
		$return = false;
		foreach ($order['cart'] as $g) {
			if ($cart_good[0] == $g['buyinfo'][0] && $cart_good[2] == $g['buyinfo'][2] && $cart_good[1] <= $g['buyinfo'][1]) {
				$return = true;
				break;
			}
		}
		if (empty($return)) {
			wmessage(error(-1, '退货商品不存在'), '', 'ajax');
		}
	}
	if (empty($good)) {
		if (((!empty($goodsid) && $goodsid != $order['roomid']) ||  (!empty($spec_id) && $spec_id != $order['spec_id'])) && $nums > $order['nums']) {
			wmessage(error(-1, '退货商品与订单不符'), '', 'ajax');
		}
	}
	$return_info = $record = array(
		'uniacid' => $_W['uniacid'],
		'storeid' => $storeid,
		'openid' => $_W['openid'],
		'orderid' => $orderid,
		'goodsid' => $goodsid,
		'spec_id' => $spec_id,
		'good' => $good,
	);
	//判断是不是已经申请过
	$record['status IN'] = array('0', '1');
	if (pdo_get('storex_return_goods', $record)) {
		wmessage(error(-1, '该商品已申请过退货了'), '', 'ajax');
	}
	$money = sprintf('%.2f', $_GPC['money']);
	if (empty($money)) {
		wmessage(error(-1, '请填写退货退款金额'), '', 'ajax');
	}
	$return_info['type'] = $type;
	//只退款
	if ($type == 1) {
		$return_info['goods_reason'] = iserializer($_GPC['goods_reason']);
	}
	$return_info['reason'] = iserializer($reason);
	$return_info['money'] = $money;
	$return_info['content'] = iserializer($_GPC['content']);
	$return_info['thumbs'] = iserializer($_GPC['thumbs']);

	$return_info['nums'] = $nums;
	$return_info['time'] = TIMESTAMP;
	$return_info['status'] = 0;
	$return_info['goods_status'] = 0;
	$return_info['refund_status'] = 0;
	if (pdo_insert('storex_return_goods', $return_info)) {
		wmessage(error(0, pdo_insertid()), '', 'ajax');
	} else {
		wmessage(error(-1, '申请退货提交失败'), '', 'ajax');
	}
}

if ($op == 'return_list') {
	$return_list = pdo_getall('storex_return_goods', array('openid' => $_W['openid']));
	if (!empty($return_list) && is_array($return_list)) {
		foreach ($return_list as &$info) {
			$info['send'] = 0;
			if (empty($info['status'])) {
				$info['status_cn'] = '等待店家确认';
			} elseif ($info['status'] == 1) {
				if (empty($info['type'])) {
					if (empty($info['goods_status'])) {
						$info['status_cn'] = '可以发货';
					} elseif ($info['goods_status'] == 1) {
						$info['status_cn'] = '等待店家收货';
					} elseif ($info['goods_status'] == 2) {
						if (empty($info['refund_status'])) {
							$info['status_cn'] = '已收货，未退款';
						} else {
							$info['status_cn'] = '退货退款成功';
						}
					}
				} else {
					if (empty($info['refund_status'])) {
						$info['status_cn'] = '已确认，未退款';
					} else {
						$info['status_cn'] = '退款成功';
					}
				}
			} elseif ($info['status'] == 2) {
				$info['status_cn'] = '审核失败';
			}
			$info['time'] = date('Y-m-d H:i:s', $info['time']);
			$good = goods_info($info);
			if (!empty($good)) {
				$info['thumb'] = tomedia($good['thumb']);
				$info['title'] = $good['title'];
				$info['sub_title'] = $good['sub_title'];
			}
			$info['reason'] = $info['reason'] ? iunserializer($info['reason']) : '';
			$info['content'] = $info['content'] ? iunserializer($info['content']) : '';
			if (empty($info['type'])) {
				$info['type_cn'] = '退货退款';
			} else {
				$info['type_cn'] = '仅退款';
			}
		}
	}
	wmessage(error(0, $return_list), '', 'ajax');
}

if ($op == 'return_detail') {
	$id = intval($_GPC['id']);
	$storeinfo = pdo_get('storex_bases', array('id' => $storeid), array('id', 'return_info'));
	if (!empty($storeinfo)) {
		$storeinfo['return_info'] = iunserializer($storeinfo['return_info']);
	}
	$return_info = pdo_get('storex_return_goods', array('id' => $id));
	if (!empty($return_info)) {
		unset($return_info['openid']);
		$return_info['goods_reason'] = !empty($return_info['goods_reason']) ? iunserializer($return_info['goods_reason']) : '';
		$return_info['reason'] = !empty($return_info['reason']) ? iunserializer($return_info['reason']) : '';
		$return_info['content'] = !empty($return_info['content']) ? iunserializer($return_info['content']) : '';
		$return_info['refuse_reason'] = !empty($return_info['refuse_reason']) ? iunserializer($return_info['refuse_reason']) : '';
		if (!empty($return_info['thumbs'])) {
			$return_info['thumbs'] = iunserializer($return_info['thumbs']);
			$return_info['thumbs'] = format_url($return_info['thumbs']);
		}
		if (empty($return_info['type'])) {//退款退货
			$return_info['flow'] = array(
				'application' => 1,
				'confirm' => 0,
				'send' => 0,
				'result' => 0,
			);
			if ($return_info['goods_status'] > 0) {
				$return_info['flow']['send'] = 1;//已发货
			} else {
				$return_info['flow']['send'] = 0;
			}
		} else {//仅退款
			$return_info['flow'] = array(
				'application' => 1,
				'confirm' => 0,
				'result' => 0,
			);
		}
		if ($return_info['status'] == 1) {
			$return_info['flow']['confirm'] = 1;//审核通过
		} elseif($return_info['status'] == 2) {
			$return_info['flow']['confirm'] = 2;//审核不通过
			$order = pdo_get('storex_order', array('id' => $return_info['orderid']), array('id', 'roomid', 'spec_id', 'cart'));
			if (!empty($order['cart'])) {
				$order['cart'] = iunserializer($order['cart']);
				foreach ($order['cart'] as $k => $c) {
					$buyinfo = implode('|', $c['buyinfo']);
					if ($buyinfo == $return_info['good']) {
						$return_info['key'] = $k;
						break;
					}
				}
			} else {
				$return_info['key'] = 0;
			}
		}
		if (empty($return_info['refund_status'])) {
			$return_info['flow']['result'] = 0;//未退款
		} else {
			$return_info['flow']['result'] = 1;//已退款
		}
		$good = goods_info($return_info);
		if (!empty($good)) {
			$return_info['thumb'] = tomedia($good['thumb']);
			$return_info['title'] = $good['title'];
			$return_info['sub_title'] = $good['sub_title'];
		}
		$return_info['express'] = express_type();
		$return_info['return_info'] = $storeinfo['return_info'];
		wmessage(error(0, $return_info), '', 'ajax');
	} else {
		wmessage(error(-1, '退款退货订单不存在'), '', 'ajax');
	}
}

if ($op == 'send_info') {
	$id = intval($_GPC['id']);
	$track_number = trim($_GPC['track_number']);
	$express_type = trim($_GPC['express_type']);
	if (empty($track_number) || empty($express_type)) {
		wmessage(error(-1, '退货信息不完善'), '', 'ajax');
	}
	$return_info = pdo_get('storex_return_goods', array('id' => $id, 'openid' => $_W['openid']));
	if (empty($return_info)) {
		wmessage(error(-1, '未发起退货'), '', 'ajax');
	}
	if ($return_info['status'] != 1) {
		wmessage(error(-1, '店家未同意退货'), '', 'ajax');
	}
	$update = array(
		'track_number' => $track_number,
		'express_type' => $express_type,
		'goods_status' => 1,
	);
	$result = pdo_update('storex_return_goods', $update, array('id' => $id));
	if (!is_error($result)) {
		wmessage(error(0, '退货信息填写成功'), '', 'ajax');
	} else {
		wmessage(error(-1, '退货信息填写失败，请联系店家'), '', 'ajax');
	}
}