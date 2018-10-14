<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
load()->model('module');

$ops = array('display', 'post', 'delete', 'status', 'default');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$store_info = $_W['wn_storex']['store_info'];
$storeid = intval($store_info['id']);

if ($op == 'display') {
	$where = ' WHERE `uniacid` = :uniacid AND storeid = :storeid';
	$params = array(':uniacid' => $_W['uniacid'], ':storeid' => $storeid);
	$condition = array('uniacid' => $_W['uniacid'], 'storeid' => $storeid);
	$sql = 'SELECT COUNT(*) FROM ' . tablename('storex_dispatch') . $where;
	$total = pdo_fetchcolumn($sql, $params);
	$list = array();
	if ($total > 0) {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_getall('storex_dispatch', $condition, array(), '', 'id DESC', ($pindex - 1) * $psize . ',' . $psize);
		$pager = pagination($total, $pindex, $psize);
	}
}

if ($op == 'post') {
	mload()->model('express');
	$express = express_type();
	$dispatch_info = pdo_get('storex_dispatch', array('id' => $_GPC['id']));
	$dispatch_content = !empty($dispatch_info['content']) ? iunserializer($dispatch_info['content']) : array();
	if (checksubmit()) {
		$id = intval($_GPC['id']);
		$dispatch_data = array(
			'name' => trim($_GPC['name']),
			'express' => trim($_GPC['express']),
			'calculate_type' => !empty($_GPC['calculate_type']) ? intval($_GPC['calculate_type']) : 1,
			'default_first' => !empty($_GPC['default_first']) ? intval($_GPC['default_first']) : 0,
			'default_firstprice' => !empty($_GPC['default_firstprice']) ? trim($_GPC['default_firstprice']) : 0,
			'default_second' => !empty($_GPC['default_second']) ? intval($_GPC['default_second']) : 0,
			'default_secondprice' => !empty($_GPC['default_secondprice']) ? trim($_GPC['default_secondprice']) : 0,
			'default_nopostage' => !empty($_GPC['default_nopostage']) ? trim($_GPC['default_nopostage']) : 0,
			'isdispatcharea' => !empty($_GPC['isdispatcharea']) ? intval($_GPC['isdispatcharea']) : 1,
			'selectedareas_code' => trim($_GPC['selectedareas_code']),
			'status' => !empty($_GPC['status']) ? intval($_GPC['status']) : 1,
		);
		if (empty($dispatch_data['name'])) {
			itoast('请填写配送方式名称', '', 'error');
		}
		if (empty($dispatch_data['express'])) {
			itoast('请选择物流公司', '', 'error');
		}
		if (empty($dispatch_data['selectedareas_code'])) {
			itoast('请选择特殊区域', '', 'error');
		}
		$city_area = $_GPC['city_area'];
		$first = $_GPC['first'];
		$firstprice = $_GPC['firstprice'];
		$second = $_GPC['second'];
		$secondprice = $_GPC['secondprice'];
		$nopostage = $_GPC['nopostage'];
		$content = array();
		if (!empty($city_area) && is_array($city_area)) {
			foreach ($city_area as $key => $value) {
				$current_first = $first[$key];
				$current_firstprice = $firstprice[$key];
				$current_second = $second[$key];
				$current_secondprice = $secondprice[$key];
				$current_nopostage = $nopostage[$key];
				if (empty($value) || empty($current_first) || empty($current_firstprice) || empty($current_second) || empty($current_secondprice) || empty($current_nopostage)) {
					itoast('请完善配送信息', '', 'error');
				}
				$content[$key] = array(
					'city_area' => $value,
					'first' => $current_first,
					'firstprice' => $current_firstprice,
					'second' => $current_second,
					'secondprice' => $current_secondprice,
					'nopostage' => $current_nopostage,
				);
			}
		}
		$dispatch_data['content'] = iserializer($content);
		$current_dispatch = pdo_get('storex_dispatch', array('id' => $id), array('id'));
		if (empty($current_dispatch)) {
			$dispatch_data['uniacid'] = $_W['uniacid'];
			$dispatch_data['storeid'] = $storeid;
			pdo_insert('storex_dispatch', $dispatch_data);
		} else {
			pdo_update('storex_dispatch', $dispatch_data, array('id' => $current_dispatch['id']));
		}
		itoast('编辑成功', $this->createWebUrl('shop_dispatch', array('storeid' => $storeid)), 'success');
	}
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete('storex_dispatch', array('id' => $id, 'uniacid' => $_W['uniacid']));
	itoast('删除成功!', referer(), 'success');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$dispatch_info = pdo_get('storex_dispatch', array('id' => $id), array('id'));
	if (empty($dispatch_info)) {
		itoast('配送信息不存在', '', 'error');
	}
	pdo_update('storex_dispatch', array('status' => $_GPC['status']), array('id' => $id));
	itoast('状态修改成功', '', 'success');
}

if ($op == 'default') {
	$id = intval($_GPC['id']);
	$dispatch_info = pdo_get('storex_dispatch', array('id' => $id), array('id'));
	if (empty($dispatch_info)) {
		itoast('配送信息不存在', '', 'error');
	}
	pdo_update('storex_dispatch', array('isdefault' => 2), array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));
	pdo_update('storex_dispatch', array('isdefault' => 1), array('id' => $id));
	itoast('状态修改成功', '', 'success');
}
include $this->template('store/shop_dispatch');