<?php
/*
 * 奖品管理类
 * @author eva
 *
 */
namespace Admin\Action;
use Think\AdminBaseAction;

class AddressAction extends AdminBaseAction {
	protected $admin,$listRows,$statusList;

	/**
	 * 构建函数
	 **/
	public function _initialize() {
        // 管理员信息
        $this->admin = session('admin');
		$this->listRows = 10; // 每页显示条数
        $this->statusList = C('deliverType');
	}

	/**
	 * 邮寄地址列表
	 **/
	public function addr_index() {
		$request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $where 		= array();
        if ($request['status']) { // 活动ID
            $status = $request['status'] - 1;
            $where['status'] = array('eq', $status);
        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) {
        	$where['name'] = array('like', '%'.$keyword.'%');
        }

        // 获取总数
        $total = M('Address')->where($where)->count();

        // 获取列表
        $fields = 'id,uid,name,address,postcode,tel,addtime,status';
        $data = M('Address')->where($where)->page($page, $this->listRows)->field($fields)->select();
        if ($data) {
            $uids = array();
            foreach ($data as $vd) {
                if (!in_array($vd['uid'], $uids)) {
                    $uids[] = $vd['uid'];
                }
            }

            // 获取用户信息
            $users = M('Voteuser')->where(array('id' => array('in', $uids)))->field('id,nickname')->select();
            $uList = array();
            if ($users) {
                foreach ($users as $uval) {
                    $uList[$uval['id']] = $uval['nickname'];
                }
            }

            // 整理数据
            foreach ($data as &$dval) {
                $dval['uname'] = '';
                if (isset($uList[$dval['uid']])) {
                    $dval['uname'] = $uList[$dval['uid']];
                }
            }
        }


        // 返回数据
        $this->assign('statusList', $this->statusList);
        $this->assign('lists', $data);
        $this->assign('_curr', $page);
        $this->assign('_total', $total);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '地址列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->display('addr_index');
    }

    /**
     * 编辑地址
     */
    public function addr_edit() {
        $request = I("request.");

        // 根据参数获取地址信息
        $id = $request['id'];
        if (!$id) {
            $return = array(
                'status' => 0,
                'msg'   => '参数错误！'
            );
            echo json_encode($return);die();
        } else {
            $info = M('Address')->where(array('id' => array('eq', $id)))->find();
        }

        // 保存数据
        if (IS_POST) {
            $post = I('post.');
            $is_succ = M('Address')->where(array('id' => array('eq', $post['id'])))->save($post);
            if($is_succ){
                $return = array(
                    'status' => 1,
                    'msg'   => '操作成功'
                );
            } else {
                $return = array(
                    'status' => 2,
                    'msg'   => '操作失败'
                );
            }
            echo json_encode($return);die();
        }

        // 用户信息
        $user = M('Voteuser')->where(array('id' => array('eq', $info['uid'])))->field('nickname')->find();
        $info['uname'] = $user['nickname'];

        // 返回信息
        $this->assign('info', $info);
        $this->assign('page_title', '地址列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->display('addr_edit');
    }

    /**
     * ajax批量操作会员状态
     */
    public function ajax_update(){
        $ids = I('ids');
        if(count($ids) > 0){
            $val   = I('nval','1','int');
            $field = I('ntype','status','trim');
            $data = array($field=>$val);
            $Goods = M('Address');
            $Goods->where(array('id'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }
}