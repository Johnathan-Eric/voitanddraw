<?php
/**
 * 会员模型
 * @author wscsky
 *
 */
namespace Common\Model;
use Think\Model\RelationModel;
class MemberModel extends RelationModel{
	
	protected $tableName 	= 'member';		//数据表名	
	protected $_validate 	= array();		//数据自动验证
	protected $_map         = array();  	//字段映射定义
	protected $_scope		= array();  	//命名范围定义
	protected $_auto		= array();		//自动填写
	protected $_link		= array();		//业务关联关系
	
	//该函数会在实列化时自动执行
	function _initialize(){
	
		$this->_auto = array(
		       array('reg_time','time',1,'function'),  //注册时间
		);
		
		$this->_validate = array(
				
		);
		
		$this->_link = array(
			'Group'		=> array(		//一个用户属于一个用户组
					'mapping_type'   => self::BELONGS_TO,
					'foreign_key'    => 'gid',
					'class_name'	 => 'Group',
					'mapping_name'   => 'group',
			),
			'Auth'      => array(                  //一个用户有一个认证信息
			    'mapping_type'   => self::HAS_ONE,
			    'foreign_key'    => 'uid',
				'class_name'	 => 'MemberAuth',
			    'mapping_name'   => 'auth',
		      ),
		    'Finance'  => array(                   	//一个用户有一条财务信息
		        'mapping_type'   => self::HAS_ONE,
		        'foreign_key'    => 'uid',
		    	'class_name'	 => 'Finance',
		        'mapping_name'   => 'finance',
		       ),
			'Bank'	=> array(						//一个用户有多个银行卡信息
				'mapping_type'   => self::HAS_MANY,
		        'foreign_key'    => 'uid',
		    	'class_name'	 => 'MemberBank',
		        'mapping_name'   => 'memberbank',
		       ),
			
				
		);
		
		$this->_scope = array(
				'auth'	=> array(
						'alias'	=> "m",
						'join'	=> array("inner join ".C("DB_PREFIX")."member_auth a on a.uid = m.uid"),
						'field'	=> "m.uname,m.status,a.*",
				),
		       "login"    => array(
					   'alias'   => 'm',
		               'join'    => array('left join '.C('DB_PREFIX').'group g on m.gid = g.gid',
		       							  'left join '.C('DB_PREFIX').'member_finance mf on mf.uid = m.uid'),
		               'field'   => 'm.uid,m.openid,m.uname,m.lat,m.lng,m.loctime,m.last_store_id,m.nickname,m.bind ,m.realname ,m.consignee,m.sex,m.mobile,m.birthday,m.address, m.subscribe_time,subscribe_msg,m.status,m.master,m.referer,m.puid,m.headimg,g.gid, g.name as gname,g.discount,mf.*', 
				),
		
		);
	}
    
}