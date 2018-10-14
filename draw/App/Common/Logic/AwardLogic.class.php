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

		// 获取列表
		$fields = 'id,thumb,pronum,online,listorder,stime,etime,actid,stock,name,atype,winnum';
		if ($page < 0) {
            $return = M('award')->where($where)->field($fields)->order($order)->select();
        } else {
            // 获取总数
            $return['total'] = M('award')->where($where)->count();

            // 列表
            $return['list'] = M('award')->where($where)->field($fields)->page($page, $listRows)->order($order)->select();

        }
		return $return;
	}
}
?>