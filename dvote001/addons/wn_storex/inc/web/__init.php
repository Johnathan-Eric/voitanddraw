<?php
defined('IN_IA') or exit('Access Denied');

global $_GPC, $_W;
define('SCRIPT_URL', $_W['siteroot'] . '/addons/wn_storex/template/style/js');
$dos = array(
	'shop_index',
	'shop_homepage',
	'shop_wxapphomepage',
	'shop_article',
	'shop_settings',
	'shop_dispatch',
	'shop_share',
	
	'shop_category',
	'shop_spec',
	'shop_spec_value',
	'shop_goods_spec',
	'shop_goodsmanage',
	'shop_comment',
	'shop_booking',
	'shop_return_goods',
	'shop_room_item',
		
	'shop_member',
	'shop_memberlevel',
		
	'shop_order',
		
	'shop_stat',
		
	'shop_clerk',
	'shop_service',
		
	'shop_agent',
	'shop_agent_log',
	
	'shop_market',
	'shop_activity',
	'shop_blast',
	'shop_blast_message',
	'shop_blast_stat',
	'shop_sales_package',
	'shop_poster',
		
	'shop_room_status',
	'shop_room_price',
	'shop_tagmanage',
	
	'shop_plugin',
	'shop_plugin_printer',
	'shop_plugin_printer_setting',
	'shop_plugin_hotelservice',
	'shop_plugin_group',
	'shop_plugin_bargain',
	'shop_plugin_bargain_setting',
	
	'shop_admin_logs',
	
);
mload()->model('log');
$log = log_admin_operation();
if (!empty($log)) {
	log_write($log);
}
if (in_array($_GPC['do'], $dos)) {
	if (empty($_GPC['storeid']) && !($_GPC['do'] == 'shop_settings' && $_GPC['action'] == 'add')) {
		message('请重新选择店铺', $this->createWebUrl('storemanage', array('op' => 'list')), 'error');
	}
	$storex_bases = pdo_get('storex_bases', array('id' => $_GPC['storeid']));
	$_W['wn_storex']['store_info'] = $storex_bases;
	$_W['wn_storex']['table_storeid'] = 'store_base_id';
	if (empty($_W['wn_storex']['store_info']['store_type'])) {
		$_W['wn_storex']['goods_table'] = 'storex_goods';
	} elseif ($_W['wn_storex']['store_info']['store_type'] == 1) {
		$_W['wn_storex']['goods_table'] = 'storex_room';
	}
	$aside_show = true;
	if ($_W['user']['type'] == 3) {
		mload()->model('clerk');
		$clerk_permission = clerk_permission($_GPC['storeid'], $_W['uid']);
		$permission_check = true;
		if ($_GPC['do'] == 'shop_order' && (empty($clerk_permission) || !in_array('wn_storex_permission_order', $clerk_permission))) {
			$permission_check = false;
		}
		if ($_GPC['do'] == 'shop_goodsmanage' && (empty($clerk_permission) || !in_array('wn_storex_permission_goods', $clerk_permission))) {
			$permission_check = false;
		}
		if ($_GPC['do'] == 'shop_room_status' && (empty($clerk_permission) || !in_array('wn_storex_permission_room', $clerk_permission))) {
			$permission_check = false;
		}
		if ($_GPC['do'] == 'shop_room_price' && (empty($clerk_permission) || !in_array('wn_storex_permission_room', $clerk_permission))) {
			$permission_check = false;
		}
		if (empty($permission_check)) {
			message('您没有管理该店铺的权限', '', 'error');
		}
		$aside_show = false;
		if (in_array('wn_storex_menu_storeprofile', $clerk_permission)) {
			$aside_show = true;
		}
	}
}

//店铺后台菜单设置
$aside_nav = array(
	'shop_index' => array(
		'title' => '店铺管理',
		'url' => $this->createWebUrl('shop_index', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-index',
		'active' => array(
			'shop_index',
			'shop_homepage',
			'shop_wxapphomepage',
			'shop_article',
			'shop_settings',
			'shop_share',
			'shop_dispatch',
		),
		'children' => array(
			'shop_index' => array(
				'title' => '概况',
				'url' => $this->createWebUrl('shop_index', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-index',
				'active' => array(
					'shop_index',
				),
			),
			'shop_homepage' => array(
				'title' => '首页设置',
				'url' => $this->createWebUrl('shop_homepage', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-home',
				'active' => array(
					'shop_homepage',
				),
			),
			'shop_wxapphomepage' => array(
				'title' => '小程序首页设置',
				'url' => $this->createWebUrl('shop_wxapphomepage', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-home',
				'active' => array(
					'shop_wxapphomepage',
				),
			),
			'shop_article' => array(
				'title' => '文章管理',
				'url' => $this->createWebUrl('shop_article', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-clerk',
				'active' => array(
					'shop_article',
				),
			),
			'shop_settings' => array(
				'title' => '店铺设置',
				'url' => $this->createWebUrl('shop_settings', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-setting',
				'active' => array(
					'shop_settings',
					'shop_share'
				),
			),
			'shop_dispatch' => array(
				'title' => '配送方式',
				'url' => $this->createWebUrl('shop_dispatch', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-setting',
				'active' => array(
					'shop_dispatch',
				),
			),
		),
	),
	'shop_category' => array(
		'title' => '商品管理',
		'url' => $this->createWebUrl('shop_category', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-goods',
		'active' => array(
			'shop_category',
			'shop_spec',
			'shop_goodsmanage',
			'shop_comment',
			'shop_spec_value',
			'shop_room_status',
			'shop_room_price',
			'shop_room_item',
			'shop_tagmanage',
			'shop_goods_spec',
			'shop_booking',
		),
		'children' => array(
			'shop_category' => array(
				'title' => '商品分类',
				'url' => $this->createWebUrl('shop_category', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-category',
				'active' => array(
					'shop_category'
				),
			),
			'shop_spec' => array(
				'title' => '商品规格',
				'url' => $this->createWebUrl('shop_spec', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-category',
				'active' => array(
					'shop_spec',
					'shop_spec_value'
				),
			),
			'shop_goodsmanage' => array(
				'title' => '商品列表',
				'url' => $this->createWebUrl('shop_goodsmanage', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-goods',
				'active' => array(
					'shop_goodsmanage',
					'shop_room_status',
					'shop_room_price',
					'shop_room_item',
					'shop_tagmanage',
					'shop_goods_spec'
				),
			),
			'shop_comment' => array(
				'title' => '商品评价',
				'url' => $this->createWebUrl('shop_comment', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-comment',
				'active' => array(
					'shop_comment',
				),
			),
			'shop_booking' => array(
				'title' => '预约表单',
				'url' => $this->createWebUrl('shop_booking', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-comment',
				'active' => array(
					'shop_booking',
				),
			),
		),
	),
	'shop_order' => array(
		'title' => '订单管理',
		'url' => $this->createWebUrl('shop_order', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-order',
		'active' => array(
			'shop_order',
		),
	),
	'shop_stat' => array(
		'title' => '数据统计',
		'url' => $this->createWebUrl('shop_stat', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-stat',
		'active' => array(
			'shop_stat',
		),
	),
	'shop_member' => array(
		'title' => '用户管理',
		'url' => $this->createWebUrl('shop_member', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-user',
		'active' => array(
			'shop_member',
			'shop_memberlevel',
		),
	),
	'shop_clerk' => array(
		'title' => '店员管理',
		'url' => $this->createWebUrl('shop_clerk', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-clerk',
		'active' => array(
			'shop_clerk',
			'shop_service'
		),
		'children' => array(
			'shop_clerk' => array(
				'title' => '店员管理',
				'url' => $this->createWebUrl('shop_clerk', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-clerk',
				'active' => array(
					'shop_clerk',
				),
			),
			'shop_service' => array(
				'title' => '客服管理',
				'url' => $this->createWebUrl('shop_service', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-clerk',
				'active' => array(
					'shop_service',
				),
			),
		),
	),
	'shop_agent' => array(
		'title' => '销售员管理',
		'url' => $this->createWebUrl('shop_agent', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-clerk',
		'active' => array(
			'shop_agent',
			'shop_agent_log',
		),
	),
	'shop_market' => array(
		'title' => '营销活动',
		'url' => $this->createWebUrl('shop_market', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-activity',
		'active' => array(
			'shop_market',
			'shop_blast',
			'shop_sales_package',
			'shop_blast_message',
			'shop_blast_stat',
			'shop_poster'
		),
		'children' => array(
			'shop_market' => array(
				'title' => '营销活动',
				'url' => $this->createWebUrl('shop_market', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-app',
				'active' => array(
					'shop_market',
					'shop_sales_package'
				),
			),
			'shop_blast' => array(
				'title' => '爆客',
				'url' => $this->createWebUrl('shop_blast', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-app',
				'active' => array(
					'shop_blast',
					'shop_blast_message',
					'shop_blast_stat'
				),
			),
			'shop_poster' => array(
				'title' => '超级海报',
				'url' => $this->createWebUrl('shop_poster', array('storeid' => $_GPC['storeid'])),
				'icon' => 'storex-menu-app',
				'active' => array(
					'shop_poster',
				),
			),
		)
	),
	'shop_plugin' => array(
		'title' => '应用',
		'url' => $this->createWebUrl('shop_plugin', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-app',
		'active' => array(
			'shop_plugin',
			'shop_plugin_printer',
			'shop_plugin_printer_setting',
			'shop_plugin_hotelservice',
			'shop_plugin_group',
			'shop_plugin_bargain',
			'shop_plugin_bargain_setting'
		),
	),
	'shop_admin_logs' => array(
		'title' => '操作日志',
		'url' => $this->createWebUrl('shop_admin_logs', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-setting',
		'active' => array(
			'shop_admin_logs',
		),
	),
);

if (empty($_W['wn_storex']['store_info']['store_type'])) {
	$aside_nav['shop_category']['active'][] = 'shop_return_goods';
	$aside_nav['shop_category']['children']['shop_return_goods'] = array(
		'title' => '退货列表',
		'url' => $this->createWebUrl('shop_return_goods', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-category',
		'active' => array(
			'shop_return_goods',
		),
	);
	$aside_nav['shop_market']['active'][] = 'shop_activity';
	$aside_nav['shop_market']['children']['shop_activity'] = array(
		'title' => '活动',
		'url' => $this->createWebUrl('shop_activity', array('storeid' => $_GPC['storeid'])),
		'icon' => 'storex-menu-app',
		'active' => array(
			'shop_activity',
		),
	);
}

if ($_GPC['do'] == 'shop_plugin_group') {
	$aside_nav['shop_plugin']['children'] = array(
		'shop_plugin_group' => array(
			'title' => '拼团活动列表',
			'url' => $this->createWebUrl('shop_plugin_group', array('storeid' => $_GPC['storeid'], 'op' => 'display')),
			'icon' => 'storex-menu-app',
			'active' => array(
				'shop_plugin_group',
			),
		),
	);
}
if ($_GPC['do'] == 'shop_plugin_bargain' || $_GPC['do'] == 'shop_plugin_bargain_setting') {
	$aside_nav['shop_plugin']['children'] = array(
		'shop_plugin_bargain' => array(
			'title' => '砍价列表',
			'url' => $this->createWebUrl('shop_plugin_bargain', array('storeid' => $_GPC['storeid'], 'op' => 'display')),
			'icon' => 'storex-menu-app',
			'active' => array(
				'shop_plugin_bargain',
			),
		),
		'shop_plugin_bargain_setting' => array(
			'title' => '砍价设置',
			'url' => $this->createWebUrl('shop_plugin_bargain_setting', array('storeid' => $_GPC['storeid'], 'op' => 'display')),
			'icon' => 'storex-menu-app',
			'active' => array(
				'shop_plugin_bargain_setting',
			),
		),
			
	);
}
if ($_GPC['do'] == 'shop_plugin_printer' || $_GPC['do'] == 'shop_plugin_printer_setting') {
	$aside_nav['shop_plugin']['children'] = array(
		'shop_plugin_printer' => array(
			'title' => '打印机列表',
			'url' => $this->createWebUrl('shop_plugin_printer', array('storeid' => $_GPC['storeid'], 'op' => 'display')),
			'icon' => 'storex-menu-app',
			'active' => array(
				'shop_plugin_printer',
			),
		),
		'shop_plugin_printer_setting' => array(
			'title' => '打印机设置',
			'url' => $this->createWebUrl('shop_plugin_printer_setting', array('storeid' => $_GPC['storeid'], 'op' => 'display')),
			'icon' => 'storex-menu-app',
			'active' => array(
				'shop_plugin_printer_setting',
			),
		),
	);
}
if (empty($_GPC['storeid'])) {
	unset($aside_nav['shop_index']['children']);
}
$sub_menu = array();
foreach ($aside_nav as $key => $value) {
	if (in_array($_GPC['do'], $value['active'])) {
		$sub_menu = $value['children'];
	}
}
//添加店铺不显示多余菜单
if ($_GPC['do'] == 'shop_settings' && $_GPC['op'] == 'post' && $_GPC['action'] == 'add') {
	unset($sub_menu['shop_index'], $sub_menu['shop_homepage'], $sub_menu['shop_wxapphomepage'], $sub_menu['shop_stat'], $sub_menu['shop_article']);
}

if ($_W['wn_storex']['store_info']['store_type'] == 1) {
	unset($sub_menu['shop_spec']);
	if ($_GPC['do'] == 'shop_spec' || $_GPC['do'] == 'shop_spec_value' || $_GPC['do'] == 'shop_goods_spec') {
		message('酒店没有规格功能', referer(), 'error');
	}
	unset($sub_menu['shop_dispatch']);
	if ($_GPC['do'] == 'shop_dispatch') {
		message('酒店没有配送功能', referer(), 'error');
	}
	unset($aside_nav['shop_activity']);
	if ($_GPC['do'] == 'shop_activity') {
		message('酒店没有活动功能', referer(), 'error');
	}
	if ($_GPC['do'] == 'shop_plugin_bargain' || $_GPC['do'] == 'shop_plugin_bargain_setting') {
		message('酒店没有砍价功能', referer(), 'error');
	}
}

if (!check_ims_version()) {
	unset($aside_nav['shop_plugin']);
	if ($_GPC['do'] == 'shop_plugin' || $_GPC['do'] == 'shop_plugin_printer' || $_GPC['shop_plugin_hotelservice']) {
		message('请升级微擎系统至1.0以上，并保持最新版本', '', 'error');
	}
}