<?php
//常量定义

//用户角色
define("SUPPER_FINANCE_ROLE", "finance");			//执行超级财务权限用户角色

define("MD5_KEY", "&^%$&^TISGHISUGIUHSBDU");		//用于普通加密码的密钥

//定义默认的推荐用户P
define('MEMBER_DEFAULT_PUID', 	0);			//如果没有推荐注册,自动设置为该用户的下线
define('MEMBER_DEFAULT_GID', 	1);				//默认用户组，即不需要投资的会员组GID
define("TEST_OPENID", 'ojA6zjpOu0IESK2hKEpZT5KTXs9M');	//测试使用的openid


//用户类型 
define('UTYPE_MEMBER',"MEMBER");            //会员
define('UTYPE_ADMIN',"ADMIN");              //管理员

//会员状态常量定义
define("MEMBER_STATUS_UNFOCUS", 	0);					//未关注
define("MEMBER_STATUS_FOCUS", 		1);					//已关注


//用户是否为店长
define("MEMBER_MASTER_NO", 			0);					//不是店长
define("MEMBER_MASTER_YES", 		1);					//是店长

//用户来源方式
define("MEMBER_REFERER_LINK", 			1);				//链接
define("MEMBER_REFERER_QRCODE", 		2);				//二维码

//会员认证状态常量定义
define("AUTH_STATUS_UNAUTH", 	0);						//未认证
define("AUTH_STATUS_AUDITING", 	1);						//审核中
define("AUTH_STATUS_AUTHED", 	2);						//已认证
define("AUTH_STATUS_FAILED", 	3);						//认证失败
define("AUTH_STATUS_EXPIRE", 	4);						//身份证过期

//文件资源应用类型定义
define('FILE_TYPE_NEWS', 			'news');		//新闻文件上传
define('FILE_TYPE_AUTH', 			'auth');		//认证附件
define('FILE_TYPE_GOODS', 			'goods');		//商品上传
define('FILE_TYPE_GALLERY', 		'gallery');		//商品相册上传
define('FILE_TYPE_STORE', 			'store');		//门店LOGO图


//积分类型
define('INTEGRAL_TYPE_PREG', 	'PREG');	//推荐注册
define('INTEGRAL_TYPE_REG', 	'REG');		//注册赠送
define('INTEGRAL_TYPE_BOOK', 	'BOOK');	//每日签到
define('INTEGRAL_TYPE_ORDER', 	'ORDER');	//订单
define('INTEGRAL_TYPE_PROFIT',  'PROFIT');	//收益
define('INTEGRAL_TYPE_OTH', 	'OTH');		//其它

//分类类型定义
define('CATE_TYPE_NEWS', 			'book');		//书
define('CATE_TYPE_NEWS1',           'news1');       //新新闻资讯
define('CATE_TYPE_GOODS',			'goods');		//商品
define('CATE_TYPE_MENU', 			'menu');		//微信菜单 
define('CATE_TYPE_SPEC', 			'spec');		//商品规格
define('CATE_TYPE_PAGES', 			'pages');		//单页面
define('CATE_TYPE_ATTR', 			'attr');		//商品属性
define('CATE_TYPE_STORE', 			'store');		//店铺

define('ORDERS_STATUS_UNPAY', 			0);				//未付款
define('ORDERS_STATUS_PAY', 			1);				//已付款
define('ORDERS_STATUS_SHIPPING', 		2);				//服务中
define('ORDERS_STATUS_SHIPPED', 		3);				//已发货
define('ORDERS_STATUS_FINISH', 			4);				//已完成
define('ORDERS_STATUS_APPLY', 			5);				//申请退货
define('ORDERS_STATUS_RETURN', 			6);				//退货中
define('ORDERS_STATUS_RETURNFINISH', 	7);				//退货完成
define('ORDERS_STATUS_CANCEL', 			10);			//取消订单

//商品状态
define('GOODS_STATUS_ONLINE', 				1);				//商品上架
define('GOODS_STATUS_NOTONLINE', 			0);				//商品下架

//订单支付状态
define('ORDERS_PAY_UNAPPLY', 			0);				//未支付
define('ORDERS_PAY_APPLY', 				1);				//已支付
define('ORDERS_PAY_RETURN', 			2);				//已退款
define('ORDERS_PAY_PART', 				3);				//部分支付

//密钥设置
define('MEMBER_LOGIN_KEY', 		 	 	'zd2&NDndkmLjndnG'); 		//会员登陆密钥
define('ADMIN_LOGIN_KEY', 		 	 	'DadiN5DPomXcgdkdd'); 		//管理员登陆密钥
define('CARD_PASSWD_KEY', 				'l4rt3@Qk7r5G7fdw');		//手机卡密钥


//提现状态定义
define('WITHDRAW_STATUS_CANCEL', 	0);		//已取消[不可再有处理]
define('WITHDRAW_STATUS_UNAUDIT',	1);		//审批中(此时用户可取消,管理员可以取消,审核成为待)
define('WITHDRAW_STATUS_AUDITED', 	2);		//处理中[付转账](待支付,此时用户不可取消)
define('WITHDRAW_STATUS_SUCCESS', 	3);		//提现成功
define('WITHDRAW_STATUS_AUDITFAILED',4);	//审批失败
define('WITHDRAW_STATUS_FAILED', 	5);		//提现失败

//充值方式定义
define("RECHARGE_TYPE_OFFLINE", 	0);		//线下充值
define("RECHARGE_TYPE_ONLINE", 		1);		//在线充值

//充值状态定义
define("RECHARGE_STATUS_FAILED", 	0);		//充值不成功
define("RECHARGE_STATUS_SUCCESS", 	1);		//充值成功
define("RECHARGE_STATUS_UNAUDIT", 	2);		//待审核[用户提交线下充值单默认状态]



//资金方式
define("MONEY_TYPE_UNPAY",        0);					//未付款
define("MONEY_TYPE_BALANCE",      1);					//余额方式
define("MONEY_TYPE_BANK",      	  2);					//银行汇款 


//会员银行卡 是否为默认使用
define("BANK_DEFAULT_USE", 1);							//会员银行卡 默认使用
define("BANK_DEFAULT_UNUSE", 0);						//会员银行卡 不是默认使用

//会员银行卡 是否认证
define("BANK_VERIFY_TRUE", 1);							//会员银行卡 已认证
define("BANK_DEFAULT_FALSE", 0);						//会员银行卡 未认证


//收益状态定义


define('PROFIT_STATUS_UNCONFIRMED', 0);	//未结算
define('PROFIT_STATUS_CONFIRMED', 	1);	//已结算
define('PROFIT_STATUS_INVALID', 	2);	//已失效(此状态为人工处理,如果原来帐户已生效,需要回退);
define('PROFIT_STATUS_UNTAKE', 		3);	//未生效

//变动方式
define('MODE_TYPE_INC',				 'inc');		//增加变动
define('MODE_TYPE_DEC',				 'dec');		//减少变动

//实体类型
define('ENTITY_TYPE_ORDER',			1);		//订单
define('ENTITY_TYPE_PROFIT',		2);		//收益
define('ENTITY_TYPE_WITHDRAW',		3);		//提现
define('ENTITY_TYPE_WORDER',		4);		//配送订单
define('ENTITY_TYPE_MEMBER',		5);		//用户信息
define('ENTITY_TYPE_MEMBERLOG',		6);		//用户升级
define('ENTITY_TYPE_ADMIN',   		10);    //管理员
define("ENTITY_TYPE_TCODE", 		11);    //提货码
define('ENTITY_TYPE_OTHER',  		99);	//其它


//收益类型定义
define('PROFIT_TYPE_CON', 'CON');		//消费提成
define('PROFIT_TYPE_REF', 'REF');		//推荐提成
define('PROFIT_TYPE_OTH', 'OTH');		//其它提成


//可用余额变动方式
define('BALANCE_TYPE_REF', 			0);					//推荐提成
define('BALANCE_TYPE_CON', 			1);					//消费提成
define('BALANCE_TYPE_WITHDRAW', 	2);					//提现
define('BALANCE_TYPE_BUY',			3);					//购买产品
define('BALANCE_TYPE_SETTLEPROFIT', 4);					//收益结算
define('BALANCE_TYPE_OTHER',		10);				//其他


//会员等级 是否锁定
define('GROUP_ISLOCK_YES',			1);					//锁定
define('GROUP_ISLOCK_NO',			0);					//未锁定


//会员升级支付方式
define('MEMBERLOG_PAY_ONLINE',	1);					//在线支付
define('MEMBERLOG_PAY_OFFLINE',	2);					//线下支付
define('MEMBERLOG_PAY_AUTO',	3);					//自动升级

//会员升级状态
define('MEMBERLOG_STATUS_UNPAY',	0);					//未支付
define('MEMBERLOG_STATUS_AUDITED',	1);					//已审核
define('MEMBERLOG_STATUS_CANCEL',	2);					//已取消

//等级审核状态定义
//0未付款 1.已审核通过  2.已取消
define('RANK_STATUS_UNPAY', 		0);				//未付款
define('RANK_STATUS_AUDITED', 		1);				//已审核
define('RANK_STATUS_CANCEL', 		2);				//已取消

//域名配置
define('HTTP_ADDRESS',  'https://lk.9daogu.com');       //接口域名