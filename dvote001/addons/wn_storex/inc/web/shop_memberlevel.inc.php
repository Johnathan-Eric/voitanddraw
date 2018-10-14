<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('display', 'edit', 'delete', 'deleteall', 'showall', 'status', 'set_default', 'discount_set');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$storeid = intval($_GPC['storeid']);
$store = $_W['wn_storex']['store_info'];

if ($op == 'display') {
	$levels = pdo_getall('storex_member_level', array('uniacid' => intval($_W['uniacid']), 'storeid' => $storeid), array(), '', 'level ASC');
}

if ($op == 'edit') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$level = pdo_get('storex_member_level', array('id' => $id));
		if (empty($level)) {
			itoast('该会员组不存在或是已经删除', referer(), 'error');
		}
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			itoast('请输入会员组名称', referer(), 'error');
		}
		if (mb_strlen($_GPC['title'], "utf-8") > 7) {
			itoast('会员组名称不要超过8个字符', referer(), 'error');
		}
		if (intval($_GPC['ask']) <= 0) {
			itoast('升级条件', referer(), 'error');
		}
		$insert = array(
			'uniacid' => intval($_W['uniacid']),
			'storeid' => $storeid,
			'title' => trim($_GPC['title']),
			'ask' => intval($_GPC['ask']),
			'level' => intval($_GPC['level']),
			'status' => intval($_GPC['status']),
		);
		if (!empty($_GPC[discount])) {
			$insert['discount'] = sprintf("%.1f", $_GPC[discount]);
		}
		if (empty($id)) {
			pdo_insert('storex_member_level', $insert);
			$msg = '添加成功！';
		} else {
			pdo_update('storex_member_level', $insert, array('id' => $id));
			$msg = '标签信息更新成功！';
		}
		itoast($msg, $this->createWebUrl('shop_memberlevel', array('storeid' => $storeid)), 'success');
	}
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		pdo_delete('storex_member_level', array('id' => $id, 'uniacid' => intval($_W['uniacid'])));
		message('删除成功！', referer(), 'success');
	}
	message('操作失败！', referer(), 'error');
}

if ($op == 'deleteall') {
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			pdo_delete('storex_member_level', array('id' => $id, 'uniacid' => intval($_W['uniacid'])));
		}
		message(error(0, '删除成功！'), '', 'ajax');
	}
	message(error(-1, '删除失败！'), '', 'ajax');
}

if ($op == 'showall') {
	if ($_GPC['show_name'] == 'showall') {
		$show_status = 1;
	} else {
		$show_status = 2;
	}
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			if (!empty($id)) {
				pdo_update('storex_member_level', array('status' => $show_status), array('id' => $id));
			}
		}
		message(error(0, '操作成功！'), '', 'ajax');
	}
	message(error(-1, '操作失败！'), '', 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		message('参数错误！', referer(), 'error');
	}
	$status = pdo_update('storex_member_level', array('status' => $_GPC['status']), array('id' => $id));
	if (!empty($status)) {
		message('设置成功！', referer(), 'success');
	} else {
		message('操作失败！', referer(), 'error');
	}
}

if ($op == 'discount_set') {
	$level = pdo_getall('storex_member_level', array('uniacid' => $_W['uniacid'], 'status' => 1, 'storeid' => $storeid), array(), '', array('level ASC'));
	$discount_set = pdo_get('storex_discount_set', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid));
	$discounts = array();
	if (!empty($discount_set['discount'])) {
		$discounts = iunserializer($discount_set['discount']);
	}
	if (checksubmit('submit')) {
		$data = array(
			'uplevel' => intval($_GPC['uplevel']),
			'status' => intval($_GPC['status']),
		);
		if (!empty($_GPC['discount'])) {
			$data['discount'] = iserializer($_GPC['discount']);
		}
 		if (!empty($discount_set)) {
			pdo_update('storex_discount_set', $data, array('id' => $discount_set['id']));
		} else {
			$data['uniacid'] = intval($_W['uniacid']);
			$data['storeid'] = intval($storeid);
			pdo_insert('storex_discount_set', $data);
		}
		itoast('设置成功', '', 'success');
	}
}

if ($op == 'set_default') {
	$level_id = intval($_GPC['id']);
	if (pdo_get('storex_member_level', array('id' => $level_id))) {
		$default_level = pdo_get('storex_member_level', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'g_default' => 1));
		if (!empty($default_level)) {
			pdo_update('storex_member_level', array('g_default' => 2), array('id' => $default_level['id']));
		}
		if (pdo_update('storex_member_level', array('g_default' => 1), array('id' => $level_id))) {
			itoast('设置成功', '', 'success');
		} else {
			itoast('设置失败', '', 'error');
		}
	}
	itoast('设置失败', '', 'error');
}
include $this->template('store/shop_memberlevel');