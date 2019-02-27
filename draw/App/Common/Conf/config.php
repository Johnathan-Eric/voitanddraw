<?php
return array(

	//SESSION处理类
    'SESSION_AUTO_START'    =>  true, 
//	'SESSION_TYPE'			=> "Redis",
//	'SESSION_PREFIX'		=> 'sess_',
//	'SESSION_EXPIRE'		=> '7200',
    'EXPIRE_TIME'       => '2018-12-12',
	'SYSTEM_LOG'		=> true,		


	'CONFIG_CACHE_NAME'		=> 'site_config',			//系统设置绑定文件名
	'LANG_CACHE_NAME'		=> 'lang_config',			//语言包绑定文件名
	
	'DEFAULT_TIMEZONE'		=>'Asia/Singapore',			//设置时区

	//session参数设置
	'SESSION_OPTIONS'	=> array(
		'name'			=> "GLSESSID",		//Session名称
		'expire' 		=> 3600,		//过期时间 30分钟
		'use_cookies'	=> 1,				//使用use_cookies来传递
// 		'cookie_domain'	=> "",				//cookie主机头

		),
		
	//cookie参数设置		
	'COOKIE_PREFIX'	   => "smr_", 			// cookie 名称前缀
	'COOKIE_EXPIRE'    =>  3600, 			// cookie 保存时间
	'COOKIE_PATH'      =>  "/", // cookie 保存路径
	//'COOKIE_DOMAIN'   =>  "smeiren.cc", // cookie 有效域名
	//'COOKIE_HTTPONLY'  =>  1, // httponly设置
		
	//表单令牌
	'TOKEN_ON' => false, 		// 是否开启令牌验证 默认关闭
    'TOKEN_NAME' => '_token_', // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE' => 'md5', 		//令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, 		//令牌验证出错后是否重置令牌 默认为true

	//图片水印参数设置
	'IMAGE_WATER_CONFIG' => array(
		"min_width"		=> 200,		//最小宽度
		"min_height"	=> 200,		//小最高度
		"path"			=> "./Public/images/water.png",		//水印图片位置
		"position"		=> 9,		//水印位置 	123
									//      	456
									//			789
		'alpha'			=> 80,		//水印透度
	),
	//文字水印参数设置
	'TEXT_WATER_CONFIG' => array(
		"min_width"		=> 200,		//最小宽度
		"min_height"	=> 200,		//小最高度
		"text"			=> "3laohu.com",//水印图片位置
		"font"			=> "./Public/images/hatten.ttf", //字体
		"size"			=> 16,		//文件字大小
		"color"			=> "#333333",
		"position"		=> 9,		//水印位置
		"offset"		=> -5,		//当相对位置偏移
		'alpha'			=> 80,		//水印透度
	),

    'DATA_CACHE_PREFIX' => 'Redis_',//缓存前缀
    'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
    'DATA_CACHE_TIMEOUT' => false,
    'REDIS_RW_SEPARATE' => true, //Redis读写分离 true 开启
    'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    'REDIS_PORT'=>'6379',//端口号
    'REDIS_TIMEOUT'=>'300',//超时时间
    'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
    'REDIS_AUTH'=>'',//AUTH认证密码

//    // 缓存 配置
//    'DATA_CACHE_PREFIX' 	=> 'Redis_',//缓存前缀
//    'DATA_CACHE_TYPE'		=> 'Redis',//默认动态缓存为Redis
//    'DATA_CACHE_TIMEOUT' 	=> '0',
//
//    // Redis 配置
//    'REDIS_RW_SEPARATE' 	=> true, //Redis读写分离 true 开启
//    'REDIS_HOST'			=> '127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
//    'REDIS_PORT'			=> '6379',//端口号
//    'REDIS_TIMEOUT'			=> '300',//超时时间
//    'REDIS_PERSISTENT'		=> false,//是否长连接 false=短连接
//    'REDIS_AUTH'			=> '',//AUTH认证密码
    'DATA_CACHE_TIME'       => 10000, // 失效时间


	//语言包设置
	'LANG_SWITCH_ON'	=> true,		// 开启语言包功能
	'LANG_AUTO_DETECT' 	=> true, 		// 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        	=> 'zh-cn', 	// 允许切换的语言列表 用逗号分隔
	'VAR_LANGUAGE'     	=> 'l', 		// 默认语言切换变量

	//URL配置设置
	'URL_HTML_SUFFIX'	=> 'html',
	'URL_PATHINFO_DEPR'	=>  '/',
	'URL_CASE_INSENSITIVE'  => true,	//URL大小写忽略

	//'配置项'=>'配置值'
	'DEFAULT_C_LAYER'=>'Action',

	'DB_TYPE'               => 'mysql',
  	'DB_HOST'               => '127.0.0.1',
    'DB_NAME'               => 'draw',
    'DB_USER'               => 'root',
    'DB_PWD'                => 'Qe2.ao4394948',
	'DB_PREFIX'             => 'ims_tyzm_diamondvote_',
	'DB_CHARSET'			=> 'utf8',
	'DB_PORT'               => 3306,

	'URL_404_REDIRECT'      => '',

	'DB_FIELDS_CACHE'		=> true,

	//推广二维码设置
	'QR_CODE_CONFIG'		=> array(
		'bgimg'		=> "./Public/qrcode_bg.jpg",	//背景文件 
		'logo'		=> "./Public/qrcode_logo.jpg",	//默认LOGO
		'width'		=> 600,	//总宽度
		'height'	=> 900,	//总高度
		'headimg'	=> array(	//头像设置
				'w' => 90,		//宽度
				'h'	=> 90,		//高度
				'x'	=> 61,		//x坐标位置
				'y'	=> 50,		//y坐标位置
				'alpha' => 100,	//秀明度 0-100
			),	
		'uname' => array(
				'x'	=> 253,
				'y' => 57,
				'font'	=> "./Public/msyh.ttf",	//字体
				'size'	=> 20,		 	       //文字大小
				'color'	=> '#CE3C6500', 		//颜色
			),
		'qrcode' => array(
				'w' => 340,		//宽度
				'h'	=> 340,		//高度
				'x'	=> 150,		//x坐标位置
				'y'	=> 455,		//y坐标位置
				'alpha' => 100,	//秀明度 0-100
		),
	),

	//设置默认MODULE,默认URL就可以不用带Home
	'MODULE_ALLOW_LIST' => array (
			'Home',
			'Admin',
	    'Merchant',
	),
	'DEFAULT_MODULE' => 'Home',

	'UPLOAD_FILE_PATH'	=> '/Uploads/',	//文件上传目录

	//模板配置信息
	'TMPL_PARSE_STRING'     =>array(
			'__PUBLIC__' => '/Public', 			// 更改默认的__PUBLIC__ 替换规则
			'__IMG__'  => '/Public/images',
			'__CSS__'  => '/Public/css',
			'__JS__'     => '/Public/js', 		// 增加新的JS类库路径替换规则
			'__UPLOAD__' => '/Uploads', 		// 增加新的上传路径替换规则
	),

	//登陆验证码参数
	'LOGIN_VERIFY_CONFIG' => array(
			'codeSet'   =>  '0123456789', //ABCDEFGHIJKLMNOPQRSTUVWXYZ',    // 验证码字符集合
			'useImgBg'  =>  false,           // 使用背景图片
			'fontSize'  =>  25,              // 验证码字体大小(px)
			'useCurve'  =>  true,            // 是否画混淆曲线
			'useNoise'  =>  false,            // 是否添加杂点
			'imageH'    =>  30,               // 验证码图片高度
			'imageW'    =>  100,               // 验证码图片宽度
			'length'    =>  4,               // 验证码位数
			'fontttf'   =>  '',              // 验证码字体，不设置随机获取
			'bg'        =>  array(243, 251, 254),  // 背景颜色
	),

    'URL_DENY_SUFFIX'       =>  'ico', // URL禁止访问的后缀设置
	
	"TAGLIB_PRE_LOAD"	=> "html,Logic,DIY",

    //图片小图参数设置
       "THUMB_PRESETS"     => array(
    	"thumb"			=> array("width"=>320, "height"=>0 ),
    	"100"			=> array("width"=>100, "height"=>0 ),
    	"100x100" 		=> array("width"=>100, "height"=>100, 'thumb_type' => 3),
    	"200"			=> array("width"=>200, "height"=>0 ),
		"200x200" 		=> array("width"=>200, "height"=>200, 'thumb_type' => 3),
        "260x165" 		=> array("width"=>260, "height"=>165, 'thumb_type' => 3),
       	"300x200" 		=> array("width"=>300, "height"=>200, 'thumb_type' => 3),
    	"320" 			=> array("width"=>320, "height"=>0),
    	"320x320" 		=> array("width"=>320, "height"=>320, 'thumb_type' => 3),
    	"480" 			=> array("width"=>480, "height"=>0),
    	"480x480" 		=> array("width"=>480, "height"=>480, 'thumb_type' => 3),
        "480x320" 		=> array("width"=>480, "height"=>320, 'thumb_type' => 3),
        "480x240" 		=> array("width"=>480, "height"=>240, 'thumb_type' => 3),
    	"640" 			=> array("width"=>640, "height"=>0),
    	"640x640" 		=> array("width"=>640, "height"=>640, 'thumb_type' => 3),
        "640x320" 		=> array("width"=>640, "height"=>320, 'thumb_type' => 3),
        "800" 			=> array("width"=>800, "height"=>0),
        "800x800" 		=> array("width"=>800, "height"=>800, 'thumb_type' => 3),
	),
   'THUMB_NOT_FOUND'	=> "./Public/images/no.jpg",

	//分页连接设置
	"PAGE_CONFIG" => array(
		'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '首页',
        'last'   => '尾页',
        'theme'  => ' %HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ',
	),
		
	//分页连接设置
	"MPPAGE_CONFIG" => array(
			'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
			'page_info' => '<span class="new-open">%NOW_PAGE%/%TOTAL_PAGE%</span>',
			'prev'   => '上一页',
			'next'   => '下一页',
			'theme'  => ' %UP_PAGE% %PAGE_INFO% %DOWN_PAGE% ',
	),

	'UPLOAD_CONFIG'  => array(
		'imageUploadLimit' 	=> 10,
		'imageSizeLimit' 	=> 2,
		'imageFileTypes' 	=> array('jpg'=>'*.jpg', 'gif'=>'*.gif', 'png'=>'*.png', 'jpeg'=>'*.jpeg'),
		'exts' 				=> array('jpg','gif','png','jpeg'),
	),

	'REGION_TYPE' 	=> array(
		'0'  	=> '国家',
		'1'  	=> '省/省级市',
		'2'     => '地级市',
		'3' 	=> '区/县',
		'4'		=> '街道',
	),
    
    'Sms'=>array(
        'appkey'=>'f8e885de634cba96',
    ),
    
    'LongmenAppsecret' => 'e57a77afa090759ed84bce3634f85b34',
    
    'LOAD_EXT_CONFIG' => 'orderCon',
	'PAGES'    =>  '($page-1)*3,3',
);