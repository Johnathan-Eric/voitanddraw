<?php
//实体类型查看,常用于AJAX查询
namespace Admin\Action;
use Think\BaseAction;

class EntityAction extends BaseAction{
	
	protected $model;
	protected $tpl;
	
	function _initialize(){
	}
	
	//权限设置
	function accessRules(){
		return array(
			
		);
	}
	/**
	 * 主要用于ajax显示信息
	 * @author wscsky
	 */
	function info(){
		$entity_type = I("type");
		$entity_id	 = I("id");
		if(IS_AJAX){
			C("LAYOUT_ON",false);
		}
		if(!$entity_id || !$entity_type){
			printf(L("ERRDIV"),"查看失败,参数有误!");
			exit();		
		}
		$data = get_entity($entity_id, $entity_type);
		if($data == "none_entity"){
			printf(L("ERRDIV"),"参数有误,无该种实体类型:{$entity_type}!");
			exit();
		}
		if(!$data){
			printf(L("ERRDIV"),"数据未找到,有可能已删除!");
			exit();
		}
		$this->assign($data);
		$this->assign("data", $data);
		$tpl = "entity_{$entity_type}";
		if(!$this->_check_tpl($tpl)){
		    printf(L("ERRDIV"),"{$tpl}模版不存在");
		    exit();
		}
		$this->display($tpl);			
	}
	
}