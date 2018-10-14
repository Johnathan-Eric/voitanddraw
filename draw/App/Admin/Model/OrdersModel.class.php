<?php
/**
 * 管理员Model
 *
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class OrdersModel extends RelationModel{
	
    protected $tableName = 'orders';		  //数据表名

    protected $_validate 	= array();	//数据自动验证

    //该函数会在实列化时自动执行
    function _initialize(){

        $this->_validate = array(
            array('order_sn', '', '订单编号已存在', 2,'unique')
        );
        
    }
    
    public function getLists($where = array(), $p = 1, $listRows = 10,$order='ctime desc')
    {
        $total = $this->where($where)->count();
        if ($total == 0) {
            return array(
                'total' => 0,
                'lists' => array()
            );
        }
        $lists = $this->where($where)->page($p,$listRows)->order($order)->select();
        return array(
            'total' => $total,
            'lists' => $lists
        );
    }
    
    public function getOrderGoods($where = array())
    {
        $list = $this -> alias("o")
                -> join("left join ".C("DB_PREFIX").'orders_book og on o.order_id = og.order_id')
                -> where($where)
                -> field("og.*,o.order_sn,o.order_status,o.pay_status,o.payment_name,o.ctime,o.goods_amount,o.mobile")
                -> order(" o.order_id desc")
                ->select();
        return $list;
    }
    
    public function getOrderSumGoods($where = array())
    {
        $list = $this -> alias("o")
                -> join("left join ".C("DB_PREFIX").'orders_goods og on o.order_id = og.order_id '
                        . "left join ".C("DB_PREFIX").'goods_spec os on og.spec_id = os.spec_id')
                -> where($where)
                -> field("og.*,o.*,os.SUKnum,sum(og.number) as totalNum")
                -> order(" o.order_id desc")
                -> group(" og.spec_id")
                ->select();
        return $list;
    }
    
    public function getOrderSumAmount()
    {
        
    }
    
}