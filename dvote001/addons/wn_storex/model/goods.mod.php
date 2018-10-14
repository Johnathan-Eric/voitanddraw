<?php

function goods_get_list_common($condition, $pageinfo) {
	global $_W, $_GPC;
	$sql = '';
	$pindex = $pageinfo['pindex'];
	$psize = $pageinfo['psize'];
	$storeid = $_W['wn_storex']['store_info']['id'];
	$sql .= ' AND store_base_id = ' . $storeid;
	if (!empty($condition['title'])) {
		$sql .= ' AND r.title LIKE :keywords';
		$params[':keywords'] = "%{$condition['title']}%";
	}
	if (!empty($condition['category_id'])) {
		$category_id = intval($condition['category_id']);
		$sql .= ' AND ( r.pcate = :category_id OR r.ccate = :category_id)';
		$params[':category_id'] = $category_id;
	}
	if (!empty($condition['recycle'])) {
		$sql .= ' AND r.recycle = :recycle';
		$params[':recycle'] = $condition['recycle'];
	}
	$params[':weid'] = $_W['uniacid'];
	$list = pdo_fetchall("SELECT r.*, h.title AS store_title FROM " . tablename('storex_goods') . " r LEFT JOIN " . tablename('storex_bases') . " h ON r.store_base_id = h.id WHERE r.weid = :weid $sql ORDER BY h.id, r.sortid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_goods') . " r LEFT JOIN " . tablename('storex_bases') . " h ON r.store_base_id = h.id WHERE r.weid = :weid $sql", $params);
	return array('list' => $list, 'total' => $total);
}

function goods_get_list_hotel($condition, $pageinfo) {
	global $_W, $_GPC;
	$sql = '';
	$pindex = $pageinfo['pindex'];
	$psize = $pageinfo['psize'];
	$storeid = $_W['wn_storex']['store_info']['id'];
	$sql .= ' AND store_base_id = ' . $storeid;
	if (!empty($condition['title'])) {
		$sql .= ' AND r.title LIKE :keywords';
		$params[':keywords'] = "%{$condition['title']}%";
	}
	if (!empty($condition['category_id'])) {
		$category_id = intval($condition['category_id']);
		$sql .= ' AND ( r.pcate = :category_id OR r.ccate = :category_id)';
		$params[':category_id'] = $category_id;
	}
	if (!empty($condition['recycle'])) {
		$sql .= ' AND r.recycle = :recycle';
		$params[':recycle'] = $condition['recycle'];
	}
	$params[':weid'] = $_W['uniacid'];
	$list = pdo_fetchall("SELECT r.*, h.title AS store_title FROM " . tablename('storex_room') . " r LEFT JOIN " . tablename('storex_bases') . " h ON r.store_base_id = h.id WHERE r.weid = :weid $sql ORDER BY h.id, r.sortid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_room') . " r LEFT JOIN " . tablename('storex_bases') . " h ON r.store_base_id = h.id WHERE r.weid = :weid $sql", $params);
	return array('list' => $list, 'total' => $total);
}

function goods_info($info) {
	if (!empty($info['goodsid'])) {
		if (!empty($info['spec_id'])) {
			$good = pdo_get('storex_spec_goods', array('id' => $info['spec_id']));
			if (empty($good)) {
				$good = pdo_get('storex_goods', array('id' => $info['goodsid']));
			}
		} else {
			$good = pdo_get('storex_goods', array('id' => $info['goodsid']));
		}
	} else {
		if (!empty($info['good'])) {
			$cart_good = explode('|', $info['good']);
			if ($cart_good['2'] == 1) {
				$good = pdo_get('storex_spec_goods', array('id' => $cart_good['0']));
			} elseif ($cart_good['2'] == 2) {
				$good = pdo_get('storex_goods', array('id' => $cart_good['0']));
			}
		}
	}
	return $good;
}

function goods_member_get_discount($storeid) {
	global $_W;
	$member = pdo_get('storex_member', array('weid' => $_W['uniacid'], 'from_user' => $_W['openid']), array('id', 'member_group'));
	$discount = 10;
	if (!empty($member)) {
		$member['member_group'] = iunserializer($member['member_group']);
		$condition = array('storeid' => $storeid, 'uniacid' => $_W['uniacid']);
		if (!empty($member['member_group'][$storeid])) {
			$condition['id'] = $member['member_group'][$storeid];
			$memberlevel = pdo_get('storex_member_level', $condition);
		} else {
			$condition['g_default'] = 1;
			$memberlevel = pdo_get('storex_member_level', $condition);
		}
		if (!empty($memberlevel) && !empty($memberlevel['discount'])) {
			$discount = $memberlevel['discount'];
		}
	}
	return $discount * 0.1;
}
 