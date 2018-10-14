<?php
namespace Admin\Behavior;
defined('THINK_PATH') or exit("Access Denied");
//自动读取后台菜单
use Think;
class ReadMenuBehavior {
	public function run(&$params){
        
	  if(IS_AJAX) return;
	  
	  $menuid = I("menuid","","intval");
	  if($menuid == "") return;
	  
	  $model = D("Common/Menu","Logic");
	  $menu = $model -> get_brother($menuid);
	  \Think\Think::instance('Think\View') -> assign("page_title", $menu['0']['name']);
	  \Think\Think::instance('Think\View') -> assign("_subnav", $menu);
	  \Think\Think::instance('Think\View') -> assign("menuid", $menuid);
	  
	}
}