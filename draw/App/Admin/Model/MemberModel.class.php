<?php
/**
 * 会员模型
 * @author wscsky@qq.com
 *
 */
namespace Admin\Model;
use Think\Model;
class MemberModel extends Model{
	
	protected $tableName = 'member';    //数据表名
	
	protected $_validate 	= array();	//数据自动验证
	protected $_map         = array();  //字段映射定义
	protected $_auto		= array();	//自动填写
  	protected $_scope		= array();  //命名范围定义
	
  	//该函数会在实列化时自动执行
	function _initialize(){
		
		$this->_auto = array(
			array('reg_time','time',1,'function'),			//新增数据时自动设置注册时间
		);
		
		$this->_validate = array(
					array('openid', 'require', 'openid不能为空!', 1),
		);
		
		$this->_scope = array(
			'auth'	=> array(
					'alias'	=> "m",
					'join'	=> array("inner join ".C("DB_PREFIX")."member_auth a on a.uid = m.uid"),
					'field'	=> "m.uname,m.status,a.*",
				),
				
		);
		
	}
	
	
	
	
	
    
}