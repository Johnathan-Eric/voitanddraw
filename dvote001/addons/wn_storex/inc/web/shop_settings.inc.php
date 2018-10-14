<?php
defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('post');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'post';

load()->model('mc');

if ($op == 'post') {
	if (!empty($_GPC['action']) && $_GPC['action'] == 'add') {
		$store_type = intval($_GPC['store_type']);
	} else {
		$store_type = intval($_W['wn_storex']['store_info']['store_type']);
	}
	$id = intval($_GPC['storeid']);
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			itoast('店铺名称不能是空！', '', 'error');
		}
		if (!is_numeric($_GPC['distance'])) {
			itoast('距离必须是数字！', '', 'error');
		}
		$express_set = array(
			'express' => is_numeric($_GPC['express']) ? $_GPC['express'] : 0,
			'full_free' => is_numeric($_GPC['full_free']) ? $_GPC['full_free'] : 0,
		);
		$common_insert = array(
			'weid' => $_W['uniacid'],
			'displayorder' => $_GPC['displayorder'],
			'title' => trim($_GPC['title']),
			'timestart' => $_GPC['timestart'],
			'timeend' => $_GPC['timeend'],
			'store_type' => $store_type,
			'thumb'=>$_GPC['thumb'],
			'phone' => $_GPC['phone'],
			'mail' => $_GPC['mail'],
			'address' => $_GPC['address'],
			'location_p' => $_GPC['district']['province'],
			'location_c' => $_GPC['district']['city'],
			'location_a' => $_GPC['district']['district'],
			'lng' => $_GPC['baidumap']['lng'],
			'lat' => $_GPC['baidumap']['lat'],
			'distance' => intval($_GPC['distance']),
			'description' => $_GPC['description'],
			'content' => $_GPC['content'],
			'store_info' => $_GPC['store_info'],
			'traffic' => $_GPC['traffic'],
			'status' => $_GPC['status'],
			'refund' => intval($_GPC['refund']),
			'market_status' => intval($_GPC['market_status']),
			'max_replace' => sprintf('%.2f', $_GPC['max_replace']),
			'express' => iserializer($express_set),
			'goods_express' => intval($_GPC['goods_express']),
			'agent_status' => intval($_GPC['agent_status']),
			'credit_pay' => intval($_GPC['credit_pay']),
			'credit_ratio' => intval($_GPC['credit_ratio']),
			'color' => trim($_GPC['color']),
		);
		
		if (empty($store_type)) {
			if (!empty($_GPC['auto_receipt'])) {
				if (intval($_GPC['auto_receipt']) > 0) {
					$common_insert['auto_receipt'] = intval($_GPC['auto_receipt']);
				} else {
					itoast('自动收货天数必须大于0！', '', 'error');
				}
			}
			$common_insert['stock_warning'] = !empty($_GPC['stock_warning']) && intval($_GPC['stock_warning']) > 0 ? intval($_GPC['stock_warning']) : 8;
			$return = array(
				'goods_status' => $_GPC['goods_status'],
				'refund_reason' => $_GPC['refund_reason'],
				'return_days' => 0,
				'return_content' => '',
				'return_address' => '',
				'return_people' => '',
				'return_phone' => '',
			);
			if (!empty($_GPC['return_days']) && $_GPC['return_days'] >= 0) {
				$return['return_days'] = intval($_GPC['return_days']);
			}
			if (!empty($_GPC['return_content'])) {
				$return['return_content'] = trim($_GPC['return_content']);
			}
			if (!empty($_GPC['return_people'])) {
				$return['return_people'] = trim($_GPC['return_people']);
			}
			if (!empty($_GPC['return_phone'])) {
				$return['return_phone'] = trim($_GPC['return_phone']);
			}
			if (!empty($_GPC['return_address'])) {
				$return['return_address'] = trim($_GPC['return_address']);
			}
			$common_insert['return_info'] = iserializer($return);
		}
		$template = array(
			'template' => intval($_GPC['template']),
			'templateid' => trim($_GPC['templateid']),
			'affirm_templateid' => trim($_GPC['affirm_templateid']),
			'refuse_templateid' => trim($_GPC['refuse_templateid']),
			'confirm_templateid' => trim($_GPC['confirm_templateid']),
			'check_in_templateid' => trim($_GPC['check_in_templateid']),
			'send_templateid' => trim($_GPC['send_templateid']),
			'finish_templateid' => trim($_GPC['finish_templateid']),
		);
		$common_insert['template'] = iserializer($template);
		$common_insert['pick_up_mode'] = empty($_GPC['pick_up_mode']) ? '' : iserializer($_GPC['pick_up_mode']);
		$receives = array('emails' => 'email', 'phones' => 'tel', 'openids' => 'openid');
		foreach ($receives as $field => $type) {
			$_GPC[$type] = array_unique($_GPC[$type]);
			$param = array();
			foreach ($_GPC[$type] as $val) {
				if ($type == 'email' && preg_match(REGULAR_EMAIL, $val)) {
					$param[] = $val;
				}
				if ($type == 'tel' && preg_match(REGULAR_MOBILE, $val)) {
					$param[] = $val;
				}
				if ($type == 'openid') {
					$user = mc_fansinfo($val);
					if (!empty($user)) {
						$param[] = $val;
					}
				}
			}
			$common_insert[$field] = iserializer($param);
		}
		$common_insert['thumbs'] = empty($_GPC['thumbs']) ? '' : iserializer($_GPC['thumbs']);
		$common_insert['detail_thumbs'] = empty($_GPC['detail_thumbs']) ? '' : iserializer($_GPC['detail_thumbs']);
		if (!empty($store_type)) {
			$insert = array(
				'weid' => $_W['uniacid'],
			);
			if (!empty($_GPC['device'])) {
				$devices = array();
				foreach ($_GPC['device'] as $key => $device) {
					if ($device != '') {
						$devices[] = array('value' => $device, 'isshow' => intval($_GPC['show_device'][$key]));
					}
				}
				$insert['device'] = empty($devices) ? '' : iserializer($devices);
			}
		}
		if (empty($id)) {
			$common_insert['store_type'] = intval($_GPC['store_type']);
			pdo_insert('storex_bases', $common_insert);
			$id = pdo_insertid();
			if (empty($id)) {
				itoast('添加失败！', '', 'error');
			}
			if (!empty($common_insert['store_type'])) {
				$insert['store_base_id'] = pdo_insertid();
				pdo_insert('storex_hotel', $insert);
			}
		} else {
			pdo_update('storex_bases', $common_insert, array('id' => $id));
			if (!empty($store_type)) {
				$hotel_info = pdo_get('storex_hotel', array('weid' => $_W['uniacid'], 'store_base_id' => $id), array('id'));
				if (!empty($hotel_info)) {
					pdo_update('storex_hotel', $insert, array('store_base_id' => $id));
				} else {
					$insert['store_base_id'] = $id;
					pdo_insert('storex_hotel', $insert);
				}
			}
		}
		itoast('店铺信息保存成功!', $this->createWebUrl('shop_index', array('storeid' => $id)), 'success');
	}
	$storex_bases = pdo_get('storex_bases', array('id' => $id));
	if (empty($storex_bases['store_type']) && empty($storex_bases['stock_warning'])) {
		$storex_bases['stock_warning'] = 8;
	}
	$item = pdo_get('storex_hotel', array('store_base_id' => $id), array('id', 'store_base_id', 'device'));
	if (empty($item['device'])) {
		$devices = array(
			array('isdel' => 0, 'value' => '有线上网'),
			array('isdel' => 0, 'isshow' => 0, 'value' => 'WIFI无线上网'),
			array('isdel' => 0, 'isshow' => 0, 'value' => '可提供早餐'),
			array('isdel' => 0, 'isshow' => 0, 'value' => '免费停车场'),
			array('isdel' => 0, 'isshow' => 0, 'value' => '会议室'),
			array('isdel' => 0, 'isshow' => 0, 'value' => '健身房'),
			array('isdel' => 0, 'isshow' => 0, 'value' => '游泳池')
		);
	} else {
		$devices = iunserializer($item['device']);
	}
	$storex_bases['thumbs'] = iunserializer($storex_bases['thumbs']);
	$storex_bases['detail_thumbs'] = iunserializer($storex_bases['detail_thumbs']);
	$storex_bases['pick_up_mode'] = iunserializer($storex_bases['pick_up_mode']);
	if (!empty($storex_bases['template'])) {
		$template = iunserializer($storex_bases['template']);
		if (!isset($template['template'])) {
			$template['template'] = '';
		}
	} else {
		$template = array('template' => '');
	}
	
	if (!empty($storex_bases['express']) && !is_numeric($storex_bases['express'])) {
		$storex_bases['express'] = iunserializer($storex_bases['express']);
	} else {
		$storex_bases['express'] = array('express' => 0, 'full_free' => 0);
	}
	$emails = iunserializer($storex_bases['emails']);
	$tels = iunserializer($storex_bases['phones']);
	$openids = iunserializer($storex_bases['openids']);
	$return_info = iunserializer($storex_bases['return_info']);
}

include $this->template('store/shop_settings');