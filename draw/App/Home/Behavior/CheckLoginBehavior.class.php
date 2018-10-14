<?php

namespace Home\Behavior;
//检查会员登陆 
class CheckLoginBehavior {
	// 行为扩展的执行入口必须是run
	public function run(&$params){
		$member = session('member');

	}
}