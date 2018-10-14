<?php
/**
 * 用户组模型
 * @author wscsky@qq.com
 *
 */
namespace Admin\Model;
use Think\Model;
class AdminGroupModel extends Model{
	
	protected $tableName = 'admin_group';		  //数据表名
	
	protected $_validate 	= array();	//数据自动验证
	protected $_map         = array();  //字段映射定义
	protected $_auto		= array();	//自动填写
  	protected $_scope		= array();  // 命名范围定义
	
  	//该函数会在实列化时自动执行
	function _initialize(){
		
		$this->_auto = array(
			array('add_time','time',1,'function'),	//添加时自动设置添加时间
		);
		
	}
	
	
	
    
}