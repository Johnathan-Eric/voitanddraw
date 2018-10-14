<?php
load()->func('communication');
/*
 * $type 快递类型
 * $number 快递单号
 * */
function express_get($type, $number) {
	global $_W, $_GPC;
	$url = 'http://www.kuaidi100.com/query?type=' . $type . '&postid=' . $number . '&id=1&valicode=&temp=' . random(4) . '&sessionid=&tmp=' . random(4);
	$result = ihttp_request($url);
	$result = @json_decode($result['content'], true);
	if ($result['message'] == 'ok' && $result['status'] == 200) {
		return $result['data'];
	}
	return false;
}

function express_type($type = '') {
	$type_list = array(
		'anxindakuaixi' => '安信达',
		'youzhengguonei' => '邮政包裹',
		'cces' => '希伊艾斯',
		'chuanxiwuliu' => '传喜物流',
		'dhl' => 'DHL快递',
		'datianwuliu' => '大田物流',
		'debangwuliu' => '德邦物流',
		'ems' => 'EMS',
		'emsguoji' => 'EMS国际',
		'feikangda' => '飞康达',
		'fedex' => 'FedEx(国际)',
		'rufengda' => '凡客如风达',
		'ganzhongnengda' => '港中能达',
		'gongsuda' => '共速达',
		'huitongkuaidi' => '汇通快递',
		'tiandihuayu' => '天地华宇',
		'jiajiwuliu' => '佳吉快运',
		'jiayiwuliu' => '佳怡物流',
		'jixianda' => '急先达',
		'kuaijiesudi' => '快捷速递',
		'longbanwuliu' => '龙邦快递',
		'lianbangkuaidi' => '联邦快递',
		'lianhaowuliu' => '联昊通',
		'quanyikuaidi' => '全一快递',
		'quanfengkuaidi' => '全峰快递',
		'quanritongkuaidi' => '全日通',
		'shentong' => '申通快递',
		'shunfeng' => '顺丰快递',
		'suer' => '速尔快递',
		'tnt' => 'TNT快递',
		'tiantian' => '天天快递',
		'ups' => 'UPS快递',
		'usps' => 'USPS',
		'xinbangwuliu' => '新邦物流',
		'xinfengwuliu' => '信丰物流',
		'neweggozzo' => '新蛋物流',
		'yuantong' => '圆通快递',
		'yunda' => '韵达快递',
		'youshuwuliu' => '优速快递',
		'zhongtong' => '中通快递',
		'zhongtiewuliu' => '中铁快运',
		'zhaijisong' => '宅急送',
		'zhongyouwuliu' => '中邮物流',
	);
	return !empty($type_list[$type]) ? $type_list[$type] : $type_list;
	// return $messages[$code];
}

function express_single_info($goodsid, $city_code) {
	$goods_info = pdo_get('storex_goods', array('id' => $goodsid), array('id', 'express_set', 'dispatchid'));
	$express_set = iunserializer($goods_info['express_set']);
	$default_express = array(
		'is_dispatch' => 2,
		'dispatch' => array(
			'express' => $express_set['default_express']
		),
		'not_dispatch' => 2,
		'not_dispatch_goods' => array()
	);
	if (!empty($goods_info['dispatchid'])) {
		$dispatch_info = pdo_get('storex_dispatch', array('id' => $goods_info['dispatchid'], 'status' => 1));
		if (!empty($dispatch_info)) {
			$dispatcharea = explode('-', $dispatch_info['selectedareas_code']);
			if ($dispatch_info['isdispatcharea'] == 1) {
				if (in_array($city_code, $dispatcharea)) {
					$default_express['not_dispatch'] = 1;
				}
			} else {
				if (!in_array($city_code, $dispatcharea)) {
					$default_express['not_dispatch'] = 1;
				}
			}
			$dispatch_info['content'] = iunserializer($dispatch_info['content']);
			if (!empty($dispatch_info['content']) && is_array($dispatch_info['content'])) {
				foreach ($dispatch_info['content'] as $key => $value) {
					$city_area = explode('-', $value['city_area']);
					if (in_array($city_code, $city_area)) {
						$current_area_key = $key;
						break;
					}
				}
			}
			if (empty($dispatch_info['content'][$current_area_key])) {
				$dispatch = array(
					'first' => $dispatch_info['default_first'],
					'firstprice' => $dispatch_info['default_firstprice'],
					'second' => $dispatch_info['default_second'],
					'secondprice' => $dispatch_info['default_secondprice'],
					'nopostage' => $dispatch_info['default_nopostage'],
				);
			} else {
				$dispatch = $dispatch_info['content'][$current_area_key];
				unset($dispatch['city_area']);
			}
			$dispatch['calculate_type'] = $dispatch_info['calculate_type'];
			$freight = array(
				'is_dispatch' => 1,
				'dispatch' => $dispatch,
				'not_dispatch' => $default_express['not_dispatch'],
				'not_dispatch_goods' => array()
			);
			$freight['not_dispatch_goods'] = $freight['not_dispatch'] == 1 ? array($goodsid) : array();
		} else {
			$freight = $default_express;
		}
	} else {
		$freight = $default_express;
	}
	return $freight;
}
function express_cart_info($goodsids, $storeid, $city_code) {
	global $_W;
	$goods_list = pdo_getall('storex_goods', array('id' => $goodsids), array('id', 'express_set', 'dispatchid'));
	$dispatch_list = pdo_getall('storex_dispatch', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'status' => 1), array(), 'id');
	$freight['not_dispatch_goods'] = array();
	if (!empty($goods_list) && is_array($goods_list)) {
		foreach ($goods_list as $k => $val) {
			if (!empty($val['dispatchid'])) {
				$dispatch_info = $dispatch_list[$val['dispatchid']];
				if (!empty($dispatch_info) && is_array($dispatch_info)) {
					foreach ($dispatch_info as $key => $value) {
						$dispatcharea = explode('-', $dispatch_info['selectedareas_code']);
						if ($dispatch_info['isdispatcharea'] == 1) {
							if (in_array($city_code, $dispatcharea)) {
								$freight['not_dispatch_goods'][] = $val['id'];
							}
						} else {
							if (!in_array($city_code, $dispatcharea)) {
								$freight['not_dispatch_goods'][] = $val['id'];
							}
						}
					}
				}
			}
		}
		$freight['not_dispatch_goods'] = array_unique($freight['not_dispatch_goods']);
	}
	$store_info = get_store_info($storeid);
	$freight['dispatch'] = $store_info['express'];
	return $freight;
}

function express_get_areadata() {
	$json = file_get_contents(IA_ROOT . '/addons/wn_storex/template/style/js/area-select/city.json');
	return json_decode($json, true);
}

function express_goods_dispatch($goodsid, $citycode) {
	$freight = express_single_info($goodsid, $citycode);
	return in_array($goodsid, $freight['not_dispatch_goods']) ? false : true;
}
