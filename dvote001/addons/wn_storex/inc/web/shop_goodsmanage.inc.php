<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');

$ops = array('display', 'edit', 'recycle_goods', 'recycleall', 'recycle', 'showall', 'status', 'copyroom', 'qrcode_entry', 'set_tag', 'spec_info', 'delete', 'deleteall', 'renew', 'renewall', 'goods_sale_rank');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$storeid = intval($_GPC['storeid']);
$store = $_W['wn_storex']['store_info'];
$store_type = $store['store_type'];
$parent = pdo_getall('storex_categorys', array('store_base_id' => $storeid, 'parentid' => 0, 'weid' => $_W['uniacid']), array(), 'id', array('parentid', 'displayorder DESC'));
if (empty($parent)) {
	itoast('请先给该店铺添加一级分类！', $this->createWebUrl('shop_category', array('storeid' => $storeid)), 'error');
}

$delete_cache_ops = array('delete', 'deleteall', 'showall', 'status', 'copyroom');
if (in_array($op, $delete_cache_ops)) {
	$cachekey = "wn_storex:goods_entry:{$storeid}";
	cache_delete($cachekey);
}

$table = gettablebytype($store_type);

if ($op == 'display' || $op == 'recycle') {
	$category = pdo_getall('storex_categorys', array('store_base_id' => $storeid, 'enabled' => 1, 'weid' => $_W['uniacid']), array('id', 'name', 'store_base_id', 'parentid'), 'id', 'parentid ASC');
	$category_set = array();
	if (!empty($category) && is_array($category)) {
		foreach ($category as $info) {
			if (empty($info['parentid'])) {
				$category_set[$info['id']] = $info;
				$category_set[$info['id']]['sub_class'] = array();
			} else {
				if (!empty($category_set[$info['parentid']])) {
					$category_set[$info['parentid']]['sub_class'][] = $info;
				}
			}
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	mload()->model('goods');
	$condition = array(
		'title' => $_GPC['title'],
		'category_id' => $_GPC['category_id']
	);
	$condition['recycle'] = 2;
	if ($op == 'recycle') {
		$condition['recycle'] = 1;
	}
	if ($store_type == 1) {
		$goods_list_info = goods_get_list_hotel($condition, array('pindex' => $pindex, 'psize' => $psize));
	} else {
		$goods_list_info = goods_get_list_common($condition, array('pindex' => $pindex, 'psize' => $psize));
	}
	$goods_list = $goods_list_info['list'];
	foreach ($goods_list as $k => $info) {
		if (!empty($category[$info['pcate']])) {
			$goods_list[$k]['pcate_name'] = $category[$info['pcate']]['name'];
		}
		if (!empty($category[$info['ccate']])) {
			$goods_list[$k]['ccate_name'] = $category[$info['ccate']]['name'];
		}
	}
	$pager = pagination($goods_list_info['total'], $pindex, $psize);
	$tags = store_goods_tags($storeid);
	include $this->template('store/shop_goodslist');
}

if ($op == 'edit') {
	if (empty($store_type)) {
		$spec_name = pdo_getall('storex_spec', array('storeid' => $storeid, 'uniacid' => $_W['uniacid']), array('id', 'name'), 'id');
		$spec_ids = array_keys($spec_name);
		$spec_value = pdo_getall('storex_spec_value', array('specid' => $spec_ids), array('id', 'name', 'displayorder', 'specid'), 'id', 'displayorder DESC');
		if (!empty($spec_value) && is_array($spec_value)) {
			foreach ($spec_value as $key => $value) {
				$spec_list[$value['specid']]['name'] = $spec_name[$value['specid']]['name'];
				$spec_list[$value['specid']]['values'][$key] = array(
					'id' => $value['id'],
					'name' => $value['name'],
					'displayorder' => $value['displayorder'],
					'specid' => $value['specid']
				);
			}
			foreach ($spec_name as $spec_id => $val) {
				if (empty($spec_list[$spec_id])) {
					unset($spec_name[$spec_id]);
				}
			}
		}
	}
	load()->func('tpl');
	$children = array();
	$category = pdo_getall('storex_categorys', array('store_base_id' => $storeid, 'weid' => $_W['uniacid']), array(), 'id', array('parentid', 'displayorder DESC'));
	if (!empty($category) && is_array($category)) {
		foreach ($category as $cid => $cate) {
			if (!empty($cate['parentid'])) {
				$children[$cate['parentid']][] = $cate;
			}
		}
	}
	$id = intval($_GPC['id']);
	$usergroup_list = pdo_getall('mc_groups', array('uniacid' => $_W['uniacid']), array(), '', array('isdefault DESC', 'credit ASC'));
	$dispatch_list = pdo_getall('storex_dispatch', array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'status' => 1), array('name', 'id'));
	if (!empty($id)) {
		$item = pdo_get($table, array('id' => $id));
		if (empty($store_type)) {
			$thumb = tomedia($item['thumb']);
			$item['express_set'] = iunserializer($item['express_set']);
			$spec_goods_list = pdo_getall('storex_spec_goods', array('storeid' => $storeid, 'uniacid' => $_W['uniacid'], 'goodsid' => $id));
			if (!empty($spec_goods_list) && is_array($spec_goods_list)) {
				foreach ($spec_goods_list as $k => $val) {
					$goods_list['sp_name'] = iunserializer($val['sp_name']);
					$goods_list['sp_val'] = iunserializer($val['sp_val']);
					$goods_val = iunserializer($val['goods_val']);
					if (!empty($goods_val) && is_array($goods_val)) {
						foreach ($goods_val as $key => $value) {
							$goods_val_keys = array_keys($goods_val);
							$goods_val_keys = 'i_' . implode('_', $goods_val_keys);
							$goods_list['spec'][$goods_val_keys] = array(
								'goodsid' => $val['id'],
								'sp_value' => $goods_val,
								'cprice' => $val['cprice'],
								'oprice' => $val['oprice'],
								'stock' => $val['stock'],
								'thumb' => tomedia($val['thumb']),
							);
						}
					}
				}
			}
		}
		if (empty($item)) {
			if ($store_type == STORE_TYPE_HOTEL) {
				message('房型不存在或是已经删除！', '', 'error');
			} else {
				message('商品不存在或是已经删除！', '', 'error');
			}
		}
		$piclist = iunserializer($item['thumbs']);
		$user_defined = get_goods_defined($storeid, $id);
		$agent_ratio = iunserializer($item['agent_ratio']);
		if (empty($agent_ratio['1']) || empty($agent_ratio['2']) || empty($agent_ratio['3'])) {
			$agent_ratio = array('1' => 0, '2' => 0, '3' => 0);
		}
	} else {
		$agent_ratio = array('1' => 0, '2' => 0, '3' => 0);
	}
	if ($store_type == STORE_TYPE_NORMAL) {
		//分享设置信息
		$fields = array();
		$fields['title'] = '店铺名称';
		$fields['name'] = '商品标题';
		$fields['sub_title'] = '商品副标题';
		$fields['oprice'] = '原价';
		$fields['cprice'] = '现价';
		$fields['tag'] = '标签';
		if (!empty($id)) {
			$share = pdo_get('storex_share_set', array('uniacid' => intval($_W['uniacid']), 'storeid' => $storeid, 'type' => 'goods', 'goodsid' => $id, 'goodstable' => 'storex_goods'));
			if (!empty($share)) {
				$share['thumb'] = tomedia($share['thumb']);
			}
		}
	}
	
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('请填写商品名称！', '', 'error');
		}
		if (empty($_GPC['category']['parentid'])) {
			message('一级分类不能为空！', '', 'error');
		}
		if ($store_type == STORE_TYPE_HOTEL && empty($_GPC['device'])) {
			message('商品说明不能为空！', '', 'error');
		}
		if (empty($_GPC['oprice']) || $_GPC['oprice'] <= 0 || empty($_GPC['cprice']) || $_GPC['cprice'] <= 0) {
			message('商品原价和优惠价不能为空！', '', 'error');
		}
		$common = array(
			'weid' => $_W['uniacid'],
			'store_base_id' => $storeid,
			'title' => $_GPC['title'],
			'thumb'=>$_GPC['thumb'],
			'oprice' => $_GPC['oprice'],
			'cprice' => $_GPC['cprice'],
			'device' => $_GPC['device'],
			'score' => intval($_GPC['score']),
			'status' => $_GPC['status'],
			'sales' => $_GPC['sales'],
			'can_buy' => intval($_GPC['can_buy']),
			'sortid'=>intval($_GPC['sortid']),
			'sold_num' => intval($_GPC['sold_num']),
			'store_type' => intval($store_type),
			'sub_title' => trim($_GPC['sub_title']),
		);
		if ($store_type == STORE_TYPE_HOTEL) {
			$is_house = 1;
		} else {
			$is_house = 2;
		}
		$common['pcate'] = $_GPC['category']['parentid'];
		$common['ccate'] = $_GPC['category']['childid'];
		if (!empty($category) && !empty($category[$_GPC['category']['parentid']])) {
			$is_house = $category[$_GPC['category']['parentid']]['category_type'];
		} else {
			$is_house = 2;
		}
		if (is_array($_GPC['thumbs'])) {
			$common['thumbs'] = iserializer($_GPC['thumbs']);
		} else {
			$common['thumbs'] = iserializer(array());
		}
		$defined = array(
			'uniacid' => intval($_W['uniacid']),
			'storeid' => $storeid,
			'goods_table' => $table,
		);
		if (!empty($_GPC['defined'])) {
			$content = array();
			foreach ($_GPC['defined'] as $val) {
				if (!empty($val['title']) && !empty($val['content'])) {
					$content[] = $val;
				}
			}
			if (!empty($content)) {
				$defined['defined'] = iserializer($content);
			}
		} else {
			$defined['defined'] = '';
		}
		if (empty($store_type)) {
			$goods = array(
				'unit' => trim($_GPC['unit']),
				'weight' => sprintf("%.2f", $_GPC['weight']),
				'stock' => intval($_GPC['stock']),
				'stock_control' => intval($_GPC['stock_control']),
				'min_buy' => intval($_GPC['min_buy']),
				'max_buy' => intval($_GPC['max_buy']),
				'fprice' => $_GPC['fprice'],
				'dispatchid' => intval($_GPC['dispatchid']),
				'isrecommend' => !empty($_GPC['isrecommend']) ? intval($_GPC['isrecommend']) : 2,
				'isnew' => !empty($_GPC['isnew']) ? intval($_GPC['isnew']) : 2,
				'ishot' => !empty($_GPC['ishot']) ? intval($_GPC['ishot']) : 2,
				'isnopostage' => !empty($_GPC['isnopostage']) ? intval($_GPC['isnopostage']) : 2,
			);
			if ($goods['stock'] < -1) {
				$goods['stock'] = 0;
			}
			if ($goods['max_buy'] != -1 && $goods['min_buy'] > $goods['max_buy']) {
				message('单次最小购买量大于单次最大购买量', '', 'error');
			}
			$express_set = array(
				'default_express' => is_numeric($_GPC['default_express']) ? $_GPC['default_express'] : 0,
				'condition' => is_numeric($_GPC['condition']) ? $_GPC['condition'] : 0,
				'express' => is_numeric($_GPC['express']) ? $_GPC['express'] : 0,
			);
			$goods['express_set'] = iserializer($express_set);
			$data = array_merge($goods, $common);
		} else {
			$room = array(
				'breakfast' => $_GPC['breakfast'],
				'area' => $_GPC['area'],
				'area_show' => $_GPC['area_show'],
				'bed' => $_GPC['bed'],
				'bed_show' => $_GPC['bed_show'],
				'bedadd' => $_GPC['bedadd'],
				'bedadd_show' => $_GPC['bedadd_show'],
				'persons' => $_GPC['persons'],
				'persons_show' => $_GPC['persons_show'],
				'floor' => $_GPC['floor'],
				'floor_show' => $_GPC['floor_show'],
				'smoke' => $_GPC['smoke'],
				'smoke_show' => $_GPC['smoke_show'],
				'service' => intval($_GPC['service']),
				'is_house' => $is_house,
			);
			$data = array_merge($room, $common);
		}
		if ($store['agent_status'] == 1 && !empty($_GPC['agent_ratio']) && is_array($_GPC['agent_ratio'])) {
			$agent_ratio = $_GPC['agent_ratio'];
			foreach ($agent_ratio as &$val) {
				if ($val < 0 || $val > 100) {
					message('分销员分销比例填写错误', '', 'error');
				}
				$val = sprintf('%.2f', $val);
			}
			$data['agent_ratio'] = iserializer($agent_ratio);
		}
		if ($store_type == STORE_TYPE_NORMAL) {
			$share_insert = array(
				'uniacid' => intval($_W['uniacid']),
				'storeid' => $storeid,
				'title' => trim($_GPC['share']['title']),
				'thumb' => trim($_GPC['share']['thumb']),
				'content' => trim($_GPC['share']['content']),
				'status' => intval($_GPC['share']['status']),
				'type' => 'goods',
				'goodstable' => 'storex_goods',
			);
		}
		if (empty($id)) {
			pdo_insert($table, $data);
			$goodsid = pdo_insertid();
			if ($store_type == STORE_TYPE_NORMAL) {
				$share_insert['goodsid'] = $goodsid;
				pdo_insert('storex_share_set', $share_insert);
			}
			$cachekey = "wn_storex:goods_entry:{$storeid}";
			cache_delete($cachekey);
		} else {
			if ($store_type == STORE_TYPE_NORMAL) {
				$share_insert['goodsid'] = $id;
				if (!empty($_GPC['share']['share_id'])) {
					pdo_update('storex_share_set', $share_insert, array('id' => intval($_GPC['share']['share_id'])));
				} else {
					pdo_insert('storex_share_set', $share_insert);
				}
			}
			pdo_update($table, $data, array('id' => $id));
		}
		//规格处理
		if ($store_type == STORE_TYPE_NORMAL) {
			if (!empty($id)) {
				$spec_goods_id = $id;
			} else {
				$spec_goods_id = $goodsid;
			}
			spec_goods($storeid, $spec_goods_id);
		}
		if (isset($defined['defined'])) {
			if (!empty($id)) {
				$goods_extend = pdo_get('storex_goods_extend', array('storeid' => $storeid, 'goodsid' => $id));
				if (!empty($goods_extend)) {
					pdo_update('storex_goods_extend', $defined, array('goodsid' => $id, 'storeid' => $storeid));
				} else {
					$defined['goodsid'] = $id;
					pdo_insert('storex_goods_extend', $defined);
				}
			} else {
				$defined['goodsid'] = $goodsid;
				pdo_insert('storex_goods_extend', $defined);
			}
		}
		if ($store_type == STORE_TYPE_HOTEL) {
			pdo_query("UPDATE " . tablename('storex_hotel') . " SET roomcount = (SELECT COUNT(*) FROM " . tablename('storex_room') . " WHERE store_base_id = :store_base_id AND is_house = :is_house) WHERE store_base_id = :store_base_id", array(':store_base_id' => $storeid, ':is_house' => $data['is_house']));
		}
		itoast('商品信息更新成功！', $this->createWebUrl('shop_goodsmanage', array('storeid' => $storeid)), 'success');
	}
	$template_suffix = '_common';
	if ($store_type == STORE_TYPE_HOTEL) {
		$template_suffix = '_hotel';
	}
	include $this->template('store/shop_goodsedit' . $template_suffix);
}

function spec_goods($storeid, $id) {
	global $_W, $_GPC;
	$common_info = pdo_get('storex_goods', array('store_base_id' => $storeid, 'weid' => $_W['uniacid'], 'id' => $id));
	$spec_goods = array(
		'storeid' => $storeid,
		'uniacid' => $_W['uniacid'],
		'goodsid' => $id,
		'title' => $common_info['title'],
		'sub_title' => $common_info['sub_title'],
		'pcate' => $common_info['pcate'],
		'ccate' => $common_info['ccate'],
		'sp_name' => iserializer($_GPC['sp_name']),
		'sp_val' => iserializer($_GPC['sp_val']),
	);
	$spec_goods_list = $_GPC['spec'];
	if (!empty($spec_goods_list) && is_array($spec_goods_list)) {
		$all_spec_list = pdo_getall('storex_spec_goods', array('storeid' => $storeid, 'uniacid' => $_W['uniacid'], 'goodsid' => $id), array('id'), 'id');
		$key_list = is_array($all_spec_list) ? array_keys($all_spec_list) : array();
		$goodsids = array();
		foreach ($spec_goods_list as $key => $value) {
			if (in_array($value['goodsid'], $key_list)) {
				$goodsids[] = $value['goodsid'];
			}
			if (empty($value['goodsid'])) {
				$spec_goods['goods_val'] = iserializer($value['sp_value']);
				$spec_goods['cprice'] = $value['cprice'] ? $value['cprice'] : 0.00;
				$spec_goods['oprice'] = $value['oprice'] ? $value['oprice'] : 0.00;
				$spec_goods['stock'] = $value['stock'];
				$spec_goods['thumb'] = $value['thumb'];
				pdo_insert('storex_spec_goods', $spec_goods);
			} else {
				pdo_update('storex_spec_goods', array('sp_val' => $spec_goods['sp_val']), array('uniacid' => $_W['uniacid'], 'storeid' => $storeid, 'goodsid' => $id));
				pdo_update('storex_spec_goods', array('goods_val' => iserializer($value['sp_value']), 'cprice' => $value['cprice'], 'oprice' => $value['oprice'], 'stock' => $value['stock'], 'thumb' => $value['thumb']), array('id' => $value['goodsid']));
			}
		}
		$diff_ids = array_diff($key_list, $goodsids);
		pdo_delete('storex_spec_goods', array('id' => $diff_ids));
	}
}

if ($op == 'recycle_goods') {
	$id = intval($_GPC['id']);
	pdo_update($table, array('recycle' => 1), array('weid' => $_W['uniacid'], 'id' => $id));
	itoast('成功加入回收站', referer(), 'success');
}

if ($op == 'recycleall') {
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			pdo_update($table, array('recycle' => 1), array('id' => $id, 'weid' => $_W['uniacid']));
		}
		message(error(0, '批量操作成功'), '', 'ajax');
	}
	message(error(-1, '参数错误'), '', 'ajax');
}

if ($op == 'showall') {
	if ($_GPC['show_name'] == 'showall') {
		$show_status = 1;
	} else {
		$show_status = 0;
	}
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			if (!empty($id)) {
				pdo_update($table, array('status' => $show_status), array('id' => $id));
			}
		}
		message(error(0, '操作成功！'), '', 'ajax');
	}
	message(error(-1, '参数错误'), '', 'ajax');
}
if ($op == 'status') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		message('参数错误！', referer(), 'error');
	}
	$status = pdo_update($table, array('status' => $_GPC['status']), array('id' => $id));
	if (!empty($status)) {
		message('设置成功！', referer(), 'success');
	}
	message('操作失败！', referer(), 'error');
}

if ($op == 'copyroom') {
	$id = intval($_GPC['id']);
	if (empty($storeid) || empty($id)) {
		message('参数错误', referer(), 'error');
	}
	$item = pdo_get($table, array('id' => $id, 'weid' => $_W['uniacid']));
	unset($item['id']);
	$item['status'] = 0;
	pdo_insert($table, $item);
	$id = pdo_insertid();
	$url = $this->createWebUrl('shop_goodsmanage', array('op' => 'edit', 'storeid' => $storeid, 'id' => $id));
	header("Location: $url");
	exit;
}

if ($op == 'qrcode_entry') {
	if ($_W['ispost'] && $_W['isajax']) {
		$goodsid = intval($_GPC['id']);
		if (!empty($goodsid)) {
			$url = murl('entry', array('m' => 'wn_storex', 'id' => $storeid, 'do' => 'display', 'type' => 'goods_info', 'goodsid' => $_GPC['id']), true, true);
			message(error(0, $url), '', 'ajax');
		}
		message(error(-1, '参数错误'), '', 'ajax');
	}
}

if ($op == 'set_tag') {
	$tid = $_GPC['tid'];
	$goodsid = $_GPC['goodsid'];
	if (!empty($tid) && !empty($goodsid)) {
		$result = pdo_update('storex_goods', array('tag' => $tid), array('weid' => $_W['uniacid'], 'id' => $goodsid));
		if (!is_error($result)) {
			message(error(0, '设置标签成功'), '', 'ajax');
		} else {
			message(error(-1, '设置标签成功'), '', 'ajax');
		}
	}
	message(error(-1, '参数错误'), '', 'ajax');
}

if ($op == 'spec_info') {
	if ($_W['isajax'] && $_W['ispost']) {
		$spec_list = array();
		$id = intval($_GPC['id']);
		$category_info = pdo_get('storex_categorys', array('weid' => $_W['uniacid'], 'store_base_id' => $storeid, 'id' => $id), array('spec', 'id'));
		$category_spec = iunserializer($category_info['spec']);
		if (is_array($category_spec)) {
			$spec_name = pdo_getall('storex_spec', array('id' => $category_spec), array('id', 'name'), 'id');
			$spec_value = pdo_getall('storex_spec_value', array('specid' => $category_spec), array('id', 'name', 'displayorder', 'specid'), '', 'displayorder DESC');
		}
		if (!empty($spec_value) && is_array($spec_value)) {
			foreach ($spec_value as $key => $value) {
				$spec_list[$value['specid']]['name'] = $spec_name[$value['specid']]['name'];
				$spec_list[$value['specid']]['values'][$key] = array(
					'id' => $value['id'],
					'name' => $value['name'],
					'displayorder' => $value['displayorder'],
					'specid' => $value['specid']
				);
			}
		}
		message(error(0, $spec_list), '', 'ajax');
	}
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete($table, array('weid' => $_W['uniacid'], 'id' => $id));
	if ($store_type == STORE_TYPE_HOTEL) {
		pdo_query("UPDATE " . tablename('storex_hotel') . " SET roomcount = (SELECT COUNT(*) FROM " . tablename('storex_room') . " WHERE store_base_id = :store_base_id) WHERE store_base_id = :store_base_id", array(':store_base_id' => $storeid));
	}
	itoast('删除成功', referer(), 'success');
}

if ($op == 'deleteall') {
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			pdo_delete($table, array('id' => $id, 'weid' => $_W['uniacid']));
			if ($store_type == STORE_TYPE_HOTEL) {
				pdo_query("UPDATE " . tablename('storex_hotel') . " SET roomcount = (SELECT COUNT(*) FROM " . tablename('storex_room') . " WHERE store_base_id = :store_base_id) WHERE id = :store_base_id", array(':store_base_id' => $id));
			}
		}
		message(error(0, '批量删除成功'), '', 'ajax');
	}
	message(error(-1, '参数错误'), '', 'ajax');
}

if ($op == 'renew') {
	$id = intval($_GPC['id']);
	pdo_update($table, array('recycle' => 2), array('weid' => $_W['uniacid'], 'id' => $id));
	itoast('恢复成功', referer(), 'success');
}

if ($op == 'renewall') {
	if (!empty($_GPC['idArr']) && is_array($_GPC['idArr'])) {
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			pdo_update($table, array('recycle' => 2), array('id' => $id, 'weid' => $_W['uniacid']));
		}
		message(error(0, '批量恢复成功'), '', 'ajax');
	}
	message(error(-1, '参数错误'), '', 'ajax');
}

if ($op == 'goods_sale_rank') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$goodslist = pdo_getall('storex_goods', array('store_base_id' => $storeid, 'weid' => $_W['uniacid']), '', '', array('fact_sold_num DESC', 'id ASC'), array($pindex, $psize));
	if (!empty($goodslist) && is_array($goodslist)) {
		foreach ($goodslist as &$goods) {
			$goods['thumb'] = $goods['thumb'] ? tomedia($goods['thumb']) : '';
			if (!empty($goods['visit_times'])) {
				$goods['ratio'] = sprintf("%.4f", $goods['fact_sold_num'] / $goods['visit_times']) * 100 . '%';
			} else {
				$goods['ratio'] = '0.00%';
			}
		}
		unset($goods);
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_goods') . " WHERE store_base_id = :store_base_id AND weid = :weid", array(':store_base_id' => $storeid, ':weid' => $_W['uniacid']));
	$pager = pagination($total, $pindex, $psize);
	include $this->template('store/shop_goodslist');
}
