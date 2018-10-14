<?php
/*
 * 实体类型逻辑
 * @autor wscsky
 */
namespace Common\Logic;
class EntityLogic
{
	protected $_error=null;
	
	/**
	 * 读取实体类型数据
	 * @param int $entity_id           ID
	 * @param int $entity_type         类型
	 * @param int $link                关联数据
	 * @return array()
	 * @author wscsky
	 */
	
	
	
	
	static function get_data($entity_id, $entity_type){
	    if(!$entity_id || !$entity_type) return array();
	    $entity_data = array();
	    switch ($entity_type){
	        case ENTITY_TYPE_ORDER: //订单
        	    $entity_data = D("Common/Orders","Logic") -> get_orders_info($entity_id,"",array('member','ordersgoods','ordershipping'));
        	    break;
        	case ENTITY_TYPE_WORDER: //配送订单
        	    $data = M("orders_shipping")->find($entity_id);
        	    if($data){
        	        $entity_data = D("Common/Orders","Logic") -> get_orders_info($data['order_id'],"",array('member','ordersgoods'));
        	        $entity_data['shipping'] = $data;
        	        $entity_data['sstatus'] = D("Common/Orders","Logic") -> get_shipping_status();
        	    }
        	    break;
	        case ENTITY_TYPE_WITHDRAW://提现
	           $entity_data = D("Common/Withdraw","Logic") -> get_withdraw($entity_id);
        	    break;
        	case ENTITY_TYPE_ADMIN:		//管理员信息
        	    $entity_data = D("Admin/Admin","Logic") -> get_info($entity_id,"",array("group"));
        	    break;
        	case ENTITY_TYPE_MEMBER:		//用户信息
        	    $entity_data = D("Common/Member","Logic") -> get_entity($entity_id);
        	    break;
        	case ENTITY_TYPE_MEMBERLOG:		//用户升级记录
        	    $entity_data = D("Common/MemberLog","Logic") -> get_info($entity_id,0,"member");
        	    break;
        	case ENTITY_TYPE_TCODE:    //兑换码
        	    $entity_data = M("tcode") -> find($entity_id);
        	    break;
        	    break;
	        default:
	            return "none_entity";
	            break;
	    }
	    return $entity_data;
	}
	
	/**
	 * 类型的定义
	 * @param string $type
	 * @return Ambigous <multitype:string , string>
	 * @author wscsky
	 */
	static function get_types($type = null){
	    $types = array(
	        ENTITY_TYPE_ORDER      => "用户订单",
	        ENTITY_TYPE_WORDER     => "配送订单",
	        ENTITY_TYPE_TCODE      => "兑换码",
	        ENTITY_TYPE_PROFIT     => "收益详情",
	        ENTITY_TYPE_WITHDRAW   => "余额提现",
	        ENTITY_TYPE_RECHARGE   => "用户充值",
	        ENTITY_TYPE_MEMBER     => "用户信息",
	        ENTITY_TYPE_ADMIN      => "管理员",
	        ENTITY_TYPE_OTHER      => "其它",
	    );
	    
	    return is_null($type) ? $types : $types[$type];
	}
}
?>