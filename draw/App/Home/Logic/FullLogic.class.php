<?php
//满减处理逻辑模块
namespace Home\Logic;
class FullLogic{

	function _initialize()
	{

	}

	/**
	 * 依据商品id获取满减信息
	 */
	function get_book_full($book_id = 0)
	{
		if (!$book_id) {
			return false;
		}
		$fid = M('FullGoods')->field('fid')->where(array('book_id'=>$book_id, 'is_del'=>0))->find();
		if (!$fid) 
		{
			$fullInfo = M('Full')->where(array('status'=>1, 'is_all_books'=>1,'is_del'=>0))->order('create_time desc')->find();
		}
		else
		{
			$fullInfo = M('Full')->where(array('id'=>$fid['fid'], 'status'=>1, 'is_del'=>0))->find();
		}
		if (!$fullInfo) 
		{
			return false;
		}
		$rdList = M('FullReduce')->where(array('fid'=>$fullInfo['id'], 'is_del'=>0))->order('reduce asc')->select();
		if (!$rdList) 
		{
			return false;
		}
		$return = array();
		foreach ($rdList as $key => $value) 
		{
			if ($value['full'] == (int)$value['full']) 
			{
				$value['full'] = (int)$value['full'];
			}
			if ($value['reduce'] == (int)$value['reduce']) 
			{
				$value['reduce'] = (int)$value['reduce'];
			}
			$detail = '满'.$value['full'].'减'.$value['reduce'];
			$return[] = array(
				'fid' => $value['fid'],
				'sort' => $value['sort'],
				'full' => $value['full'],
				'reduce' => $value['reduce'],
				'detail' => $detail
			);
		}
		return $return;
	}

	/**
	 * 依据订单id 获取可用层级 推荐可用层级
	 */
	function get_order_full($order_id=0)
	{
		if (!$order_id) 
		{
			return false;
		}
		$order_books = M('OrdersBook')->where(array('order_id'=>$order_id))->select();
		if (!$order_books) 
		{
			return false;
		}
		//书籍id下标的数量和价格
		$book_pn = array();
		//拿到book_id下标的优惠信息
		$book_fid = array();
		foreach ($order_books as $key => $value) 
		{
			$book_pn[$value['book_id']]['price'] = $value['price'];
			$book_pn[$value['book_id']]['number'] = $value['number'];
			$rdInfo = $this->get_book_full($value['book_id']);
			if ($rdInfo) 
			{
				$book_fid[$value['book_id']] = $rdInfo[0]['fid'];
			}
		}
		//拿到fid下标的钱
		$fid_money = array();
		foreach ($book_fid as $key => $value) 
		{	
			//算钱
			$money = $fid_money[$value] ? $fid_money[$value] : 0;
			$money += $book_pn[$key]['price']*$book_pn[$key]['number'];
			$fid_money[$value] = $money;
		}

		//遍历信息 拿到最高优惠金额的优惠
		//拿到最优优惠信息id
		$fid = 0;
		//层级表id
		$frid = 0;
		//当前的钱
		$now_money = 0;
		//当前层级
		$now_k = -1;
		//达到的门槛
		$now_full = 0;
		//优惠金额
		$now_reduce = 0;
		//下一个门槛
		$next_full = 0;
		//下一个优惠金额
		$next_reduce = 0;
		foreach ($fid_money as $key => $value) 
		{
			$rdInfo = $this->get_full_reduce($key);
			foreach ($rdInfo as $k => $v) 
			{
				if ($value >= $v['full']) 
				{
					if ($v['reduce'] >= $reduce) 
					{
						$fid = $v['fid'];
						$frid = $v['id'];
						$now_money = $value;
						$now_k = $k;
						$now_full = $v['full'];
						$now_reduce = $v['reduce'];
					}
				}
				else
				{
					if ($k-1 == $now_k) 
					{
						$next_full = $v['full'];
						$next_reduce = $v['reduce'];
					}
					
				}
			}
		}
		if ($now_full == (int)$now_full) 
		{
			$now_full = (int)$now_full;
		}
		if ($now_reduce == (int)$now_reduce) 
		{
			$now_reduce = (int)$now_reduce;
		}
		return array(
			'fid' => $fid,
			'frid' => $frid,
			'now_money' => $now_money,
			'now_full' => $now_full,
			'now_reduce' => $now_reduce,
			'now_short' => $next_full-$now_money,
			'next_full' => $next_full,
			'next_reduce' => $next_reduce,
			'full_info'=>$rdInfo
			);

	}

	/**
	 * 获取满减层级
	 */
	private function get_full_reduce($fid)
	{
		return M('FullReduce')->where(array('fid'=>$fid, 'is_del'=>0))->order('reduce asc')->select();
	}

	/**
	 * 支付完成回调满减日志
	 */
	function set_full_log($order_id)
	{
		$orderInfo = M('Orders')->find($order_id);
		$fullReduce = M('FullReduce')->find($orderInfo['full_red_id']);
		if ($fullReduce) 
		{
			$addData = array(
				'fid' => $fullReduce['fid'],
				'frid' => $orderInfo['full_red_id'],
				'reduce' => $fullReduce['reduce'],
				'order_id' => $order_id,
				'order_pay' => $orderInfo['order_amount'],
				'uid' => $orderInfo['uid'],
				'create_time' => time()
				);
			M('fullLog')->add($addData);
		}
	}


}