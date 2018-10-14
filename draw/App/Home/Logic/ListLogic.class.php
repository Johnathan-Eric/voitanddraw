<?php
/*
 * 前台模版list调用逻辑代码
 * @author wscsky
 */
namespace Home\Logic;
class ListLogic{
	
    protected $cache_time = 1800; //缓存时间为30分种
	/**
	 * 读取商品列表
	 * @param int $limit 每页显示数
	 * @param array $filter 查询条件
	 * @return array
	 * @author wscsky
	 */
	function goods($limit = 10, $filter = array(), &$pages = null){
	    //查缓存
	    if($filter['cache']){
	       $cache_file = "goods_list_".md5($limit . serialize($filter));
	       $data       = S($cache_file);
	       if($data['list']){
	       	   $pages = $data['pages'];
	       	   return $data['list'];
	       } 
	    }
	    
		$model = D("Common/Goods");
		$weight = "";
		$where = "1=1";
		$map   = array();
		$join  = array();
		$join[] = "left join ".C("DB_PREFIX") . "category c on c.cat_id = g.gid";
		//关键字
		if(!empty($filter['keyword'])){
		    $map['g.name|g.goods_sn'] = array('like',"%{$filter['keyword']}%");
		}
		//属性条件
		if(isset($filter['tp']) && $filter['tp'] >=0 ){
		    $map['g.type'] = array("in",explode(',',$filter['tp']));
		}
		//分类条件
		if($filter['cat_id']){
		    if(is_numeric($filter['cat_id']))
			    $map['c.cat_id|c.cat_pid'] = $filter['cat_id'];
		    else 
		        $map['c.cat_id|c.cat_pid'] = array("in",$filter['cat_id']);
		}
		//不显示的ID
		if($filter['noid']){
		    if(is_numeric($filter['noid']))
		        $map['g.goods_id'] = array("neq",$filter['noid']);
		    else
		        $map['g.goods_id'] = array("notin",$filter['noid']);
		}
		//指定ID
		if($filter['ids']){
		    if(is_array($filter['ids'])){
		        $map['g.goods_id'] = array("in",$filter['ids']);
		    }else{
		        $map['g.goods_id'] = array("in",explode(',', $filter['ids']));
		    }
		}
		//审核
		if(is_numeric($filter['status'])) $map['g.online'] = $filter['status'];
		//日期条件[多少天内]
		if($filter['day'] > 0) $map['addtime'] = array("GT", time() - $filter['day'] * 84600);
		//排序
		switch ($filter['order']){
			case 'sales':
				$order = "g.sales desc";
				break;
			case 'hitnum':
			case 'hot':
				$order = "g.hitnum desc";
				break;
			case "addtime":
			    $order = "g.addtime desc";
			    break;
		    case "fav":
		    case "favnum":
		        $order = "g.favnum desc,g.sales desc";
		        break;
		    case "uptime":
		    case "new":
		        $order = "g.uptime desc";
		        break;
			default:
				$order = $filter['order'];
				break;			
		}
		//属性处理
		if(is_numeric($filter['attr'])){
		    $where .=" and g.attr & {$filter['attr']} = {$filter['attr']}";
		}
		if(is_numeric($filter['noattr'])){
			$where .=" and g.attr & {$filter['noattr']} != {$filter['noattr']}";
		}
		if(is_numeric($filter['xorattr'])){
		    $where .=" and g.attr & {$filter['xorattr']} > 0";
		}
		if(is_numeric($filter['xornoattr'])){
		    $where .=" and g.attr & {$filter['xornoattr']} = 0";
		}
		//跳过
		$skip = 0;
		if($filter['skip'] > 0) $skip = $filter['skip'];
		
		if(empty($filter['field'])){
		      $filter['field'] = "c.cat_name,c.cat_ename,c.cat_code,g.goods_id,g.goods_sn,g.gid,g.name,g.en_name,g.title,g.share_title,g.share_des,g.thumb,g.unit,g.old_price,g.price,g.sales,g.type,g.group_num,g.group_price,g.sdate,g.edate,g.online,g.attr,g.listorder,g.hitnum,g.addtime,g.uptime,g.maxnum";
        }
        //收藏条件
        if($filter['favuid'] > 0){
            $join[] = "right join ".C("DB_PREFIX") . "fav f on f.goods_id = g.goods_id";
            $map['f.uid'] = $filter['favuid'];
            $filter['field'] .= ",f.id as favid,f.favtime,f.favtitle";
            unset($map['g.online']);
        }
        //首页显示
        if($filter['index_show']){
        	$map['g.index_show'] = $filter['index_show'] ? 1 : 0;
        }
		if($where !=="") $map['_string'] = $where;
		//翻页处理
		if($filter['page']){
		    $model->alias('g');
		    if(!empty($join)) $model->join($join);
		    $total = $model-> where($map) -> count();
		    if($total <= $skip){
		    	$pagelink = "";
		    	return array();
		    }
		    if(is_numeric($filter['max']) && $total > $filter['max']) $total = $filter['max'];
		    $page = intval($filter['page']);
		    $page < 1 && $page = 1;
		    $skip += ($page-1)*$limit;
		    unset($filter['rnd']);
		    //分页
		    $page_obj = new \Think\Page($total, $limit);
		    $page_config = $filter['page_config'] ? $filter['page_config'] : "PAGE_CONFIG";
		    $page_obj = page_config($page_obj,$page_config);
		    C("SHORT_URL_ON",true);
		    $pages['pagelink']  = $page_obj->show($page);
		    $pages['totalpage'] = $page_obj->totalPages;
		    $pages['page']      = $filter['page'];
		    $pages['limit']     = $limit;
		    $pages['total']     = $total;
		}
		//关联查询
		$link = array();
		if($filter['link']){
			if(!is_array($filter['link'])){
				$link = explode(",", $filter['link']);
			}else{
				$link = $filter['link'];
			}
		}
		//读取数据
		$model->alias('g');
		if(!empty($join)) $model->join($join);
		$list = $model 	-> where($map) 
						-> order($order)
						-> field($filter['field'])
						-> relation($link)
						-> limit($skip, $filter['rnd'] > $limit ? $filter['rnd'] : $limit)
						-> select();
 		//随机读取
 		if($filter['rnd'] > $limit){
 			$filter['cache'] = false;
 			$keys  = array_rand($list, $limit);
 			$_list = array();
 			foreach ($keys as $key){
 			    $_list[] = $list[$key];
 			}
 			$list = $_list;
 		}
 		$data = array("list"=>$list, "pages"=> $pages);
		APP_DEBUG or $filter['cache'] && S($cache_file,$data,$this->cache_time);
// 		echo $model->getLastSQL();
		return $list;
	}
	
	/**
	 * 读取门店列表
	 * @author watchman
	 * @param int $limit 每页显示数
	 * @param array $filter 查询条件
	 * @return array
	 */
	function store($limit = 10, $filter = array(), &$pages = null){
		//查缓存
		if($filter['cache']){
			$cache_file = "store_list_".md5($limit . serialize($filter));
			$data       = S($cache_file);
			if($data['list']){
				$pages = $data['pages'];
				return $data['list'];
			}
		}
		
		$model = D("Common/Store");
		$weight = "";
		$where = "1=1";
		$map   = array();
		$join  = array();
		//关键字
		if(!empty($filter['keyword'])){
			$map['s.store_name|s.address'] = array('like',"%{$filter['keyword']}%");
		}
	
		//不显示的ID
		if($filter['noid']){
			if(is_numeric($filter['noid']))
				$map['s.store_id'] = array("neq",$filter['noid']);
			else
				$map['s.store_id'] = array("notin",$filter['noid']);
		}
		//审核
		if(is_numeric($filter['status'])) $map['s.status'] = $filter['status'];
		//排序
		switch ($filter['order']){
			default:
				$order = $filter['order'];
				break;
		}
		//跳过
		$skip = 0;
		if($filter['skip'] > 0) $skip = $filter['skip'];
	
		if(empty($filter['field'])){
			$filter['field'] = "s.store_id,s.store_name,s.logo,s.store_desc,s.open_on,s.open_end,s.status,s.book_on,s.book_end,s.region_id,s.region_name,s.address,s.lng,s.lat,s.contacter,s.mobile,s.telphone,s.wechat,s.listorder";
		}
		
		if($where !=="") $map['_string'] = $where;
		//翻页处理
		if($filter['page']){
			$model->alias('s');
			if(!empty($join)) $model->join($join);
			$total = $model-> where($map) -> count();
			if($total <= $skip){
				$pagelink = "";
				return array();
			}
			if(is_numeric($filter['max']) && $total > $filter['max']) $total = $filter['max'];
			$page = intval($filter['page']);
			$page < 1 && $page = 1;
			$skip += ($page-1)*$limit;
			unset($filter['rnd']);
			//分页
			$page_obj = new \Think\Page($total, $limit);
			$page_config = $filter['page_config'] ? $filter['page_config'] : "PAGE_CONFIG";
			$page_obj = page_config($page_obj,$page_config);
			C("SHORT_URL_ON",true);
			$pages['pagelink']  = $page_obj->show($page);
			$pages['totalpage'] = $page_obj->totalPages;
			$pages['page']      = $filter['page'];
			$pages['limit']     = $limit;
			$pages['total']     = $total;
		}
		//读取数据
		$model->alias('s');
		if(!empty($join)) $model->join($join);
		$list = $model 	-> where($map)
        		-> order($order)
        		-> field($filter['field'])
        		-> limit($skip, $filter['rnd'] > $limit ? $filter['rnd'] : $limit)
        		-> select();
		//随机读取
		if($filter['rnd'] > $limit){
			$filter['cache'] = false;
			$keys  = array_rand($list, $limit);
			$_list = array();
			foreach ($keys as $key){
				$_list[] = $list[$key];
			}
			$list = $_list;
		}
		$data = array("list"=>$list, "pages"=> $pages);
		APP_DEBUG or $filter['cache'] && S($cache_file,$data,$this->cache_time);
		return $list;
	}
	
    /**
	 * 读取新闻列表
	 * @param int $limit 显示数量
	 * @param array $filter 查询条件
	 * @return array
	 * @author wscsky
	 */
	function news($limit = 10, $filter = array(), &$pagelink = null){
		
	    //查缓存
	    if($filter['cache']){
	       $cache_file = "news_list_".md5($limit . serialize($filter));
	       $list       = S($cache_file);
	       $pagelink   = S($cache_file."_page");
	       if($list) return  $list;
	    }
		$model = D("Common/News");
		$weight = "";
		$map   = array();
		$join  = array();
		$join[] = "right join ".C("DB_PREFIX") . "category c on c.cat_id = n.cat_id";
		//关键字
		if(!empty($filter['keyword'])){
		    $map['title|tags'] = array('like',"%{$filter['keyword']}%");
		}
		
		//分类条件
		if($filter['cat_id']){
		    if(is_numeric($filter['cat_id']))
			     $map['c.cat_id'] = $filter['cat_id'];
		    else 
		        $map['c.cat_id'] = array("in",$filter['cat_id']);
		}
		
		if($filter['nocat_id']){
		    if(is_numeric($filter['nocat_id']))
		        $map['c.cat_id'] = array("neq", $filter['nocat_id']);
		    else
		        $map['c.cat_id'] = array("notin",$filter['nocat_id']);
		}

		//tag条件
		if($filter['tagid']){
		    $join[] = "right join ".C("DB_PREFIX") . "tags_data wt on wt.id = n.id";
		    $map['wt.type'] = TAG_TYPE_NEWS;
		    if(is_numeric($filter['tagid']))
		        $map['wt.tagid'] = $filter['tagid'];
		    else
		        $map['wr.tagid'] = array("in",$filter['tagid']);
		}
		
		//不显示的ID
		if($filter['noid']){
		    if(is_numeric($filter['noid']))
		        $map['n.id'] = array("neq",$filter['noid']);
		    else
		        $map['n.id'] = array("notin",$filter['noid']);
		}
		//微信ID限制
		if($filter['wx_id']){
		    if(is_numeric($filter['wx_id']))
		        $map['n.wx_id'] = array("eq",$filter['wx_id']);
		    else
		        $map['n.wx_id'] = array("in",$filter['wx_id']);
		}
		//审核
		if(is_numeric($filter['status'])) $map['n.status'] = $filter['status'];

		//日期条件[多少天内]
		if(is_numeric($filter['day'])) $map['uptime'] = array("EGT", time() - $filter['day'] * 84600);
		$where = "1=1";
		//属性处理
		if(is_numeric($filter['attr'])){
		    $where .=" and n.attr_type & {$filter['attr']} = {$filter['attr']}";
		}
		if(is_numeric($filter['noattr'])){
		    $where .=" and n.attr_type & {$filter['noattr']} != {$filter['noattr']}";
		}
		if(is_numeric($filter['xorattr'])){
		    $where .=" and n.attr_type & {$filter['xorattr']} > 0";
		}
		if(is_numeric($filter['xornoattr'])){
		    $where .=" and n.attr_type & {$filter['xornoattr']} = 0";
		}
		
		//跳过
		$skip = 0;
		if($filter['skip'] > 0) $skip = $filter['skip'];
		
		//排序
		switch ($filter['order']){
			case 'uptime':
				$order = "n.uptime desc";
				break;
			case 'addtime':
				$order = "n.addtime desc";
				break;
			case 'hit':
				$order = "view_num desc";
				break;
			default:
				$order = $filter['order'];
				break;			
		}
		
		if(empty($filter['field'])){
		  $filter['field'] = "n.id,`code`,author,n.title,title_style,tags,link,jump,linkurl,addtime,uptime,user_type,attr_type,template,imgurl,about_ids,intro,view_num,c.cat_code,c.cat_name";
        }
        
        if($where !=="") $map['_string'] = $where;
       
//         print_r($map);return;
        //统计
        if($filter['page']){
            $model->alias('n');
            if(!empty($join)) $model->join($join);
        	$total = $model-> where($map) -> count();
        	if($total <= $skip){
        	    $pagelink = "";
        		return array();
        	}
            $page = intval($filter['page']);
            $page < 1 && $page = 1;
            $skip += ($page-1)*$limit;
            //分页
    		$page_obj = new \Think\Page($total, $limit);
    		$page_obj = page_config($page_obj);
    		C("SHORT_URL_ON",true);
    		$pagelink = $page_obj->show($page);
        }
		$model->alias('n');
		if(!empty($join)) $model->join($join);
		
		$list = $model 	-> where($map) 
						-> order($order)
						-> field($filter['field'])
						-> limit($skip,$limit)
						-> select();
		APP_DEBUG or $filter['cache'] && S($cache_file,$list,$this->cache_time);
		APP_DEBUG or $filter['cache'] && $filter['page'] &&  S($cache_file."_page",$list,$this->cache_time);
		return $list;
		
	}
	
	/**
	 * 计取列表数据
	 * @author wscsky
	 */
	function data($limit = 10, $filter = array(), &$pages = null){
	    $member = session('member');
	    
	    $map   = $filter['map'] ? $filter['map'] : array();
	    $field = $filter['field'] ? $filter['field'] : "*";
        if(!$filter['data']) return array();
        $ttime = $filter['time'];
        $order = $filter['order'] ? $filter['order'] : '';
        $model = D($filter['data'])->alias("a");
	    //时间条件
	    if($ttime){
	        if($filter['sdate'] > 0 && $filter['edate'] > 0){
	            $map[$ttime] = array(array("egt", strtotime($filter['sdate'])),array("lt", strtotime($filter['edate']) + 86400),'and');
	        }else{
	            if($filter['sdate'] > 0) $map[$ttime] = array('egt', strtotime($filter['sdate']));
	            if($filter['edate'] > 0) $map[$ttime] = array('lt', strtotime($filter['edate'])+86400);
	        }
	    }
	    $skip = 0;
	    //翻页处理
	    if($filter['page']){
	        $model-> where($map)->join($filter['join']);
	        if($filter['group']) $model->group($filter['group']);
	        if($filter['cfield']){
	            $total = $model -> count("distinct({$filter['cfield']})");
	        }else{
	            $total = $model -> count();
	        }
	        if($total <= $skip){
	            $pagelink = "";
	            $pages['total']     = 0;
	            if(!APP_DEBUG) return array();
	        }
	        $page = intval($filter['page']);
	        $page < 1 && $page = 1;
	        $skip += ($page-1)*$limit;
	        //分页
	        $page_obj = new \Think\Page($total, $limit);
	        $page_config = $filter['page_config'] ? $filter['page_config'] : "PAGE_CONFIG";
	        $page_obj = page_config($page_obj,$page_config);
	        C("SHORT_URL_ON",true);
	        $pages['pagelink']  = $page_obj->show($page);
	        $pages['totalpage'] = $page_obj->totalPages;
	        $pages['page']      = $filter['page'];
	        $pages['limit']     = $limit;
	        $pages['total']     = $total;
	    }
	    //读取数据
	    $model  -> alias("a")
        	    -> where($map)
        	    -> order($order)
        	    -> field($field)
        	    -> limit($skip, $limit);
	    if($filter['join']) $model->join($filter['join']);
	    if($filter['group']) $model->group($filter['group']);
	    if($filter['link']) $model->relation($filter['link']);
	    $list = $model -> select();
// 	    echo $model->getLastSQL();
// 	    dump($filter);
	    return $list;
	}
	
}