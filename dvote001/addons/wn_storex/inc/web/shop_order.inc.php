<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
mload()->model('card');
mload()->model('order');
mload()->model('log');
mload()->model('room');
mload()->model('plugin');

$ops = array('display', 'edit', 'delete', 'deleteall', 'edit_msg','edit_price', 'print_order', 'check_print_plugin', 'assign_room');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$storeid = intval($_GPC['storeid']);
order_check_shipped($storeid);
$store = $_W['wn_storex']['store_info'];
$store_type = $store['store_type'];
$table = gettablebytype($store_type);

$roomid = intval($_GPC['roomid']);
if (!empty($roomid)) {
	$room = pdo_get($table, array('id' => $roomid), array('id', 'title', 'sold_num'));
}

if ($op == 'display') {
	$order_status = array(
		'0' => array('name' => '未确认', 'num' => 0, 'status' => 0),
		'-1' => array('name' => '已取消', 'num' => 0, 'status' => -1),
		'1' => array('name' => '已确认', 'num' => 0, 'status' => 1),
		'2' => array('name' => '已拒绝', 'num' => 0, 'status' => 2),
		'3' => array('name' => '已完成', 'num' => 0, 'status' => 3),
	);
	//拼团的订单不显示
	$condition_group = '';
	$group_params = array();
	$group_id = intval($_GPC['group_id']);
	if (pdo_fieldexists('storex_order', 'group_goodsid') && pdo_fieldexists('storex_order', 'group_id') && !empty($group_id)) {
		$condition_group .= ' AND group_id = :group_id';
		$group_params[':group_id'] = $group_id;
	}
	$group_params[':hotelid'] = $storeid;
	foreach ($order_status as $s => &$info) {
		$group_params[':status'] = $info['status'];
		$info['num'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " WHERE status = :status AND hotelid = :hotelid" . $condition_group, $group_params);
	}
	unset($info);
	$search_name = $_GPC['search_name'];
	$keyword = trim($_GPC['keyword']);
	$condition = $condition_group;
	$params = $group_params;
	if (!empty($search_name) && !empty($keyword)) {
		if ($search_name == 'roomtitle') {
			$condition .= ' AND r.title LIKE :roomtitle';
			$params[':roomtitle'] = "%{$keyword}%";
		} elseif ($search_name == 'realname') {
			$condition .= ' AND o.contact_name LIKE :realname';
			$params[':realname'] = "%{$keyword}%";
		} elseif ($search_name == 'mobile') {
			$condition .= ' AND o.mobile LIKE :mobile';
			$params[':mobile'] = "%{$keyword}%";
		} elseif ($search_name == 'ordersn') {
			$condition .= ' AND o.ordersn LIKE :ordersn';
			$params[':ordersn'] = "%{$keyword}%";
		}
	}
	if (!empty($storeid)) {
		$condition .= " AND o.hotelid = :hotelid";
		$params[':hotelid'] = $storeid;
	}
	if (!empty($roomid)) {
		$condition .= " AND o.roomid = :roomid";
		$params[':roomid'] = $roomid;
	}
	$condition .= " AND o.status = :status";
	$params[':status'] = intval($_GPC['status']);
	if (!empty($_GPC['paystatus'])) {
		$condition .= " and o.paystatus = :paystatus";
		if ($_GPC['paystatus'] == 2) {
			$params[':paystatus'] = 0;
		} else {
			$params[':paystatus'] = intval($_GPC['paystatus']);
		}
	}
	$date = $_GPC['date'];
	if (!empty($date)) {
		$start = strtotime($date['start']);
		if ($date['start'] == $date['end'] || $date['end'] == date('Y-m-d', TIMESTAMP)) {
			$end = strtotime($date['end']) + 86399;
		} else {
			$end = strtotime($date['end']);
		}
		$condition .= " AND o.time > " . $start . " AND o.time < " . $end;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	pdo_update('storex_order', array('status' => -1, 'newuser' => 0), array('time <' => (time() - 86400), 'weid' => $_W['uniacid'], 'paystatus' => 0, 'status !=' => 1, 'status !=' => 3));
	$field = '';
	if ($table == 'storex_room') {
		$field = ' , r.is_house ';
	}
	$params[':weid'] = $_W['uniacid'];
	$show_order_lists = pdo_fetchall("SELECT o.*, h.title AS hoteltitle, r.title AS roomtitle, r.thumb " . $field . " FROM " . tablename('storex_order') . " AS o LEFT JOIN " . tablename('storex_bases') . " h ON o.hotelid = h.id LEFT JOIN " . tablename($table) . " AS r ON r.id = o.roomid WHERE o.weid = :weid " . $condition . " ORDER BY o.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	order_uniontid($show_order_lists);
	if (!empty($show_order_lists) && is_array($show_order_lists)) {
		foreach ($show_order_lists as $key => $value) {
			if (!empty($value['roomid'])) {
				if ($value['is_package'] == 2) {
					$packageids[] = $value['roomid'];
				}
			} else {
				$value['cart'] = iunserializer($value['cart']);
				if (!empty($value['cart']) && is_array($value['cart'])) {
					foreach ($value['cart'] as $g) {
						$roomtitle = $g['good']['title'] . ',';
						$nums = $g['good']['title'] . '*' . $g['good']['buynums'] . ',';
					}
					$show_order_lists[$key]['roomtitle'] = trim($roomtitle, ',');
					$show_order_lists[$key]['nums'] = trim($nums, ',');
					$good_thumb = pdo_get('storex_goods', array('id' => $value['cart'][0]['good']['id']), array('thumb'));
					if (!empty($good_thumb)) {
						$value['thumb'] = $good_thumb['thumb'];
					}
				}
			}
			$show_order_lists[$key]['thumb'] = tomedia($value['thumb']);
		}
		$packageids = is_array($packageids) ? array_unique($packageids) : array();
		$sales_package = pdo_getall('storex_sales_package', array('uniacid' => $_W['uniacid'], 'id' => $packageids), array('title', 'sub_title', 'thumb', 'price', 'id'), 'id');
		foreach ($show_order_lists as $k => &$val) {
			if ($val['is_package'] == 2) {
				$val['roomtitle'] = $sales_package[$val['roomid']]['title'];
				$val['thumb'] = $sales_package[$val['roomid']]['thumb'];
			}
		}
		unset($val);
	}
	$printer_plugin_status = false;
	if (plugin_exists('wn_storex_plugin_printer')) {
		$printer_plugin_status = true;
		$printers = store_printers($storeid);
		if (!empty($printers) && is_array($printers)) {
			foreach ($printers as $k => $print) {
				if ($print['disabled'] == 2) {
					if ($print['status'] != 2) {
						unset($printers[$k]);
					}
				} elseif ($print['disabled'] == 1) {
					unset($printers[$k]);
				}
			}
		}
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_order') . " AS o LEFT JOIN " . tablename('storex_bases') . " AS h on o.hotelid = h.id LEFT JOIN " . tablename($table) . " AS r on r.id = o.roomid  WHERE o.weid = :weid $condition", $params);
	if (!empty($_GPC['export'])) {
		$export_order_lists = pdo_fetchall("SELECT o.*, h.title as hoteltitle, r.title AS roomtitle FROM " . tablename('storex_order') . " o LEFT JOIN " . tablename('storex_bases') . " AS h on o.hotelid = h.id LEFT JOIN " . tablename($table) . " AS r ON r.id = o.roomid  WHERE o.weid = :weid $condition ORDER BY o.id DESC" . ',' . $psize, $params);
		order_uniontid($export_order_lists);
		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF";
		/* 输出表头 */
		$filter = array(
			'ordersn' => '订单号',
			'uniontid' => '商户订单号',
			'hoteltitle' => '酒店',
			'roomtitle' => '房型',
			'name' => '预订人',
			'mobile' => '手机',
			'nums' => '预订数量',
			'sum_price' => '总价',
			'paytype' => '支付方式',
			'time' => '订单生成时间',
			'paystatus' => '订单状态'
		);
		if ($store_type == STORE_TYPE_HOTEL) {
			$filter['btime'] = '到店时间';
			$filter['etime'] = '离店时间';
		}
		foreach ($filter as $key => $title) {
			$html .= $title . "\t,";
		}
		$html .= "\n";
		foreach ($export_order_lists as $k => $v) {
			foreach ($filter as $key => $title) {
				if ($key == 'time') {
					$html .= date('Y-m-d H:i:s', $v[$key]) . "\t, ";
				} elseif ($key == 'btime') {
					$html .= date('Y-m-d', $v[$key]) . "\t, ";
				} elseif ($key == 'etime') {
					$html .= date('Y-m-d', $v[$key]) . "\t, ";
				} elseif ($key == 'paytype') {
					$html .= $v['paytype_text'] . "\t, ";
				} elseif ($key == 'paystatus') {
					$html .= $v['status_text'] . "\t, ";
				} else {
					$html .= $v[$key] . "\t, ";
				}
			}
			$html .= "\n";
		}
		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=全部数据.csv");
		echo $html;
		exit();
	}
	$pager = pagination($total, $pindex, $psize);
	include $this->template('store/shop_orderlist');
}

if ($op == 'edit') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$item = pdo_get('storex_order', array('id' => $id));
		if (empty($item)) {
			message(error(-1, '抱歉，订单不存在或是已经删除！'), '', 'ajax');
		}
		$uid = mc_openid2uid(trim($item['openid']));
		if (!empty($item['addressid'])) {
			$address_info = pdo_get('storex_member_address', array('uid' => $uid, 'id' => $item['addressid']), '', '', 'isdefault DESC');
			$address = '';
			if (!empty($address_info) && is_array($address_info)) {
				foreach ($address_info as $k => $v) {
					if (in_array($k, array('province', 'city', 'district', 'address')) && !empty($v)) {
						$address .= $v . '-';
					}
				}
				$address = trim($address, '-');
			}
		}
		if (!empty($item['wechat_address'])) {
			$item['wechat_address'] = iunserializer($item['wechat_address']);
			$field = array('provinceName', 'cityName', 'detailInfo');
			$address = '';
			foreach ($field as $f) {
				if (!empty($item['wechat_address'][$f])) {
					$address .= $item['wechat_address'][$f];
				}
			}
		}
		if (!empty($item['order_time'])) {
			$item['order_time'] = date('Y-m-d H:i', $item['order_time']);
		}
		$is_house = 2;
		if ($store_type == STORE_TYPE_HOTEL) {
			$good_info = pdo_get('storex_room', array('store_base_id' => $storeid, 'id' => $item['roomid']), array('id', 'is_house', 'thumb'));
			$is_house = $good_info['is_house'];
		}
		$paylog = pdo_get('core_paylog', array('uniacid' => $item['weid'], 'tid' => $item['id'], 'module' => 'wn_storex'), array('uniacid', 'uniontid', 'tid'));
		$refund_logs = pdo_get('storex_refund_logs', array('uniacid' => $_W['uniacid'], 'orderid' => $item['id']), array('id', 'status'));
		$actions = order_actions($item, $store_type, $is_house);
		if (!empty($paylog)) {
			$item['uniontid'] = $paylog['uniontid'];
			if (!empty($paylog['uniontid'])) {
				$refund = pdo_get('core_refundlog', array('uniacid' => $_W['uniacid'], 'uniontid' => $paylog['uniontid']));
				if (!empty($refund) && $refund['status'] == 1) {
					if (isset($actions['refund'])) {
						unset($actions['refund']);
					}
				}
			}
		}
		order_pay_text($item);
		if ($is_house == 1) {
			$room_list = pdo_getall('storex_room_items', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'roomid' => $item['roomid']));
			$room_item = pdo_getall('storex_room_items', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'id' => explode(',', $item['roomitemid'])), array('id', 'roomnumber'));
			if (!empty($room_list) && is_array($room_list)) {
				foreach ($room_list as $r => $val) {
					$show = room_check_assign($item, array($val['id']));
					if (empty($show)) {
						unset($room_list[$r]);
					}
				}
			}
			if (!empty($room_item) && is_array($room_item)) {
				$roomnum = array();
				foreach ($room_item as $roominfo) {
					$roomnum[] = $roominfo['roomnumber'];
				}
				$roomnumber = implode(',', $roomnum);
			}
			$bdate = date('Y-m-d', $item['btime']);
			$days = $item['day'];
			$edate = date('Y-m-d', $item['etime']);
			$dates = get_dates($bdate, $days);
			$search_data = array(
				'btime' => $bdate,
				'etime' => $edate,
				'nums' => $item['nums'],
			);
			$good_info = room_calcul_sumprice($dates, $search_data, $good_info);
			if (!empty($good_info['price_list']) && is_array($good_info['price_list'])) {
				foreach ($good_info['price_list'] as $k => $price_info) {
					if (empty($price_info['cprice'])) {
						unset($good_info['price_list'][$k]);
					}
				}
			}
		}
		if ($store_type != STORE_TYPE_HOTEL) {
			if (empty($item['roomid'])) {
				$item['cart'] = iunserializer($item['cart']);
				if (!empty($item['cart']) && is_array($item['cart'])) {
					$item['style'] = $item['cart'][0]['good']['title'];
					foreach ($item['cart'] as &$g) {
						if ($g['buyinfo'][2] == 3) {
							$package = pdo_get('storex_sales_package', array('id' => $g['buyinfo'][0]));
							$package['goodsids'] = iunserializer($package['goodsids']);
							$goods = pdo_getall('storex_goods', array('id' => $package['goodsids']), array('id', 'title'));
							$g['package']['good'] = '';
							if (!empty($goods)) {
								foreach ($goods as $v) {
									$g['package']['good'] .= $v['title'] . ',';
								}
								$g['package']['good'] = trim($g['package']['good'], ',');
							}
							$g['package']['price'] = $package['price'];
							$g['package']['express'] = $package['express'];
						}
					}
					unset($g);
				}
			} else {
				$item['spec'] = '';
				$item['spec_info'] = iunserializer($item['spec_info']);
				if (!empty($item['spec_info']['goods_val'])) {
					$item['spec'] = implode(' ', $item['spec_info']['goods_val']);
				}
			}
		}
		mload()->model('express');
		$express = express_type();
		if ($store_type == STORE_TYPE_HOTEL && !empty($good_info) && $good_info['is_house'] == 1) {
			$btime = $item['btime'];
			$etime = $item['etime'];
			$start = date('m-d', $btime);
			$end = date('m-d', $etime);
			//日期列
			$days = ceil(($etime - $btime) / 86400);
			$date_array = array();
			$date_array[0]['date'] = $start;
			$date_array[0]['day'] = date('j', $btime);
			$date_array[0]['time'] = $btime;
			$date_array[0]['month'] = date('m', $btime);
			if ($days > 1) {
				for ($i = 1; $i < $days; $i++) {
					$date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
					$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
					$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
					$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
				}
			}
			$room_date_list = pdo_getall('storex_room_price', array('roomid' => $item['roomid'], 'roomdate >=' => $item['btime'], 'roomdate <' => $item['etime'], 'status' => 1), array('id', 'roomdate', 'num', 'status'));
			$flag = 0;
			if (!empty($room_date_list)) {
				$flag = 1;
			}
			$list = array();
			if ($flag == 1) {
				for ($i = 0; $i < $days; $i++) {
					$k = $date_array[$i]['time'];
					foreach ($room_date_list as $p_key => $p_value) {
						//判断价格表中是否有当天的数据
						if ($p_value['roomdate'] == $k) {
							$list[$k]['status'] = $p_value['status'];
							if (empty($p_value['num'])) {
								$list[$k]['num'] = 0;
							} elseif ($p_value['num'] == -1) {
								$list[$k]['num'] = '不限';
							} else {
								$list[$k]['num'] = $p_value['num'];
							}
							$list[$k]['has'] = 1;
							break;
						}
					}
					//价格表中没有当天数据
					if (empty($list[$k])) {
						$list[$k]['num'] = '不限';
						$list[$k]['status'] = 1;
					}
				}
			} else {
				//价格表中没有数据
				for ($i = 0; $i < $days; $i++) {
					$k = $date_array[$i]['time'];
					$list[$k]['num'] = "不限";
					$list[$k]['status'] = 1;
				}
			}
		}
		$member_info = pdo_get('storex_member', array('id' => $item['memberid']), array('from_user', 'isauto'));
		$logs = order_status_logs($id);
	}

	if ($_W['isajax'] && $_W['ispost']) {
		$all_actions = array('cancel', 'refund', 'refuse', 'confirm', 'send', 'live', 'over');
		$data = array(
			'mngtime' => TIMESTAMP,
		);
		$action = $_GPC['action'];
		if (in_array($action, $all_actions) && !empty($actions) && !empty($actions[$action])) {
			$logs = array(
				'time' => TIMESTAMP,
				'orderid' => $item['id'],
				'table' => 'storex_order_logs',
			);
			if ($action == 'cancel') {
				$data['status'] = ORDER_STATUS_CANCEL;
			} elseif ($action == 'refund') {
				$store_info = get_store_info($item['hotelid']);
				if ($store_info['refund'] == 2) {
					message(error(-1, '后台未开启退款设置'), '', 'ajax');
				}
				if ($item['paytype'] != 'credit' && !check_ims_version()) {
					message(error(-1, '请升级微擎系统至1.0以上，并保持最新版本'), '', 'ajax');
				}
				$refund = pdo_get('storex_refund_logs', array('uniacid' => $_W['uniacid'], 'orderid' => $orderid), array('status'));
				if ($item['paytype'] == 'credit') {
					$result = order_begin_refund($item['id']);
				} elseif ($item['paytype'] == 'wechat') {
					$result = $this->refund($item['id']);
				} elseif ($item['paytype'] == 'alipay') {

				}
				
				if (is_error($result)) {
					message($result, '', 'ajax');
				} else {
					$logs['type'] = 'refund';
					$logs['before_change'] = $refund['status'];
					$logs['clerk_type'] = 2;
					if ($item['paytype'] == 'credit') {
						$logs['after_change'] = REFUND_STATUS_SUCCESS;
						log_write($logs);
						message(error(0, '退款成功'), '', 'ajax');
					} elseif ($item['paytype'] == 'wechat') {
						if (order_check_refund($id)) {
							$logs['after_change'] = REFUND_STATUS_SUCCESS;
							log_write($logs);
							message(error(0, '退款成功'), '', 'ajax');
						} else {
							$logs['after_change'] = REFUND_STATUS_FAILED;
							log_write($logs);
							message(error(-1, $result['err_code_des'] ? $result['err_code_des'] : '退款失败'), '', 'ajax');
						}
					}
				}
			} elseif ($action == 'refuse') {
				$data['status'] = ORDER_STATUS_REFUSE;
			} elseif ($action == 'confirm') {
				$data['status'] = ORDER_STATUS_SURE;
			} elseif ($action == 'send') {
				$data['goods_status'] = GOODS_STATUS_SHIPPED;
				$data['shipped_time'] = TIMESTAMP;
			} elseif ($action == 'live') {
				$data['goods_status'] = GOODS_STATUS_CHECKED;
			} elseif ($action == 'over') {
				$data['status'] = ORDER_STATUS_OVER;
				$data['over_time'] = TIMESTAMP;
			}
			if ($store_type == STORE_TYPE_HOTEL) {
				//订单取消和拒绝
				if ($data['status'] == ORDER_STATUS_CANCEL || $data['status'] == ORDER_STATUS_REFUSE) {
					if ($item['paystatus'] == PAY_STATUS_PAID) {
						$data['refund_status'] = REFUND_STATUS_PROCESS;
					}
					$room_date_list = pdo_getall('storex_room_price', array('roomid' => $item['roomid'], 'roomdate >=' => $item['btime'], 'roomdate <' => $item['etime'], 'status' => 1), array('id', 'roomdate', 'num', 'status'));
					if (!empty($room_date_list)) {
						foreach ($room_date_list as $key => $value) {
							$num = $value['num'];
							if ($num >= 0) {
								$now_num = $num + $item['nums'];
								pdo_update('storex_room_price', array('num' => $now_num), array('id' => $value['id']));
							}
						}
					}
				}
			}
			$params = array();
			$params['room'] = $room['title'];
			$params['store'] = $store['title'];
			$params['store_type'] = $store['store_type'];
			$params['openid'] = $item['openid'];
			$params['btime'] = $item['btime'];
			$params['tpl_status'] = false;
			$setting = array();
			if (!empty($store['template'])) {
				$setting = iunserializer($store['template']);
				if (!empty($setting['template'])) {
					$params['tpl_status'] = true;
				}
			}
			if (!empty($setting['template'])) {
				$params['tpl_status'] = true;
			}
			if (!empty($data['status'])) {
				$logs['type'] = 'status';
				$logs['before_change'] = $item['status'];
				$logs['after_change'] = $data['status'];
				//订单拒绝
				if ($data['status'] == ORDER_STATUS_REFUSE) {
					$params['ordersn'] = $item['ordersn'];
					$params['nums'] = $item['nums'];
					$params['sum_price'] = $item['sum_price'];
					$params['etime'] = $item['etime'];
					$params['refuse_templateid'] = isset($setting['refuse_templateid']) ? $setting['refuse_templateid'] : '';
					order_refuse_notice($params);
				}
				//订单确认提醒
				if ($data['status'] == ORDER_STATUS_SURE) {
					$params['ordersn'] = $item['ordersn'];
					$params['style'] = $item['style'];
					$params['sum_price'] = $item['sum_price'];
					if ($store_type == STORE_TYPE_HOTEL) {
						if (!empty($good_info) && $is_house == 1) {
							$data['goods_status'] = GOODS_STATUS_NOT_CHECKED;
						}
					} else {
						$data['goods_status'] = GOODS_STATUS_NOT_SHIPPED;
					}
					if ($store_type == STORE_TYPE_HOTEL && !empty($setting['templateid'])) {
						$params['contact_name'] = $item['contact_name'];
						$params['etime'] = $item['etime'];
						$params['nums'] = $item['nums'];
						$params['templateid'] = isset($setting['templateid']) ? $setting['templateid'] : '';
						order_sure_notice($params);
					} else {
						$params['paytext'] = order_paytext($item['paytype']);
						order_affirm_notice($params);
					}
					mload()->model('sms');
					$content = array(
						'store' => $store['title'],
						'ordersn' => $item['ordersn'],
						'price' => $item['sum_price'],
					);
					$sms_result = '';
					if (plugin_exists('wn_storex_plugin_sms')) {
						$sms_result = sms_send($item['mobile'], $content, 'user');
					}
					sms_send_cloud_system($item['mobile'], $content, 'user', $sms_result);
				}
			
				//订单完成提醒
				if ($data['status'] == ORDER_STATUS_OVER) {
					//订单完成后增加积分
					card_give_credit($uid, $item['sum_price']);
					//增加出售货物的数量
					add_sold_num($room, $store['store_type']);
					$params['sum_price'] = $item['sum_price'];
					$params['etime'] = $item['etime'];
					$params['finish_templateid'] = isset($setting['finish_templateid']) ? $setting['finish_templateid'] : '';
					order_over_notice($params);
					
					mload()->model('sales');
					mload()->model('plugin');
					mload()->model('print');
					sales_update(array('storeid' => $item['hotelid'], 'sum_price' => $item['sum_price']));
					if (plugin_exists('wn_storex_plugin_printer')) {
						$mode_info = print_get_mode($item['hotelid']);
						if (!empty($mode_info['defaultid']) && in_array('3', $mode_info['print_mode'])) {
							print_order($mode_info['defaultid'], $item['id'], $item['hotelid']);
						}
					}
				}
				if ($data['status'] == ORDER_STATUS_CANCEL) {
					$info = '您在' . $store['title'] . '预订的' . $room['title'] . "订单已取消，请联系管理员！";
					$status = send_custom_notice('text', array('content' => urlencode($info)), $item['openid']);
				}
			}
			
			if (!empty($data['goods_status'])) {
				$params['phone'] = $store['phone'];
				if ($data['goods_status'] == GOODS_STATUS_CHECKED || $data['goods_status'] == GOODS_STATUS_SHIPPED) {
					$logs['type'] = 'goods_status';
					$logs['before_change'] = $item['goods_status'];
					$logs['after_change'] = $data['goods_status'];
				}
				//已入住提醒
				if ($data['goods_status'] == GOODS_STATUS_CHECKED) {
					$params['check_in_templateid'] = isset($setting['check_in_templateid']) ? $setting['check_in_templateid'] : '';
					order_checked_notice($params);
				}
				//发货提醒
				if ($data['goods_status'] == GOODS_STATUS_SHIPPED) {
					if (!empty($params['tpl_status']) && empty($setting['send_templateid'])) {
						$params['send_templateid'] = $setting['send_templateid'];
						$params['express_name'] = $item['express_name'];
						$params['track_number'] = $item['track_number'];
						$params['style'] = $item['style'];
						order_send_notice($params);
					} else {
						$info = '您在' . $store['title'] . '预订的' . $room['title'] . '已发货';
						$status = send_custom_notice('text', array('content' => urlencode($info)), $item['openid']);
					}
				}
			}
			//卡券状态修改
			if (!empty($item['coupon'])) {
				if ($data['status'] == ORDER_STATUS_CANCEL || $data['status'] == ORDER_STATUS_REFUSE) {
					pdo_update('storex_coupon_record', array('status' => 1), array('id' => $item['coupon']));
				} elseif ($data['status'] == ORDER_STATUS_OVER) {
					pdo_update('storex_coupon_record', array('status' => 3), array('id' => $item['coupon']));
				}
			}
			$logs['clerk_type'] = 2;
			pdo_update('storex_order', $data, array('id' => $id));
			log_write($logs);
			if (in_array($data['status'], array(ORDER_STATUS_CANCEL, ORDER_STATUS_REFUSE))) {
				order_update_newuser($id);
				room_delete_assign($item);
			}
			if ($data['status'] == ORDER_STATUS_OVER) {
				order_market_gift($id);
				order_salesman_income($id, ORDER_STATUS_OVER);
				order_member_add_cost_money($id);
				if ($store_type != STORE_TYPE_HOTEL) {
					order_goods_profit($id);//盈利记录
				}
			}
			message(error('0', '订单信息处理完成！'), '', 'ajax');
		} else {
			message(error('-1', '订单操作错误！'), '', 'ajax');
		}
	}
	$template_suffix = '_common';
	if ($store_type == STORE_TYPE_HOTEL) {
		$template_suffix = '_hotel';
	}
	include $this->template('store/shop_orderedit' . $template_suffix);
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	$item = pdo_get('storex_order', array('id' => $id), array('id'));
	if (empty($item)) {
		message('抱歉，订单不存在或是已经删除！', referer(), 'error');
	}
	pdo_delete('storex_order', array('id' => $id));
	message('删除成功！', referer(), 'success');
}

if ($op == 'deleteall') {
	foreach ($_GPC['idArr'] as $k => $id) {
		$id = intval($id);
		pdo_delete('storex_order', array('id' => $id));
	}
	message(error(0, '删除成功！'), '', 'ajax');
}

if ($op == 'edit_msg') {
	mload()->model('express');
	$order = pdo_get('storex_order', array('id' => intval($_GPC['id'])), array('id'));
	if (empty($order)) {
		message('抱歉，订单不存在或是已经删除！', referer(), 'error');
	}
	$data = array(
		'msg' => trim($_GPC['msg'])
	);
	if ($store_type != STORE_TYPE_HOTEL) {
		if (empty($_GPC['track_number']) || empty($_GPC['express_type'])) {
			message('请填写快递及单号', referer(), 'error');
		}
		$express_name = express_type($_GPC['express_type']);
		$data['track_number'] = trim($_GPC['track_number']);
		$data['express_name'] = $express_name;
		$data['express_type'] = trim($_GPC['express_type']);
	}
	$result = pdo_update('storex_order', $data, array('id' => intval($_GPC['id'])));
	if (!empty($result)) {
		message('备注修改成功！', referer(), 'success');
	} else {
		message('备注修改失败！', referer(), 'error');
	}
}

if ($op == 'edit_price') {
	$order_id = intval($_GPC['id']);
	$sum_price = $_GPC['sum_price'];
	if (!is_numeric($sum_price)) {
		message(error(-1, '价格必须是数字！'), '', 'ajax');
	}
	$sum_price = sprintf('%1.2f', $sum_price);
	if ($sum_price <= 0) {
		message(error(-1, '价格保留两位小数后不能小于零！'), '', 'ajax');
	}
	$status = array('0', '1');
	$order_info = pdo_get('storex_order', array('weid' => $_W['uniacid'], 'id' => $order_id, 'paystatus' => 0, 'status' => $status), array('id', 'sum_price'));
	if (empty($order_info)) {
		message(error(-1, '抱歉，订单不是未支付状态或者订单已取消！'), '', 'ajax');
	}
	$core_paylog = pdo_get('core_paylog', array('tid' => $order_info['id'], 'module' => $_GPC['m'], 'uniacid' => $_W['uniacid']));
	if (!empty($core_paylog)) {
		pdo_delete('core_paylog', array('plid' => $core_paylog['plid']));
	}
	$result = pdo_update('storex_order', array('sum_price' => $sum_price), array('id' => $order_info['id']));
	if (!empty($result)) {
		message(error(0, '修改成功！'), '', 'ajax');
	} else {
		message(error(-1, '修改失败！'), '', 'ajax');
	}
}

if ($op == 'print_order') {
	$order_id = intval($_GPC['id']);
	$storeid = intval($_GPC['storeid']);
	$print = intval($_GPC['print']);
	if (empty($order_id)) {
		message('获取订单失败!', referer(), 'error');
	}
	mload()->model('print');
	$result = print_order($print, $order_id, $storeid);
	if ($result['errno'] != 0) {
		message($result['message'], referer(), 'error');
	} else {
		message('打印成功！', referer(), 'success');
	}
}

if ($op == 'check_print_plugin') {
	if (plugin_exists('wn_storex_plugin_printer')) {
		$url = wurl('site/entry/shop_plugin_printer', array('op' => 'post', 'm'=> 'wn_storex', 'storeid' => $storeid), true);
		header("Location: {$url}");
		exit;
	} else {
		message('微擎版本不支持插件或店铺未安装或未设置打印机插件', referer(), 'error');
	}
}

if ($op == 'assign_room') {
	if ($_W['ispost'] && $_W['isajax']) {
		$rooms = $_GPC['rooms'];
		$orderid = intval($_GPC['id']);
		$roomid = intval($_GPC['roomid']);
		$order_info = pdo_get('storex_order', array('id' => $orderid, 'roomid' => $roomid, 'weid' => $_W['uniacid']));
		if (empty($order_info)) {
			message(error(-1, '订单信息错误'), '', 'ajax');
		}
		if (count($rooms) != $order_info['nums']) {
			message(error(-1, '所选房间数量跟订单房间数量不一致'), '', 'ajax');
		}
		if (!empty($order_info['roomitemid'])) {
			$assign_roomitemid = explode(',', $order_info['roomitemid']);
		}
		if (!room_check_assign($order_info, $rooms, true)) {
			message(error(-1, '所选房间存在不空闲'), '', 'ajax');
		}
		$result = pdo_update('storex_order', array('roomitemid' => implode(',', $rooms)), array('id' => $orderid));
		if (!empty($result)) {
			if (!empty($assign_roomitemid) && is_array($assign_roomitemid)) {
				$order_info['roomitemid'] = '';
				foreach ($assign_roomitemid as $roomid) {
					room_delete_assign($order_info, $roomid);
				}
			}
			message(error(0, ''), referer(), 'ajax');
		} else {
			message(error(-1, '分配失败'), referer(), 'ajax');
		}
	}
}