<?php
/**
 * Model 类 操作数据表
 * Users表是跨数据库biao
 * @author Jeffreyzhu.cn@gmail.com
 *
 */
namespace Admin\Model;
use Think\Model;
class UserModel extends Model{
	
	protected $tableName = 'member';		  //数据表名
	
	protected $_validate 	= array();	//数据自动验证
	protected $_map         = array();  // 字段映射定义
  	protected $_scope		= array();  // 命名范围定义
	
  	//该函数会在实列化时自动执行
	function _initialize(){
		
		$this -> _scope['login'] = array(
			'alias' => 'm',
			'join'	=> array('left join '. C("DB_PREFIX").'member_staff s on s.uid = m.uid'),
			);
		
	}
	
    
}