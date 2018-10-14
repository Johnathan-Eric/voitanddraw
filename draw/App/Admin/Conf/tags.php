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
			//'Admin\Behavior\CheckLoginBehavior',		 //检查权限
			'Admin\Behavior\ReadMenuBehavior',
		),
				
);