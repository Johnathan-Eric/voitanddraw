<?php
/**
 * 图形化数据： 将树结构数据处理成 树结构图形
 * @param array $tree
 * @param string $pk
 * @param string $icon
 * @param string $child
 * @return array
 * @author Jeffreyzhu.cn@gmail.com
 */
function graph_tree_list($tree, $pk, $icon = ' ', $child = '_child', $replace_icon = array()) {
    $list = array();
    if(count($replace_icon)<1) $replace_icon = array('a' => '│', 'b' => '├', 'c' => '└');
    $replace_icon = $replace_icon + array(' ' => $icon);
    //array('a' => '│', 'b' => '├', 'c' => '└', ' ' => $icon)
  if (is_array($tree)) {
    $next = array();
    $depth = 0; $_icon = ''; $__icon= '                                ';
    // icon  │├└ 对应 abc

    while ($depth > -1) {
      $data = current($tree);
      if (!empty($data)) {
        $list[$data[$pk]] =& $data;
        if (false !== next($tree)) {
          $next[$depth] =& $tree;

          if ($icon && $depth) {
            $__icon[$depth-1] == 'b' && $__icon[$depth-1] = 'a';
            $__icon[$depth] = 'b';
            $_icon = rtrim($__icon, ' ');
          }
        }
        else if ($icon && $depth) {
          $__icon[$depth-1] == 'b' && $__icon[$depth-1] = 'a';
          $__icon[$depth] = 'c';
          $_icon = rtrim($__icon, ' ');
          $__icon[$depth] = ' ';
        }

        $data['_depth'] = $depth;
        $icon && $data['_icon'] = strtr($_icon, $replace_icon);

        if (isset($data[$child])) {
          $depth++;
          unset($tree);
          $tree =& $data[$child];
          unset($data[$child]);
          $data['_has_child'] = true;
        }
        else {
          $data['_has_child'] = false;
        }
      }
      else {
        $icon && ($__icon[$depth] = ' ') && ($_icon = '');
        $depth--;
        unset($tree);
        $tree =& $next[$depth];
        unset($next[$depth]);
      }

      unset($data);
    }
  }

  return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 读取实体类型信息
 * @param int $entity_id  		实体ID
 * @param int $type 	实体实型
 * @return array()
 */
function get_entity($entity_id, $entity_type = ENTITY_TYPE_MEMBER){
    return \Common\Logic\EntityLogic::get_data($entity_id, $entity_type);
}

/**
 * 读取实体类型名称
 * @param int $entity_type
 * @return string
 */
function get_entity_name($entity_type){
    $types = \Common\Logic\EntityLogic::get_types();
    if(is_null($entity_type)){
        return $types;
    }elseif(isset($types[$entity_type])){
        return $types[$entity_type];
    }else{
        return '其它';
    }
}

/**
 * 显示变动方式信息
 * @param int $mode 变动类型
 * @param bool $color 是否显示颜色
 * @return string
 * @author wscsky 
 */
function get_mode_name($mode, $color=true){
	$_color = ""; 
	if($mode == MODE_TYPE_DEC){
		$mode = "减少";
		$_color = 'blue';
	}
	elseif($mode == MODE_TYPE_INC) {
		$mode = "增加";
		$_color = "red";
	}else{
		$mode = "未知";
		$_color = "black";		
	}
	
	if($color)
		return "<font color='{$_color}'>{$mode}</font>";
	else 
		return $mode;
	
}
/**
 * 显示状态信息
 * @param int $is_del 删除状态
 * @param bool $color 是否显示颜色
 * @return string
 * @author wscsky
 */
function get_isdel_name($is_del, $color=true){
	$_color = "";
	if($is_del == 1){
		$is_del = "删除";
		$_color = 'red';
	}
	else {
		$is_del = "正常";
		$_color = "blue";
	}

	if($color)
		return "<font color='{$_color}'>{$is_del}</font>";
	else
		return $is_del;

}

/**
 * 显示管理员组角色
 */
function get_admin_roles($roles){
	if(empty($roles)) return "--";
	$roles      = explode(",", $roles);
	$_roles 	= D("Admin/Admin","Logic")->get_roles();
	foreach ($roles as &$val){
		$val = $_roles[$val];
	}
	return implode(",", $roles);
}

/**
 * 生成编号SN
 * @param int $type 实体类型 
 * @param int $length 编号长度
 * @return @string
 */
function create_sn($type, $length = 12) {
	if($length < 10) $length = 10;
	$prefix = $type;
	if($type < 10){
		$prefix = "0" . $prefix;
	}
	$prefix .= date('ymd');
	mt_srand((double) microtime() * 1000000);
	return $prefix . str_pad(mt_rand(1, 99999), $length - 6, '0', STR_PAD_LEFT);
}

/**
 * 生成编号SN
 * @param int $type 实体类型 
 * @param int $length 编号长度
 * @return @string
 */
function create_new_sn($type, $length = 12) {
        $prefix = $type;
	$prefix .= date('ymd');
	mt_srand((double) microtime() * 1000000);
	return $prefix . str_pad(mt_rand(1, 99999), $length - 6, '0', STR_PAD_LEFT);
}

/**
 * 生成提货码
 * @author wscsky
 */
function create_tcode($sno = "", $len = 16){
    $len -= strlen($sno);
    for ($i;$i<$len;$i++){
        $sno .= rand(0,9);
    }
    return $sno;
}

/**
 * 格式两位小数不四舍五入
 * @param number $val :要处理的数字
 * @param int $digit  :需要的小数位
 * @author wscsky
 */
function num2money($val,$digit=2){
	list($m,$n) = explode('.',$val);
	$p = substr($n,0, $digit);
	$p = str_pad($p, $digit, '0', STR_PAD_RIGHT);
	if($m=="") $m="0";
	$o = ($m.'.'.$p);
	return $o;
}

/**
 * 显示日期
 */
function show_date($format = "Y-m-d", $time = 0, $default= '--'){
	if($time == 0 || !is_numeric($time)) return $default;
	return date($format, $time);
}

/**
 * 日志日期Ymd转成Y-m-d
 */
function show_logdate($date){
	if(strlen($date) != 8) return $date;
	return substr($date, 0,4) . "-" .substr($date, 4,2) . "-" .substr($date, 6);
}

/**
 * 读取提现状态信息
 * @author eva
 */
	
function withdraw_status_name($status){
	switch($status){
		case WITHDRAW_STATUS_CANCEL:
			$status = "已取消";
			break;
		case WITHDRAW_STATUS_UNAUDIT:
			$status = "审批中";
			break;
		case WITHDRAW_STATUS_AUDITED:
			$status = "处理中";
			break;
		case WITHDRAW_STATUS_SUCCESS:
			$status = "提现成功";
			break;
		case WITHDRAW_STATUS_AUDITFAILED:
			$status = "审批失败";
			break;
		case WITHDRAW_STATUS_FAILED:
			$status = "提现失败";
			break;
		default:
			break;
	}
	return $status;
}

/*
 * 读取收益状态
* @param int $status 状态值
* @param bool $color 是否颜色显示
* @author eva
*/
function profit_status_name($status, $color = true){
	switch ($status){
		case PROFIT_STATUS_UNTAKE:
			$status = "未生效";
			$_color = "#000";
			break;
		case PROFIT_STATUS_UNCONFIRMED:
			$status = "未结算";
			$_color = "#000";
			break;
		case PROFIT_STATUS_CONFIRMED:
			$status = "已结算";
			$_color = "green";
			break;
		case PROFIT_STATUS_INVALID:
			$status = "已失效";
			$_color = "gray";
			break;
		default:
			$status = "无效状态";
			$_color = "red";
			break;
	}
	if($color){
		return "<font color='{$_color}'>{$status}</font>";
	}else
		return $status;
}


/**
 * 资金变动方式
 * @author eva
 */
function cash_mode_name($mode, $color=true){
	switch ($mode){
		case MODE_TYPE_INC:
			$mode = "增加";
			$_color = "red";
			break;
		case MODE_TYPE_DEC:
			$mode = "减少";
			$_color = "blue";
			break;
		default:
			return $mode;
			break;
	}
	if($color)
		return "<font color='{$_color}'>{$mode}</font>";
	else 
		return $mode;
}


/**
 * 读取管理员的名字
 * @author eva
 */
function get_admin_name($aid){
	static $_admins = array();
	if(!is_numeric($aid)) return "--";
	
	if(!isset($_admins[$aid])){
		$admin = M('admin')->where('aid=%d',$aid)->field("uname")->find();
		if($admin){
			$_admins[$aid] = $admin['uname'];
		}else{
			$_admins[$aid] = "--";
		}
	}
	return $_admins[$aid]; 
}

/**
 * 用用户组ID来读取用户组名
 * @author wscsky
 */
function get_group_name($gid){
	static $groups = array();
	if(empty($groups)){
		$data = M("group")->field('gid,name')->select();
		foreach ($data as $group){
			$groups[$group['gid']] = $group['name'];
		}
	}
	if(isset($groups[$gid]))
		return $groups[$gid];
	else 
		return "未知";
}

/*
 * 读取充值状态
* @author eva
*/
function recharge_status_name($status, $color=true){
	
	switch ($status){
		case RECHARGE_STATUS_FAILED:
			$status = "未成功";
			$_color = "red";
			break;
		case RECHARGE_STATUS_SUCCESS:
			$status = "已成功";
			$_color = "green";
			break;
		case RECHARGE_STATUS_UNAUDIT:
			$status = "待审核";
			$_color = "blue";
			break;
		default:
			$status = "无效状态";
			$_color = "#999";
			break;
	}
	if($color)
		return "<font color='{$_color}'>{$status}</font>";
	else 
		return $status;
}

/*
 * 读取充值类型
* @author eva
*/
function recharge_type_name($type){

	switch ($type){
		case RECHARGE_TYPE_OFFLINE:
			$type = "线下充值";
			break;
		case RECHARGE_TYPE_ONLINE:
			$type = "在线充值";
			break;
		
		default:
			break;
	}
		return $type;
}

/**
 * 读取会员状态名字
 * @author eva
 */
function member_status_name($status){
	switch($status){
		case MEMBER_STATUS_UNAUDIT:
			return '未开通';
			break;
		case MEMBER_STATUS_AUDITED:
			return '已开通';
			break;
		case MEMBER_STATUS_LOCKED:
			return '锁定';
			break;
		default:
			return "--";
	}
}

/**
 * 读取用户信息
 * @author eva
 */
function member_info($uid,$field=""){
	if(empty($uid) || !is_numeric($uid)){
		return "参数不对";
	}
	$info = M('member')->where('uid=%d',$uid)->find();
	if($info && empty($field)){
		return $info;
	}elseif($info && $field){
		return $info[$field];
	}else{
		return '';
	}
}

//截取utf8字符串
function utf8Substr($str, $from, $len)
{
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
			'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
			'$1',$str);
}

//统计字符长度包括中文、英文、数字
function sstrlen($str,$charset) {
	$n = 0; $p = 0; $c = '';
	$len = strlen($str);
	if($charset == 'utf-8') {
		for($i = 0; $i < $len; $i++) {
			$c = ord($str{$i});
			if($c > 252) {
				$p = 5;
			} elseif($c > 248) {
				$p = 4;
			} elseif($c > 240) {
				$p = 3;
			} elseif($c > 224) {
				$p = 2;
			} elseif($c > 192) {
				$p = 1;
			} else {
				$p = 0;
			}
			$i+=$p;$n++;
		}
	} else {
		for($i = 0; $i < $len; $i++) {
			$c = ord($str{$i});
			if($c > 127) {
				$p = 1;
			} else {
				$p = 0;
			}
			$i+=$p;$n++;
		}
	}
	return $n;
}

//会员状态
function member_status($val =  null){
	$data = array(
			MEMBER_STATUS_UNFOCUS	=>'未关注',
			MEMBER_STATUS_FOCUS		=>'已关注',
	);
	return is_null($val) ? $data : $data[$val];
}

//会员是否为店长
function member_master($val =  null){
	$data =  array(
			MEMBER_MASTER_NO		=>'否',
			MEMBER_MASTER_YES		=>'是',
	);
	return is_null($val) ? $data : $data[$val];
}

//会员来源方式
function member_referer($val =  null){
	$data =  array(
			MEMBER_REFERER_LINK			=>'链接',
			MEMBER_REFERER_QRCODE		=>'二维码',
	);
	return is_null($val) ? $data : $data[$val];
}

//订单状态
function orders_status($val =  null){
    $data = \Common\Logic\OrdersLogic::get_orders_status(true);
	return is_null($val) ? $data : $data[$val];
}

//团购状态
function group_status($val =  null){
    $data = \Common\Logic\OrdersLogic::get_group_status(true);
    return is_null($val) ? $data : $data[$val];
}

//团购状态2
function group_status2($data){
    switch ($data['group_status']){
        case 0:
            return '待付款';
            break;
        case 1:
        case 2:
            return $data['order_pid'] == 0 ? "开团成功":"参团成功";
            break;
        case 3:
            return $data['pay_status'] == 1 ? "团购成功":"待补尾款";
            break;
        case 4:
            return "团购失败";
            break;
        case 10:
            return "已取消";
            break;
    }
}

//订单支付状态
function orders_pay_status($val =  null){
	$data =  array(
			ORDERS_PAY_UNAPPLY			=>'未支付',
			ORDERS_PAY_APPLY			=>'已支付',
	        ORDERS_PAY_PART             =>'部分支付',
			ORDERS_PAY_RETURN			=>'已退款',
	);
	return is_null($val) ? $data : $data[$val];
}

/**
 * 通过分类ID读取分类基本信息
 * @param int $cat_id
 * @author wscsky
 */
function get_cat_info($cat_id = null, $type = CATE_TYPE_GOODS){
	$cach_file = "all_{$type}_cate_data";
	$data = S($cach_file);
	if(!$data){
		$list = M("category") -> where("type = '%s'", $type) -> field("*") -> select();
		foreach ($list as $cat){
			$data[0][$cat['cat_id']] 	= $cat;
			$data[1][strtolower($cat['cat_code'])] 	= $cat;
		}
		APP_DEBUG or S($cach_file, $data,3600);
	}
	if(is_null($cat_id)) return $data;
	if(is_numeric($cat_id))
		return $data[0][$cat_id];
	else
		return $data[1][strtolower($cat_id)];
}

/**
 * 读取商品属性项
 */
function get_goods_attrs(){
    $cach_file = "all_".CATE_TYPE_ATTR."_cate_data";
    $data = S($cach_file);
    if(!$data){
        $data = M("category") -> where("type = '%s' and cat_pid = 0", CATE_TYPE_ATTR) -> getField("cat_id,cat_name");
        APP_DEBUG or S($cach_file, $data,3600);
    }
    return $data;
}

/**
 * 属性配置设置项
 * @author wscsky
 */
function get_attr_config(){
	return array(
			//0 			=> "普通",
			1			=> "推荐",
			2		  	=> "最新",
	        4           => "首页",
	);
}

/**
 * 读取推荐属性
 * @param int @attr 推荐属性
 * @param bool 是否还返回可选
 * @param string option的名字
 * @return html
 * @author wscsky
 */
function get_attr($attr = 0, $option = true, $option_name='attr[]'){
    $attr_data = get_attr_config();
    $html = "";
    foreach ($attr_data as $key => $val){
        if($option){
            if(($attr & $key) == $key){
                $html .= "<label><input type='checkbox' name='{$option_name}' value='{$key}' checked='checked'>{$val}</label>";
            }else{
                $html .= "<label><input type='checkbox' name='{$option_name}' value='{$key}'>{$val}</label>";
            }
        }else{
            if(($attr & $key) == $key){
                $html .= "<lable>{$val}</lable>";
            }
        }
    }
    return $html;
}

/**
 * 优惠券发送方式配置设置项
 * @author watchman
 */
function get_sendtype_config(){
	return array(
			1 => "领取",
			2 => "购买",
			4 => "关注赠送",
	);
}

/**
 * 优惠券发送方式
 * @param int $sendtype 发送类型
 * @param bool 是否还返回可选
 * @param string option的名字
 * @return html
 * @author watchman
 */
function get_sendtype($sendtype = 0, $option = true, $option_name='sendtype[]'){
	$sendtype_data = get_sendtype_config();
	$html = "";
	foreach ($sendtype_data as $key => $val){
		if($option){
			if(($sendtype & $key) == $key){
				$html .= "<label><input type='checkbox' name='{$option_name}' value='{$key}' checked='checked'>{$val}</label>";
			}else{
				$html .= "<label><input type='checkbox' name='{$option_name}' value='{$key}'>{$val}</label>";
			}
		}else{
			if(($sendtype & $key) == $key){
				$html .= "<lable>{$val}</lable>";
			}
		}
	}
	return $html;
}

/**
 * 读取商品规格分类
 * @param int @attr 推荐属性
 * @param bool 是否还返回可选
 * @param string option的名字
 * @return html
 * @author wscsky
 */
function get_spec_cat($spec_cat_id = '', $option = true, $option_name='spec_cat_id[]'){
	$spec_cat_id = explode(',', $spec_cat_id);
	$where['type']		= CATE_TYPE_SPEC;
	$where['is_show'] 	= array('eq', 1);
	$where['cat_level']	= array('eq', 1);
	$spec_cat = M('category')->where($where)->select();
	
	$html = "";
	foreach ($spec_cat as $spec){
		if($option){
			if(count($spec_cat_id) != 0 && in_array($spec['cat_id'], $spec_cat_id)){
				$html .= "<label><input type='checkbox' name='{$option_name}' value='{$spec['cat_id']}' checked='checked'>{$spec['cat_name']}</label>";
			}else{
				$html .= "<label><input type='checkbox' name='{$option_name}' value='{$spec['cat_id']}'>{$spec['cat_name']}</label>";
			}
		}else{
			if(count($spec_cat_id) != 0 && in_array($spec['cat_id'], $spec_cat_id)){
				$html .= "<lable class='spec_class'>{$spec['cat_name']}</lable>";
			}
		}
	}
	return $html;
}

/**
 * 读取收益类型信息
 * @author eva
 */
function get_profit_type_name($type){
	switch ($type){
		case PROFIT_TYPE_CON:
			return "订单提成";
			break;
		case PROFIT_TYPE_REF:
			return "推荐提成";
			break;
		case PROFIT_TYPE_OTH:
			return "其他提成";
			break;
		case "TOTAL":
		case "total":
			return "总计";
			break;
		default:
			return "收益类型不存在";
			break;
	}
}

/**
 * 读取会员升级状态
 * @author eva
 */
function memberlog_status_name($status){
	switch($status){
		case MEMBERLOG_STATUS_UNPAY:
			$status = "未支付";
			break;
		case MEMBERLOG_STATUS_AUDITED:
			$status = "已审核";
			break;
		case MEMBERLOG_STATUS_CANCEL:
			$status = "已取消";
			break;
		default:
			break;
	}
	return $status;
}

/**
 * 读取会员升级支付方式
 * @author eva
 */
function memberlog_paytype_name($pay_type){
	switch($pay_type){
		case MEMBERLOG_PAY_ONLINE:
			$status = "在线支付";
			break;
		case MEMBERLOG_PAY_OFFLINE:
			$status = "线下支付";
			break;
		default:
			break;
	}
	return $status;
}

/**
 * 处理图片后加载
 * @param string $html 要处理的HTML代码
 * @param string $lazy_css 延时加时样式
 * @param string $lazy_flag 延时加载标记
 * @author wscsky
 */
function img_lazyload($html, $lazy_css = "load", $lazy_flag = "_src"){
	
	$pattern="/<[img|IMG].*?src=([\'|\"].*?(?:[\.gif|\.jpg|\.png])[\'|\"]).*?[\/]?>/i";
	preg_match_all($pattern, $html, $match);
	$imgs = array_flip(array_flip($match[1]));
	$lazy_css = $lazy_css == "" ? "" : "class=\"{$lazy_css}\"";
	foreach ($imgs as $key => $img){
		$str 	= "\"".C("zy_load_goods")."\" {$lazy_css} {$lazy_flag}={$img}";
		$html 	= str_replace($img, $str, $html);
	}
	return $html;
}


/**
 * 处理生成新闻URL连接地址
 * @param array $data 为新闻数据
 * @author wscsky
 */
function news_url($data = array()){
    
    if(C("SITE_TYPE") == "ADMIN") $data['test_mode'] = 1;
    
    if($data['jump']) return $data['linkurl'];
	if(empty($data['code'])){
		//return U("/".$data['cat_code']."/detail/".$data['id']);
		return U("/news/detail/id/".$data['id'],'test_mode='.$data['test_mode']);
	}else{
		return U("/news/".$data['code'],'test_mode='.$data['test_mode']);
		//return U("/".$data['cat_code']."/".$data['code']);
	}
}

/**
 * 转化分享方式
 */
function get_share_type($type=null){
    $types = array(
        "ShareAppMessage" => "分享给朋友",
        "ShareTimeline"   => "分享到朋友圈"
    );
    if(is_null($type)) return $types;
    return isset($types[$type]) ? $types[$type]: $type;
}

/**
 * 优惠券变动方式
 * @author brent
 * @param unknown $status
 * @return Ambigous <multitype:string , string>
 */
function get_couponstatus($status){
	$_status = D("Common/Coupon", "Logic") -> get_coupon_status();
	return is_null($status) ? $_status : $_status[$status];
}

/**
 * 获取管理员姓名
 * @author watchman
 */
function get_aname($aid = 0){
	if(!is_numeric($aid) || $aid <= 0) return "系统";
	$aname = M("admin")->where("aid = %d", $aid)->getField("uname", 1);
	return $aname;
}

/**
 * 获取月份名称数据
 * @author watchman
 */
function get_cn_month($month = null){
    $month = intval($month);
    $month_data = array(1=>'一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月');
    return is_null($month) ? $month_data : $month_data[$month];
}

/**
 * 获取星期对应的中文名称
 * @author watchman
 */
function get_cn_week($week = null, $prefix="星期"){
    $names = array(0=>"日", "一", "二", "三", "四", "五", "六");
    return is_null($week) ? $names : $prefix.$names[$week];
}

/**
 * 红包领取方式配置设置项
 * @author watchman
 * @param int $type 1普通红包 2组合红包
 */
function get_gettype_config(){
    $config = array(
        1		  	=> "关注赠送",
        2		  	=> "回复领取",
        4			=> "买单赠送",
    );
    return $config;
}

/**
 * 读取红包领取方式
 * @author watchman
 * @param int $gettype 红包领取方式
 * @param bool 是否还返回可选
 * @param string option的名字
 * @param bool 是否为必选
 * @return html
 */
function get_gettype($gettype = 0, $option = true, $option_name='get_type[]', $required = false, $title=""){
    $type_data = get_gettype_config();
    $html = "";
    foreach ($type_data as $key => $val){
        if($option){
            $validate = $required ? "validate='required:true'" : "";
            $checked = ($gettype & $key) == $key ? "checked='checked'" : "";
            $html .= "<input type='checkbox' id='gettype_chk_{$key}' title='{$title}' name='{$option_name}' value='{$key}' {$checked} {$validate}><label for='gettype_chk_{$key}'>{$val}</label>";
        }else{
            if(($gettype & $key) == $key){
                $html .= "<lable>{$val}</lable>";
            }
        }
    }
    return $html;
}

/**
 * 读取可选择的预约星期
 * @author wscsky
 */
  function  get_book_weeks(){
    $period = array();
    $ttime = strtotime(date("Y-m-d H:0")) + C('book_delay_hour',null,31)*3600+1;
    $book_weeks = C('book_weeks',null,array(1,6));
    in_array(0, $book_weeks) && $period[0] = array(
        'name'    => '周日送达',
        'time'    => strtotime("next sunday",$ttime)
    );
    in_array(1, $book_weeks) && $period[1] = array(
        'name'    => '周一送达',
        'time'    => strtotime("next monday",$ttime)
    );
    in_array(2, $book_weeks) && $period[2] = array(
        'name'    => '周二送达',
        'time'    => strtotime("next Tuesday",$ttime)
    );
    in_array(3, $book_weeks) && $period[3] = array(
        'name'    => '周三送达',
        'time'    => strtotime("next Wednesday",$ttime)
    );
    in_array(4, $book_weeks) && $period[4] = array(
        'name'    => '周四送达',
        'time'    => strtotime("next Thursday",$ttime)
    );
    in_array(5, $book_weeks) && $period[5] = array(
        'name'    => '周五送达',
        'time'    => strtotime("next Friday",$ttime)
    );
    in_array(6, $book_weeks) &&  $period[6] = array(
        'name'    => '周六送达',
        'time'    => strtotime("next Saturday",$ttime)
    );
    return $period;
}
