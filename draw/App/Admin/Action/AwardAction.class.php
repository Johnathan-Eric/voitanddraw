<?php
/*
 * 奖品管理类
 * @author eva
 *
 */
namespace Admin\Action;
use Think\AdminBaseAction;

class AwardAction extends AdminBaseAction {
	protected $admin,$listRows,$model,$atypes;

	/**
	 * 构建函数
	 **/
	public function _initialize() {
        // 管理员信息
        $this->admin = session('admin');

		$this->listRows = 9; // 每页显示条数
		$this->model = D("Common/Award", "Logic");
        $this->atypes = C('awardType');
	}

	/**
	 * 奖品列表
	 **/
	public function index() {
        $request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 获取活动列表
        $actList = M('Reply')->field('rid,title')->select();
        $actData = array();
        if ($actList) {
            foreach ($actList as $aval) {
                $actData[$aval['rid']] = $aval['title'];
            }
        }

        // 查询条件
        $where 		= array();
        if ($request['actid']) { // 活动ID
            $where['actid'] = array('eq', $request['actid']);
        }
        if ($request['uniacid']) { // 公众号ID
            $where['uniacid'] = array('eq', $request['uniacid']);
        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) { // 搜索关键字
        	$where['name'] = array('like', '%'.$keyword.'%');
        }

        // 全部/上架/未上架
        $request['search'] = empty($request['search']) ? 1 : $request['search'];
        switch ($request['search']) {
            case '1': // 全部奖品
                if ($request['actid']) { // 活动ID
                    $c1Where['actid'] = array('eq', $request['actid']);
                }
                if ($request['uniacid']) { // 公众号ID
                    $c1Where['uniacid'] = array('eq', $request['uniacid']);
                }
                $c1Where['online'] = array('eq', 1);
                $count1 = M('Award')->where($c1Where)->count();
                $c2where = $c1Where;
                $c2where['online'] = array('eq', 2);
                $count2 = M('Award')->where($c2where)->count();
                break;
            case '2': // 已上架
                if ($request['actid'] || $request['uniacid']) { // 活动ID
                    if ($request['actid']) {
                        $cWhere['actid'] = array('eq', $request['actid']);
                    }
                    if ($request['uniacid']) {
                        $cWhere['uniacid'] = array('eq', $request['uniacid']);
                    }
                    $count0 = M('Award')->where($cWhere)->count();
                } else {
                    $count0 = M('Award')->count();
                }
                $cWhere['online'] = array('eq', 2);
                $count2 = M('Award')->where($cWhere)->count();
                $where['online'] = array('eq', 1);
                break;
            case '3': // 未上架
                if ($request['actid'] || $request['uniacid']) { // 活动ID
                    if ($request['actid']) {
                        $cWhere['actid'] = array('eq', $request['actid']);
                    }
                    if ($request['uniacid']) {
                        $cWhere['uniacid'] = array('eq', $request['uniacid']);
                    }
                    $count0 = M('Award')->where($cWhere)->count();
                } else {
                    $count0 = M('Award')->count();
                }
                $cWhere['online'] = array('eq', 1);
                $count1 = M('Award')->where($cWhere)->count();
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

        // 查找默认出奖率
        $tpnum = 0;
        if ($request['actid']) { // 活动ID
            // $defData = M('Daward')->where(array('actid' => array('eq', $request['actid'])))->field('num')->find();
            // $tpnum = (9 - $data['total']) * $defData['num'];
            $xxPnum = $xxNum = 0;
            if ($data['list']) {
                foreach ($data['list'] as &$value) {
                    // 谢谢关注的出奖率
                    if ($value['atype'] == 2) {
                        $xxPnum = $value['pronum'];
                        $xxNum += 1;
                    } else {
                        $tpnum += $value['pronum'];
                    }
                }

                // 计算总抽奖数
                if ($xxPnum) {
                    $tpnum += (9 - $data['total'] + $xxNum) * $xxPnum;
                }
            }
        }
        if ($data['list']) {
            foreach ($data['list'] as &$value) {
                $value['atype'] = $this->atypes[$value['atype']];
                // 活动标题
                $value['title'] = '';
                if (isset($actData[$value['actid']])) {
                    $value['title'] = $actData[$value['actid']];
                }
            }
        }

        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');

        // 返回数据
        $this->assign('uniList', $uniList);
        $this->assign('actList', $actList);
        $this->assign('tpnum', $tpnum);
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
        $request = I("request.");
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
        } else {
            $this->assign('info', array('actid' => $request['actid']));
        }

        // 获取活动列表
        $actList = M('Reply')->field('rid,title')->select();
        $this->assign('actList', $actList);

        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');
        $this->assign('uniList', $uniList);

        // 奖品类型
        $this->assign('atypes', $this->atypes);

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
     * 保存默认中奖率
     */
    public function saveData(){
        $defNum = I('defNum');
        $actid = I('actid');
        if($defNum && $actid){
            // 查询是否存在谢谢关注出奖率信息
            $dinfo = M('Daward')->where(array('actid'=>array('eq', $actid)))->find();
            if ($dinfo) {
                M('Daward')->where(array('actid'=>array('eq', $actid)))->save(array('num' => $defNum));
            } else {
                M('Daward')->add(array('num' => $defNum, 'actid' => $actid));
            }
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

    /**
     * 计算出奖概率
     **/
    public function proRand() {
        // 接收参数
        $request = I("request.");
        if (!$request['actid']) {
            $date = array("error"=>1,"msg"=>'活动ID参数错误');
            echo json_encode($date);die;
        }

        // 查询数据
        $where['actid'] = array('eq', $request['actid']);
        $where['online'] = array('eq', 1);
        $where['stock'] = array('gt', 0);
        $fields = 'id,name,stock,pronum,atype';
        $order = 'listorder asc';
        $return = M('Award')->where($where)->field($fields)->order($order)->limit(9)->select();
        $xxNum = 0;
        $xxArr = array();
        if ($return) {
            foreach ($return as $value) {
                if ($value['atype'] == 2) {
                    $xxNum += 1;
                    $xxArr = $value;
                }
            }
        }

        // 总数
        $awTotal = count($return);
        if ($xxArr && $awTotal < $this->listRows) {
            for($i = $awTotal; $i < $this->listRows; $i ++) {
                $tmp = $xxArr;
                $return[$i] = $tmp;
            }
        }

        // 奖品ID对于的中奖率
        $arr = $awData = array();
        $id = 0;
        foreach ($return as $k => $value) {
            $arr[$k] = (float)$value['pronum'];
            $id = $value['id'];
            $awData[$k] = $value;
        }

        // 计算实际概率
        $data = $this->getRand($arr);

        // $rid中奖的序列号码
        $sum = array_sum($arr);
        $msg = '抽奖总数：10000 <br>';

        //计算实际概率
        $rArr = $bArr = array();
        foreach ($awData as $ak => $aval) {
            $rArr[$ak] = round($aval['pronum'] / $sum, 8);
            $bArr[$ak] = $aval['pronum'];
        }


        $lastArr = array();
        for ($i = 0; $i < 10000; $i ++) {
            $data = $this->getRand($arr);
            if (isset($lastArr[$data['key']])) {
                $lastArr[$data['key']] += 1;
            } else {
                $lastArr[$data['key']] = 1;
            }
        }

        // 最终结果
        foreach ($rArr as $rk => $rval) {
            $num = isset($lastArr[$rk]) ? $lastArr[$rk] : 0;
            $aradio = round($num / $sum, 8);
            $msg .= '索引值：'.$rk.'; 出奖率：'.$bArr[$rk].'; 抽中次数：'.$num.'; 概率：'.$aradio.'; 实际概率：'.$rval.'<br>';
        }

        $date = array("error"=>0,"msg"=>$msg);
        echo json_encode($date);die;
    }

    /**
     * 抽奖算法
     **/
    private function getRand($proArr){
        $proSum = array_sum($proArr); //概率数组的总概率精度
        $data['sum'] = $proSum;
        foreach ($proArr as $k => $v) { //概率数组循环
            $randNum = mt_rand(1, $proSum); //重点
            if($randNum <= $v){ //重点
                $data['key'] = $k;
                if (isset($data[$k])) {
                    $data[$k] += 1;
                } else {
                    $data[$k] = 1;
                }
                break;
            } else {
                $proSum -= $v;
            }
        }
        unset($proArr);
        return $data;
    }
}

?>