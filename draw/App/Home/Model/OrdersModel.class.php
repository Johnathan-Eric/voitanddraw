<?php
/**
 * 用户订单模型
 * @author watchman
 */
namespace Home\Model;
use Think\Model\RelationModel;
class OrdersModel extends RelationModel{
	/**
	 * 生成订单
	 * @param array $data 订单数据
	 * @return int $order_id 订单ID
	 **/
	public function addData($data) {
		if (empty($data)) {
			return false;
		}
		return M('Orders')->add($data);
	}

	/**
	 * 保存数据
	 * @param array $where 条件
	 * @param array $data 保存数据
	 * @return int true/false
	 **/
	public function saveData($where, $data) {
		if (empty($where) || empty($data)) {
			return false;
		}
		return M('Orders')->where($where)->save($data);
	}

	/**
	 * 获取订单信息
	 * @param int $order_id 订单id
	 * @param string $fields 查询字段
	 * @return array $orders 订单信息
	 **/
	public function getInfo($order_id, $fields = '*') {
		if (!$order_id) {
			return false;
		}
		return M('Orders')->where(array('order_id' => array('eq', $order_id)))->field($fields)->find();
	}

	/**
	 * 获取订单信息
	 * @param int $order_id 订单id
	 * @param string $fields 查询字段
	 * @return array $orders 订单信息
	 **/
	public function getInfoByOrdersn($ordersn, $fields = '*') {
		if (!$ordersn) {
			return false;
		}
		return M('Orders')->where(array('order_sn' => array('eq', $ordersn)))->field($fields)->find();
	}

	/**
	 * 获取列表
	 * @param array $where 查询条件
	 * @param string $fields 查询字段
	 * @param int $page 第几页
	 * @param int $listRows 每页多少条
	 * @param string $order 排序
	 * @return array $list 订单列表
	 **/
	public function getList($where, $fields = '*', $page = 1, $listRows = 10, $order = 'order_id desc') {
		return M('Orders')->where($where)->field($fields)->page($page, $listRows)->order($order)->select();
	}

}