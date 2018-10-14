<?php
/*
 * 奖品管理逻辑模块
 * @autor eva
 */
namespace Common\Logic;
class AwardLogic {
	protected $_error=null;

	/**
	 * 获取列表
	 * @param array $where 查询条件
	 **/
	public function searchData($where, $page = 1, $listRows = 10, $order = 'id desc') {
		// 获取总数
		$total = M('award')->where($where)->count();

		// 获取列表
		$list = M('award')->where($where)->page($page, $listRows)->order($order)->select();
		return array('total' => $total, 'list' => $list);
	}
}
?>