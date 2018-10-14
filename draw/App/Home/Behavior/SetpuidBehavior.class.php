<?php
namespace Home\Behavior;
defined('THINK_PATH') or exit("Access Denied");
//设置推荐者cookie
class SetpuidBehavior {
	public function run(&$params){
	    D("Common/Weixin","Logic")->set_puid_cookie();
	}
}