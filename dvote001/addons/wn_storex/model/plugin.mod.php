<?php

/**
 *
 * @param string $condition_str
 * @param int $days
 * @return int or array
 */
function plugin_day_statis($condition_str, $days, $type = '') {
	if (!empty($days)) {
		$data = array();
		$day_start = strtotime(date('Y-m-d'));
		$day_end = $day_start + 86399;
		if ($days == 1) {
			$condition_str .= " AND time >= " . $day_start . " AND time <= " . $day_end;
				
		} elseif ($days == -1) {
			$day_start -= 86400;
			$day_end -= 86400;
			$condition_str .= " AND time >= " . $day_start . " AND time <= " . $day_end;
		} elseif ($days == 7) {
			$day_start -= 7 * 86400;
			$condition_str .= " AND time >= " . $day_start . " AND time <= " . $day_end;
		} elseif ($days == 30) {
			$day_start -= 30 * 86400;
			$condition_str .= " AND time >= " . $day_start . " AND time <= " . $day_end;
		}
		$data['nums'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " WHERE " . $condition_str);
		$money = pdo_fetch("SELECT SUM(sum_price) AS money FROM " . tablename('storex_order') . " WHERE " . $condition_str);
		$data['money'] = empty($money['money']) ? '0.00' : $money['money'];
		return $data;
	} else {
		if ($type == 'group') {
			return pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_plugin_group') . " WHERE " . $condition_str);
		}
		if ($type == 'bargain') {
			return pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_plugin_bargain_list') . " WHERE " . $condition_str);
		}
	}
}

function plugin_exists($plugin_name) {
	$plugin_list = plugin_list();
	return !empty($plugin_list[$plugin_name]) && check_ims_version() ? true : false;
}

function plugin_list() {
	load()->model('module');
	$plugin_list = module_get_plugin_list('wn_storex');
	if (!empty($plugin_list) && is_array($plugin_list)) {
		foreach ($plugin_list as $name => $plugin) {
			$plugins[$name] = true;
		}
	}
	return $plugins;
}

function plugin_check_group($order) {
	global $_W;
	if ($_W['openid'] != $order['openid']) {
		return ;
	}
	$activity_goods = pdo_get('storex_plugin_activity_goods', array('id' => $order['group_goodsid']));
	if (!empty($activity_goods)) {
		$group_activity = pdo_get('storex_plugin_group_activity', array('id' => $activity_goods['group_activity']));
		if (!empty($group_activity) && $group_activity['starttime'] <= TIMESTAMP && TIMESTAMP < $group_activity['endtime']) {
			if (!empty($order['group_id'])) {
				$group = pdo_get('storex_plugin_group', array('id' => $order['group_id']));
				if (!empty($group)) {
					if ($group['head'] == $_W['openid']) {
						return ;
					}
					if (!empty($group['member'])) {
						$group['member'] = iunserializer($group['member']);
						if (count($group['member']) < ($activity_goods['number'] - 1)) {
							$group['member'][] = $order['openid'];
						}
					} else {
						$group['member'] = array();
						$group['member'][] = $order['openid'];
					}
					if (count($group['member']) == ($activity_goods['number'] - 1)) {
						$group['over'] = 1;
					}
					$group['member'] = iserializer($group['member']);
					pdo_update('storex_plugin_group', $group, array('id' => $order['group_id']));
				}
			} else {
				$group = array(
					'uniacid' => $_W['uniacid'],
					'storeid' => $order['hotelid'],
					'group_activity_id' => $group_activity['id'],
					'activity_goodsid' => $order['group_goodsid'],
					'head' => $order['openid'],
					'member' => '',
					'start_time' => time(),
				);
				pdo_insert('storex_plugin_group', $group);
				$group_id = pdo_insertid();
				pdo_update('storex_order', array('group_id' => $group_id), array('id' => $order['id']));
			}
		}
	}
}

function plugin_group_status($group_id = '') {
	global $_GPC;
	if (empty($_GPC['group_id'])) {
		if (empty($group_id)) {
			return;
		}
	} else {
		$group_id = intval($_GPC['group_id']);
	}
	$group_info = pdo_get('storex_plugin_group', array('id' => $group_id));
	$activity_goods = pdo_get('storex_plugin_activity_goods', array('id' => $group_info['activity_goodsid']));
	if (empty($group_info)) {
		return error(-1, '该拼团不存在');
	}
	if (empty($activity_goods)) {
		return error(-1, '该活动商品不存在');
	}
	$group_activity = pdo_get('storex_plugin_group_activity', array('id' => $activity_goods['group_activity']));
	if (empty($group_activity)) {
		return error(-1, '该活动不存在');
	}
	if ($group_activity['starttime'] > TIMESTAMP) {
		return error(-1, '该活动未开始');
	}
	if ($group_activity['endtime'] < TIMESTAMP) {
		return error(-1, '该活动已经结束了');
	}
	$group_info['member'] = iunserializer($group_info['member']);
	if (count($group_info['member']) == ($activity_goods['number'] - 1)) {
		return error(-1, '该拼团名额已满');
	}
}