<?php
namespace Common\Logic;
class MenuLogic{
	
	/*
	 * 读取数据库设置的菜单
	 * @param int $type 菜单类型 0.后台 1.会员
	 * @param int $parentid 父级ID
	 * @param bool $ALL 是否读取全部
	 */
	function get_menu($type = 0, $parentid = NULL, $ALL = false){
	    
		if($ALL)
			$file = "menus_all_{$type}";
		else 
			$file = "menus_{$type}";
		
	    is_numeric($parentid) && $file .= "_{$parentid}";
	    
	    $datas = F($file);
	    
	    if($parentid == NULL)
	    	$menu = isset($datas['all'])? $datas['all'] : false;
	    else 
	    	$menu = isset($datas[$parentid])? $datas[$parentid] : false;
	    
	    if(!$menu){
	        $model = M("menu");
	        $map   = array("type"=> $type);
	        if($ALL == false) $map['status'] = 1;
	        if(is_numeric($parentid)) $map['parentid'] = $parentid;
	        $menu = $model -> where($map)->order("parentid,listorder")->select();
	        
	        if($parentid == NULL)
	        	$datas['all'] = $menu;
	        else
	        	$datas[$parentid] = $menu;
	        	        
	        APP_DEBUG or F($file, $datas);
	    }
		return $menu;
	}
	
	/**
	 * 读取同级菜单
	 * 
	 */
	function get_brother($menuid = 0, $type = null){
	    if($menuid == 0 and is_nan($type)) return;
	    
	    if($menuid == 0) return self::get_menu($type);
	    
	    $file = "menus_brother";
	    $datas = F($file);
	   	
	    $menu = isset($datas[$menuid]) ? $datas[$menuid] : false;
	    
	    if(!$menu){
	    	$map =  array("m.parentid|m.id" => $menuid);
	    	APP_DEBUG or $map['m.status'] = 1;
	        $model = M("Menu");
	        $model -> alias("m")
	               -> join("inner join " . C("DB_PREFIX"). "menu m2 on m.parentid = m2.parentid")
	               -> where($map)
	               -> group("m.id")
	               -> field("m.*")
	       		   -> order("parentid,listorder");
	        
	        $menu = $model -> select();
	        $datas[$menuid]  = $menu;	        
	        APP_DEBUG or F($file, $datas);    
	    }
	    
	    return $menu;
	}
	
	/**
	 * 读取指定ID的菜单信息
	 * @param int $menuid :菜单ID
	 * @return array
	 * @author wscsky
	 */
	function get_menu_info($menuid){
		if(!is_numeric($menuid) || $menuid == 0) return false;
		$file = "menus_info_data";
		$datas = F($file);		 
		$menu = isset($datas[$menuid])? $datas[$menuid] : false;
		if(!$menu){
			$model = M("menu");
			$menu = $model -> where("id = %d", $menuid)->find();
			$datas[$menuid] = $menu;
			APP_DEBUG or F($file, $datas);
		}
		return $menu;
	}
	
}


