<?php
/*
 * 前台模版分类调用逻辑代码
 * @author wscsky
 */
namespace Home\Logic;
class CateLogic{
	
    /**
     * 读取分类信息
     * @param int $limit
     * @param array $filter
     * @return array
     * @author wscsky
     */
	function getcate($limit, $filter = array()){
		//查缓存
	    $cache_file = "cate_list_".md5($limit . serialize($filter));
	    $list = S($cache_file);
	    
		if(!$list){
    		$model  = M("category");
    		$map 	= array('is_show'=>1);
    		if(isset($filter['pid'])) $map['cat_pid'] = array("in",$filter['pid']);
    		if(isset($filter['ids'])) $map['cat_id']  = array("in",$filter['ids']);
    		if(isset($filter['level'])) $map['cat_level'] = array("in",$filter['level']);
    		if(isset($filter['com'])) $map['is_com']  = $filter['com'];
    		if(isset($filter['hot'])) $map['is_hot']  = $filter['hot'];
    		if(isset($filter['type'])) $map['type'] = $filter['type'];
    		if($limit > 0) $model->limit($limit);
    		
    		$list = $model -> where($map) -> order('listorder asc') -> select();
    		//echo $model->getLastSQL();
    		APP_DEBUG or S($cache_file, $list,3600);
		}
		return $list;
	}
	/**
	 * 
	 * @param unknown $cat_id
	 */
	function get_pos($cat_id){
	    if(!is_numeric($cat_id)) return "";
	    $data = F("cate_pos_data");
	    
	    if(!$data[$cat_id]){
	        $cat_full_id = M("category")->where("cat_id = %d", $cat_id)->getField('cat_full_id');
	        if($cat_full_id){
	            $data[$cat_id] = M("category") -> where("cat_id in (%s)", trim($cat_full_id,","))
	                                         -> order("cat_level asc")
	                                         -> select();
	        }
	        APP_DEBUG or F("cate_pos_data", $data);
	    }
	    return $data[$cat_id];
	}
	
	
}