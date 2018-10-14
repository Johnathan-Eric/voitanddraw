<?php
/**
 * 配置系统行为扩展文件列表
 *
 * @author wscsky<wscsky@qq.com>
 */

return array(
		'app_init' => array(
				'Home\Behavior\ReadConfigBehavior',   //读取参数设置
		),
		'action_begin' => array(			
			//'Home\Behavior\ReadMenuBehavior',
		    //'Home\Behavior\TaskBehavior',			//执行计划任务
 		    'Home\Behavior\SetpuidBehavior',   //设置推荐者信息
		),
				
);