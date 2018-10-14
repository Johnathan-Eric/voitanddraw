<?php
//用户配置信息逻辑代码
namespace Common\Logic;
class UcfgLogic{
    
    /**
     * 读取配置信息
     * @param string $pos
     * @return string
     * @author wscsky
     */
    function read($cfg_name=null,$cfg_type = "msg",$uid = null){
    	$member = session('member');
    	if(is_null($uid)) $uid = $member->uid;
    	static $uconfig = array();
    	if(!$uconfig[$uid][$cfg_type]){
    		$map   = array("uid"=>$uid, "cfg_type"=>$cfg_type);
    		$model = M("member_config");
    		$list  = $model -> where($map)->select();
    		foreach ($list as $cfg){
    			$uconfig[$uid][$cfg_type][$cfg['cfg_name']]=$cfg['cfg_val'];
    		}
    	}
    	if(is_null($cfg_name)) return $uconfig[$uid][$cfg_type];
    	return $uconfig[$uid][$cfg_type][$cfg_name];
    }
    
    /*
     * 保存 的设置信息
     */
    function save($cfg_name, $cfg_val,$cfg_type = "msg", $uid=null){
    	$member = session('member');
    	$uid = is_null($uid) ? $member->uid : $uid;
    	if($uid==0) return false;
    	$model = M("member_config");
    	$map = array("uid"=>$uid, "cfg_type"=>$cfg_type,"cfg_name"=>$cfg_name);
    	if($model -> where($map)->find()){
    		$result = $model -> where($map)->save(array("cfg_val"=>$cfg_val));
    	}else{
    		$map['cfg_val'] = $cfg_val;
    		$result = $model ->add($map);
    	}
    	return $result === false ? false : true;
	    }
    
    /**
     * 配置信息列表
     */
    function get_msg_cfg(){
    	$cfgs = array(
    		array(
    			'tag'  => "我的订单通知",
    			'cfgs' => array(	
						'm_neworder_notice'	=> "新订单通知",
		    			'm_payorder_notice'	=> '订单支付成功通知',
		    			'm_order_shipped'	=> "订单发货通知",
    			)),
    		array(
    			'tag'  => "分销信息通知",
    			'cfgs' => array(
		    			'm_profit_goods'	=> "推荐商品提成通知",
		    			'm_profit_notice' 	=> "订单分销提成通知",
		    			'my_newmember_notice'=> "有新闺蜜好友通知",
    			))
    	);
    	
    	return $cfgs;
    }
		
}


