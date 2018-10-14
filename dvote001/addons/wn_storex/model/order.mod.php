<?php
/**
 * 订单状态
 * @return array 
 */
function order_status($status) {
	$order_status = array(
		ORDER_STATUS_CANCEL => '订单取消',
		ORDER_STATUS_NOT_SURE => '订单已提交',
		ORDER_STATUS_SURE => '订单已确认',
		ORDER_STATUS_REFUSE => '订单拒绝',
		ORDER_STATUS_OVER => '订单完成'
	);
	return $order_status[$status] ? $order_status[$status] : '未知';
}

/**
 * 订单支付状态
 * @return array 
 */
function order_pay_status($status) {
	$order_pay_status = array(
		PAY_STATUS_UNPAID => '未支付',
		PAY_STATUS_PAID => '已支付'
	);
	return $order_pay_status[$status] ? $order_pay_status[$status] : '未知';
}

/**
 * 订单退款状态
 * @return array 
 */
function order_refund_status($status) {
	$order_refund_status = array(
		REFUND_STATUS_PROCESS => '退款处理中',
		REFUND_STATUS_SUCCESS => '退款成功',
		REFUND_STATUS_FAILED => '退款失败'
	);
	return $order_refund_status[$status] ? $order_refund_status[$status] : '未知';
}

/**
 * 订单商品状态
 * @return array 
 */
function order_goods_status($status) {
	$order_goods_status = array(
		GOODS_STATUS_NOT_SHIPPED => '待发货',
		GOODS_STATUS_SHIPPED => '已发货',
		GOODS_STATUS_RECEIVED => '已收货',
		GOODS_STATUS_NOT_CHECKED => '未入住',
		GOODS_STATUS_CHECKED => '已入住',
	);
	return $order_goods_status[$status] ? $order_goods_status[$status] : '未知';
}


/**
 * 获取订单的状态
 * status -1取消，0未确认，1已确认，2退款，3完成，4已入住
 * goods_status 1待发货，2已发货，3已收货，4待入住，5已入住
 */
function orders_check_status($item) {
	global $_W;
	if ($item['store_type'] == STORE_TYPE_HOTEL) {
		$room = pdo_get('storex_room', array('id' => $item['roomid']), array('id', 'is_house'));
	}
	if ($item['paystatus'] == PAY_STATUS_PAID) {
		$refund_log = pdo_get('storex_refund_logs', array('uniacid' => $_W['uniacid'], 'orderid' => $item['id']), array('id', 'status'));
	}
	//1是显示,2不显示
	//酒店
	$item['is_checked'] = 2;//在待入住中
	//普通
	$item['is_send'] = 2;//代发货状态is_send
	$item['is_confirm'] = 2;//确认收货is_confirm
	//公共
	$item['is_pay'] = 2;//立即付款 is_pay
	$item['is_comment'] = 2;//显示评价is_comment
	
	$item['is_cancel'] = 2;//取消订单is_cancel
	$item['is_over'] = 2;//再来一单is_over	
	$item['is_refund'] = 2;//显示退款is_refund
	
	if ($item['status'] == ORDER_STATUS_NOT_SURE) {//未确认
		if ($item['paystatus'] == PAY_STATUS_UNPAID) {
			$item['is_pay'] = 1;
		}
		if ($item['paystatus'] == PAY_STATUS_PAID && $item['store_type'] == STORE_TYPE_HOTEL) {
			$item['is_checked'] = 1;
		}
		$item['is_cancel'] = 1;
	} elseif ($item['status'] == ORDER_STATUS_CANCEL) {//取消
		if ($item['paystatus'] == PAY_STATUS_UNPAID) {
			$item['is_over'] = 1;
		} elseif ($item['paystatus'] == PAY_STATUS_PAID) {
			if (empty($refund_log)) {
				if (check_ims_version() || $item['paytype'] == 'credit') {
					$item['is_refund'] = 1;
				}
			}
		}
	} elseif ($item['status'] == ORDER_STATUS_SURE) {//已确认
		if ($item['store_type'] == STORE_TYPE_HOTEL) {//酒店
			if (!empty($room)) {
				if ($item['goods_status'] == GOODS_STATUS_NOT_CHECKED || empty($item['goods_status'])) {
					$item['is_cancel'] = 1;
					$item['is_checked'] = 1;
				}
				//已入住，就可以评价
				if ($item['goods_status'] == GOODS_STATUS_CHECKED) {
					if ($item['comment'] == 0) {
						$item['is_comment'] = 1;
					}
				}
			}
		} else {//非酒店
			if ($item['paystatus'] == PAY_STATUS_PAID) {//已支付
				if ($item['mode_distribute'] == 1) {//自提
					$item['is_cancel'] = 1;
				} elseif ($item['mode_distribute'] == 2) {
					if ($item['goods_status'] == GOODS_STATUS_NOT_SHIPPED) {
						$item['is_cancel'] = 1;
						$item['is_send'] = 1;
					} elseif ($item['goods_status'] == GOODS_STATUS_SHIPPED) {
						$item['is_confirm'] = 1;
					}
				}
			} elseif ($item['paystatus'] == PAY_STATUS_UNPAID) {
				$item['is_cancel'] = 1;
				$item['is_pay'] = 1;
			}
		}
	} elseif ($item['status'] == ORDER_STATUS_REFUSE) {//拒绝
		if ($item['paystatus'] == PAY_STATUS_PAID) {
			if (empty($refund_log)) {
				$item['is_refund'] = 1;
			}
			$item['is_over'] = 1;
		}
	} elseif ($item['status'] == ORDER_STATUS_OVER) {//完成
		if ($item['comment'] == 0) {
			$item['is_comment'] = 1;
		}
		$item['is_over'] = 1;
	}
	$store_info = get_store_info($item['hotelid']);
	if ($store_info['refund'] == 2) {
		$item['is_refund'] = 2;
	}
	if ($item['is_comment'] == 2 && $item['status'] == 3) {
		$item['order_status_cn'] = '已评价';
	} else {
		$item['order_status_cn'] = order_status($item['status']);
	}
	$item['pay_status_cn'] = order_pay_status($item['paystatus']);
	$item['goods_status_cn'] = '';
	if ($item['paystatus'] == 1 && !empty($refund_log['status'])) {
		$item['pay_status_cn'] = order_refund_status($refund_log['status']);
	}
	if ($item['mode_distribute'] == 1) {
		$item['goods_status_cn'] = '自提';
	} elseif ($item['mode_distribute'] == 2) {
		if (!empty($item['goods_status'])) {
			$item['goods_status_cn'] = order_goods_status($item['goods_status']);
		}
	}
	return $item;
}

//提交申请退款
function order_build_refund($orderid) {
	global $_W;
	$order = pdo_get('storex_order', array('id' => $orderid));
	$refund = pdo_get('storex_refund_logs', array('orderid' => $orderid));
	if (empty($order)) {
		return error(-1, '订单不存在或已删除');
	}
	if ($order['sum_price'] <= 0) {
		return error(-1, '订单支付金额为0, 不能发起退款申请');
	}
	$logs = array(
		'type' => $order['paytype'],
		'uniacid' => $_W['uniacid'],
		'orderid' => intval($orderid),
		'storeid' => intval($order['hotelid']),
		'refund_fee' => $order['sum_price'],
		'total_fee' => $order['sum_price'],
		'status' => REFUND_STATUS_PROCESS,
		'time' => TIMESTAMP
	);
	if (empty($refund)) {
		pdo_update('storex_order', array('refund_status' => 1), array('id' => $orderid));
		pdo_insert('storex_refund_logs', $logs);
	} else {
		if ($refund['status'] == REFUND_STATUS_SUCCESS) {
			return error(-1, '退款已成功, 不能发起退款');
		} elseif ($refund['status'] == REFUND_STATUS_PROCESS) {
			return error(-1, '退款处理中');
		} elseif ($refund['status'] == REFUND_STATUS_FAILED) {
		}
	}
	return true;
}

function order_begin_refund($orderid) {
	global $_W;
	$refund = pdo_get('storex_refund_logs', array('uniacid' => $_W['uniacid'], 'orderid' => $orderid));
	$order = pdo_get('storex_order', array('weid' => $_W['uniacid'], 'id' => $orderid));
	if (empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if ($refund['status'] == REFUND_STATUS_SUCCESS) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if ($order['paytype'] == 'credit') {
		load()->model('mc');
		$uid = mc_openid2uid($order['openid']);
		if (!empty($uid)) {
			$log = array(
				$uid,
				"订单退款, 订单号:{$order['ordersn']}, 退款金额:{$order['sum_price']}元",
				'wn_storex'
			);
			mc_credit_update($uid, 'credit2', $order['sum_price'], $log);
			pdo_update('storex_refund_logs', array('status' => REFUND_STATUS_SUCCESS, 'time' => TIMESTAMP), array('id' => $refund['id'], 'uniacid' => $_W['uniacid']));
		}
		return true;
	}
}

//订单拒绝
function order_refuse_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['refuse_templateid'])) {
		order_notice_tpl($params['openid'], 'refuse_templateid', $params, $params['refuse_templateid']);
	} else {
		$info = '您在' . $params['store'] . '预订的' . $params['room'] . "不足。已为您取消订单";
		$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
	}
}

//订单确认酒店
function order_sure_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['templateid']) && $params['store_type'] == STORE_TYPE_HOTEL) {
		order_notice_tpl($params['openid'], 'templateid', $params, $params['templateid']);
	} else {
		$info = '您在' . $params['store'] . '预订的' . $params['room'] . '已预订成功';
		$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
	}
}

//订单确认（普通和酒店）
function order_affirm_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['affirm_templateid'])) {
		order_notice_tpl($params['openid'], 'affirm_templateid', $params, $params['affirm_templateid']);
	} else {
		$info = '您在' . $params['store'] . '预订的' . $params['room'] . '已预订成功';
		$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
	}
}

//发货通知
function order_send_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['send_templateid'])) {
		order_notice_tpl($params['openid'], 'send_templateid', $params, $params['send_templateid']);
	}
}

//订单完成
function order_over_notice($params) {
	$info = '您在' . $params['store'] . '购买的' . $params['room'] . '的订单已完成,欢迎下次光临';
	$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
}

//订单入住
function order_checked_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['check_in_templateid']) && $params['store_type'] == STORE_TYPE_HOTEL) {
		order_notice_tpl($params['openid'], 'check_in_templateid', $params, $params['check_in_templateid']);
	} else {
		$info = '您已成功入住' . $params['store'] . '预订的' . $params['room'];
		$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
	}
}

//订单提交成功
function order_confirm_notice($params) {
	if (!empty($params['tpl_status']) && !empty($params['confirm_templateid'])) {
		order_notice_tpl($params['openid'], 'confirm_templateid', $params, $params['confirm_templateid']);
	} else {
		$info = '您在' . $params['store'] . '购买' . $params['room'] . '的订单已提交成功！';
		$status = send_custom_notice('text', array('content' => urlencode($info)), $params['openid']);
	}
}

function order_notice_tpl($openid, $type, $params, $templateid) {
	global $_W;
	if (!in_array($type, array('refuse_templateid', 'templateid', 'finish_templateid', 'check_in_templateid', 'confirm_templateid', 'affirm_templateid', 'send_templateid'))) {
		return false;
	}
	$tplnotice_list = array(
		'refuse_templateid' => array(
			'first' => array('value' => '尊敬的宾客，非常抱歉的通知您，您的预订订单被拒绝。'),
			'keyword1' => array('value' => $params['ordersn']),
			'keyword3' => array('value' => $params['nums']),
			'keyword4' => array('value' => $params['sum_price']),
			'keyword5' => array('value' => '商品不足'),
		),
		'templateid' => array(
			'first' => array('value' => '您好，您已成功预订' . $params['store'] . '-' . $params['style'] . '！'),
			'order' => array('value' => $params['ordersn']),
			'Name' => array('value' => $params['contact_name']),
			'datein' => array('value' => date('Y-m-d', $params['btime'])),
			'dateout' => array('value' => date('Y-m-d', $params['etime'])),
			'number' => array('value' => $params['nums']),
			'room type' => array('value' => $params['style']),
			'pay' => array('value' => $params['sum_price']),
			'remark' => array('value' => '酒店预订成功')
		),
		'affirm_templateid' => array(
			'first' => array('value' => '您好，您已成功预订' . $params['store'] . '！'),
			'keyword1' => array('value' => $params['ordersn']),//订单编号
			'keyword2' => array('value' => $params['style']),//预订信息
			'keyword3' => array('value' => $params['paytext']),//支付方式
			'keyword4' => array('value' => $params['sum_price']),//订单总价
			'keyword5' => array('value' => $params['style']),//订单详情
			'remark' => array('value' => '您的订单已被商家确认！')
		),
		'finish_templateid' => array(
			'first' => array('value' =>'您已成功办理离店手续，您本次入住酒店的详情为'),
			'keyword1' => array('value' => date('Y-m-d', $params['btime'])),
			'keyword2' => array('value' => date('Y-m-d', $params['etime'])),
			'keyword3' => array('value' => $params['sum_price']),
			'remark' => array('value' => '欢迎您的下次光临。')
		),
		'check_in_templateid' => array(
			'first' =>array('value' => '您好,您已入住' . $params['store'] . $params['room']),
			'hotelName' => array('value' => $params['store']),
			'roomName' => array('value' => $params['room']),
			'date' => array('value' => date('Y-m-d', $params['btime'])),
			'remark' => array('value' => '如有疑问，请咨询' . $params['phone'] . '。'),
		),
		'send_templateid' => array(
			'first' =>array('value' => '您好,您在' . $params['store'] . '的订单已发货'),
			'keyword1' => array('value' => $params['express_name']),//快递公司
			'keyword2' => array('value' => $params['track_number']),//快递单号
			'keyword3' => array('value' => $params['style']),//商品名称
			'remark' => array('value' => '如有疑问，请咨询商家。'),
		),
		'confirm_templateid' => array(
			'first' => array('value' => '您的订单已提交成功！'),
			'keyword1' => array('value' => $params['ordersn']),
			'keyword2' => array('value' => $params['contact_name']),
			'remark' => array('value' => '如有疑问，请咨询' . $params['phone'] . '。'),
		),
	);
	$tplnotice = $tplnotice_list[$type];
	if ($type == 'refuse_templateid' && $params['store_type'] == STORE_TYPE_HOTEL) {
		$tplnotice['keyword2'] = array('value' => date('Y.m.d', $params['btime']) . '-' . date('Y.m.d', $params['etime']));
	}
	$account_api = WeAccount::create($_W['acid']);
	$account_api->sendTplNotice($openid, $templateid, $tplnotice);
}

function order_status_logs($id) {
	$logs = pdo_getall('storex_order_logs', array('orderid' => $id), array(), '', 'time ASC');
	if (!empty($logs) && is_array($logs)) {
		$types = array('status', 'goods_status', 'paystatus', 'refund', 'refund_status');
		foreach ($logs as &$val) {
			if (in_array($val['type'], $types)) {
				$val['time'] = date('Y-m-d H:i', $val['time']);
				if ($val['type'] == 'status') {
					$val['type'] = "订单状态为";
					if ($val['after_change'] == -1) {
						$val['msg'] = "订单取消";
					} elseif ($val['after_change'] == 1) {
						$val['msg'] = "订单确认";
					} elseif ($val['after_change'] == 2) {
						$val['msg'] = "订单拒绝";
					} elseif ($val['after_change'] == 3) {
						$val['msg'] = "订单完成";
					} elseif ($val['after_change'] == 0) {
						$val['type'] = "";
						$val['msg'] = "下单成功";
					}
				} elseif ($val['type'] == 'goods_status') {
					$val['type'] = "商品状态为";
					if ($val['after_change'] == 5) {
						$val['msg'] = "客户入住";
					} elseif ($val['after_change'] == 2) {
						$val['msg'] = "商家发货";
					} elseif ($val['after_change'] == 3) {
						$val['msg'] = "客户收货";
					}
				} elseif ($val['type'] == 'paystatus') {
					$val['type'] = "";
					if ($val['after_change'] == 1) {
						$val['msg'] = "成功支付订单";
					}
				} elseif ($val['type'] == 'refund') {
					$val['type'] = "退款状态";
					if ($val['after_change'] == 2) {
						$val['msg'] = "退款成功";
					}
				} elseif ($val['type'] == 'refund_status') {
					if ($val['after_change'] == 2) {
						$val['type'] = "退款状态为";
						$val['msg'] = "订单退款成功";
					} elseif ($val['after_change'] == 1) {
						$val['type'] = "客户申请退款";
						$val['msg'] = "申请退款成功";
					}
				}
			}
			if ($val['clerk_type'] == 1) {
				$val['clerk_type'] = '用户';
			} elseif ($val['clerk_type'] == 2) {
				$val['clerk_type'] = '后台操作';
			} elseif ($val['clerk_type'] == 3) {
				$val['clerk_type'] = '店员操作';
			}
		}
		unset($val);
	}
	return $logs;
}

function order_update_newuser($orderid) {
	$order = pdo_get('storex_order', array('id' => $orderid, 'newuser' => 1), array('id', 'newuser'));
	if (!empty($order)) {
		pdo_update('storex_order', array('newuser' => 0), array('id' => $orderid));
	}
}

function order_market_gift($orderid) {
	$order = pdo_get('storex_order', array('id' => $orderid, 'market_types !=' => ''), array('id', 'market_types', 'hotelid', 'style', 'openid'));
	if (!empty($order)) {
		$order['market_types'] = iunserializer($order['market_types']);
		if (in_array('gift', $order['market_types'])) {
			$market = pdo_get('storex_market', array('storeid' => $order['hotelid'], 'type' => 'gift'));
			if (!empty($market)) {
				$market['items'] = iunserializer($market['items']);
				if ($market['items']['condition'] > 0 && $market['items']['back'] > 0) {
					load()->model('mc');
					$uid = mc_openid2uid($order['openid']);
					$remark = '您的订单' . $order['style'] . '满' . $market['items']['condition'] . '元，赠送余额' . $market['items']['back'] . '元';
					$record[] = $uid;
					$record[] = $remark;
					$record[] = 'wn_storex';
					mc_credit_update($uid, 'credit2', $market['items']['back'], $record);
				}
			}
		}
	}
}
//给销售员的提成
//storex_order
//storex_agent_apply
//storex_agent_log
function order_salesman_income($orderid, $status) {
	global $_W;
	$order = pdo_get('storex_order', array('id' => $orderid, 'agentid !=' => 0), array('id', 'hotelid', 'roomid', 'cart', 'agentid', 'nums', 'cprice', 'sum_price', 'status', 'is_package', 'openid'));
	$recored = pdo_get('storex_agent_log', array('orderid' => $orderid));
	if (!empty($order) && $status == ORDER_STATUS_OVER && empty($recored)) {
		$member_agent = pdo_get('storex_member_agent', array('uniacid' => $_W['uniacid'], 'openid' => $order['openid'], 'storeid' => $order['hotelid']), array('id', 'agentid'));
		if (empty($member_agent['agentid'])) {
			return;
		}
		$store = pdo_get('storex_bases', array('id' => $order['hotelid']), array('id', 'store_type'));
		$table = gettablebytype($store['store_type']);
		if (!empty($store)) {
			$goods = array();
			if (!empty($order['cart'])) {
				$order['cart'] = iunserializer($order['cart']);
				foreach ($order['cart'] as $g) {
					$goods[] = array(
						'gid' => $g['good']['id'],
						'type' => $g['good']['is_package'],
						'cprice' => $g['good']['cprice'],
						'nums' => $g['good']['buynums'],
					);
				}
			} elseif (!empty($order['roomid']) && $order['is_package'] == 1) {
				$goods[] = array('gid' => $order['roomid'], 'type' => 1, 'cprice' => $order['cprice'], 'nums' => $order['nums']);
			} elseif (!empty($order['roomid']) && $order['is_package'] == 2) {
				$goods[] = array('gid' => $order['roomid'], 'type' => 2, 'cprice' => $order['cprice'], 'nums' => $order['nums']);
			}
			if (!empty($goods)) {
				$agents_money = array();
				$agent = pdo_get('storex_agent_apply', array('id' => $member_agent['agentid'], 'status' => 2), array('id', 'pid', 'uid'));
				$second_agent = $third_agent = array();
				if (!empty($agent['pid'])) {
					$second_agent = pdo_get('storex_agent_apply', array('id' => $agent['pid'], 'status' => 2), array('id', 'pid', 'uid'));
					if (!empty($second_agent['pid'])) {
						$third_agent = pdo_get('storex_agent_apply', array('id' => $second_agent['pid'], 'status' => 2), array('id', 'pid', 'uid'));
					}
				}
				foreach ($goods as $info) {
					if ($info['type'] == 2) {
						$good = pdo_get('storex_sales_package', array('id' => $info['gid']), array('id', 'agent_ratio'));//套餐返给销售员的比例
					} else {
						$good = pdo_get($table, array('id' => $info['gid']), array('id', 'agent_ratio'));//返给销售员的比例
					}
					$good['agent_ratio'] = iunserializer($good['agent_ratio']);
					if (!empty($good['agent_ratio'][1])) {
						$money = get_ratio_money($good['agent_ratio'][1], $info);
						$agents_money = add_ratio_money($agents_money, $agent, $money);
						if (count($goods) == 1) {
							$agents_money[$agent['id']]['ratio'] = $good['agent_ratio'][1];
						}
					}
					if (!empty($second_agent) && !empty($good['agent_ratio'][2])) {
						$money = get_ratio_money($good['agent_ratio'][2], $info);
						$agents_money = add_ratio_money($agents_money, $second_agent, $money);
						if (count($goods) == 1) {
							$agents_money[$second_agent['id']]['ratio'] = $good['agent_ratio'][2];
						}
					}
					if (!empty($third_agent) && !empty($good['agent_ratio'][3])) {
						$money = get_ratio_money($good['agent_ratio'][3], $info);
						$agents_money = add_ratio_money($agents_money, $third_agent, $money);
						if (count($goods) == 1) {
							$agents_money[$third_agent['id']]['ratio'] = $good['agent_ratio'][3];
						}
					}
				}
				if (!empty($agents_money) && is_array($agents_money)) {
					foreach ($agents_money as $aid => $agent_info) {
						give_agent_money($order, $agent_info);
					}
				}
			}
		}
	}
}

function get_ratio_money($ratio, $info) {
	return sprintf('%.2f', $info['cprice'] * $info['nums'] * $ratio * 0.01);
}

function add_ratio_money($agents_money, $agent, $money) {
	if (!empty($agents_money[$agent['id']])) {
		$agents_money[$agent['id']]['money'] += $money;
	} else {
		$agents_money[$agent['id']] = array(
			'id' => $agent['id'],
			'uid' => $agent['uid'],
			'money' => $money,
		);
	}
	return $agents_money;
}

function give_agent_money($order, $agent_info) {
	global $_W;
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'orderid' => $order['id'],
		'goodid' => $order['roomid'],
		'storeid' => $order['hotelid'],
		'sumprice' => $order['sum_price'],
		'time' => TIMESTAMP,
	);
	$insert['uid'] = $agent_info['uid'];
	$insert['agentid'] = $agent_info['id'];
	$insert['money'] = $agent_info['money'];
	$insert['rate'] = $agent_info['ratio'];//提成比例,单件商品会记录，购物车多件不记录
	
	pdo_insert('storex_agent_log', $insert);
	pdo_update('storex_agent_apply', array('income +=' => $insert['money'], 'outcome +=' => $insert['money']), array('id' => $insert['agentid']));
}

function order_member_add_cost_money($id) {
	global $_W;
	$order = pdo_get('storex_order', array('id' => $id, 'status' => 3), array('id', 'openid', 'weid', 'sum_price', 'hotelid'));
	if (!empty($order)) {
		$member = pdo_get('storex_member', array('weid' => $order['weid'], 'from_user' => $order['openid']));
		if (!empty($member)) {
			if (!empty($member['cost_money'])) {
				$member['cost_money'] = iunserializer($member['cost_money']);
				if (isset($member['cost_money'][$order['hotelid']])) {
					$member['cost_money'][$order['hotelid']] += $order['sum_price'];
				} else {
					$member['cost_money'][$order['hotelid']] = $order['sum_price'];
				}
				$money = $member['cost_money'][$order['hotelid']];
				pdo_update('storex_member', array('cost_money' => iserializer($member['cost_money'])), array('id' => $member['id']));
				$storex_discount_set = pdo_get('storex_discount_set', array('storeid' => $order['hotelid'], 'uniacid' => $_W['uniacid']));
				if (!empty($storex_discount_set) && $storex_discount_set['uplevel'] == 1) {
					$storex_member_level = pdo_getall('storex_member_level', array('uniacid' => $_W['uniacid'], 'storeid' => $order['hotelid']), array(), '', 'level ASC');
					if (!empty($storex_member_level)) {
						$level_id = '';
						foreach ($storex_member_level as $level) {
							if ($money >= $level['ask']) {
								$level_id = $level['id'];
							}
						}
						if (!empty($level_id)) {
							$member['member_group'][$order['hotelid']] = $level_id;
							pdo_update('storex_member', array('member_group' => iserializer($member['member_group'])), array('id' => $member['id']));
						}
					}
				}
			}
		}
	}
}

function order_check_return($order_info, $cart = array()) {
	global $_W;
	$is_return = 2;
	if ($order_info['status'] == ORDER_STATUS_OVER) {
		$storeinfo = pdo_get('storex_bases', array('id' => $order_info['hotelid']), array('id', 'return_info'));
		if (!empty($storeinfo)) {
			$storeinfo['return_info'] = iunserializer($storeinfo['return_info']);
		}
		if (TIMESTAMP > ($order_info['over_time'] + 86400 * $storeinfo['return_info']['return_days'])) {
			return 2;
		}
		$return_info = array(
			'uniacid' => $_W['uniacid'],
			'storeid' => $order_info['hotelid'],
			'openid' => $_W['openid'],
			'orderid' => $order_info['id'],
			'goodsid' => $order_info['roomid'],
			'spec_id' => $order_info['spec_id'],
		);
		if (empty($cart)) {
			$return_log = pdo_get('storex_return_goods', $return_info);
		} else {
			$return_info['good'] = implode('|', $cart['buyinfo']);
			$return_log = pdo_get('storex_return_goods', $return_info);
		}
		if (empty($return_log)) {
			$is_return = 1;
		} else {
			if($return_log['status'] == 2) {
				$is_return = 1;
			}
		}
	}
	return $is_return;
}

/**
 * 增加每个订单的利润记录
 * @param 订单id $id
 */
function order_goods_profit($id) {
	global $_W;
	$order = pdo_get('storex_order', array('id' => $id, 'is_package' => 1));
	if (empty($order)) {
		return ;
	}
	$goods_profit = array(
		'uniacid' => $_W['uniacid'],
		'storeid' => $order['hotelid'],
		'orderid' => $id,
		'time' => TIMESTAMP,
		'order_time' => $order['time'],
	);
	if (!empty($order['cart'])) {
		$order['cart'] = iunserializer($order['cart']);
		foreach ($order['cart'] as $cart) {
			if ($cart['buyinfo']['2'] == 1) {//规格
				$good = pdo_get('storex_goods', array('id' => $cart['good']['id']));
			} elseif($cart['buyinfo']['2'] == 2) {//普通
				$good = pdo_get('storex_goods', array('id' => $cart['good']['id']));
			}
			if (!empty($good['fprice'])) {
				$order['sum_price'] -= $good['fprice'] * $cart['buyinfo']['1'];
			}
			pdo_update('storex_goods', array('fact_sold_num +=' => $order['nums']), array('id' => $cart['good']['id']));
		}
		$goods_profit['money'] = $order['sum_price'];
		pdo_insert('storex_goods_profit', $goods_profit);
	} else {
		$good = pdo_get('storex_goods', array('id' => $order['roomid']));
		pdo_update('storex_goods', array('fact_sold_num +=' => $order['nums']), array('id' => $order['roomid']));
		if (!empty($good['fprice'])) {
			$goods_profit['money'] = sprintf("%.2f", ($order['sum_price'] - $good['fprice'] * $order['nums']));
			pdo_insert('storex_goods_profit', $goods_profit);
		}
	}
}

function order_cart_goodsinfo($good) {
	global $_W;
	if ($good['buyinfo'][2] == 3) {
		$goods_info = pdo_get('storex_sales_package', array('uniacid' => $_W['uniacid'], 'id' => $good['buyinfo'][0]), array('title', 'sub_title', 'thumb', 'price', 'id'));
	} elseif ($good['buyinfo'][2] == 2) {
		$goods_info = pdo_get('storex_goods', array('weid' => $_W['uniacid'], 'id' => $good['buyinfo'][0]), array('id', 'thumb', 'oprice', 'cprice', 'title', 'sub_title'));
	} elseif ($good['buyinfo'][2] == 1) {
		$goods_info = pdo_get('storex_spec_goods', array('id' => $good['buyinfo'][0], 'uniacid' => $_W['uniacid']), array('id', 'thumb', 'oprice', 'cprice', 'title', 'sub_title'));
		$goods_info['style'] = implode(' ', $good['good']['spec_info']['goods_val']);
	}
	return $goods_info;
}

//获取订单的商户订单号
function order_uniontid(&$lists) {
	if (!empty($lists) && is_array($lists)) {
		foreach ($lists as $orderkey => &$orderinfo) {
			$paylog = pdo_get('core_paylog', array('uniacid' => $orderinfo['weid'], 'tid' => $orderinfo['id'], 'module' => 'wn_storex'), array('uniacid', 'uniontid', 'tid'));
			if (!empty($paylog)) {
				$lists[$orderkey]['uniontid'] = $paylog['uniontid'];
			}
			if (!empty($orderinfo['thumb'])) {
				$orderinfo['thumb'] = tomedia($orderinfo['thumb']);
			}
			order_pay_text($orderinfo);
		}
	}
	return $lists;
}
/**
 * cancel 订单取消
 * refund 订单退款
 * refuse 订单拒绝
 * confirm 订单确认
 * send 订单发货
 * live 订单入住
 * over 订单完成
 */
function order_actions($order, $store_type, $is_house) {
	global $_W;
	$actions = array();
	if ($order['paystatus'] == PAY_STATUS_PAID) {
		$order_refund = pdo_get('storex_refund_logs', array('uniacid' => $_W['uniacid'], 'orderid' => $order['id']), array('id', 'status'));
		if ($order['status'] == ORDER_STATUS_CANCEL || $order['status'] == ORDER_STATUS_REFUSE) {
			if (!empty($order_refund) && ($order_refund['status'] == REFUND_STATUS_PROCESS || $order_refund['status'] == REFUND_STATUS_FAILED)) {
				if (!order_check_refund($order['id'])) {
					$actions['refund'] = '订单退款';
				}
			}
		} elseif($order['status'] == ORDER_STATUS_NOT_SURE) {
			$actions['cancel'] = '订单取消';
			// 				$actions['refuse'] = '订单拒绝';
			$actions['confirm'] = '订单确认';
		} elseif($order['status'] == ORDER_STATUS_SURE) {
			if ($store_type == STORE_TYPE_HOTEL) {
				if ($is_house == 1) {
					if (empty($order['goods_status']) || $order['goods_status'] == GOODS_STATUS_NOT_CHECKED) {
						$actions['live'] = '订单入住';
					}
					if ($order['goods_status'] == GOODS_STATUS_CHECKED) {
						$actions['over'] = '订单完成';
					}
				} else {
					$actions['over'] = '订单完成';
				}
			} else {
				if ($order['mode_distribute'] == 1) {
					$actions['over'] = '订单完成';
				} else {
					if (empty($order['goods_status']) || $order['goods_status'] == GOODS_STATUS_NOT_SHIPPED) {
						$actions['send'] = '订单发货';
					}
					if ($order['goods_status'] == GOODS_STATUS_RECEIVED) {
						$actions['over'] = '订单完成';
					}
				}
			}
		}
	} else {
		if ($order['status'] != ORDER_STATUS_CANCEL && $order['status'] != ORDER_STATUS_REFUSE) {
			if ($order['status'] == ORDER_STATUS_NOT_SURE) {
				$actions['cancel'] = '订单取消';
				// 					$actions['refuse'] = '订单拒绝';
				if ($store_type != STORE_TYPE_HOTEL) {
					$actions['confirm'] = '订单确认';
				}
			}
		}
	}
	return $actions;
}

function order_paytext($paytype = '') {
	if ($paytype == 'credit') {
		$paytype_text = '余额支付';
	} elseif ($paytype == 'wechat') {
		$paytype_text = '微信支付';
	} elseif ($paytype == 'alipay') {
		$paytype_text = '支付宝';
	} elseif ($paytype == 'delivery') {
		$paytype_text = '到店付款';
	} elseif (empty($paytype)) {
		$paytype_text = '未支付(或其它)';
	}
	return $paytype_text;
}

function order_pay_text(&$order) {
	$order['paytype_text'] = order_paytext($order['paytype']);
	if ($order['paystatus'] == 0) {
		if ($order['status'] == 0) {
			$order['status_text'] = "已提交订单,待付款";
		} elseif ($order['status'] == -1) {
			$order['status_text'] = "已取消";
		} elseif ($order['status'] == 1) {
			$order['status_text'] = "已接受";
		} elseif ($order['status'] == 2) {
			$order['status_text'] = "已拒绝";
		} elseif ($order['status'] == 3) {
			$order['status_text'] = "订单完成";
		}
	} else {
		if ($order['status'] == 0) {
			if ($order['paytype'] == 'delivery') {
				$order['status_text'] = "待付款";
			} else {
				$order['status_text'] = "已支付,等待确认";
			}
		} elseif ($order['status'] == -1) {
			if ($order['paytype'] == 'delivery') {
				$order['status_text'] = "已取消";
			} else {
				$refund_log = pdo_get('storex_refund_logs', array('orderid' => $order['id']));
				if (!empty($refund_log)) {
					if (empty($refund_log['status']) || $refund_log['status'] == 1) {
						$order['status_text'] = "已支付,已取消,用户申请退款";
					} else if ($refund_log['status'] == 2) {
						$order['status_text'] = "已支付,已取消,退款成功";
					} else if ($refund_log['status'] == 3) {
						$order['status_text'] = "已支付,已取消,退款失败";
					}
				} else {
					$order['status_text'] = "已支付,已取消,未退款";
				}
			}
		} elseif ($order['status'] == 1) {
			$order['status_text'] = "已确认,已接受";
		} elseif ($order['status'] == 2) {
			$refund_log = pdo_get('storex_refund_logs', array('orderid' => $order['id']));
			if (!empty($refund_log)) {
				if (empty($refund_log['status'])) {
					$order['status_text'] = "已支付,已拒绝,用户申请退款";
				} else if ($refund_log['status'] == 2) {
					$order['status_text'] = "已支付,已拒绝,退款成功";
				} else if ($refund_log['status'] == 3) {
					$order['status_text'] = "已支付,已拒绝,退款失败";
				}
			} else {
				$order['status_text'] = "已支付,已拒绝,未退款";
			}
		} elseif ($order['status'] == 3) {
			$order['status_text'] = "订单完成";
		}
	}
}

function order_check_refund($id) {
	$order = pdo_get('storex_order', array('id' => $id));
	$paylog = pdo_get('core_paylog', array('uniacid' => $order['weid'], 'tid' => $order['id'], 'module' => 'wn_storex'), array('uniacid', 'uniontid', 'tid'));
	if (!empty($paylog)) {
		$refundlog = pdo_get('core_refundlog', array('uniacid' => $order['weid'], 'uniontid' => $paylog['uniontid'], 'status' => 1));
		if (!empty($refundlog)) {
			return true;
		}
	}
	return false;
}

function order_check_shipped($storeid) {
	$store = pdo_get('storex_bases', array('id' => $storeid), array('id', 'auto_receipt'));
	if (!empty($store) && !empty($store['auto_receipt'])) {
		$orders = pdo_getall('storex_order', array('hotelid' => $storeid, 'status' => 1, 'goods_status' => 2), array('id', 'status', 'goods_status', 'shipped_time'));
		if (!empty($orders) && is_array($orders)) {
			foreach ($orders as $order) {
				if (!empty($order['shipped_time'])) {
					$day = (TIMESTAMP - $order['shipped_time']) / 86400;
					if ($day >= $store['auto_receipt']) {
						pdo_update('storex_order', array('goods_status' => 3), array('id' => $order['id']));
					}
				}
			}
		}
	}
	
}
