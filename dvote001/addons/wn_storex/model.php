<?php
/**
 * 万能小店
 *
 * @author WeEngine Team & ewei
 * @url
 */
defined('IN_IA') or exit('Access Denied');

define('STORE_TYPE_NORMAL', '0');
define('STORE_TYPE_HOTEL', '1');

define('ORDER_STATUS_CANCEL', '-1');
define('ORDER_STATUS_NOT_SURE', '0');
define('ORDER_STATUS_SURE', '1');
define('ORDER_STATUS_REFUSE', '2');
define('ORDER_STATUS_OVER', '3');

define('PAY_STATUS_UNPAID', '0');
define('PAY_STATUS_PAID', '1');

define('GOODS_STATUS_NOT_SHIPPED', '1');
define('GOODS_STATUS_SHIPPED', '2');
define('GOODS_STATUS_RECEIVED', '3');
define('GOODS_STATUS_NOT_CHECKED', '4');
define('GOODS_STATUS_CHECKED', '5');

define('REFUND_STATUS_APPLY', '0');
define('REFUND_STATUS_PROCESS', '1');
define('REFUND_STATUS_SUCCESS', '2');
define('REFUND_STATUS_FAILED', '3');

define('AGENT_STATUS_NOT_VERIFY', '1');
define('AGENT_STATUS_VERIFY', '2');
define('AGENT_STATUS_REFUSE', '3');

define('ACTIVITY_SECKILL', '1');
define('ACTIVITY_LIMITED', '2');

function mload() {
	static $mloader;
	if (empty($mloader)) {
		$mloader = new Mloader();
	}
	return $mloader;
}
class Mloader {
	private $cache = array();
	function func($name) {
		if (isset($this->cache['func'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/wn_storex/function/' . $name . '.func.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['func'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Helper Function /addons/wn_storex/function/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}

	function model($name) {
		if (isset($this->cache['model'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/wn_storex/model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['model'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Model /addons/wn_storex/model/' . $name . '.mod.php', E_USER_ERROR);
			return false;
		}
	}

	function classs($name) {
		if (isset($this->cache['class'][$name])) {
			return true;
		}
		$file = IA_ROOT . '/addons/wn_storex/class/' . $name . '.class.php';
		if (file_exists($file)) {
			include $file;
			$this->cache['class'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Class /addons/wn_storex/class/' . $name . '.class.php', E_USER_ERROR);
			return false;
		}
	}
}

if (!function_exists('get_storex_set')) {
	function get_storex_set() {
		global $_GPC, $_W;
		$cachekey = "wn_storex_set:{$_W['uniacid']}";
		$set = cache_load($cachekey);
		if (!empty($set) && !empty($set['id'])) {
			return $set;
		}
		$set = pdo_get('storex_set', array('weid' => intval($_W['uniacid'])));
		if (empty($set)) {
			$set = array(
				"weid" => intval($_W['uniacid']),
				"user" => 1,
				"bind" => 1,
				"reg" => 1,
				"ordertype" => 1,
				"regcontent" => "",
				"paytype1" => 0,
				"paytype2" => 0,
				"paytype3" => 0,
				"is_unify" => 0,
				"version" => 0,
				"tel" => "",
				"source" => 2,
				"location" => 1,
				"credit_pw" => 2,
			);
			pdo_insert('storex_set', $set);
			$set['id'] = pdo_insertid();
		} else {
			$set['credit_pw_mode'] = iunserializer($set['credit_pw_mode']);
		}
		cache_write($cachekey, $set);
		return $set;
	}
}

/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
if (!function_exists('get_page_array')) {
	function get_page_array($tcount, $pindex, $psize = 15) {
		global $_W;
		$pdata = array(
			'tcount' => 0,
			'tpage' => 0,
			'cindex' => 0,
			'findex' => 0,
			'pindex' => 0,
			'nindex' => 0,
			'lindex' => 0,
			'options' => ''
		);
		$pdata['tcount'] = $tcount;
		$pdata['tpage'] = ceil($tcount / $psize);
		if ($pdata['tpage'] <= 1) {
			$pdata['isshow'] = 0;
			return $pdata;
		}
		$cindex = $pindex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];
		if ($pdata['cindex'] == $pdata['lindex']) {
			$pdata['isshow'] = 0;
			$pdata['islast'] = 1;
		} else {
			$pdata['isshow'] = 1;
			$pdata['islast'] = 0;
		}
		return $pdata;
	}
}
//完成订单后加售出数量
if (!function_exists('add_sold_num')) {
	function add_sold_num($room, $store_type) {
		if ($store_type == STORE_TYPE_HOTEL) {
			pdo_update('storex_room', array('sold_num' => ($room['sold_num']+1)), array('id' => $room['id']));
		} else {
			pdo_update('storex_goods', array('sold_num' => ($room['sold_num']+1)), array('id' => $room['id']));
		}
	}
}

if (!function_exists('gettablebytype')) {
	function gettablebytype($store_type) {
		if ($store_type == 1) {
			return 'storex_room';
		} else {
			return 'storex_goods';
		}
	}
}

if (!function_exists('format_list')) {
	function format_list($category, $list) {
		if (!empty($category) && !empty($list)) {
			$cate = array();
			foreach ($category as $category_info) {
				$cate[$category_info['id']] = $category_info;
			}
			foreach ($list as $k => $info) {
				if (!empty($cate[$info['pcate']])) {
					$list[$k]['pcate_name'] = $cate[$info['pcate']]['name'];
				}
				if (!empty($cate[$info['ccate']])) {
					$list[$k]['ccate_name'] = $cate[$info['ccate']]['name'];
				}
			}
		}
		return $list;
	}
}

function check_ims_version() {
	$compare = ver_compare(IMS_VERSION, '1.0');
	if ($compare != -1) {
		return true;
	} else {
		return false;
	}
}

function wn_tpl_form_field_location_category($name, $values = array(), $del = false) {
	$html = '';
	if (!defined('TPL_INIT_LOCATION_CATEGORY')) {
		$html .= '
		<script type="text/javascript" src="../addons/wn_storex/template/style/js/location.js"></script>';
		define('TPL_INIT_LOCATION_CATEGORY', true);
	}
	if (empty($values) || !is_array($values)) {
		$values = array('cate'=>'','sub'=>'','clas'=>'');
	}
	if (empty($values['cate'])) {
		$values['cate'] = '';
	}
	if (empty($values['sub'])) {
		$values['sub'] = '';
	}
	if (empty($values['clas'])) {
		$values['clas'] = '';
	}
	$html .= '
		<div class="row row-fix tpl-location-container">
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[cate]" data-value="' . $values['cate'] . '" class="form-control tpl-cate">
				</select>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[sub]" data-value="' . $values['sub'] . '" class="form-control tpl-sub">
				</select>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
				<select name="' . $name . '[clas]" data-value="' . $values['clas'] . '" class="form-control tpl-clas">
				</select>
			</div>';
	if ($del) {
		$html .='
			<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="padding-top:5px">
				<a title="删除" onclick="$(this).parents(\'.tpl-location-container\').remove();return false;"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>';
	} else {
		$html .= '</div>';
	}

	return $html;
}

function wn_tpl_district($name, $values = array()) {
	$html = '';
	if (!defined('TPL_INIT_DISTRICT')) {
		$html.= '
		<script type="text/javascript" src="../addons/wn_storex/template/style/js/district.js">
			require(["district"], function(dis){
				$(".tpl-district-container").each(function(){
					var elms = {};
					elms.province = $(this).find(".tpl-province")[0];
					elms.city = $(this).find(".tpl-city")[0];
					elms.district = $(this).find(".tpl-district")[0];
					var vals = {};
					vals.province = $(elms.province).attr("data-value");
					vals.city = $(elms.city).attr("data-value");
					vals.district = $(elms.district).attr("data-value");
					dis.render(elms, vals, {withName: true});
				});
			});
		</script>';
		define('TPL_INIT_DISTRICT', true);
	}
	if (empty($values) || !is_array($values)) {
		$values = array('province'=>'','city'=>'','district'=>'');
	}
	if(empty($values['province'])) {
		$values['province'] = '';
	}
	if(empty($values['city'])) {
		$values['city'] = '';
	}
	if(empty($values['district'])) {
		$values['district'] = '';
	}
	$html .= '
		<div class="row row-fix tpl-district-container">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[province]" data-value="' . $values['province'] . '" class="form-control tpl-province">
				</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[city]" data-value="' . $values['city'] . '" class="form-control tpl-city">
				</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[district]" data-value="' . $values['district'] . '" class="form-control tpl-district">
				</select>
			</div>
		</div>';
	return $html;
}

function wmessage($msg, $share = '', $type = '') {
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		$vars = array();
		$vars['message'] = $msg;
		$vars['share'] = $share;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
}

//检查商品库存，最大购买，最小购买
function check_goods_stock($goodsid, $buynums, $spec_goods = array()) {
	$goods = pdo_get('storex_goods', array('id' => $goodsid), array('id', 'min_buy', 'max_buy', 'stock'));
	if (!empty($spec_goods)) {
		$stock = $spec_goods['stock'];
	} else {
		$stock = $goods['stock'];
	}
	if ($stock >= 0 && ($stock < $buynums || $stock < $goods['min_buy'])) {
		return error(-1, '商品库存不足');
	}
	if ($buynums < $goods['min_buy']) {
		return error(-1, '单次最小购买量是' . $goods['min_buy']);
	}
	if ($goods['max_buy'] != -1 ) {
		if ($buynums > $goods['max_buy']) {
			return error(-1, '单次最大购买量是' . $goods['max_buy']);
		}
	}
}

function stock_control($order, $type) {
	$store = pdo_get('storex_bases', array('id' => $order['hotelid']), array('id', 'stock_warning', 'openids'));
	if (empty($store['stock_warning'])) {
		$store['stock_warning'] = 8;
	}
	if (!empty($order['cart'])) {
		$cart = iunserializer($order['cart']);
		foreach ($cart as $g) {
			if (!empty($g['good']) && $g['buyinfo'][2] != 3) {
				$goods = pdo_get('storex_goods', array('id' => $g['good']['id']), array('id', 'stock', 'stock_control', 'title'));
				if ($goods['stock'] == -1 || $goods['stock_control'] == 1) {
					continue;
				}
				//下单扣库存或者支付成功扣库存
				if (($type == 'order' && $goods['stock_control'] == 2) || ($type == 'pay' && $goods['stock_control'] == 3)) {
					if ($g['buyinfo'][2] == 1) {
						$spec_goods = pdo_get('storex_spec_goods', array('id' => $g['buyinfo'][0]), array('title', 'stock'));
						if (!empty($spec_goods) && $g['buyinfo'][1] <= $spec_goods['stock']) {
							$now_stock = $spec_goods['stock'] - $g['buyinfo'][1];
							pdo_update('storex_spec_goods', array('stock' => $now_stock), array('id' => $g['buyinfo'][0]));
							send_stock_warning($store['openids'], $now_stock, $store['stock_warning'], $spec_goods['title']);
						}
					} else {
						if ($g['buyinfo'][1] <= $goods['stock']) {
							$now_stock = $goods['stock'] - $g['buyinfo'][1];
							pdo_update('storex_goods', array('stock' => $now_stock), array('id' => $g['good']['id']));
							send_stock_warning($store['openids'], $now_stock, $store['stock_warning'], $goods['title']);
						}
					}
				}
			}
		}
	} else {
		$goods = pdo_get('storex_goods', array('id' => $order['roomid']), array('id', 'stock', 'stock_control', 'title'));
		if ($goods['stock'] == -1 || $goods['stock_control'] == 1) {
			return;
		}
		//下单扣库存或者支付成功扣库存
		if (($type == 'order' && $goods['stock_control'] == 2) || ($type == 'pay' && $goods['stock_control'] == 3)) {
			if (!empty($order['spec_id'])) {
				$spec_goods = pdo_get('storex_spec_goods', array('id' => $order['spec_id']), array('stock', 'title'));
				if (!empty($spec_goods) && $order['nums'] <= $spec_goods['stock']) {
					$now_stock = $spec_goods['stock'] - $order['nums'];
					pdo_update('storex_spec_goods', array('stock' => $now_stock), array('id' => $order['spec_id']));
					send_stock_warning($store['openids'], $now_stock, $store['stock_warning'], $spec_goods['title']);
				}
			} else {
				if ($order['nums'] <= $goods['stock']) {
					$now_stock = $goods['stock'] - $order['nums'];
					pdo_update('storex_goods', array('stock' => $now_stock), array('id' => $order['roomid']));
					send_stock_warning($store['openids'], $now_stock, $store['stock_warning'], $goods['title']);
				}
			}
		}
	}
}

function send_stock_warning ($openids, $now_stock, $stock_warning, $title) {
	if (!empty($openids) && $now_stock < $stock_warning) {
		$openids = iunserializer($openids);
		if (is_array($openids)) {
			$info = '<' .$title . '> 库存不足,请及时补充!';
			foreach ($openids as $openid) {
				send_custom_notice('text', array('content' => urlencode($info)), $openid);
			}
		}
	}
}

/*
 * 刷卡支付成功后的操作.
* $result 数组是微信刷卡支付成功返回的数据
* */
function NoticeMicroSuccessOrder($result) {
	if(empty($result['out_trade_no'])) {
		return array('errno' => -1, 'message' => '交易单号错误');
	}
	$pay_log = pdo_get('core_paylog', array('uniontid' => $result['out_trade_no']));
	if(empty($pay_log)) {
		return array('errno' => -1, 'message' => '交易日志不存在');
	}
	$order = pdo_get('storex_paycenter_order', array('uniontid' => $result['out_trade_no']));
	if(empty($order)) {
		return array('errno' => -1, 'message' => '交易订单不存在');
	}
	$data = array(
			//'transaction_id' => $result['transaction_id'],
			'status' => 1,
			'openid' => $result['openid'],
	);
	pdo_update('core_paylog', $data, array('uniontid' => $result['out_trade_no']));
	$data['trade_type'] = strtolower($result['trade_type']);
	$data['paytime'] = strtotime($result['time_end']);
	$data['uniontid'] = $result['out_trade_no'];
	$data['follow'] = $result['is_subscribe'] == 'Y' ? 1 : 0;
	pdo_update('storex_paycenter_order', $data, array('uniontid' => $result['out_trade_no']));
	if(!$order['credit_status'] && $order['uid'] > 0) {
		load()->model('mc');
		$member_credit = mc_credit_fetch($order['uid']);
		$message = '';
		if($member_credit['credit1'] < $order['credit1']) {
			$message = '会员账户积分少于需扣除积分';
		}
		if($member_credit['credit2'] < $order['credit2']) {
			$message = '会员账户余额少于需扣除余额';
		}
		if(!empty($message)) {
			return array('errno' => -10, 'message' => "该订单需要扣除会员积分:{$order['credit1']}, 扣除余额{$order['credit2']}.出错:{$message}.你需要和会员沟通解决该问题.");
		}
		if($order['credit1'] > 0) {
			$status = mc_credit_update($order['uid'], 'credit1', -$order['credit1'], array(0, "会员刷卡消费,使用积分抵现,扣除{$order['credit1']}积分", 'system', $order['clerk_id'], $order['store_id'], $order['clerk_type']));
		}
		if($order['credit2'] > 0) {
			$status = mc_credit_update($order['uid'], 'credit2', -$order['credit2'], array(0, "会员刷卡消费,使用余额支付,扣除{$order['credit2']}余额", 'system', $order['clerk_id'], $order['store_id'], $order['clerk_type']));
		}
	}
	pdo_update('storex_paycenter_order', array('credit_status' => 1), array('id' => $order['id']));
	return array('errno' => 0, 'message' => '支付成功');
}

function get_share_data($type, $param = array(), $share = array()) {
	global $_W;
	$agent = pdo_get('storex_agent_apply', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'storeid' => $param['storeid'], 'status' => 2), array('id'));
	$link = '';
	if (!empty($agent)) {
		$link = '&agentid=' . $agent['id'];
	}
	$store_info = pdo_get('storex_bases', array('id' => $param['storeid']), array('title', 'location_p', 'location_c', 'location_a', 'phone', 'mail', 'store_type'));
	if (!empty($type)) {
		$condition = array('type' => $type, 'storeid' => $param['storeid'], 'uniacid' => $_W['uniacid'], 'status' => 1);
		if ($type == 'goods') {
			$condition['goodsid'] = $param['goodsid'];
		}
		$share_set = pdo_get('storex_share_set', $condition);
		if (!empty($share_set)) {
			$share_data = array(
				'title' => $share_set['title'],
				'desc' => $share_set['content'],
				'link' => $share_set['link'],
				'imgUrl' => tomedia($share_set['thumb'])
			);
			$data = array();
			if ($type == 'homepage') {
				$fields = array('title', 'province', 'city', 'town', 'phone', 'mail');
				if (!empty($store_info) && is_array($store_info)) {
					foreach ($fields as $v) {
						$data['$' . $v] = '';
						if (!empty($store_info[$v])) {
							$data['$' . $v] = $store_info[$v];
						}
					}
					$data['$province'] = $store_info['location_p'];
					$data['$city'] = $store_info['location_c'];
					$data['$town'] = $store_info['location_a'];
				}
			} elseif ($type == 'category') {
				$fields = array('title', 'name');
				$data['$title'] = $store_info['title'];
				if (!empty($param['categoryid'])) {
					$category = pdo_get('storex_categorys', array('id' => $param['categoryid']), array('name'));
					$data['$name'] = '';
					if (!empty($category)) {
						$data['$name'] = $category['name'];
					}
				}
			} elseif ($type == 'goods') {
				$fields = array('title', 'name', 'sub_title', 'oprice', 'cprice', 'tag');
				$data['$title'] = $store_info['title'];
				$data['$name'] = '';
				if (!empty($param['goodsid'])) {
					$table = gettablebytype($store_info['store_type']);
					$goods = pdo_get($table, array('id' => $param['goodsid']), array('title', 'sub_title', 'oprice', 'cprice', 'tag'));
					if (!empty($goods) && is_array($goods)) {
						foreach ($fields as $v) {
							$data['$' . $v] = '';
							if (!empty($goods[$v])) {
								$data['$' . $v] = $goods[$v];
							}
						}
						$data['$title'] = $store_info['title'];
						$data['$name'] = $goods['title'];
					}
				}
			}
			if (!empty($data) && is_array($data)) {
				foreach ($data as $key => $value) {
					$share_data['title'] = str_replace($key, $value, $share_data['title']);
					$share_data['desc'] = str_replace($key, $value, $share_data['desc']);
				}
				if (empty($share)) {
					$share_data['link'] .= $link;
					return $share_data;
				}
				if (!empty($share_data) && is_array($share_data)) {
					foreach ($share_data as $field => $info) {
						if (!empty($info) && isset($share[$field])) {
							$share[$field] = $info;
						}
					}
				}
			}
		}
	}
	if ($store_info['store_type'] == STORE_TYPE_HOTEL) {
		$share['link'] = murl('entry', array('do' => 'display', 'id' => $param['storeid'], 'm' => 'wn_storex', 'type' => 'storeindex'), true, true);
	}
	$share['link'] .= $link;
	return $share;
}

function wxapp_uniacid() {
	global $_W, $_GPC;
	if ($_W['account']['type'] == 4) {
		load()->model('wxapp');
		load()->model('account');
		$account = uni_fetch(intval($_GPC['i']));
		$account['versions'] = wxapp_get_some_lastversions($account['uniacid']);
		$v = trim($_GPC['v']);
		if (!empty($account['versions'])) {
			foreach ($account['versions'] as $version) {
				if ($version['version'] == $v) {
					$modules = iunserializer($version['modules']);
					breack;
				}
			}
		}
		if (!empty($modules) && !empty($modules['wn_storex']['uniacid'])) {
			$_W['uniacid'] = $modules['wn_storex']['uniacid'];
		}
	}
}

function wn_debug($value){
	$value_type = gettype($value);
	echo "<pre>";
	if (in_array($value_type, array('boolean', 'number')) || $value === '') {
		var_dump($value);
	} else {
		print_r($value);
	}
	echo "</pre>";
}

function array_has_element($params) {
	return is_array($params) && !empty($params);
}

function change_domain($url) {
	global $_W;
	if (!empty($url)) {
		$urls = explode('/', $url);
		foreach ($urls as $k => $v) {
			if ($v == 'attachment' || $v == 'app') {
				$url = $_W['siteroot'];
				$url .= '/' . implode('/', array_splice($urls, $k));
				break;
			}
		}
	}
	return $url;
}

//检查具体行为的权限
function check_user_permit($actions = array()) {
	global $_W;
	$user = user_single($_W['uid']);
	$uni_account_user = pdo_get('uni_account_users', array('uniacid' => $_W['uniacid'], 'uid' => $user['uid'], 'role !=' => 'clerk'), array('id', 'role'));
	$permit = false;
	if ($_W['isfounder'] == 1 || $user['founder_groupid'] == 1 || $user['founder_groupid'] == 2 || $user['type'] != 3) {
		$permit = true;
	}
	$actions_permit = array();
	if (!empty($actions) && is_array($actions)) {
		$menu_permission = pdo_get('users_permission', array('uid' => $user['uid'], 'uniacid' => $_W['uniacid'], 'type' => 'wn_storex'));
		if (!empty($menu_permission['permission']) && $menu_permission['permission'] != 'all') {
			$permissions = explode('|', $menu_permission['permission']);
		}
		//操作员和管路员需要判断权限
		if (!empty($uni_account_user)) {
			if ($uni_account_user['role'] == 'manager' || $uni_account_user['role'] == 'operator') {
				$permit = false;
				if (empty($menu_permission['permission'])) {
					$permit = true;
				}
			}
		}
		foreach ($actions as $action) {
			if (!empty($permit) || $menu_permission['permission'] == 'all') {
				$actions_permit[$action] = true;
			} else {
				if (!empty($permissions) && in_array($action, $permissions)) {
					$actions_permit[$action] = true;
				} else {
					$actions_permit[$action] = false;
				}
			}
		}	
	}
	return $actions_permit;
}
