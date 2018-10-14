<?php
return array(
  'ACCESS_CHECK_ON'		=> true,	//是否要检查权限
  'SITE_TYPE'          => "ADMIN",
   //是否为后台管理用户
   'IS_ADMIN'  => TRUE,
		
   'URL_MODEL'  => 2,

   //session参数设置
   'SESSION_OPTIONS'	=> array(
        'name'			=> "GLSESSID",		//Session名称
        'expire' 		=> 1800,			//过期时间 30分钟
        'use_cookies'	=> 1,				//使用use_cookies来传递
        // 		'cookie_domain'	=> "",				//cookie主机头

    ),

  'LAYOUT_ON'   => true,
  'MENU_TYPE'  => 0,
		
  'URL_TRIM_INDEX'		=> false,   //是否去掉URL里的index.php
  'URL_TRIM_INDEX_ACT'   => false,   //是否去掉URL默认的index action

  'SHOW_PAGE_TRACE' 	=>false,			//显示页面TRACE

  'USER_LOGIN_URL'	    => "/index.php/Admin/User/login",        //后台登陆
  'USER_ISLOCK_URL'     => "/index.php/Admin/User/islock",	     //用户锁定后URL
  'ACCESS_DENIED_URL'   => '',
    
  'LOAD_EXT_CONFIG' => 'menu,parm',
    'site_title' => '抽奖',
  );
  
?>