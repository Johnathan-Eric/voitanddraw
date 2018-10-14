<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
load()->model('module');
load()->model('permission');

$ops = array('display', 'delete', 'deleteall', 'showall', 'status', 'assign_store', 'assign', 'copystore');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$actions_permit = check_user_permit(array('wn_storex_menu_storeprofile'));

if ($op == 'display') {
	$storeids = array();
	$clerk = pdo_getall('storex_clerk', array('weid' => intval($_W['uniacid']), 'userid' => intval($_W['uid'])), array('id', 'storeid'), 'storeid');
	if (!empty($clerk)) {
		$storeids = array_keys($clerk);
	}
	$founders = explode(',', $_W['config']['setting']['founder']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$where = ' WHERE `weid` = :weid';
	$params = array(':weid' => $_W['uniacid']);
	$condition = array('weid' => $_W['uniacid']);
	if (!empty($storeids)) {
		$where .= ' AND `id` in (' . implode(',', $storeids) . ')';
		$condition['id'] = $storeids;
	}
	if (!empty($_GPC['keywords'])) {
		$where .= ' AND `title` LIKE :title';
		$params[':title'] = "%{$_GPC['keywords']}%";
		$condition['title LIKE'] = "%{$_GPC['keywords']}%";
	}
	$sql = 'SELECT COUNT(*) FROM ' . tablename('storex_bases') . $where;
	$total = pdo_fetchcolumn($sql, $params);
	
	if ($total > 0) {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$list = pdo_getall('storex_bases', $condition, array(), '', 'displayorder DESC', ($pindex - 1) * $psize . ',' . $psize);
		$pager = pagination($total, $pindex, $psize);
		if (!empty($list)) {
			foreach ($list as $key => &$value) {
				$value['store_entry'] = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=wn_storex&do=display&id=' . $value['id'] . '#/StoreIndex/' . $value['id'];
				$value['mc_entry'] = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=wn_storex&do=display&id=' . $value['id'] . '#/Home/Index';
			}
			unset($value);
		}
	}
	
	if (!empty($_GPC['export'])) {
		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF";
		/* 输出表头 */
		$filter = array(
			'title' => '酒店名称',
			'roomcount' => '房间数',
			'phone' => '电话',
			'status' => '状态',
		);
		foreach ($filter as $key => $value) {
			$html .= $value . "\t,";
		}
		$html .= "\n";
		if (!empty($list)) {
			$status = array('隐藏', '显示');
			foreach ($list as $key => $value) {
				foreach ($filter as $index => $title) {
					if ($index != 'status') {
						$html .= $value[$index] . "\t, ";
					} else {
						$html .= $status[$value[$index]] . "\t, ";
					}
				}
				$html .= "\n";
			}
		}
		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=全部数据.csv");
		echo $html;
		exit();
	}
	include $this->template('hotel');
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	$store = pdo_get('storex_bases', array('id' => $id), array('store_type'));
	$table = gettablebytype($store['store_type']);
	pdo_delete($table, array('store_base_id' => $id, 'weid' => $_W['uniacid']));
	pdo_delete('storex_bases', array('id' => $id, 'weid' => $_W['uniacid']));
	pdo_delete('storex_categorys', array('store_base_id' => $id, 'weid' => $_W['uniacid']));
	itoast('店铺信息删除成功!', 'referer', 'success');
}

if ($op == 'deleteall') {
	foreach ($_GPC['idArr'] as $k => $id) {
		$id = intval($id);
		$id = intval($_GPC['id']);
		$store = pdo_get('storex_bases', array('id' => $id), array('store_type'));
		$table = gettablebytype($store['store_type']);
		pdo_delete($table, array('store_base_id' => $id, 'weid' => $_W['uniacid']));
		pdo_delete('storex_bases', array('id' => $id, 'weid' => $_W['uniacid']));
		pdo_delete('storex_categorys', array("store_base_id" => $id, 'weid' => $_W['uniacid']));
	}
	message(error(0, '店铺信息删除成功！'), '', 'ajax');
}

if ($op == 'showall') {
	if ($_GPC['show_name'] == 'showall') {
		$show_status = 1;
	} else {
		$show_status = 0;
	}
	foreach ($_GPC['idArr'] as $k => $id) {
		$id = intval($id);
		if (!empty($id)) {
			pdo_update('storex_bases', array('status' => $show_status), array('id' => $id));
		}
	}
	message(error(0, '操作成功！'), '', 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		itoast('抱歉，传递的参数错误！', '', 'error');
	}
	$temp = pdo_update('storex_bases', array('status' => $_GPC['status']), array('id' => $id));
	if ($temp == false) {
		itoast('抱歉，刚才操作数据失败！', '', 'error');
	} else {
		itoast('状态设置成功！', 'referer', 'success');
	}
}

if ($op == 'assign_store') {
	$user_permissions = module_clerk_info('wn_storex');
	$current_module_permission = module_permission_fetch('wn_storex');
	$uids = array_keys($user_permissions);
	$clerks = pdo_getall('storex_clerk', array('userid' => $uids, 'weid' => $_W['uniacid']), array('id', 'userid', 'storeid'));
	$user_store = array();
	if (!empty($clerks) && is_array($clerks)) {
		foreach ($clerks as $clerk) {
			$user_store[$clerk['userid']][] = $clerk['storeid'];
		}
	}
	$permission_name = array();
	if (!empty($current_module_permission)) {
		foreach ($current_module_permission as $key => $permission) {
			$permission_name[$permission['permission']] = $permission['title'];
		}
	}
	if (!empty($user_permissions)) {
		foreach ($user_permissions as $key => &$permission) {
			if (!empty($permission['permission'])) {
				$permission['permission'] = explode('|', $permission['permission']);
				foreach ($permission['permission'] as $k => $val) {
					$permission['permission'][$val] = $permission_name[$val];
					unset($permission['permission'][$k]);
				}
			}
		}
		unset($permission);
	}
	$stores = pdo_getall('storex_bases', array('weid' => intval($_W['uniacid'])), array('id', 'title'));
	include $this->template('assign_store');
}

if ($op == 'assign') {
	$storeids = $_GPC['stores'];
	$uid = intval($_GPC['id']);
	if (!empty($uid)) {
		$user_permissions = module_clerk_info('wn_storex');
		$user = user_single($uid);
		$permission = $user_permissions[$uid]['permission'];
	} else {
		message(error(-1, '参数错误！'), '', 'ajax');
	}
	if (!empty($storeids)) {
		$clerks = pdo_getall('storex_clerk', array('userid' => $uid, 'weid' => intval($_W['uniacid'])), array('id', 'storeid'));
		if (!empty($clerks) && is_array($clerks)) {
			$exist_storeid = array();
			foreach ($clerks as $clerk) {
				if (!in_array($clerk['storeid'], $storeids)) {
					pdo_delete('storex_clerk', array('id' => $clerk['id']));
				}
				$exist_storeid[] = $clerk['storeid'];
			}
			$storeids = array_diff($storeids, $exist_storeid);
		}
		if (!empty($storeids) && is_array($storeids)) {
			foreach ($storeids as $storeid) {
				$insert = array(
					'weid' => intval($_W['uniacid']),
					'userid' => $uid,
					'createtime' => TIMESTAMP,
					'status' => 1,
					'username' => $user['username'],
					'permission' => $permission,
					'storeid' => $storeid,
				);
				pdo_insert('storex_clerk', $insert);
			}
		}
	} else {
		pdo_delete('storex_clerk', array('userid' => $uid, 'weid' => intval($_W['uniacid'])));
	}
	message(error(0, '分配成功'), '', 'ajax');
}

if ($op == 'copystore') {
	if (empty($permit)) {
		itoast('不是主管理，不能复制店铺', '', 'error');
	}
	$id = intval($_GPC['id']);
	$store = pdo_get('storex_bases', array('id' => $id));
	if (!empty($store)) {
		unset($store['id']);
		pdo_insert('storex_bases', $store);
		if (pdo_insertid()) {
			itoast('成功复制店铺', 'referer', 'success');
		} else {
			itoast('复制店铺失败', '', 'error');
		}
	} else {
		itoast('店铺不存在', '', 'error');
	}
}
