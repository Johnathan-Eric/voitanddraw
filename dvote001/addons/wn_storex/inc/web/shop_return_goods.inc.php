<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
mload()->model('card');
mload()->model('order');
mload()->model('log');
mload()->model('goods');
mload()->model('express');

$ops = array('returnlist', 'edit', 'refuse_reason');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'returnlist';

$storeid = intval($_GPC['storeid']);
$store = $_W['wn_storex']['store_info'];
$store_type = $store['store_type'];

if ($op == 'returnlist') {
	$express = express_type();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$returnlist = pdo_getall('storex_return_goods', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid), array(), '', 'time DESC', array($pindex, $psize));
	if (!empty($returnlist) && is_array($returnlist)) {
		foreach ($returnlist as &$info) {
			if (!empty($info['reason'])) {
				$info['reason'] = iunserializer($info['reason']);
			}
			if (!empty($info['goods_reason'])) {
				$info['goods_reason'] = iunserializer($info['goods_reason']);
			}
			if (!empty($info['content'])) {
				$info['content'] = iunserializer($info['content']);
			}
			if (!empty($info['thumbs'])) {
				$info['thumbs'] = iunserializer($info['thumbs']);
				if (!empty($info['thumbs']) && is_array($info['thumbs'])) {
					$info['thumbs'] = format_url($info['thumbs']);
				}
			}
			if (!empty($info['refuse_reason'])) {
				$info['refuse_reason'] = iunserializer($info['refuse_reason']);
			}
			$good = goods_info($info);
			if (!empty($good)) {
				$info['thumb'] = tomedia($good['thumb']);
				$info['title'] = $good['title'];
				$info['sub_title'] = $good['sub_title'];
			}
		}
		unset($info);
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_return_goods') . " WHERE uniacid = :uniacid AND storeid = :storeid", array(':uniacid' => $_W['uniacid'], ':storeid' => $storeid));
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'edit') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$goods_status = intval($_GPC['goods_status']);
	$refund_status = intval($_GPC['refund_status']);
	$update = array();
	if (!empty($status)) {
		$update['status'] = $status;
	}
	if (!empty($goods_status)) {
		$update['goods_status'] = $goods_status;
	}
	if (!empty($refund_status)) {
		$update['refund_status'] = $refund_status;
	}
	$result = pdo_update('storex_return_goods', $update, array('id' => $id));
	if (is_error($result)) {
		itoast('状态修改失败！', '', 'error');
	}
	itoast('状态修改成功！', '', 'success');
}

if ($op == 'refuse_reason') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	$refuse_reason = trim($_GPC['refuse_reason']);
	$update = array();
	if (empty($refuse_reason)) {
		message(error(-1, '请填写拒绝原因'), '', 'ajax');
	}
	$update['status'] = $status;
	$update['refuse_reason'] = iserializer($refuse_reason);
	$result = pdo_update('storex_return_goods', $update, array('id' => $id));
	if (!is_error($result)) {
		message(error(0, '成功拒绝退货请求'), '', 'ajax');
	} else {
		message(error(-1, '拒绝退货请求失败'), '', 'ajax');
	}
}
include $this->template('store/shop_return_goods');