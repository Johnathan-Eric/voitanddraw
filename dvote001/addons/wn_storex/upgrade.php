<?php 
if (pdo_fieldexists('storex_room', 'hotelid')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " CHANGE `hotelid` `store_base_id` INT(11) NULL DEFAULT '0';");
}
if (!pdo_fieldexists('storex_room', 'is_house')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " ADD `is_house` INT(11) NOT NULL DEFAULT '1' COMMENT '是否是房型 1 是，2不是 ';");
}
if (!pdo_fieldexists('storex_comment', 'goodsid')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `goodsid` INT(11) NOT NULL COMMENT '评论商品的id';");
}
if (!pdo_fieldexists('storex_comment', 'comment_level')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `comment_level` TINYINT(11) NOT NULL COMMENT '评论商品的级别';");
}
if (!pdo_fieldexists('storex_order', 'track_number')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `track_number` varchar(64) NOT NULL COMMENT '物流单号';");
}
if (!pdo_fieldexists('storex_order', 'express_type')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `express_type` varchar(100) NOT NULL COMMENT '快递类型';");
}
if (!pdo_fieldexists('storex_order', 'express_name')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `express_name` varchar(50) NOT NULL COMMENT '物流类型';");
}
if (!pdo_fieldexists('storex_bases', 'distance')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `distance` int(11) NOT NULL COMMENT '配送距离';");
}
if (pdo_fieldexists('storex_bases', 'lng')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " CHANGE `lng` `lng` DECIMAL(10,6) NULL DEFAULT '0.00';");
}
if (pdo_fieldexists('storex_bases', 'lat')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " CHANGE `lat` `lat` DECIMAL(10,6) NULL DEFAULT '0.00';");
}

$sql = "
	CREATE TABLE IF NOT EXISTS `ims_storex_clerk` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`weid` int(11) DEFAULT '0',
	`userid` int(11) DEFAULT '0',
	`from_user` varchar(50) DEFAULT '',
	`realname` varchar(255) DEFAULT '',
	`mobile` varchar(255) DEFAULT '',
	`score` int(11) DEFAULT '0' COMMENT '积分',
	`createtime` int(11) DEFAULT '0',
	`userbind` int(11) DEFAULT '0',
	`status` int(11) DEFAULT '0',
	`username` varchar(30) DEFAULT '' COMMENT '用户名',
	`password` varchar(200) DEFAULT '' COMMENT '密码',
	`salt` varchar(8) NOT NULL DEFAULT '' COMMENT '加密盐',
	`nickname` varchar(255) NOT NULL DEFAULT '',
	`permission` text NOT NULL COMMENT '店员权限',
	`storeid` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `indx_weid` (`weid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_notices` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`uid` int(10) unsigned NOT NULL DEFAULT '0',
	`type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:公共消息，2:个人消息',
	`title` varchar(30) NOT NULL,
	`thumb` varchar(100) NOT NULL,
	`groupid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知会员组。默认为所有会员',
	`content` text NOT NULL,
	`addtime` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `uid` (`uid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_notices_unread` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`notice_id` int(10) unsigned NOT NULL DEFAULT '0',
	`uid` int(10) unsigned NOT NULL DEFAULT '0',
	`is_new` tinyint(3) unsigned NOT NULL DEFAULT '1',
	`type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:公共通知，2：个人通知',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `uid` (`uid`),
	KEY `notice_id` (`notice_id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_sign_record` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`uid` int(10) unsigned NOT NULL DEFAULT '0',
	`credit` int(10) unsigned NOT NULL DEFAULT '0',
	`is_grant` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`addtime` int(10) unsigned NOT NULL DEFAULT '0',
	`year` smallint(4) NOT NULL COMMENT '签到的年',
	`month` smallint(2) NOT NULL COMMENT '签到的月',
	`day` smallint(2) NOT NULL COMMENT '签到的日',
	`remedy` tinyint(2) NOT NULL COMMENT '是否是补签 1 是补签,2 是额外',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `uid` (`uid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_sign_set` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`sign` varchar(1000) NOT NULL,
	`share` varchar(500) NOT NULL,
	`content` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_mc_card` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`title` varchar(100) NOT NULL DEFAULT '' COMMENT '会员卡名称',
	`color` varchar(255) NOT NULL DEFAULT '' COMMENT '会员卡字颜色',
	`background` varchar(255) NOT NULL DEFAULT '' COMMENT '背景设置',
	`logo` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo图片',
	`format_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否用手机号作为会员卡号',
	`format` varchar(50) NOT NULL DEFAULT '' COMMENT '会员卡卡号规则',
	`description` varchar(512) NOT NULL DEFAULT '' COMMENT '会员卡说明',
	`fields` varchar(1000) NOT NULL DEFAULT '' COMMENT '会员卡资料',
	`snpos` int(11) NOT NULL DEFAULT '0',
	`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用1:启用0:关闭',
	`business` text NOT NULL,
	`discount_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '折扣类型.1:满减,2:折扣',
	`discount` varchar(3000) NOT NULL DEFAULT '' COMMENT '各个会员组的优惠详情',
	`grant` varchar(3000) NOT NULL COMMENT '领卡赠送:积分,余额,优惠券',
	`grant_rate` varchar(20) NOT NULL DEFAULT '0' COMMENT '消费返积分比率',
	`offset_rate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分抵现比例',
	`offset_max` int(10) NOT NULL DEFAULT '0' COMMENT '每单最多可抵现金数量',
	`nums_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '计次是否开启，0为关闭，1为开启',
	`nums_text` varchar(15) NOT NULL COMMENT '计次名称',
	`nums` varchar(1000) NOT NULL DEFAULT '' COMMENT '计次规则',
	`times_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '计时是否开启，0为关闭，1为开启',
	`times_text` varchar(15) NOT NULL COMMENT '计时名称',
	`times` varchar(1000) NOT NULL DEFAULT '' COMMENT '计时规则',
	`params` longtext NOT NULL,
	`html` longtext NOT NULL,
	`recommend_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`sign_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '签到功能是否开启，0为关闭，1为开启',
	`brand_name` varchar(128) NOT NULL DEFAULT '' COMMENT '商户名字,',
	`notice` varchar(48) NOT NULL DEFAULT '' COMMENT '卡券使用提醒',
	`quantity` int(10) NOT NULL DEFAULT '0' COMMENT '会员卡库存',
	`max_increase_bonus` int(10) NOT NULL DEFAULT '0' COMMENT '用户单次可获取的积分上限',
	`least_money_to_use_bonus` int(10) NOT NULL DEFAULT '0' COMMENT '抵扣条件',
	`source` int(1) NOT NULL DEFAULT '1' COMMENT '1.系统会员卡，2微信会员卡',
	`card_id` varchar(250) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_mc_card_members` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`uid` int(10) DEFAULT NULL,
	`openid` varchar(50) NOT NULL,
	`cid` int(10) NOT NULL DEFAULT '0',
	`cardsn` varchar(20) NOT NULL DEFAULT '',
	`mobile` varchar(11) NOT NULL COMMENT '注册手机号',
	`email` varchar(50) NOT NULL COMMENT '邮箱',
	`realname` varchar(255) NOT NULL COMMENT '真实姓名',
	`status` tinyint(1) NOT NULL,
	`createtime` int(10) unsigned NOT NULL,
	`nums` int(10) unsigned NOT NULL DEFAULT '0',
	`endtime` int(10) unsigned NOT NULL DEFAULT '0',
	`fields` varchar(2500) NOT NULL COMMENT '扩展的信息',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_mc_card_record` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`uid` int(10) unsigned NOT NULL DEFAULT '0',
	`type` varchar(15) NOT NULL,
	`model` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1：充值，2：消费',
	`fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值金额',
	`tag` varchar(10) NOT NULL COMMENT '次数|时长|充值金额',
	`note` varchar(255) NOT NULL,
	`remark` varchar(200) NOT NULL COMMENT '备注，只有管理员可以看',
	`addtime` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `uid` (`uid`),
	KEY `addtime` (`addtime`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_activity_exchange` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`title` varchar(100) NOT NULL COMMENT '物品名称',
	`description` text NOT NULL COMMENT '描述信息',
	`thumb` varchar(500) NOT NULL COMMENT '缩略图',
	`type` tinyint(1) unsigned NOT NULL COMMENT '物品类型，1系统卡券，2微信呢卡券，3实物，4虚拟物品(未启用)，5营销模块操作次数',
	`extra` varchar(3000) NOT NULL DEFAULT '' COMMENT '兑换产品属性 卡券自增id',
	`credit` int(10) unsigned NOT NULL COMMENT '兑换积分数量',
	`credittype` varchar(10) NOT NULL COMMENT '兑换积分类型',
	`pretotal` int(11) NOT NULL COMMENT '每个人最大兑换次数',
	`num` int(11) NOT NULL COMMENT '已兑换礼品数量',
	`total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总量',
	`status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
	`starttime` int(10) unsigned NOT NULL,
	`endtime` int(10) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `extra` (`extra`(333))
	) DEFAULT CHARSET=utf8 COMMENT='兑换表';
		
	CREATE TABLE IF NOT EXISTS `ims_storex_coupon` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`acid` int(10) unsigned NOT NULL DEFAULT '0',
	`card_id` varchar(50) NOT NULL,
	`type` varchar(15) NOT NULL COMMENT '卡券类型',
	`logo_url` varchar(150) NOT NULL,
	`code_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'code类型（二维码/条形码/code码）',
	`brand_name` varchar(15) NOT NULL COMMENT '商家名称',
	`title` varchar(15) NOT NULL,
	`sub_title` varchar(20) NOT NULL,
	`color` varchar(15) NOT NULL,
	`notice` varchar(15) NOT NULL COMMENT '使用说明',
	`description` varchar(1000) NOT NULL,
	`date_info` varchar(200) NOT NULL COMMENT '使用期限',
	`quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总库存',
	`use_custom_code` tinyint(3) NOT NULL DEFAULT '0',
	`bind_openid` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`can_share` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可分享',
	`can_give_friend` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可转赠给朋友',
	`get_limit` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '每人领取限制',
	`service_phone` varchar(20) NOT NULL,
	`extra` varchar(1000) NOT NULL,
	`status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:审核中,2:未通过,3:已通过,4:卡券被商户删除,5:未知',
	`is_display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架',
	`is_selfconsume` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启自助核销',
	`promotion_url_name` varchar(10) NOT NULL,
	`promotion_url` varchar(100) NOT NULL,
	`promotion_url_sub_title` varchar(10) NOT NULL,
	`source` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '来源，1是系统，2是微信',
	`dosage` int(10) unsigned DEFAULT '0' COMMENT '已领取数量',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`,`acid`),
	KEY `card_id` (`card_id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_coupon_record` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`acid` int(10) unsigned NOT NULL,
	`card_id` varchar(50) NOT NULL,
	`openid` varchar(50) NOT NULL,
	`friend_openid` varchar(50) NOT NULL,
	`givebyfriend` tinyint(3) unsigned NOT NULL,
	`code` varchar(50) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`addtime` int(10) unsigned NOT NULL,
	`usetime` int(10) unsigned NOT NULL,
	`status` tinyint(3) NOT NULL,
	`clerk_name` varchar(15) NOT NULL,
	`clerk_id` int(10) unsigned NOT NULL,
	`store_id` int(10) unsigned NOT NULL,
	`clerk_type` tinyint(3) unsigned NOT NULL,
	`couponid` int(10) unsigned NOT NULL,
	`uid` int(10) unsigned NOT NULL,
	`grantmodule` varchar(255) NOT NULL,
	`remark` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`,`acid`),
	KEY `card_id` (`card_id`),
	KEY `hash` (`hash`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_coupon_store` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) NOT NULL,
	`couponid` varchar(255) NOT NULL DEFAULT '',
	`storeid` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `couponid` (`couponid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_mc_member_property` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`property` varchar(200) NOT NULL DEFAULT '' COMMENT '当前公众号用户属性',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 COMMENT='用户属性设置表';

	CREATE TABLE IF NOT EXISTS `ims_storex_coupon_activity` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) NOT NULL,
	`msg_id` int(10) NOT NULL DEFAULT '0',
	`status` int(10) NOT NULL DEFAULT '1',
	`title` varchar(255) NOT NULL DEFAULT '',
	`type` int(3) NOT NULL DEFAULT '0' COMMENT '1 发送系统卡券 2发送微信卡券',
	`thumb` varchar(255) NOT NULL DEFAULT '',
	`coupons` int(11) NOT NULL COMMENT '选择派发的卡券的id',
	`description` varchar(255) NOT NULL DEFAULT '‘’',
	`members` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_activity_stores` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`business_name` varchar(50) NOT NULL,
	`branch_name` varchar(50) NOT NULL,
	`category` varchar(255) NOT NULL,
	`province` varchar(15) NOT NULL,
	`city` varchar(15) NOT NULL,
	`district` varchar(15) NOT NULL,
	`address` varchar(50) NOT NULL,
	`longitude` varchar(15) NOT NULL,
	`latitude` varchar(15) NOT NULL,
	`telephone` varchar(20) NOT NULL,
	`photo_list` varchar(10000) NOT NULL,
	`avg_price` int(10) unsigned NOT NULL,
	`recommend` varchar(255) NOT NULL,
	`special` varchar(255) NOT NULL,
	`introduction` varchar(255) NOT NULL,
	`open_time` varchar(50) NOT NULL,
	`location_id` int(10) unsigned NOT NULL,
	`status` tinyint(3) unsigned NOT NULL COMMENT '1 审核通过 2 审核中 3审核未通过',
	`source` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1为系统门店，2为微信门店',
	`message` varchar(500) NOT NULL,
	`store_base_id` int(11) NOT NULL COMMENT '普通店铺添加为微信门店',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `location_id` (`location_id`)
	) DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	CREATE TABLE IF NOT EXISTS `ims_storex_activity_clerks` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联users表uid',
	`storeid` int(10) unsigned NOT NULL DEFAULT '0',
	`name` varchar(20) NOT NULL,
	`password` varchar(20) NOT NULL,
	`mobile` varchar(20) NOT NULL,
	`openid` varchar(50) NOT NULL,
	`nickname` varchar(30) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `password` (`password`),
	KEY `openid` (`openid`)
	) DEFAULT CHARSET=utf8 COMMENT='积分兑换店员表';
	
	CREATE TABLE IF NOT EXISTS `ims_storex_paycenter_order` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`uid` int(10) unsigned NOT NULL DEFAULT '0',
	`pid` int(10) unsigned NOT NULL DEFAULT '0',
	`clerk_id` int(10) unsigned NOT NULL DEFAULT '0',
	`store_id` int(10) unsigned NOT NULL DEFAULT '0',
	`clerk_type` tinyint(3) unsigned NOT NULL DEFAULT '2',
	`uniontid` varchar(40) NOT NULL,
	`transaction_id` varchar(40) NOT NULL,
	`type` varchar(10) NOT NULL COMMENT '支付方式',
	`trade_type` varchar(10) NOT NULL COMMENT '支付类型:刷卡支付,扫描支付',
	`body` varchar(255) NOT NULL COMMENT '商品信息',
	`fee` varchar(15) NOT NULL COMMENT '商品费用',
	`final_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠后应付价格',
	`credit1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '抵消积分',
	`credit1_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分抵消金额',
	`credit2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额支付金额',
	`cash` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '线上支付金额',
	`remark` varchar(255) NOT NULL,
	`auth_code` varchar(30) NOT NULL,
	`openid` varchar(50) NOT NULL,
	`nickname` varchar(50) NOT NULL COMMENT '付款人',
	`follow` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否关注公众号',
	`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '线上支付状态',
	`credit_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '积分,余额的交易状态.0:未扣除,1:已扣除',
	`paytime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
	`createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_users_permission` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`uid` int(10) unsigned NOT NULL,
	`type` varchar(30) NOT NULL,
	`permission` varchar(10000) NOT NULL,
	`url` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_activity_clerk_menu` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`displayorder` int(4) NOT NULL,
	`pid` int(6) NOT NULL,
	`group_name` varchar(20) NOT NULL,
	`title` varchar(20) NOT NULL,
	`icon` varchar(50) NOT NULL,
	`url` varchar(200) NOT NULL,
	`type` varchar(20) NOT NULL,
	`permission` varchar(50) NOT NULL,
	`system` int(2) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_sales` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`storeid` int(10) unsigned NOT NULL,
	`cumulate` decimal(10,2) DEFAULT '0.00',
	`date` varchar(8) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`,`date`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_refund_logs` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(20) NOT NULL DEFAULT '',
	`uniacid` int(11) NOT NULL,
	`orderid` int(10) unsigned NOT NULL DEFAULT '0',
	`storeid` int(10) unsigned NOT NULL DEFAULT '0',
	`out_refund_no` varchar(40) NOT NULL COMMENT '商户退款订单号',
	`refund_fee` decimal(10,2) NOT NULL,
	`total_fee` decimal(10,2) NOT NULL,
	`status` tinyint(4) NOT NULL DEFAULT '0',
	`time` int(11) DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `orderid` (`orderid`),
	KEY `storeid` (`storeid`),
	KEY `uniacid` (`uniacid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_homepage` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`storeid` int(10) unsigned NOT NULL DEFAULT '0',
	`type` varchar(15) NOT NULL COMMENT '首页块类型',
	`items` longtext NOT NULL,
	`displayorder` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_order_logs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`time` int(11) NOT NULL COMMENT '操作时间',
	`before_change` tinyint(2) NOT NULL COMMENT '更改前的状态',
	`after_change` tinyint(2) NOT NULL COMMENT '更改后的状态',
	`type` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '状态类型',
	`remark` varchar(500) CHARACTER SET utf8 NOT NULL COMMENT '内容',
	`uid` int(10) NOT NULL,
	`clerk_id` int(11) NOT NULL COMMENT '店员id，0 后台操作',
	`clerk_type` tinyint(3) NOT NULL COMMENT '1线上操作，2系统后台，3店员',
	`orderid` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `orderid` (`orderid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_tags` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL,
	`title` varchar(48) NOT NULL COMMENT '标签名称',
	`status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 1开启，2关闭',
	`displayorder` int(10) NOT NULL COMMENT '排序',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_room_items` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	`storeid` int(10) unsigned NOT NULL DEFAULT '0',
	`roomid` int(10) unsigned NOT NULL DEFAULT '0',
	`roomnumber` varchar(100) NOT NULL COMMENT '房间号',
	`status` int(11) DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`),
	KEY `roomid` (`roomid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_market` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) unsigned NOT NULL DEFAULT '0',
	`storeid` int(11) unsigned NOT NULL DEFAULT '0',
	`type` varchar(15) NOT NULL COMMENT '活动类型',
	`items` varchar(1000) NOT NULL,
	`status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
	`starttime` int(11) unsigned NOT NULL,
	`endtime` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_member_level` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL COMMENT '店铺id',
	`title` varchar(24) NOT NULL COMMENT '名称',
	`ask` int(11) NOT NULL COMMENT '条件',
	`level` int(8) NOT NULL COMMENT '等级',
	`status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 1开启，2关闭',
	`default` tinyint(2) NOT NULL DEFAULT '2' COMMENT '1是2不是',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_goods_extend` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`defined` text NOT NULL COMMENT '自定义字段的键值',
	`goodsid` int(11) NOT NULL COMMENT '商品id',
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL COMMENT '酒店id',
	`goods_table` varchar(24) NOT NULL COMMENT '商品表名',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_plugin_room_goods` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) DEFAULT '0',
	`storeid` int(11) DEFAULT NULL COMMENT '酒店id',
	`title` varchar(255) DEFAULT '',
	`price` decimal(10,2) DEFAULT '0.00',
	`status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态1是待确认，2是已确认',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
	CREATE TABLE IF NOT EXISTS `ims_storex_article_category` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) DEFAULT '0',
	`storeid` int(11) DEFAULT NULL COMMENT '酒店id',
	`title` varchar(30) NOT NULL,
	`displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`status` tinyint(3) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_article` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uniacid` int(10) unsigned NOT NULL,
	`storeid` int(10) unsigned NOT NULL,
	`pcate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '一级分类',
	`title` varchar(100) NOT NULL DEFAULT '',
	`description` varchar(100) NOT NULL DEFAULT '',
	`content` mediumtext NOT NULL,
	`thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
	`source` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
	`author` varchar(50) NOT NULL COMMENT '作者',
	`displayorder` int(10) unsigned NOT NULL DEFAULT '0',
	`createtime` int(10) unsigned NOT NULL DEFAULT '0',
	`click` int(10) unsigned NOT NULL DEFAULT '0',
	`type` varchar(10) NOT NULL DEFAULT '',
	`credit` varchar(255) NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_agent_apply` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售员父级',
	`uniacid` int(11) DEFAULT '0',
	`storeid` int(11) DEFAULT '0',
	`openid` varchar(50) NOT NULL,
	`uid` int(10) unsigned NOT NULL,
	`orderids` longtext,
	`status` tinyint(3) DEFAULT '0' COMMENT '1,待审核，2审核通过，3拒绝',
	`applytime` int(11) DEFAULT '0',
	`paytime` int(11) DEFAULT '0',
	`refusetime` int(11) DEFAULT '0',
	`income` decimal(10,2) DEFAULT '0.00',
	`outcome` decimal(10,2) NOT NULL DEFAULT '0.00',
	`alipay` varchar(50) NOT NULL DEFAULT '',
	`realname` varchar(50) NOT NULL DEFAULT '',
	`tel` varchar(20) NOT NULL DEFAULT '',
	`reason` varchar(50) NOT NULL DEFAULT '' COMMENT '拒绝原因',
	`level` int(10) unsigned DEFAULT '0' COMMENT '等级',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`),
	KEY `status` (`status`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_agent_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`uid` int(10) NOT NULL,
	`agentid` int(11) NOT NULL COMMENT '分销员id',
	`orderid` int(11) NOT NULL COMMENT '订单id',
	`storeid` int(11) NOT NULL,
	`goodid` int(11) NOT NULL,
	`sumprice` decimal(10,2) NOT NULL COMMENT '订单总价',
	`money` decimal(10,2) NOT NULL COMMENT '抽成',
	`rate` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '抽成比例百分比',
	`time` int(11) NOT NULL,
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_agent_apply_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`uid` int(10) NOT NULL,
	`ordersn` varchar(30) NOT NULL COMMENT '订单号',
	`agentid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL,
	`money` decimal(10,2) NOT NULL COMMENT '提现金额',
	`time` int(11) NOT NULL COMMENT '申请时间',
	`status` tinyint(4) NOT NULL COMMENT '提现状态0未成功1成功',
	`mngtime` int(11) NOT NULL COMMENT '管理员操作时间',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_sales_package` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`storeid` int(11) DEFAULT '0',
	`uniacid` int(11) DEFAULT '0',
	`title` varchar(255) DEFAULT '',
	`sub_title` varchar(12) NOT NULL COMMENT '副标题',
	`thumb` varchar(255) DEFAULT '',
	`price` decimal(10,2) DEFAULT '0.00',
	`express` decimal(10,2) DEFAULT '0.00',
	`goodsids` varchar(1000) DEFAULT '',
	`status` int(11) DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_goods_package` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`packageid` int(11) DEFAULT '0',
	`storeid` int(11) DEFAULT '0',
	`uniacid` int(11) DEFAULT '0',
	`goodsid` int(11) DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `uniacid` (`uniacid`),
	KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_room_assign` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL,
	`roomid` int(11) NOT NULL,
	`roomitemid` int(11) NOT NULL,
	`time` int(11) NOT NULL COMMENT '房间在此时间内不空闲',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_admin_logs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`uid` int(11) NOT NULL COMMENT '操作者id',
	`username` varchar(50) NOT NULL,
	`time` int(11) NOT NULL COMMENT '操作时间',
	`storeid` int(11) NOT NULL COMMENT '店铺id',
	`content` varchar(500) NOT NULL COMMENT '操作内容',
	`op` varchar(24) NOT NULL,
	`do` varchar(24) NOT NULL,
	`url` varchar(500) NOT NULL COMMENT '访问的url地址',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_blast_message` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  `type` int(11) DEFAULT NULL,
	  `time` int(11) DEFAULT NULL,
	  `title` varchar(255) NOT NULL,
	  `content` varchar(600) NOT NULL,
	  `status` int(11) NOT NULL,
	  `clerkid` int(11) NOT NULL,
	  `uid` int(10) unsigned NOT NULL DEFAULT '0',
	  `isdefault` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1为默认，2为不是默认',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_blast_user` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  `clerkid` int(10) unsigned NOT NULL,
	  `time` int(11) DEFAULT NULL,
	  `uuid` varchar(255) NOT NULL,
	  `openid` varchar(255) NOT NULL,
	  `redirect_uri` varchar(500) NOT NULL,
	  `wxuin` varchar(500) NOT NULL,
	  `wxsid` varchar(500) NOT NULL,
	  `pass_ticket` varchar(1000) NOT NULL,
	  `post_url_header` varchar(800) NOT NULL,
	  `synckey` varchar(1000) NOT NULL,
	  `username` varchar(400) NOT NULL DEFAULT '',
	  `skey` varchar(500) NOT NULL,
	  `contact` longtext NOT NULL,
	  `cookie` varchar(1000) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_blast_stat` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  `clerkid` int(11) NOT NULL,
	  `msgid` int(11) NOT NULL,
	  `type` int(11) DEFAULT NULL,
	  `time` int(11) DEFAULT NULL,
	  `date` varchar(8) NOT NULL,
	  `num` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_clerk_pay` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `clerkid` int(11) NOT NULL COMMENT '店员id',
	  `type` varchar(24) NOT NULL,
	  `money` decimal(10,2) NOT NULL,
	  `openid` varchar(100) NOT NULL COMMENT '扫码用户',
	  `time` int(11) NOT NULL COMMENT '扫码时间',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_spec` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
	  `displayorder` tinyint(4) NOT NULL,
	  `uniacid` int(10) unsigned NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 COMMENT='规格名称表';

	CREATE TABLE IF NOT EXISTS `ims_storex_spec_value` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  `specid` int(10) unsigned NOT NULL,
	  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
	  `displayorder` tinyint(4) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 COMMENT='规格值表';

	CREATE TABLE IF NOT EXISTS `ims_storex_spec_goods` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `storeid` int(11) DEFAULT '0',
	  `uniacid` int(11) DEFAULT '0',
	  `goodsid` int(11) DEFAULT '0',
	  `pcate` int(10) unsigned NOT NULL DEFAULT '0',
	  `ccate` int(10) unsigned NOT NULL DEFAULT '0',
	  `title` varchar(255) DEFAULT '',
	  `sub_title` varchar(12) NOT NULL COMMENT '副标题',
	  `sp_name` varchar(255) DEFAULT NULL,
	  `sp_val` varchar(1000) NOT NULL DEFAULT '' COMMENT '已选中商品规格',
	  `goods_val` text COMMENT '商品规格属性',
	  `thumb` varchar(255) DEFAULT '',
	  `oprice` decimal(10,2) DEFAULT '0.00',
	  `cprice` decimal(10,2) DEFAULT '0.00',
	  `status` int(11) DEFAULT '0',
	  `stock` int(11) NOT NULL DEFAULT '-1' COMMENT '库存',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_wxcard_reply` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0',
	  `title` varchar(30) NOT NULL,
	  `card_id` varchar(50) NOT NULL,
	  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应卡券表里的id',
	  `brand_name` varchar(30) NOT NULL,
	  `logo_url` varchar(255) NOT NULL,
	  `success` varchar(255) NOT NULL,
	  `error` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `rid` (`rid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_share_set` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `type` varchar(50) NOT NULL COMMENT '分享类型',
	  `title` varchar(200) NOT NULL COMMENT '标题',
	  `thumb` varchar(200) NOT NULL COMMENT '图标',
	  `content` varchar(500) NOT NULL COMMENT '描述',
	  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
	  `goodstable` varchar(50) NOT NULL COMMENT '商品属于的表',
 	  `goodsid` int(11) NOT NULL COMMENT '商品id',
	  `link` varchar(200) NOT NULL COMMENT '链接',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_goods_activity` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `storeid` int(11) DEFAULT '0',
	  `uniacid` int(11) DEFAULT '0',
	  `title` varchar(255) DEFAULT '',
	  `price` decimal(10,2) DEFAULT '0.00',
	  `nums` int(10) unsigned NOT NULL,
	  `starttime` int(10) unsigned NOT NULL,
	  `endtime` int(10) unsigned NOT NULL,
	  `goodsid` int(10) unsigned NOT NULL,
	  `type` tinyint(1) DEFAULT '1',
	  `status` int(11) DEFAULT '1',
	  `is_spec` tinyint(1) DEFAULT '1',
	  `specid` int(10) unsigned NOT NULL,
	  `sell_nums` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_blast_set` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) unsigned NOT NULL,
	  `storeid` int(11) unsigned NOT NULL,
	  `bg_image` varchar(500) NOT NULL,
	  `tail` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_cart` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL,
	  `storeid` int(10) unsigned NOT NULL,
	  `goods` varchar(1000) NOT NULL,
	  `uid` varchar(50) NOT NULL,
	  `total` int(10) unsigned NOT NULL,
	  `total_price` decimal(10,2) DEFAULT '0.00',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_poster` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
	  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
	  `name` varchar(100) NOT NULL,
	  `keyword` varchar(50) NOT NULL,
	  `wait` varchar(255) NOT NULL,
	  `background` varchar(255) NOT NULL,
	  `type` varchar(100) NOT NULL COMMENT '海报类型',
	  `params` longtext NOT NULL,
	  `rid` int(10) unsigned NOT NULL,
	  `reward` text NOT NULL COMMENT '奖励',
	  PRIMARY KEY (`id`),
	  KEY `uniacid` (`uniacid`),
	  KEY `storeid` (`storeid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_member_agent` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `openid` varchar(50) NOT NULL,
	  `memberid` int(11) NOT NULL COMMENT '用户id',
	  `agentid` int(11) NOT NULL COMMENT '分销员id',
	  `time` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_wxapp_userdata` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `openid` varchar(255) NOT NULL,
	  `data` text NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_reply` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`weid` int(11) NOT NULL,
	`rid` int(11) NOT NULL,
	`hotelid` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `indx_weid` (`weid`),
	KEY `indx_rid` (`rid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_booking_list` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `openid` varchar(200) NOT NULL,
	  `text` text NOT NULL,
	  `booking_time` int(11) NOT NULL,
	  `status` tinyint(2) NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_booking_set` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `text` text NOT NULL,
	  `prompt` varchar(200) NOT NULL COMMENT '提示',
	  `value` text NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_storex_discount_set` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `uplevel` tinyint(2) NOT NULL DEFAULT '2' COMMENT '1开启升级2不开',
	  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0不开，1满减，2满折扣',
	  `discount` text NOT NULL,
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_customservice` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `kf_account` varchar(255) DEFAULT '',
	  `kf_nick` varchar(255) DEFAULT '',
	  `kf_headimgurl` varchar(500) DEFAULT '',
	  `kf_wx` varchar(500) DEFAULT '',
	  `kf_id` int(11) DEFAULT '0',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;

	CREATE TABLE IF NOT EXISTS `ims_storex_dispatch` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) DEFAULT '0',
	  `storeid` int(11) DEFAULT '0',
	  `name` varchar(50) DEFAULT NULL COMMENT '配送名称',
	  `express` varchar(250) DEFAULT NULL COMMENT '物流公司',
	  `default_first` int(11) DEFAULT '0' COMMENT '默认首重/件',
	  `default_firstprice` decimal(10,2) DEFAULT '0.00' COMMENT '默认首价',
	  `default_second` int(11) DEFAULT '0' COMMENT '默认续重/件',
	  `default_secondprice` decimal(10,2) DEFAULT '0.00' COMMENT '默认续价',
	  `default_nopostage` int(11) DEFAULT '0' COMMENT '包邮价',
	  `content` text COMMENT '区域价格设置',
	  `calculate_type` tinyint(1) DEFAULT '1' COMMENT '计算方式1为重量计费，2为件数计费',
	  `isdispatcharea` tinyint(1) DEFAULT '1' COMMENT '特殊区域设置1为不配送区域，2为只配送区域',
	  `selectedareas_code` text COMMENT '特殊区域列表',
	  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1为开启，2为关闭',
	  `isdefault` tinyint(1) DEFAULT '2' COMMENT '是否默认，1为是，2为否',
	  PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_return_goods` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL,
	`orderid` int(11) NOT NULL,
	`openid` varchar(50) NOT NULL,
	`goodsid` int(11) NOT NULL COMMENT '商品id',
	`spec_id` int(11) NOT NULL COMMENT '规格id',
	`good` varchar(20) NOT NULL COMMENT '用| 分割的字符串',
	`nums` int(11) NOT NULL COMMENT '退货数量',
	`time` int(11) NOT NULL COMMENT '退货申请时间',
	`reason` varchar(500) NOT NULL COMMENT '退货原因',
	`goods_reason` varchar(200) NOT NULL COMMENT '货物状态',
	`content` varchar(500) NOT NULL COMMENT '退款备注',
	`status` tinyint(2) NOT NULL COMMENT '0发起申请， 1后台同意，2不同意',
	`refuse_reason` varchar(500) NOT NULL COMMENT '拒绝原因',
	`goods_status` tinyint(2) NOT NULL COMMENT '0未发货， 1 已发货，2已收货',
	`track_number` varchar(64) NOT NULL COMMENT '物流单号',
	`express_type` varchar(100) NOT NULL COMMENT '快递类型',
	`refund_status` tinyint(2) NOT NULL COMMENT '0未退款， 1已退款',
	`money` decimal(10,2) NOT NULL COMMENT '退款金额',
	`type` tinyint(2) NOT NULL COMMENT '0退货退款，1仅退款',
	`thumbs` text NOT NULL COMMENT '上传凭证图片',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_goods_profit` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`storeid` int(11) NOT NULL,
	`orderid` int(11) NOT NULL,
	`time` int(11) NOT NULL COMMENT '增加记录时间',
	`order_time` int(11) NOT NULL COMMENT '下单时间',
	`money` decimal(10,2) NOT NULL COMMENT '利润',
	PRIMARY KEY (`id`)
	) DEFAULT CHARSET=utf8 COMMENT='成交商品利润记录';

	CREATE TABLE IF NOT EXISTS `ims_storex_member_address` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `uid` int(50) unsigned NOT NULL COMMENT '会员ID',
	  `username` varchar(20) NOT NULL COMMENT '姓名',
	  `mobile` varchar(11) NOT NULL COMMENT '手机',
	  `zipcode` varchar(6) NOT NULL COMMENT '邮政编码',
	  `province` varchar(32) NOT NULL COMMENT '省',
	  `city` varchar(32) NOT NULL COMMENT '市',
	  `district` varchar(32) NOT NULL COMMENT '区',
	  `address` varchar(512) NOT NULL COMMENT '详细地址',
	  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认地址',
	  `citycode` varchar(300) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `idx_uinacid` (`uniacid`),
	  KEY `idx_uid` (`uid`)
	) DEFAULT CHARSET=utf8;
		
	CREATE TABLE IF NOT EXISTS `ims_storex_goods_visit` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `uniacid` int(11) NOT NULL,
	  `storeid` int(11) NOT NULL,
	  `goodsid` int(11) NOT NULL,
	  `openid` varchar(100) NOT NULL,
	  `time` int(11) NOT NULL COMMENT '浏览时间',
	  PRIMARY KEY (`id`)
	)  DEFAULT CHARSET=utf8;
";
pdo_run($sql);

if (pdo_tableexists('storex_set')) {
	$storex_set_info = pdo_getall('storex_set');
	if (empty($storex_set_info)) {
		pdo_run("DROP TABLE " . tablename('storex_set'));
		pdo_run("
			CREATE TABLE IF NOT EXISTS `ims_storex_set` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`weid` int(11) DEFAULT '0',
			`user` tinyint(1) DEFAULT '0' COMMENT '用户类型0微信用户1独立用户',
			`reg` tinyint(1) DEFAULT '0' COMMENT '是否允许注册0禁止注册1允许注册',
			`bind` tinyint(1) DEFAULT '0' COMMENT '是否绑定',
			`regcontent` text COMMENT '注册提示',
			`ordertype` tinyint(1) DEFAULT '0' COMMENT '预定类型0电话预定1电话和网络预订',
			`is_unify` tinyint(1) DEFAULT '0' COMMENT '0使用各分店电话,1使用统一电话',
			`tel` varchar(20) DEFAULT '' COMMENT '统一电话',
			`email` varchar(255) NOT NULL DEFAULT '' COMMENT '提醒接受邮箱',
			`mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '提醒接受手机',
			`template` varchar(32) DEFAULT NULL COMMENT '发送模板消息',
			`templateid` varchar(255) NOT NULL,
			`paytype1` tinyint(1) DEFAULT '0',
			`paytype2` tinyint(1) DEFAULT '0',
			`paytype3` tinyint(1) DEFAULT '0',
			`version` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0单酒店版1多酒店版',
			`location_p` varchar(50) DEFAULT '',
			`location_c` varchar(50) DEFAULT '',
			`location_a` varchar(50) DEFAULT '',
			`smscode` int(3) NOT NULL DEFAULT '0',
			`refund` int(3) NOT NULL DEFAULT '0',
			`refuse_templateid` varchar(255) NOT NULL DEFAULT '' COMMENT '提醒接受邮箱',
			`confirm_templateid` varchar(255) NOT NULL DEFAULT '' COMMENT '提醒接受邮箱',
			`check_in_templateid` varchar(255) NOT NULL DEFAULT '' COMMENT '酒店已入住通知模板id',
			`finish_templateid` varchar(255) NOT NULL DEFAULT '' COMMENT '酒店订单完成通知模板id',
			`nickname` varchar(20) NOT NULL COMMENT '提醒接收微信',
			`extend_switch` varchar(400) NOT NULL COMMENT '扩展开关',
			`source` tinyint(4) NOT NULL DEFAULT '2' COMMENT '卡券类型，1为系统卡券，2为微信卡券',
			`location` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否开启定位1开2关',
			`credit_pw` tinyint(2) NOT NULL DEFAULT '2' COMMENT '1开2关',
			`credit_pw_mode` varchar(100) NOT NULL COMMENT '余额支付密码验证方式',
			`map_key_name` varchar(50) NOT NULL COMMENT '地图秘钥名称',
			`map_key` varchar(200) NOT NULL COMMENT '腾讯地图秘钥',
			PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;
		");
	}
}

if (!pdo_fieldexists('storex_agent_apply', 'pid')) {
	pdo_query("ALTER TABLE " . tablename('storex_agent_apply') . " ADD `pid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销售员父级';");
}
if (!pdo_fieldexists('storex_bases', 'skin_style')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `skin_style` VARCHAR(48) NOT NULL DEFAULT 'display' COMMENT '皮肤选择';");
}
if (!pdo_fieldexists('storex_categorys', 'category_type')) {
	pdo_query("ALTER TABLE " . tablename('storex_categorys') . " ADD `category_type` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '分类类型 1 酒店，2,普通';");
}
$category = pdo_getall('storex_categorys');
$stores = pdo_getall('storex_bases', array(), array('id', 'store_type', 'skin_style'), 'id');
if (!empty($stores)) {
	foreach ($stores as $val) {
		if ($val['skin_style'] == 'style1') {
			pdo_update('storex_bases', array('skin_style' => 'display'), array('id' => $val['id']));
		}
	}
}
if (!empty($category) && !empty($stores)) {
	foreach ($category as &$info){
		if ($info['category_type'] != 2) {
			if (!empty($stores[$info['store_base_id']])) {
				if ($stores[$info['store_base_id']]['store_type'] == 1) {
					$data = array('category_type' => 1);
					$info['category_type'] = 1;
				} else {
					$data = array('category_type' => 2);
					$info['category_type'] = 2;
				}
				pdo_update('storex_categorys', $data, array('id' => $info['id']));
			}
			if (!empty($info['parentid'])) {
				pdo_update('storex_room', array('is_house' => $info['category_type']), array('id' => $val['id'], 'pcate' => $info['id']));
			}
		}
	}
	unset($info);
}


if (!pdo_fieldexists('storex_set', 'extend_switch')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `extend_switch` varchar(400) NOT NULL COMMENT '扩展开关';");
}
$extend = pdo_getall('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'title' => '扩展功能', 'do' => 'extend'));
if (empty($extend)) {
	pdo_insert('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'title' => '扩展功能', 'do' => 'extend', 'icon' => 'fa fa-puzzle-piece'));
} else {
	if (count($extend) > 1) {
		foreach ($extend as $key => $value) {
			if ($value['icon'] == '') {
				pdo_delete('modules_bindings', array('eid' => $value['eid']));
				unset($extend[$key]);
			}
		}
	}
	if (count($extend) > 1) {
		array_pop($extend);
		foreach ($extend as $k => $val) {
			pdo_delete('modules_bindings', array('eid' => $val['eid']));
		}
	}
}
if (pdo_fieldexists('storex_bases', 'timeend')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " CHANGE `timeend` `timeend` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '运营结束时间';");
}
if (pdo_fieldexists('storex_bases', 'timestart')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " CHANGE `timestart` `timestart` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT '运营开始时间';");
}
//删除会员价字段
if (pdo_fieldexists('storex_order', 'mprice')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') ." DROP `mprice`;");
}
if (pdo_fieldexists('storex_room', 'mprice')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') ." DROP `mprice`;");
}
if (pdo_fieldexists('storex_room_price', 'mprice')) {
	pdo_query("ALTER TABLE " . tablename('storex_room_price') ." DROP `mprice`;");
}
if (pdo_fieldexists('storex_goods', 'mprice')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') ." DROP `mprice`;");
}
//删除返积分比例字段
if (pdo_fieldexists('storex_bases', 'integral_rate')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') ." DROP `integral_rate`;");
}
//order表加入coupon字段，使用卡券的recordid
if (!pdo_fieldexists('storex_order', 'coupon')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') ." ADD `coupon` INT NOT NULL COMMENT '使用卡券信息';");
}
if (!pdo_fieldexists('storex_coupon_record', 'granttype')) {
	pdo_query("ALTER TABLE " . tablename('storex_coupon_record') ." ADD `granttype` tinyint(4) NOT NULL COMMENT '获取卡券的方式：1 兑换，2 扫码，3派发';");
}
if (!pdo_fieldexists('storex_set', 'source')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `source` TINYINT NOT NULL DEFAULT '2' COMMENT '卡券类型，1为系统卡券，2为微信卡券';");
}
if (!pdo_fieldexists('storex_room', 'express_set')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " ADD `express_set` TEXT NOT NULL COMMENT '运费设置';");
}
if (!pdo_fieldexists('storex_goods', 'express_set')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `express_set` TEXT NOT NULL COMMENT '运费设置';");
}
if (!pdo_fieldexists('storex_sign_set', 'status')) {
	pdo_query("ALTER TABLE " . tablename('storex_sign_set') . " ADD `status` TINYINT(2) NOT NULL COMMENT '开启状态 1开启，2关闭';");
}

if (!pdo_fieldexists('storex_order', 'static_price')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `static_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '初始订单的价格，不可更改';");
	$orders = pdo_getall('storex_order', array(), array('id', 'sum_price'));
	if (!empty($orders) && is_array($orders)) {
		foreach ($orders as $info) {
			pdo_update('storex_order', array('static_price' => $info['sum_price']), array('id' => $info['id']));
		}
	}
}
if (!pdo_fieldexists('storex_order', 'refund_status')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `refund_status` TINYINT(2) NOT NULL COMMENT '退款状态 1退款中，2成功，3失败';");
}
//删除商圈和品牌不必要功能
$delete_fields = array('ordermax', 'numsmax', 'daymax', 'sales', 'level', 'brandid', 'businessid');
foreach ($delete_fields as $field) {
	if (pdo_fieldexists('storex_hotel', $field)) {
		pdo_query("ALTER TABLE " . tablename('storex_hotel') . " DROP " . $field);
	}
}
if (pdo_fieldexists('storex_bases', 'extend_table')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " DROP `extend_table`;");
}

//酒店的分类只有一级分类，将已有的二级分类的数据兼容到一级下
pdo_update('storex_room', array('ccate' => 0));
pdo_delete('storex_categorys', array('category_type' => 1, 'parentid !=' => 0));

$subscribes = array('user_get_card', 'user_del_card', 'user_consume_card',);
$subscribes = iserializer($subscribes);
pdo_update('modules', array('subscribes' => $subscribes), array('name' => 'wn_storex'));

//order表paytype修改
if (pdo_fieldexists('storex_order', 'paytype')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " CHANGE `paytype` `paytype` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
}
$order_lists = pdo_getall('storex_order');
$pay_type_list = array('credit', 'wechat', 'delivery', 'alipay');
if (!empty($order_lists) && is_array($order_lists)) {
	foreach ($order_lists as $key => $value) {
		if (!empty($value['paytype']) && !in_array($value['paytype'], $pay_type_list)) {
			if (in_array($value['paytype'], array('1', '21', '22', '3'))) {
				if ($value['paytype'] == 1) {
					$update['paytype'] = 'credit';
				} elseif ($value['paytype'] == 21) {
					$update['paytype'] = 'wechat';
				} elseif ($value['paytype'] == 22) {
					$update['paytype'] = 'alipay';
				} elseif ($value['paytype'] == 3) {
					$update['paytype'] = 'delivery';
				}
				pdo_update('storex_order', $update, array('id' => $value['id']));
			}
		}
	}
}
//删除弃用的业务菜单
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'business'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'brand'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'goodscategory'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'goodsmanage'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'order'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'member'));
pdo_delete('modules_bindings', array('module' => 'wn_storex', 'entry' => 'menu', 'do' => 'clerk'));

//商品增加副标题
if (!pdo_fieldexists('storex_room', 'sub_title')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " ADD `sub_title` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '副标题';");
}
if (!pdo_fieldexists('storex_goods', 'sub_title')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `sub_title` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '副标题';");
}
if (pdo_fieldexists('storex_bases', 'category_set')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " DROP `category_set`;");
}
//商品增加标签，单位，重量，库存属性
if (!pdo_fieldexists('storex_goods', 'tag')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `tag` INT(11) NOT NULL;");
}
if (!pdo_fieldexists('storex_goods', 'unit')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `unit` VARCHAR(12) NOT NULL COMMENT '单位';");
}
if (!pdo_fieldexists('storex_goods', 'weight')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `weight` DOUBLE NOT NULL COMMENT '重量';");
}
if (!pdo_fieldexists('storex_goods', 'stock')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `stock` INT(11) NOT NULL DEFAULT '-1' COMMENT '库存';");
}
if (!pdo_fieldexists('storex_goods', 'stock_control')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `stock_control` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '1永不减库存，2拍下减库存，3付款减库存';");
}
if (!pdo_fieldexists('storex_goods', 'min_buy')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `min_buy` INT(11) NOT NULL DEFAULT '1' COMMENT '单次最小购买';");
}
if (!pdo_fieldexists('storex_goods', 'max_buy')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `max_buy` INT(11) NOT NULL DEFAULT '-1' COMMENT '单次最多购买 -1不限';");
}
if (!pdo_fieldexists('storex_clerk', 'storeid')) {
	pdo_query("ALTER TABLE " . tablename('storex_clerk') . " ADD `storeid` int(11) NOT NULL DEFAULT '0', ADD INDEX (`storeid`);");
}
if (pdo_fieldexists('storex_clerk', 'userid')) {
	pdo_query("ALTER TABLE " . tablename('storex_clerk') . " CHANGE `userid` `userid` INT(11) NULL DEFAULT '0'");
}

//用户表增加余额支付的密码和加密盐,改密码的依据
if (!pdo_fieldexists('storex_member', 'credit_password')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `credit_password` VARCHAR(200) NOT NULL COMMENT '余额支付密码';");
}
if (!pdo_fieldexists('storex_member', 'credit_salt')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `credit_salt` VARCHAR(8) NOT NULL COMMENT '加密盐';");
}
if (!pdo_fieldexists('storex_member', 'password_lock')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `password_lock` VARCHAR(24) NOT NULL COMMENT '改密码的依据';");
}

//评论表增加字段
if (!pdo_fieldexists('storex_comment', 'type')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `type` int(10) unsigned DEFAULT '1' COMMENT '回复类型，1为用户，2为虚拟，3为管理员回复';");
}
if (!pdo_fieldexists('storex_comment', 'cid')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当type为3，管理员回复时评价id';");
}
if (!pdo_fieldexists('storex_comment', 'nickname')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `nickname` varchar(255) NOT NULL;");
}
if (!pdo_fieldexists('storex_comment', 'thumb')) {
	pdo_query("ALTER TABLE " . tablename('storex_comment') . " ADD `thumb` varchar(64) NOT NULL DEFAULT '';");
}

$wn_storex_comments = pdo_getall('storex_comment', array('nickname' => '', 'thumb' => ''), array('id', 'uid', 'nickname', 'thumb'));
if (!empty($wn_storex_comments) && is_array($wn_storex_comments)) {
	load()->model('mc');
	foreach ($wn_storex_comments as $comment) {
		if (!empty($comment['uid'])) {
			$fans_info = mc_fansinfo($comment['uid']);
			pdo_update('storex_comment', array('nickname' => $fans_info['nickname'], 'thumb' => $fans_info['avatar']), array('id' => $comment['id']));
		}
	}
}

//店铺表增加字段refund，emails，phones，openids
if (!pdo_fieldexists('storex_bases', 'refund')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `refund` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '是否可退款 1可以 ，2不可以';");
}
if (!pdo_fieldexists('storex_bases', 'emails')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `emails` VARCHAR(200) NOT NULL COMMENT '接收所有邮箱';");
}
if (!pdo_fieldexists('storex_bases', 'phones')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `phones` VARCHAR(200) NOT NULL COMMENT '接收所有电话';");
}
if (!pdo_fieldexists('storex_bases', 'openids')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `openids` VARCHAR(200) NOT NULL COMMENT '接收所有微信';");
}
//加入营销开关
if (!pdo_fieldexists('storex_bases', 'market_status')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `market_status` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '营销开关';");
}
if (!pdo_fieldexists('storex_order', 'roomitemid')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `roomitemid` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '房间号ID';");
}
if (!pdo_fieldexists('storex_order', 'newuser')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `newuser` INT(4) NOT NULL COMMENT '0未使用新用户活动，1已使用';");
}
if (!pdo_fieldexists('storex_order', 'market_types')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `market_types` VARCHAR(48) NOT NULL COMMENT '订单使用店铺内活动的类型';");
}
if (!pdo_fieldexists('storex_order', 'agentid')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `agentid` INT(11) NOT NULL COMMENT '销售员id';");
}
if (!pdo_fieldexists('storex_goods', 'agent_ratio')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `agent_ratio` VARCHAR(300) NOT NULL COMMENT '分销比例';");
}
if (!pdo_fieldexists('storex_room', 'agent_ratio')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " ADD `agent_ratio` VARCHAR(300) NOT NULL COMMENT '分销比例';");
}
if (!pdo_fieldexists('storex_order', 'is_package')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `is_package` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否为套餐，1是普通商品，2是套餐';");
}
load()->model('module');
$wn_storex_module = module_fetch('wn_storex');
if (ver_compare('1.6.0', $wn_storex_module['version']) == 1) {
	$members = pdo_getall('storex_member', array('from_user !=' => ''), array('id', 'realname', 'from_user'));
	if (!empty($members) && is_array($members)) {
		load()->model('mc');
		$uids = array();
		$members_list = array();
		foreach ($members as $val) {
			$uid = mc_openid2uid($val['from_user']);
			if (empty($uid)) {
				continue;
			}
			$members_list[$uid] = $val;
			$uids[] = $uid;
		}
		if (!empty($uids) && is_array($uids)) {
			$mc_members = pdo_getall('mc_members', array('uid' => $uids), array('uid', 'realname', 'mobile', 'nickname'), 'uid');
			if (!empty($mc_members) && is_array($mc_members)) {
				foreach ($mc_members as $mc_uid => $info) {
					if (!empty($members_list[$mc_uid])) {
						pdo_update('storex_member', array('realname' => $info['realname'], 'nickname' => $info['nickname'], 'mobile' => $info['mobile']), array('id' => $members_list[$mc_uid]['id'], 'from_user' => $members_list[$mc_uid]['from_user']));
					}
				}
			}
		}
	}
}

if (pdo_fieldexists('storex_order', 'roomitemid')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " CHANGE `roomitemid` `roomitemid` VARCHAR(200) NOT NULL COMMENT '房间号ID';");
}

if (!pdo_fieldexists('storex_homepage', 'is_wxapp')) {
	pdo_query("ALTER TABLE " . tablename('storex_homepage') . " ADD `is_wxapp` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '是不是小程序1是2不是';");
}
//是否开启定位功能
if (!pdo_fieldexists('storex_set', 'location')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `location` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '是否开启定位1开2关';");
}

if (pdo_tableexists('storex_activity_exchange_trades')) {
	if (!pdo_fieldexists('storex_activity_exchange_trades', 'num')) {
		pdo_query("ALTER TABLE " . tablename('storex_activity_exchange_trades') . " ADD `num` INT(11) NOT NULL COMMENT '数量';");
	}
	$exchange_trades = pdo_getall('storex_activity_exchange_trades', array('num' => 0), array('tid', 'num'));
	if (!empty($exchange_trades) && is_array($exchange_trades)) {
		foreach ($exchange_trades as $val) {
			if (empty($val['num'])) {
				pdo_update('storex_activity_exchange_trades', array('num' => 1), array('tid' => $val['tid']));
			}
		}
	}
}
if (pdo_tableexists('storex_activity_exchange_trades_shipping')) {
	if (!pdo_fieldexists('storex_activity_exchange_trades_shipping', 'num')) {
		pdo_query("ALTER TABLE " . tablename('storex_activity_exchange_trades_shipping') . " ADD `num` INT(11) NOT NULL COMMENT '数量';");
	}
	$trades_shipping = pdo_getall('storex_activity_exchange_trades_shipping', array('num' => 0), array('tid', 'num'));
	if (!empty($trades_shipping) && is_array($trades_shipping)) {
		foreach ($trades_shipping as $val) {
			if (empty($val['num'])) {
				pdo_update('storex_activity_exchange_trades_shipping', array('num' => 1), array('tid' => $val['tid']));
			}
		}
	}
}

//首页设置选择文章修改

$storex_homepage = pdo_getall('storex_homepage', array('type' => 'notice'), array('id', 'items'));
if (!empty($storex_homepage) && is_array($storex_homepage)) {
	foreach ($storex_homepage as $homepage) {
		if (!empty($homepage['items'])) {
			$homepage['items'] = iunserializer($homepage['items']);
			if (!empty($homepage['items']) && is_array($homepage['items'])) {
				foreach ($homepage['items'] as $info) {
					if (!empty($info['value'])) {
						pdo_update('storex_homepage', array('items' => ''), array('id' => $homepage['id']));
						continue;
					}
				}
			}
		}
	}
}

//订单表增加规格对应的id,规格信息
if (!pdo_fieldexists('storex_order', 'spec_id')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `spec_id` INT(11) NOT NULL COMMENT '规格对应的商品id';");
}
if (!pdo_fieldexists('storex_order', 'spec_info')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `spec_info` TEXT NOT NULL COMMENT '商品规格信息';");
}

//回收站功能
if (!pdo_fieldexists('storex_goods', 'recycle')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `recycle` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '1在回收站，2不在';");
}
if (!pdo_fieldexists('storex_room', 'recycle')) {
	pdo_query("ALTER TABLE " . tablename('storex_room') . " ADD `recycle` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '1在回收站，2不在';");
}
//给店铺设置最大抵扣金额值
if (!pdo_fieldexists('storex_bases', 'max_replace')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `max_replace` DECIMAL(10,2) NOT NULL COMMENT '最大抵扣金额';");
}
//订单表增加抵扣的设置
if (!pdo_fieldexists('storex_order', 'cost_credit')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `cost_credit` DECIMAL(10,2) NOT NULL COMMENT '抵扣的积分';");
}
if (!pdo_fieldexists('storex_order', 'replace_money')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `replace_money` DECIMAL(10,2) NOT NULL COMMENT '抵扣的钱';");
}
//修改首页设置表的type
if (pdo_fieldexists('storex_homepage', 'type')) {
	pdo_query("ALTER TABLE " . tablename('storex_homepage') . " CHANGE `type` `type` VARCHAR(100) NOT NULL COMMENT '首页块类型'");
}
//member表增加用户接收验证码的手机号和邮箱
if (!pdo_fieldexists('storex_member', 'phone')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `phone` VARCHAR(11) NOT NULL COMMENT '只接收验证码的手机号';");
}
if (!pdo_fieldexists('storex_member', 'email')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `email` VARCHAR(50) NOT NULL COMMENT '只接收验证码的邮箱';");
}
if (ver_compare('1.6.1', $wn_storex_module['version'])) {
	pdo_update('storex_member', array('phone' => '', 'email' => '', 'credit_password' => '', 'credit_salt' => ''));
}
if (!pdo_fieldexists('storex_code', 'email')) {
	pdo_query("ALTER TABLE " . tablename('storex_code') . " ADD `email` VARCHAR(50) NOT NULL COMMENT '邮箱';");
}
if (pdo_fieldexists('storex_code', 'status')) {
	pdo_query("ALTER TABLE " . tablename('storex_code') . " CHANGE `status` `status` TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1未使用，2已使用';");
}
if (!pdo_fieldexists('storex_code', 'send_status')) {
	pdo_query("ALTER TABLE " . tablename('storex_code') . " ADD `send_status` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '发送状态1成功，2失败'");
}
if (!pdo_fieldexists('storex_blast_user', 'cookie')) {
	pdo_query("ALTER TABLE " . tablename('storex_blast_user') . " ADD `cookie` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
}

//设置余额支付密码设置的开关和验证方式
if (!pdo_fieldexists('storex_set', 'credit_pw')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `credit_pw` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '1开2关'");
}
if (!pdo_fieldexists('storex_set', 'credit_pw_mode')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `credit_pw_mode` VARCHAR(100) NOT NULL COMMENT '余额支付密码验证方式'");
}

//店铺增加提货方式设置，购物车统计结算运费
if (!pdo_fieldexists('storex_bases', 'pick_up_mode')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `pick_up_mode` VARCHAR(100) NOT NULL COMMENT '取货方式'");
}
if (!pdo_fieldexists('storex_bases', 'express')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `express` varchar(200) NOT NULL COMMENT '购物车统一结算运费'");
}
if (pdo_fieldexists('storex_bases', 'express')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " CHANGE `express` `express` VARCHAR(200) NOT NULL COMMENT '购物车统一结算运费'");
}
if (!pdo_fieldexists('storex_bases', 'goods_express')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `goods_express` INT(2) NOT NULL DEFAULT '1' COMMENT '单件商品运费模板'");
}
if (!pdo_fieldexists('storex_bases', 'agent_status')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `agent_status` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '分销功能开关'");
}

//订单表增加购物车计算时的商品记录
if (!pdo_fieldexists('storex_order', 'cart')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `cart` TEXT NOT NULL COMMENT '购物车结算订单的商品'");
}
if (!pdo_fieldexists('storex_sales_package', 'agent_ratio')) {
	pdo_query("ALTER TABLE " . tablename('storex_sales_package') . " ADD `agent_ratio`  VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分销员提成'");
}
//删除订单表不需要字段
if (pdo_fieldexists('storex_order', 'action')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " DROP `action`;");
}
//订单增加微信地址
if (!pdo_fieldexists('storex_order', 'wechat_address')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `wechat_address` TEXT NOT NULL COMMENT '使用微信地址';");
}
//积分设置改为店铺内设置
if (!pdo_fieldexists('storex_bases', 'credit_pay')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `credit_pay` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '积分抵扣设置1开启，2关闭';");
}
if (!pdo_fieldexists('storex_bases', 'credit_ratio')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `credit_ratio` INT(11) NOT NULL COMMENT '抵扣比例';");
}
if (pdo_fieldexists('storex_set', 'credit_pay') && pdo_fieldexists('storex_set', 'credit_ratio')) {
	$storex_set = pdo_getall('storex_set', array(), array('id', 'weid', 'credit_pay', 'credit_ratio'), 'weid');
	$storex_bases = pdo_getall('storex_bases', array(), array('id', 'weid', 'credit_pay', 'credit_ratio'));
	if (!empty($storex_bases)) {
		foreach ($storex_bases as $store) {
			if (!empty($storex_set[$store['weid']])) {
				$store_update = array(
					'credit_pay' => $storex_set[$store['weid']]['credit_pay'],
					'credit_ratio' => $storex_set[$store['weid']]['credit_ratio'],
				);
				pdo_update('storex_bases', $store_update, array('id' => $store['id']));
			}
		}
	}
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `credit_pay`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `credit_ratio`;");
}
//模板信息修改,改为在店铺内设置
if (!pdo_fieldexists('storex_bases', 'template')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `template` TEXT NOT NULL COMMENT '模板信息';");
}
//用户表增加用户属于哪个用户组
if (!pdo_fieldexists('storex_member', 'member_group')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `member_group` TEXT NOT NULL COMMENT '该用户所在的店铺用户组';");
}
if (!pdo_fieldexists('storex_member', 'cost_money')) {
	pdo_query("ALTER TABLE " . tablename('storex_member') . " ADD `cost_money` TEXT NOT NULL COMMENT '用户各店铺的消费总金额';");
}
//会员组设置默认会员组
if (!pdo_fieldexists('storex_member_level', 'default')) {
	pdo_query("ALTER TABLE " . tablename('storex_member_level') . " ADD `default` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '1是2不是';");
}
//店铺增加自定义色调
if (!pdo_fieldexists('storex_bases', 'color')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `color` VARCHAR(24) NOT NULL COMMENT '自定义主色调';");
}
//设置增加导航地图秘钥名称和秘钥
if (!pdo_fieldexists('storex_set', 'map_key')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `map_key` VARCHAR(200) NOT NULL COMMENT '腾讯地图秘钥';");
}
if (!pdo_fieldexists('storex_set', 'map_key_name')) {
	pdo_query("ALTER TABLE " . tablename('storex_set') . " ADD `map_key_name` VARCHAR(50) NOT NULL COMMENT '地图秘钥名称';");
}
if (pdo_fieldexists('storex_set', 'templateid')) {
	$storex_set = pdo_getall('storex_set', array(), array('id', 'weid', 'template', 'templateid', 'refuse_templateid', 'confirm_templateid', 'check_in_templateid', 'finish_templateid'), 'weid');
	$storex_bases = pdo_getall('storex_bases', array(), array('id', 'weid', 'template'));
	if (!empty($storex_set)) {
		foreach ($storex_bases as $store) {
			if (!empty($storex_set[$store['weid']])) {
				$store_update = array();
				$template = array(
					'template' => $storex_set[$store['weid']]['template'],
					'templateid' => $storex_set[$store['weid']]['templateid'],
					'refuse_templateid' => $storex_set[$store['weid']]['refuse_templateid'],
					'confirm_templateid' => $storex_set[$store['weid']]['confirm_templateid'],
					'check_in_templateid' => $storex_set[$store['weid']]['check_in_templateid'],
					'finish_templateid' => $storex_set[$store['weid']]['finish_templateid'],
				);
				$store_update['template'] = iserializer($template);
				pdo_update('storex_bases', $store_update, array('id' => $store['id']));
			}
		}
	}
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `template`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `templateid`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `refuse_templateid`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `confirm_templateid`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `check_in_templateid`;");
	pdo_query("ALTER TABLE " . tablename('storex_set') . " DROP `finish_templateid`;");
}

if (pdo_fieldexists('storex_member', 'agentid')) {
	$member = pdo_getall('storex_member', array('agentid !=' => 0), array('id', 'agentid', 'weid', 'from_user'));
	if (!empty($member)) {
		$agentids = array();
		foreach ($member as $m) {
			$agentids[] = $m['agentid'];
		}
		$agents = pdo_getall('storex_agent_apply', array('id' => $agentids), array('id', 'storeid'), 'id');
		foreach ($member as $v) {
			if (!empty($agents[$v['agentid']])) {
				$member_agentid_insert = array(
					'uniacid' => $v['weid'],
					'openid' => $v['from_user'],
					'storeid' => $agents[$v['agentid']]['storeid'],
					'memberid' => $v['id'],
					'agentid' => $v['agentid'],
					'time' => TIMESTAMP,
				);
				pdo_insert('storex_member_agent', $member_agentid_insert);
			}
		}
		pdo_query("ALTER TABLE " . tablename('storex_member') . " DROP `agentid`;");
	}
}

if (pdo_tableexists('storex_brand')) {
	pdo_run("DROP TABLE " . tablename('storex_brand'));
}
if (pdo_tableexists('storex_business')) {
	pdo_run("DROP TABLE " . tablename('storex_business'));
}
//店铺增加退货设置
if (!pdo_fieldexists('storex_bases', 'return_info')) {
	pdo_query("ALTER TABLE " . tablename('storex_bases') . " ADD `return_info` TEXT NOT NULL COMMENT '退货的设置';");
}
if (!pdo_fieldexists('storex_order', 'over_time')) {
	pdo_query("ALTER TABLE " . tablename('storex_order') . " ADD `over_time` INT(11) NOT NULL COMMENT '订单完成的时间';");
}
if (!pdo_fieldexists('storex_goods', 'fprice')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `fprice` DECIMAL(10,2) NOT NULL COMMENT '商品成本价格';");
}
if (!pdo_fieldexists('storex_goods', 'fact_sold_num')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `fact_sold_num` INT(11) NOT NULL COMMENT '实际卖出的数量';");
}
if (!pdo_fieldexists('storex_goods', 'visit_times')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `visit_times` INT(11) NOT NULL COMMENT '访问次数';");
}

//2.6.1新加入
//预约表单增加单选多选
if (!pdo_fieldexists('storex_booking_set', 'value')) {
	pdo_query("ALTER TABLE " . tablename('storex_booking_set') . " ADD `value` TEXT NOT NULL;");
}
//商品表加入标签
if (!pdo_fieldexists('storex_goods', 'isrecommend')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `isrecommend` TINYINT NOT NULL DEFAULT '2' COMMENT '推荐';");
}
if (!pdo_fieldexists('storex_goods', 'isnew')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `isnew` TINYINT NOT NULL DEFAULT '2' COMMENT '新品';");
}
if (!pdo_fieldexists('storex_goods', 'ishot')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `ishot` TINYINT NOT NULL DEFAULT '2' COMMENT '热卖';");
}
if (!pdo_fieldexists('storex_goods', 'isnopostage')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `isnopostage` TINYINT NOT NULL DEFAULT '2' COMMENT '包邮';");
}
if (!pdo_fieldexists('storex_goods', 'dispatchid')) {
	pdo_query("ALTER TABLE " . tablename('storex_goods') . " ADD `dispatchid` INT UNSIGNED NOT NULL COMMENT '运费模板id' ;");
}
//商品增加单个分享设置
if (!pdo_fieldexists('storex_share_set', 'goodstable')) {
	pdo_query("ALTER TABLE " . tablename('storex_share_set') . " ADD `goodstable` VARCHAR(50) NOT NULL COMMENT '商品属于的表';");
}
if (!pdo_fieldexists('storex_share_set', 'goodsid')) {
	pdo_query("ALTER TABLE " . tablename('storex_share_set') . " ADD `goodsid` INT(11) NOT NULL COMMENT '商品id';");
}

//删除商品分类设置的规格
if (pdo_fieldexists('storex_categorys', 'spec')) {
	pdo_query("ALTER TABLE " . tablename('storex_categorys') . " DROP `spec`;");
}
//删除分销员等级表
if (pdo_tableexists('storex_agent_level')) {
	pdo_run("DROP TABLE " . tablename('storex_agent_level'));
}

//处理mobile更新遗留的js，css和svg文件
load()->func('file');
$js_file_trees = file_tree(IA_ROOT . '/addons/wn_storex/template/style/mobile/js');
$css_file_trees = file_tree(IA_ROOT . '/addons/wn_storex/template/style/mobile/css');
$svg_file_trees = file_tree(IA_ROOT . '/addons/wn_storex/template/style/mobile/img');
$current_js_files = array(
	IA_ROOT . '/addons/wn_storex/template/style/mobile/js/wn-hotel.20180310274.js',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/js/service.20180310274.js',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/js/manifest.20180310274.js',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/js/vendor.20180310274.js',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/js/wn-common.20180310274.js',
);
$current_css_files = array(
	IA_ROOT . '/addons/wn_storex/template/style/mobile/css/service.20180310274.css',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/css/wn-hotel.20180310274.css',
	IA_ROOT . '/addons/wn_storex/template/style/mobile/css/wn-common.20180310274.css'
);
$current_svg_files = array(
	IA_ROOT . '/addons/wn_storex/template/style/mobile/img/storex.20180310274.svg',
);
$css_diff_files = array_diff($css_file_trees, $current_css_files);
$js_diff_files = array_diff($js_file_trees, $current_js_files);
$svg_diff_files = array_diff($svg_file_trees, $current_svg_files);
if (!empty($js_diff_files)) {
	foreach ($js_diff_files as $value) {
		file_delete($value);
	}
}
if (!empty($css_diff_files)) {
	foreach ($css_diff_files as $value) {
		file_delete($value);
	}
}
if (!empty($svg_diff_files)) {
	foreach ($svg_diff_files as $value) {
		file_delete($value);
	}
}
$unused_files = array(
	IA_ROOT . '/addons/wn_storex/template/manage.html',
	IA_ROOT . '/addons/wn_storex/template/query.html',
	IA_ROOT . '/addons/wn_storex/template/form.html',
	IA_ROOT . '/addons/wn_storex/template/brand.html',
	IA_ROOT . '/addons/wn_storex/template/brand_form.html',
	IA_ROOT . '/addons/wn_storex/template/business.html',
	IA_ROOT . '/addons/wn_storex/template/business_form.html',
	IA_ROOT . '/addons/wn_storex/template/business_query.html',
	IA_ROOT . '/addons/wn_storex/template/category.html',
	IA_ROOT . '/addons/wn_storex/template/room.html',
	IA_ROOT . '/addons/wn_storex/template/room_form.html',
	IA_ROOT . '/addons/wn_storex/template/order.html',
	IA_ROOT . '/addons/wn_storex/template/order_form.html',
	IA_ROOT . '/addons/wn_storex/template/goodscomment.html',
	IA_ROOT . '/addons/wn_storex/template/room_price.html',
	IA_ROOT . '/addons/wn_storex/template/room_price_list.html',
	IA_ROOT . '/addons/wn_storex/template/room_price_lot.html',
	IA_ROOT . '/addons/wn_storex/template/room_price_lot_list.html',
	IA_ROOT . '/addons/wn_storex/template/room_status.html',
	IA_ROOT . '/addons/wn_storex/template/room_status_list.html',
	IA_ROOT . '/addons/wn_storex/template/room_status_lot.html',
	IA_ROOT . '/addons/wn_storex/template/room_status_lot_list.html',
	IA_ROOT . '/addons/wn_storex/template/clerk_comment.html',
	IA_ROOT . '/addons/wn_storex/template/clerk_form.html',
	IA_ROOT . '/addons/wn_storex/template/clerk.html',
	IA_ROOT . '/addons/wn_storex/template/clerklist.html',
	IA_ROOT . '/addons/wn_storex/template/member.html',
	IA_ROOT . '/addons/wn_storex/template/member_form.html',
	IA_ROOT . '/addons/wn_storex/inc/web/business.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/brand.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/goodscategory.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/goodsmanage.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/goodscomment.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/order.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/room_price.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/room_status.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/clerk.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/clerklist.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/member.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/shop_goods_spec.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/web/shop_agent_level.inc.php',
	IA_ROOT . '/addons/wn_storex/inc/mobile/wxapphomepage.inc.php',
	IA_ROOT . '/addons/wn_storex/template/store/shop_goodsedit.html',
	IA_ROOT . '/addons/wn_storex/template/store/shop_orderedit.html',
	IA_ROOT . '/addons/wn_storex/template/clerkdeskmenu.html',
	IA_ROOT . '/addons/wn_storex/template/coupon_nav.html',
	IA_ROOT . '/addons/wn_storex/template/couponconsume.html',
	IA_ROOT . '/addons/wn_storex/template/couponexchange.html',
	IA_ROOT . '/addons/wn_storex/template/couponmanage.html',
	IA_ROOT . '/addons/wn_storex/template/couponmarket.html',
	IA_ROOT . '/addons/wn_storex/template/fans.html',
	IA_ROOT . '/addons/wn_storex/template/membercard.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-basic.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-edit-cardActivity.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-edit-cardBasic.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-edit-cardNums.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-edit-cardRecharge.html',
	IA_ROOT . '/addons/wn_storex/template/membercard-edit-cardTimes.html',
	IA_ROOT . '/addons/wn_storex/template/memberproperty.html',
	IA_ROOT . '/addons/wn_storex/template/noticemanage.html',
	IA_ROOT . '/addons/wn_storex/template/paycenterwxmicro.html',
	IA_ROOT . '/addons/wn_storex/template/signmanage.html',
	IA_ROOT . '/addons/wn_storex/template/stat_nav.html',
	IA_ROOT . '/addons/wn_storex/template/statcard.html',
	IA_ROOT . '/addons/wn_storex/template/statcash.html',
	IA_ROOT . '/addons/wn_storex/template/statcredit1.html',
	IA_ROOT . '/addons/wn_storex/template/statcredit2.html',
	IA_ROOT . '/addons/wn_storex/template/statpaycenter.html',
	IA_ROOT . '/addons/wn_storex/template/wxcardreply.html',
	IA_ROOT . '/addons/wn_storex/template/wxstore.html',
	IA_ROOT . '/addons/wn_storex/template/hotel_form.html',
	IA_ROOT . '/addons/wn_storex/template/cardmanage.html',
	IA_ROOT . '/addons/wn_storex/template/cardmodel.html',
	IA_ROOT . '/addons/wn_storex/template/store/shop_goods_spec.html',
	IA_ROOT . '/addons/wn_storex/template/store/shop_agent_level.html',
	IA_ROOT . '/addons/wn_storex/black.png',
	IA_ROOT . '/addons/wn_storex/display.png',
);
if (!empty($unused_files)) {
	foreach ($unused_files as $file) {
		file_delete($file);
	}
}