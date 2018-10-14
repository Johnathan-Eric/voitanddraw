<?php
/**
 * 管理员Model
 *
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class AdminroleModel extends RelationModel{
	
	protected $tableName = 'admin_role';		  //数据表名
	
  	//该函数会在实列化时自动执行
	function _initialize(){
                
	}
        
    
}