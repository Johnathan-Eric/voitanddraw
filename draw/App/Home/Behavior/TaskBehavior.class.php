<?php
namespace Home\Behavior;
defined('THINK_PATH') or exit("Access Denied");
//计划任务执行
class TaskBehavior {
	public function run(&$params){
	    $task_interval_time = C('task_interval_time') ? C('task_interval_time') : 20;
	}
}