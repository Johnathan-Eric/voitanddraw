<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('display', 'exchange', 'mine', 'detail', 'confirm', 'comment');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

load()->model('mc');

require IA_ROOT . '/addons/wn_storex/model/card.mod.php';
require IA_ROOT . '/addons/wn_storex/model/express.mod.php';
require IA_ROOT . '/addons/wn_storex_plugin_credit_mall/model.php';
$uid = mc_openid2uid($_W['openid']);
activity_get_coupon_type();
wxapp_uniacid();
get_wxapp_appid();

$profile = mc_fetch($_W['member']['uid']);
//真实物品列表
if ($op == 'display') {
	$goods_lists = pdo_fetchall('SELECT id, title, extra, thumb, type, credittype, endtime, description, credit FROM ' . tablename('storex_activity_exchange') . ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime AND status = 1 ORDER BY endtime ASC ', array(':uniacid' => $_W['uniacid'], ':type' => 3, ':endtime' => TIMESTAMP));
	foreach ($goods_lists as &$list) {
		$list['thumb'] = tomedia($list['thumb']);
		$list['extra'] = iunserializer($list['extra']);
		if (!is_array($list['extra'])) {
			$list['extra'] = array();
		}
	}
	unset($list);
	message(error(0, $goods_lists), '', 'ajax');
}
//兑换过程
if ($op == 'exchange') {
	$id = intval($_GPC['id']); 
	$shipping_data = array(
		'name' => trim($_GPC['username']),
		'mobile' => trim($_GPC['mobile']),
		'zipcode' => trim($_GPC['zipcode']),
		'province' => trim($_GPC['province']),
		'city' => trim($_GPC['city']),
		'address' => trim($_GPC['address']),
	);
	$shipping_data['district'] = trim($_GPC['district']);
	$creditnames = array(
		'credit1' => '积分',
		'credit2' => '余额'
	);
	$goods = activity_get_exchange_info($id, $_W['uniacid']);
	if (empty($goods)) {
		message(error(-1, '没有指定的礼品兑换'), '', 'ajax');
	}
	$exchange_num = intval($_GPC['num']);
	$exchange_num = empty($exchange_num) ? 1 : $exchange_num;
	$credit = mc_credit_fetch($uid, array($goods['credittype']));
	if ($credit[$goods['credittype']] < $goods['credit'] * $exchange_num) {
		message(error(-1, "{$creditnames[$goods['credittype']]}不足"), '', 'ajax');
	}
	$ret = activity_user_get_goods($uid, $id, $exchange_num);
	if (is_error($ret)) {
		message($ret, '', 'ajax');
	}
	pdo_update('storex_activity_exchange_trades_shipping', $shipping_data, array('tid' => $ret));
	mc_credit_update($uid, $goods['credittype'], -$exchange_num * $goods['credit'], array($uid, '礼品兑换:' . $goods['title'] . ' 消耗 ' . $creditnames[$goods['credittype']] . ':' . $exchange_num * $goods['credit']));
	//微信通知
	if ($goods['credittype'] == 'credit1') {
		mc_notice_credit1($_W['openid'], $uid, -$exchange_num * $goods['credit'], '兑换礼品消耗积分');
	} else {
		mc_notice_credit2($_W['openid'], $uid, -$exchange_num * $goods['credit'], 0, '线上消费，兑换礼品');
	}
	message(error(0, '兑换成功'), '', 'ajax');
}
//收获地址
if ($op == 'detail') {
	$tid = intval($_GPC['tid']);//收货人信息id
	$id = intval($_GPC['id']);
	$goods_info = pdo_get('storex_activity_exchange', array('id' => $id));
	$goods_info['reside'] = $goods_info['total'] - $goods_info['num'];
	$goods_info['exp_date'] = '有效期:' . date('Y.m.d', $goods_info['starttime']) . '-' . date('Y.m.d', $goods_info['endtime']);
	$goods_info['description'] = htmlspecialchars_decode($goods_info['description']);
	$goods_info['thumb'] = tomedia($goods_info['thumb']);
	$goods_info['extra'] = iunserializer($goods_info['extra']);
	if (empty($tid)) {
		$address_data = pdo_getall('storex_member_address', array('uid' => $uid, 'uniacid' => intval($_W['uniacid'])));
	} else {
		$address_data = pdo_get('storex_activity_exchange_trades_shipping', array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'tid' => $tid));
		$address_data['username'] = $address_data['name'];
	}
	$exchange_info['goods'] = $goods_info;
	$exchange_info['address'] = $address_data;
	if (empty($address_data['iscomment'])) {
		$exchange_info['iscomment'] = 0;
	} else {
		$exchange_info['iscomment'] = 1;
		$comment = pdo_get('storex_activity_exchange_comment', array('tid' => $tid));
		if (!empty($comment)) {
			$comment['createtime'] = date('Y-m-d H:i', $comment['createtime']);
			$exchange_info['comment'] = $comment;
		}
	}
	if (!empty($address_data['express_type']) && !empty($address_data['express_number'])) {
		$express_info = express_get($address_data['express_type'], $address_data['express_number']);
		$express_info = array_reverse($express_info);
		$exchange_info['express_info'] = $express_info;
	}
	message(error(0, $exchange_info), '', 'ajax');
}
//我的实物
if ($op == 'mine') {
	$lists = pdo_fetchall("SELECT a.*, b.id AS gid, b.title, b.extra, b.thumb, b.type, b.credittype, b.endtime, b.description, b.credit FROM " . tablename('storex_activity_exchange_trades_shipping') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') . " AS b ON a.exid = b.id WHERE a.uid = :uid ORDER BY a.status", array(':uid' => $uid));
	foreach ($lists as &$list) {
		$list['thumb'] = tomedia($list['thumb']);
		$list['extra'] = iunserializer($list['extra']);
		if(!is_array($list['extra'])) {
			$list['extra'] = array();
		}
	}
	unset($list);
	message(error(0, $lists), '', 'ajax');
}
//确认收货
if ($op == 'confirm') {
	if ($_W['isajax'] && $_W['ispost']) {
		$tid = intval($_GPC['tid']);
		$shipping_info = pdo_get('storex_activity_exchange_trades_shipping', array('tid' => $tid, 'uid' => $uid), array('tid', 'status'));
		if (empty($shipping_info)) {
			message(error(-1,'订单信息不存在'), '', 'ajax');
		}
		if ($shipping_info['status'] == 1) {
			pdo_update('storex_activity_exchange_trades_shipping', array('status' => 2), array('uid' => $uid, 'tid' => $tid));
			message(error(0, '确认收货成功'), '', 'ajax');
		} else {
			if ($shipping_info['status'] == 0) {
				$message = '该商品待发货';
			} elseif ($shipping_info['status'] == 2) {
				$message = '订单已完成';
			} elseif ($shipping_info['status'] == -1) {
				$message = '订单已关闭';
			}
			message(error(-1, $message), '', 'ajax');
		}
	}
}

if ($op == 'comment') {
	$tid = intval($_GPC['tid']);
	if (empty($tid)) {
		message(error(-1, '评价订单错误'), '', 'ajax');
	}
	$shipping = pdo_get('storex_activity_exchange_trades_shipping', array('tid' => $tid));
	if ($shipping['status'] != 2) {
		message(error(-1, '未完成不能评价'), '', 'ajax');
	}
	if ($shipping['iscomment'] == 1) {
		message(error(-1, '已评价'), '', 'ajax');
	}
	if (empty($_GPC['comment'])) {
		message(error(-1, '请留下你的宝贵的评价'), '', 'ajax');
	}
	$comment = array(
		'uniacid' => $_W['uniacid'],
		'tid' => $tid,
		'exid' => $shipping['exid'],
		'uid' => $uid,
		'star' => $_GPC['star'] ? intval($_GPC['star']) : 5,
		'comment' => trim($_GPC['comment']),
		'createtime' => TIMESTAMP,
	);
	pdo_insert('storex_activity_exchange_comment', $comment);
	if (pdo_insertid()) {
		pdo_update('storex_activity_exchange_trades_shipping', array('iscomment' => 1), array('tid' => $tid));
		message(error(0, '感谢您的评价'), '', 'ajax');
	} else {
		message(error(-1, '评价失败'), '', 'ajax');
	}
}
