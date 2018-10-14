<?php
/**
 * 奖品模型
 * @author eva
 *
 */
namespace Common\Model;
use Think\Model\RelationModel;
class AwardModel extends RelationModel{
	
	protected $tableName 	= 'award';		//数据表名	
	protected $_validate 	= array();		//数据自动验证
	protected $_map         = array();  	//字段映射定义
	protected $_scope		= array();  	//命名范围定义
	protected $_auto		= array();		//自动填写
	protected $_link		= array();		//业务关联关系
	
	//该函数会在实列化时自动执行
	function _initialize(){
	
		$this->_auto = array(
			array('addtime','time',1,'function'),			//添加时间
		);
		
		$this->_validate = array(
				
		);
		
	}
    
}