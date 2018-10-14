<?php
/*
 * 会员类：订单列表
 * @author xuebs
 * @date 2018-08-29
 */
namespace Home\Action;
use Think\Action;

class OrdersAction extends Action {
    /**
    * 接受客户端传过来的参数
    **/
    private $_ClientData = NULL;
    private $_Code = array('success' => 200, 'fail' => 402);
    private $_MemMod,$_GoodsMod,$_OrdersMod;

    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {
    	$client_data = file_get_contents("php://input");
    	if(!empty($client_data)) {
            try{
                $this->_ClientData = json_decode($client_data,TRUE);
                if(empty($this->_ClientData)) {
                    throw new \Think\Exception("Error Processing Request");
                }
            }catch(\Think\Exception $e){
                $ErrString = $e->getMessage();
                $this->ajaxReturn(array('code' => $this->_Code['fail'], 'msg' => $ErrString),'JSON');
                die();
            }
    	}
        $this->_MemMod = D('Member');
        $this->_GoodsMod = D('Goods');
        $this->_OrdersMod = D('Orders');
    }

    /**
     * 订单列表
     **/
    public function index() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 查询条件
        $where['buid'] = array('eq', 1);
        $where['uid'] = array('eq', $client_data['uid']);

        // 状态判断 1，全部；2，待付款；3，已完成；4，已取消；
        $status = isset($client_data['status']) ? $client_data['status'] : 1;
        switch ($status) {
            case '2': // 待付款订单
                $where['order_status'] = array('eq', 0);
                break;
            case '3': // 已完成订单
                $where['order_status'] = array('eq', 1);
                break;
            case '4': // 已取消订单
                $where['order_status'] = array('eq', 4);
                break;
            default: // 全部订单
                $where['order_status'] = array('in', array(0, 1, 4));
                break;
        }

        // 分页
        $page = isset($client_data['page']) && $client_data['page'] ? $client_data['page'] : 1;

        // 获取订单列表
        $fields = 'order_id,order_sn,order_status,gid,ctime,order_amount,gnum';
        $data = $this->_OrdersMod->getList($where, $fields, $page);
        if (!empty($data)) {
            $gids = array();
            foreach ($data as $value) {
                if (!in_array($value['gid'], $gids)) {
                    $gids[] = $value['gid'];
                }
            }

            // 根据商品ID获取商品信息
            $goods = $this->_GoodsMod->getList(array('gid' => array('in', $gids)), 'gid,name,thumb', 0);
            $goodsList = array();
            if (!empty($goods)) {
                foreach ($goods as $gval) {
                    $goodsList[$gval['gid']] = $gval;
                }
            }

            // 整理数据
            foreach ($data as &$val) {
                $val['gname'] = $val['thumb'] = '';
                if (isset($goodsList[$val['gid']])) {
                    $val['gname'] = $goodsList[$val['gid']]['name'];
                    $val['thumb'] = $goodsList[$val['gid']]['thumb'];
                }
                $val['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
            }
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 订单取消
     **/
    public function doCancel() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['order_id']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 查看订单信息
        $orders = $this->_OrdersMod->getInfo($client_data['order_id'], 'order_status');
        if (empty($orders)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '订单不存在！'));
            return;
        }

        // 执行保存
        $res = $this->_OrdersMod->saveData(array('order_id' => array('eq', $client_data['order_id'])), array('order_status' => 4));
        if ($res) {
            $return = array('code' => 200, 'msg' => '取消成功');
        } else {
            $return = array('code' => 203, 'msg' => '取消失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 删除取消
     **/
    public function doDel() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['order_id']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 查看订单信息
        $orders = $this->_OrdersMod->getInfo($client_data['order_id'], 'order_status');
        if (empty($orders)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '订单不存在！'));
            return;
        }

        // 执行保存
        $res = $this->_OrdersMod->saveData(array('order_id' => array('eq', $client_data['order_id'])), array('order_status' => 2));
        if ($res) {
            $return = array('code' => 200, 'msg' => '删除成功');
        } else {
            $return = array('code' => 203, 'msg' => '删除失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }
}