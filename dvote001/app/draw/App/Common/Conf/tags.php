<?php
/**
 * 配置系统行为扩展文件列表
 *
 * @author wscsky<wscsky@qq.com>
 */

return array(
		'app_init' => array(
				'Home\Behavior\ReadConfigBehavior',   //读取参数设置
				//检查访问权限
		),
		'action_begin' => array(			
			'Behavior\CheckLangBehavior',			 //语言包
			'Home\Behavior\CheckLoginBehavior'		 //检查权限
		),
		//表单验证Token
		'view_filter' => array('Behavior\TokenBuildBehavior'),
				
);