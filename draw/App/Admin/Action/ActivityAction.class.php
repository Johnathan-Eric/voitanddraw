<?php
/*
 * 奖品管理类
 * @author eva
 *
 */
namespace Admin\Action;
use Think\AdminBaseAction;

class ActivityAction extends AdminBaseAction {
	protected $admin,$listRows;

	/**
	 * 构建函数
	 **/
	public function _initialize() {
        // 管理员信息
        $this->admin = session('admin');
		$this->listRows = 2; // 每页显示条数
	}


	/**
	 * 活动列表
	 **/
	public function act_index() {
		$request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $where 		= array();
        if ($request['uniacid']) { // 活动ID
            $where['uniacid'] = array('eq', $request['uniacid']);
        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) {
        	$where['title'] = array('like', '%'.$keyword.'%');
        }

        // 获取总数
        $total = M('Reply')->where($where)->count();

        // 获取列表数据
        $fields = 'rid,title,starttime,endtime,apstarttime,apendtime,votestarttime,voteendtime,pervote';
        $list = M('Reply')->where($where)->page($page, $this->listRows)->field($fields)->order('id desc')->select();

        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');

		// 返回页面数据
        $this->assign('uniList', $uniList);
		$this->assign('lists', $list);
        $this->assign('_curr', $page);
        $this->assign('_total', $total);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '活动列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->display('act_index');
	}

    /**
     * ajax批量操作会员状态
     */
    public function ajax_update(){
        $ids = I('ids');
        if(count($ids) > 0){
            $val   = I('nval','1','int');
            $field = I('ntype','pervote','trim');
            $data = array($field=>$val);
            $Goods = M('Reply');
            $Goods->where(array('rid'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }
}