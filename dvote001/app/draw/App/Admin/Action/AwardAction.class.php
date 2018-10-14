<?php
/*
 * 奖品管理类
 * @author eva
 *
 */
namespace Admin\Action;
use Think\AdminBaseAction;

class AwardAction extends AdminBaseAction {
	protected $admin,$listRows,$model;

	/**
	 * 构建函数
	 **/
	public function _initialize() {
        // 管理员信息
        $this->admin = session('admin');

		$this->listRows = 10; // 每页显示条数
		$this->model = D("Common/Award", "Logic");
	}

	/**
	 * 奖品列表
	 **/
	public function index() {
        $request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $where 		= array();
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) {
        	$where['name'] = array('like', '%'.$keyword.'%');
        }

        // 全部/上架/未上架
        $request['search'] = empty($request['search']) ? 1 : $request['search'];
        switch ($request['search']) {
            case '1': // 全部奖品
                $count1 = M('Award')->where(array('online' => array('eq', 1)))->count();
                $count2 = M('Award')->where(array('online' => array('eq', 2)))->count();
                break;
            case '2': // 已上架
                $count0 = M('Award')->count();
                $count2 = M('Award')->where(array('online' => array('eq', 2)))->count();
                $where['online'] = array('eq', 1);
                break;
            case '3': // 未上架
                $count0 = M('Award')->count();
                $count1 = M('Award')->where(array('online' => array('eq', 1)))->count();
                $where['online'] = array('eq', 2);
                break;
        }

        // 获取列表
        $data = $this->model->searchData($where, $page, $this->listRows);
        switch ($request['search']) {
            case '1': // 全部奖品
                $count0 = $data['total'];
                break;
            case '2': // 已上架
                $count1 = $data['total'];
                break;
            case '3': // 未上架
                $count2 = $data['total'];
                break;
        }

        // 返回数据
        $this->assign('lists', $data['list']);
        $this->assign('_curr', $page);
        $this->assign('_total', $data['total']);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '奖品列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->assign('count0', $count0);
        $this->assign('count1', $count1);
        $this->assign('count2', $count2);
        $this->display('index');
	}

	/**
	 * 奖品添加
	 **/
	public function awAdd() {
        // 奖品ID
        $gid = I("id",0,'intval');
        if ($gid) {
            // 获取奖品信息
            $info = M('Award')->where(array('id' => array('eq', $gid)))->find();
            if ($info['stime']) {
                $info['stime'] = date('Y-m-d H:i:s', $info['stime']);
            } else {
                $info['stime'] = '';
            }
            if ($info['etime']) {
                $info['etime'] = date('Y-m-d H:i:s', $info['etime']);
            } else {
                $info['etime'] = '';
            }
            $this->assign('info', $info);
        }

        // 保存数据
        if (IS_POST) {
            $post = I('post.');
            if ($post['stime']) {
                $post['stime'] = strtotime($post['stime']);
            }
            if ($post['etime']) {
                $post['etime'] = strtotime($post['etime']);
            }
            if ($post['id']) { // 更新
                $post['uptime'] = time();
                $is_succ = M('Award')->where(array('id' => array('eq', $post['id'])))->save($post);
            } else {
                $post['addtime'] = time();
                $is_succ = M('Award')->add($post);
            }
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

		// 返回数据
		$this->display('add');
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
            $Goods = M('Award');
            $Goods->where(array('id'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }

    /**
     * 删除
     **/
    public function del() {
        $ids  = I('ids','','trim');
        if(empty($ids)) {
            $result = false;
        } else {
            $result = M('Award')->where(array('id' => array('in', $ids)))->delete();
        }
        echo json_encode(array('state'=>0,'message'=>'删除成功'));die;
    }
}

?>