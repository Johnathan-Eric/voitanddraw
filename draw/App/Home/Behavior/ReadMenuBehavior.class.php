<?php
namespace Home\Behavior;
defined('THINK_PATH') or exit("Access Denied");
//自动读取后台菜单
use Think;
class ReadMenuBehavior {
	public function run(&$params){
        
	  if(IS_AJAX) return;
	  
	  $model = D("Common/Menu","Logic");
	  $menu = $model -> get_menu(C("MENU_TYPE"),0);
	  \Think\Think::instance('Think\View') -> assign("_subnav", $menu);
	 
	}
}