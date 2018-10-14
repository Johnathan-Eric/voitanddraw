<?php
/*
 * 前台模版list调用逻辑代码
 * @author wscsky
 */
namespace Home\Logic;
class LinkLogic{
	
    protected $cache_time = 1800; //缓存时间为30分种
	
    /**
     * 读取上一个
     */
    function get_pre($filter=array()){
    	switch ($filter['type']){
    		case CATE_TYPE_WEIXIN:
                $map = array('id' => array("GT", $filter['id']));	   
                if($filter['cat_id'] >0) $map['cat_id'] = $filter['cat_id'];
                $r = M("weixin")->where($map)->order("id ASC")->find(); 
                $html  = $r ? '<a class="pre_a" href="'.U('/show/'.$r['account']).'"'.$filter['target'].'>'.$r['wx_name'].'</a>' : $filter['msg'];
	    	    break;
	    	case CATE_TYPE_NEWS:
	    	    $map = array('id' => array("GT", $filter['id']));
	    	    if($filter['cat_id'] >0) $map['cat_id'] = $filter['cat_id'];
	    	    $r = M("news")->where($map)->order("id ASC")->find();
	    	    if(!$r){
	    	        $html =  $filter['msg'];
	    	    }else{
    	    	    $cat_info =  get_cat_info($r['cat_id']);
    	    	    $r['cat_code'] = $cat_info['cat_code'];
    	    	    $r['cat_name'] = $cat_info['cat_name'];
    	    	    $html  = '<a class="pre_a" href="'.news_url($r).'"'.$filter['target'].'>'.$r['title'].'</a>';
	    	    }
	    	    break;
	        default:
	            $html = $filter['msg'];
	            break;
    	}
    	return $html;
    }
    
    /**
     * 读取下一个
     */
    function get_next($filter=array()){
        switch ($filter['type']){
        	case CATE_TYPE_WEIXIN:
        	    $map = array('id' => array("LT", $filter['id']));
        	    if($filter['cat_id'] >0) $map['cat_id'] = $filter['cat_id'];
        	    $r = M("weixin")->where($map)->order("id Desc")->find();
        	    $html  = $r ? '<a class="pre_a" href="'.U('/show/'.$r['account']).'"'.$filter['target'].'>'.$r['wx_name'].'</a>' : $filter['msg'];
        	    break;
        	case CATE_TYPE_NEWS:
        	    $map = array('id' => array("LT", $filter['id']));
        	    if($filter['cat_id'] >0) $map['cat_id'] = $filter['cat_id'];
        	    $r = M("news")->where($map)->order("id desc")->find();
        	    if(!$r){
        	        $html =  $filter['msg'];
        	    }else{
        	        $cat_info =  get_cat_info($r['cat_id']);
        	        $r['cat_code'] = $cat_info['cat_code'];
        	        $r['cat_name'] = $cat_info['cat_name'];
        	        $html  = '<a class="pre_a" href="'.news_url($r).'"'.$filter['target'].'>'.$r['title'].'</a>';
        	    }
        	    break;
        	default:
        	    $html = $filter['msg'];
        	    break;
        }
        return $html;
    }
    
}