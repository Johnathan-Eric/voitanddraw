<?php 

//根据日期和数量获取可预定的房型
function room_category_status($goods_list, $search_data) {
	global $_GPC,$_W;
	$btime = $search_data['btime'];
	$etime = $search_data['etime'];
	$num = $search_data['nums'];
	if (!empty($btime) && !empty($etime) && !empty($num)) {
		if ($num <= 0 || strtotime($etime) < strtotime($btime) || strtotime($btime) < strtotime('today')) {
			wmessage(error(-1, '数量不能是零'), '', 'ajax');
		}
		if (strtotime($etime) < strtotime($btime)) {
			wmessage(error(-1, '结束时间不能小于开始时间'), '', 'ajax');
		}
		if (strtotime($btime) < strtotime('today')) {
			wmessage(error(-1, '开始时间不能小于当天'), '', 'ajax');
		}
	} else {
		$num = 1;
		$btime = date('Y-m-d');
		$etime = date('Y-m-d', time() + 86400);
	}
	$days = ceil((strtotime($etime) - strtotime($btime)) / 86400);
	$sql = "SELECT * FROM " . tablename('storex_room_price') . " WHERE weid = :weid AND roomdate >= :btime AND roomdate <= :etime ORDER BY roomdate ASC";
	$modify_recored = pdo_fetchall($sql, array(':weid' => intval($_W['uniacid']), ':btime' => strtotime($btime), ':etime' => strtotime($etime)));
	if (!empty($modify_recored)) {
		foreach ($modify_recored as $value) {
			foreach ($goods_list as &$info) {
				if ($value['roomid'] == $info['id'] && $value['hotelid'] == $info['store_base_id']) {
					if (isset($info['max_room']) && $info['max_room'] == 0) {
						$info['room_counts'] = 0;
						continue;
					}
					if ($value['status'] == 1) {
						if ($value['num'] == -1) {
							if (empty($info['max_room']) && $info['max_room'] != 0) {
								$info['max_room'] = 8;
								$info['room_counts'] = '不限';
							}
						} else {
							if ($value['num'] > 8 && $value['num'] > $info['max_room']) {
								$info['max_room'] = 8;
							} elseif ($value['num'] < $info['max_room'] || !isset($info['max_room'])) {
								$info['max_room'] = $value['num'];
							}
							$info['room_counts'] = $value['num'];
						}
					} else {
						$info['max_room'] = 0;
						$info['room_counts'] = 0;
					}
				}
			}
		}
	}
	foreach ($goods_list as $k => $val) {
		if (!isset($val['max_room'])) {
			$val['max_room'] = 8;
			$val['room_counts'] = '不限';
		} elseif (!empty($num) && $val['max_room'] < $num) {
			unset($goods_list[$k]);
			continue;
		}
		$goods_list[$k] = room_params($val);
	}
	$goods_list = array_values($goods_list);
	return $goods_list;
}

function room_params($info) {
	$info['params'] = '';
	if ($info['bed_show'] == 1) {
		$info['params'] = "床位(" . $info['bed'] . ")";
	}
	if ($info['floor_show'] == 1) {
		if (!empty($info['params'])) {
			$info['params'] .= " | 楼层(" . $info['floor'] . ")";
		} else {
			$info['params'] = "楼层(" . $info['floor'] . ")";
		}
	}
	return $info;
}

//计算所选房型的总价
function room_calcul_sumprice($dates, $search_data, $goods_info) {
	$prices = array('oprice' => $goods_info['oprice'], 'cprice' => $goods_info['cprice']);
	$goods_info = room_special_price($goods_info, $search_data, false);
	$sumprice = 0;
	$noexist_date = 0;
	$exist_date = 0;
	$price_detail = array();
	if (!empty($dates) && is_array($dates)) {
		foreach ($dates as $date) {
			if (!empty($goods_info['price_list']) && !empty($goods_info['price_list'][$date['date']])) {
				$sumprice += $goods_info['price_list'][$date['date']]['cprice'];
				$exist_date += 1;
				$price_detail[] = array(
						'date' => $date['date'],
						'oprice' => $goods_info['price_list'][$date['date']]['oprice'],
						'cprice' => $goods_info['price_list'][$date['date']]['cprice'],
				);
			} else {
				$noexist_date += 1;
				$price_detail[] = array(
						'date' => $date['date'],
						'oprice' => $prices['oprice'],
						'cprice' => $prices['cprice'],
				);
			}
		}
	}
	if (($exist_date + $noexist_date) < count($dates)) {
		$noexist_date += count($dates) - ($exist_date + $noexist_date);
	}
	$sumprice += $noexist_date * $prices['cprice'];
	if (empty($search_data['nums'])) {
		$search_data['nums'] = 1;
	}
	$goods_info['sum_price'] = ($sumprice + $goods_info['service'] * count($dates)) * $search_data['nums'];
	$goods_info['price_list'] = $price_detail;
	return $goods_info;
}

//根据信息获取房型的某一天的价格
function room_special_price($goods, $search_data = array(), $plural = true) {
	global $_W;
	if (!empty($goods)) {
		if (!empty($search_data) && !empty($search_data['btime']) && !empty($search_data['etime']) && !empty($search_data['nums'])) {
			$btime = strtotime($search_data['btime']);
			$etime = strtotime($search_data['etime']);
		} else {
			$search_data['btime'] = date('Y-m-d');
			$search_data['etime'] = date('Y-m-d', TIMESTAMP + 86400);
			$search_data['nums'] = 1;
			$btime = strtotime(date('Y-m-d'));
			$etime = $btime + 86400;
		}
		$condition = '';
		$params = array(':weid' => $_W['uniacid'], ':btime' => $btime, ':etime' => $etime);
		if (empty($plural)) {
			$condition = ' AND roomid = :roomid ';
			$params[':roomid'] = $goods['id'];
		}
		$sql = "SELECT * FROM " . tablename('storex_room_price') . " WHERE `weid` = :weid AND `roomdate` >= :btime AND `roomdate` < :etime {$condition} ORDER BY roomdate ASC";
		$room_price_list = pdo_fetchall($sql, $params);
		$edit_price_list = array();
		if (!empty($room_price_list) && is_array($room_price_list)) {
			foreach ($room_price_list as $val) {
				$edit_price_list[$val['roomid']][$val['thisdate']] = $val;
			}
		}
		if (!empty($plural)) {
			foreach ($goods as $key => $val) {
				$goods[$key]['price_list'] = array();
				if (!empty($edit_price_list[$val['id']]) && !empty($edit_price_list[$val['id']][$search_data['btime']])) {
					$goods[$key]['oprice'] = $edit_price_list[$val['id']][$search_data['btime']]['oprice'];
					$goods[$key]['cprice'] = $edit_price_list[$val['id']][$search_data['btime']]['cprice'];
					if ($edit_price_list[$val['id']][$search_data['btime']]['num'] == -1) {
						$goods[$key]['max_room'] = 8;
					} else {
						$goods[$key]['max_room'] = $edit_price_list[$val['id']][$search_data['btime']]['num'];
					}
					$goods[$key]['price_list'] = $edit_price_list[$val['id']];
				} else {
					$goods[$key]['max_room'] = 8;
				}
			}
		} else {
			$goods['price_list'] = array();
			if (!empty($edit_price_list[$goods['id']])) {
				$goods['oprice'] = $edit_price_list[$goods['id']][$search_data['btime']]['oprice'];
				$goods['cprice'] = $edit_price_list[$goods['id']][$search_data['btime']]['cprice'];
				if ($edit_price_list[$goods['id']][$search_data['btime']]['num'] == -1) {
					$goods['max_room'] = 8;
				} else {
					$goods['max_room'] = $edit_price_list[$goods['id']][$search_data['btime']]['num'];
				}
				$goods['price_list'] = $edit_price_list[$goods['id']];
			} else {
				$goods['max_room'] = 8;
			}
		}
	}
	return $goods;
}

function room_check_nums($dates, $search_data, $goods_info) {
	$sql = 'SELECT `id`, `roomdate`, `num`, `status` FROM ' . tablename('storex_room_price') . ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND `roomdate` < :etime AND `status` = :status';
	$params = array(':roomid' => $goods_info['roomid'], ':btime' => strtotime($search_data['btime']), ':etime' => strtotime($search_data['etime']), ':status' => '1');
	$room_date_list = pdo_fetchall($sql, $params);
	$max_room = 8;
	$list = array();
	if (!empty($room_date_list) && is_array($room_date_list)) {
		for($i = 0; $i < count($dates); $i++) {
			$k = $dates[$i]['time'];
			foreach ($room_date_list as $p_key => $p_value) {
				// 判断价格表中是否有当天的数据
				if ($p_value['roomdate'] == $k) {
					if ($p_value['num'] == -1) {
						$max_room = 8;
					} else {
						$room_num = $p_value['num'];
						$list['date'] =  $dates[$i]['date'];
						if (empty($room_num) || $room_num < 0) {
							$max_room = 0;
							$list['num'] = 0;
						} elseif ($room_num > 0 && $room_num <= $max_room) {
							$max_room = $room_num;
							$list['num'] =  $room_num;
						} elseif ($room_num > 0 && $room_num > $max_room) {
							$list['num'] =  $max_room;
						}
					}
					break;
				}
			}
			if ($max_room == 0 || $max_room < $search_data['nums']) {
				wmessage(error(-1, '房间数量不足,请选择其他房型或日期!'), '', 'ajax');
			}
		}
	}
}

function room_check_assign($order, $roomids, $insert = false) {
	global $_W;
	$date= array();
	if (empty($roomids)) {
		return false;
	}
	$roomassign_record = pdo_getall('storex_room_assign', array('storeid' => $order['hotelid'], 'roomid' => $order['roomid'], 'roomitemid' => $roomids, 'time >=' => $order['btime'], 'time <' => $order['etime']));
	$status = true;
	if (!empty($roomassign_record)) {
		return false;
	}
	if (!empty($insert) && !empty($status)) {
		if ($order['day'] > 0 && !empty($roomids)) {
			foreach ($roomids as $roomid) {
				for ($i = 0; $i < $order['day']; $i ++) {
					$insert_data = array(
						'uniacid' => $_W['uniacid'],
						'storeid' => $order['hotelid'],
						'roomid' => $order['roomid'],
						'roomitemid' => $roomid,
						'time' => $order['btime'] + $i * 86400,
					);
					pdo_insert('storex_room_assign', $insert_data);
				}
			}
		}
	}
	return $status;
}

function room_delete_assign($order, $roomitemid = '') {
	if (!empty($order['roomitemid'])) {
		$roomitemid = explode(',', $order['roomitemid']);
	}
	if (!empty($roomitemid)) {
		pdo_delete('storex_room_assign', array('storeid' => $order['hotelid'], 'roomid' => $order['roomid'], 'roomitemid' => $roomitemid, 'time >=' => $order['btime'], 'time <' => $order['etime']));
	}
}

//获取房型某天的记录
function roomPrice($hotelid, $roomid, $date) {
	global $_W;
	$btime = strtotime($date);
	$roomprice = pdo_get('storex_room_price', array('weid' => intval($_W['uniacid']), 'hotelid' => $hotelid, 'roomid' => $roomid, 'roomdate' => $btime));
	if (empty($roomprice)) {
		$room = pdo_get('storex_room', array('store_base_id' => $hotelid, 'id' => $roomid, 'weid' => intval($_W['uniacid'])));
		$roomprice = array(
			"weid" => $_W['uniacid'],
			"hotelid" => $hotelid,
			"roomid" => $roomid,
			"oprice" => $room['oprice'],
			"cprice" => $room['cprice'],
			"roomdate" => strtotime($date),
			"thisdate" => $date,
			"num" => "-1",
			"status" => 1,
		);
	}
	return $roomprice;
}
