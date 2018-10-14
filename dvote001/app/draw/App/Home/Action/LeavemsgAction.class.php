<?php
/*
 * 留言类
 * @author xuebs
 * @date 2018-07-26
 */
namespace Home\Action;
use Think\Action;

class LeavemsgAction extends Action {
    /**
    * 接受客户端传过来的参数
    **/
    private $_ClientData = NULL;
    private $_Code = array('success' => 200, 'fail' => 402, 'empty' => 401);
    private $_pageSize = 10; // 每页10条数据

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
    }

    /**
     * 留言内容列表
     **/
    public function index() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['ds_id'] || !$client_data['uid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 分页数（第几页）
        // $page = $client_data['page'] ? $client_data['page'] : 1;

        // 根据商户ID获取留言信息和回复信息
        $where['ds_id'] = array('eq', $client_data['ds_id']);
        $where['uid'] = array('eq', $client_data['uid']);
        $fields = 'headimg,umsg,rmsg';
        $data = M('LeaveMsg')->where($where)->field($fields)->order('raddtime asc,uaddtime asc')->select();
        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
            return;
    }

    /**
     * 留言
     **/
    public function add() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['ds_id'] || !$client_data['uid'] || !$client_data['msg']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 根据uid获取信息
        $user = M('Member')->where(array('uid' => array('eq', $client_data['uid'])))->field('uname,headimg')->find();
        if (empty($user)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户不存在！'));
            return;
        }

        // 保存留言信息
        $add['ds_id'] = $client_data['ds_id'];
        $add['uid'] = $client_data['uid'];
        $add['uname'] = $user['uname'];
        $add['headimg'] = $user['headimg'];
        $add['umsg'] = $client_data['msg'];
        $add['uaddtime'] = time();
        // 执行保存
        $is_succ = M('LeaveMsg')->add($add);
        if ($is_succ) {
            $this->ajaxReturn(array('code' => 200, 'msg' => '提交成功！'));
            return;
        } else {
            $this->ajaxReturn(array('code' => 203, 'msg' => '提交失败！'));
            return;
        }
    }
}