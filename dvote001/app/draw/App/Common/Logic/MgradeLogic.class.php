<?php
namespace Common\Logic;
class MgradeLogic {
	/**
	 * 获取会员等级列表
	 * @param array $where 查询条件
	 * @param int $page 第几页
	 * @param int $perPage 每页显示的条数
	 * @return array $data 返回的数据列表
	 **/
	public function getList($where = array(), $page = 1, $perPage = 10) {
		// 获取总数
		$total = M('MemberGrade')->where($where)->count();

		// 获取数据列表
		$data = M('MemberGrade')->where($where)->page($page, $perPage)->order('id desc')->select();

		// 返回数据
		$return['total'] = $total;
		$return['list'] = $data;
		return $return;
	}
}