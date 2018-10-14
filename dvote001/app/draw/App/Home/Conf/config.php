<?php

return array(
	'ACCESS_CHECK_ON'		=> true,	//是否要检查权限
	'LAYOUT_ON'   			=> true,
	'MENU_TYPE'  			=> 1,
	'SITE_TYPE'				=> "api",	//微信登陆				
	
	'ROLLPAPGE'				=> 8,		//前台每页显示数量
    
	//模板配置信息
	'TMPL_PARSE_STRING'     =>array(
			'__PUBLIC__' => '/app/draw/Public', 			// 更改默认的__PUBLIC__ 替换规则
			'__IMG__'  => '/app/draw/Public/Home/Images',
			'__CSS__'  => '/app/draw/Public/Home/Css',
			'__JS__'     => '/app/draw/Public/Home/Js', 		// 增加新的JS类库路径替换规则
			'__UPLOAD__' => '/app/draw/Uploads', 		// 增加新的上传路径替换规则
	),
	
	'TMPL_ACTION_ERROR'	=> "Public/error",					//报错页模版
	'TMPL_ACTION_SUCCESS' => "Public/error",				//成功页模版
    'ATTACHMENT_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Profile/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //附件上传配置（文件上传类配置
  );
?>