<?php
/**
 * 订单模型
 * @author wscsky
 *
 */
namespace Common\Model;
use Think\Model\RelationModel;
class OrdersModel extends RelationModel{
	
	protected $tableName 	= 'orders';		//数据表名	
	protected $_validate 	= array();		//数据自动验证
	protected $_map         = array();  	//字段映射定义
	protected $_scope		= array();  	//命名范围定义
	protected $_auto		= array();		//自动填写
	protected $_link		= array();		//业务关联关系
	
	//该函数会在实列化时自动执行
	function _initialize(){
		$this->_link = array(
			'Member'	=> array(		//一个订单属于一个用户
					'mapping_type'   => self::BELONGS_TO,
					'foreign_key'    => 'uid',
					'class_name'	 => 'Member',
					'mapping_name'   => 'member',
			),
			'OrdersGoods' => array(		//一个订单商品表
					'mapping_type'	 => self::HAS_MANY,
					'foreign_key'	 => 'order_id',
					'class_name'	 => 'OrdersGoods',
					'mapping_name'	 => 'ordersgoods',
			),
			'OrdersShipping' => array(	//一个订单有多个配送单
					'mapping_type'   => self::HAS_MANY,
					'foreign_key'    => 'order_id',
					'class_name'	 => 'OrdersShipping',
					'mapping_name'   => 'ordershipping',
			        'mapping_order'  => "no asc",
			),
		);
	}
    
}