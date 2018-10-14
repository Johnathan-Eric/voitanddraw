<?php
/**
 * 管理员Model
 *
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class AdminModel extends RelationModel{
	
	protected $tableName = 'admin';		  //数据表名
	
	protected $_validate 	= array();	//数据自动验证
	protected $_map         = array();  // 字段映射定义
  	protected $_scope		= array();  // 命名范围定义
	
  	//该函数会在实列化时自动执行
	function _initialize(){
		
            $this -> _validate = array(
                array('username', 'require', '用户名不能为空!', 1, '', 1),
                array('username', '/[^(^(-?\d+)(\.\d+)?$)]/', '用户名不能为纯数字(整数、浮点数)!', 2, 'regex'),
                array('username', '','该帐户已经存在！',2,'unique',1),
                array('password', 'require', '登录密码不能为空!', 1, '', 1),
                array('password', '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/', '登录密码格式不正确(数字、字母、特殊字符的任意组合，长度为6至22个字符串)！', 2, 'regex', 1),
            );

            $this -> _scope['login'] = array(
                    'alias' => 'm',
                    'join'  => array('left join '. C("DB_PREFIX").'admin_role r on m.rid = r.rid'),
                    );

            $this -> _auto = array(
                array('add_time', 'time', 1, 'function'), //添加时自动设置添加时间
            );
            
	}
        
    
}