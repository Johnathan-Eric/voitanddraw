<?php
/**
 * Created by PhpStorm.
 * User: xuebangshu
 * Date: 2018/9/15
 * Time: 11:21
 */
namespace Admin\Action;
use Think\AdminBaseAction;
class AwlogsAction extends AdminBaseAction
{
    protected $admin, $listRows, $model, $atypes, $sendType;

    /**
     * 构建函数
     **/
    public function _initialize()
    {
        // 管理员信息
        $this->admin = session('admin');

        $this->listRows = 9; // 每页显示条数
        $this->model = D("Common/Award", "Logic");
        $this->atypes = C('awardType');
        $this->sendType = C('sendType');
    }

    /**
     * 出奖日志
     **/
    public function logs_index() {
        $request = I("request.");

        // 获取活动列表
        $actList = M('Reply')->field('rid,title')->select();
        $actData = array();
        if ($actList) {
            foreach ($actList as $aval) {
                $actData[$aval['rid']] = $aval['title'];
            }
        }

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $where 		= array();
        if ($request['actid']) { // 活动ID
            $where['actid'] = array('eq', $request['actid']);
        }
        if ($request['uniacid']) { // 公众号ID
            $where['uniacid'] = array('eq', $request['uniacid']);
        }
//        if ($request['atype']) { // 奖品类型
//            $where['atype'] = array('eq', $request['atype']);
//        }
        if ($request['stype']) { // 发送类型
            $where['status'] = array('eq', $request['stype']);
        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) { // 搜索关键字
            $where['uname|aname'] = array('like', '%'.$keyword.'%');
        }

        // 获取总数
        $where['atype'] = array('eq', 1);
        $total = M('AwardLog')->where($where)->count();

        // 获取奖励列表
        $fields = 'id,uname,uid,cid,addtime,aname,thumb,atype,status,is_post';
        $list = M('AwardLog')->where($where)->page($page, $this->listRows)->field($fields)->order('id desc')->select();
        if ($list) {
            foreach ($list as &$val) {
                $val['title'] = '';
                if (isset($actData[$val['cid']])) {
                    $val['title'] = $actData[$val['cid']];
                }
                $val['atype'] = $this->atypes[$val['atype']];
            }
        }

        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');

        // 返回数据
        $this->assign('sendType', $this->sendType);
        $this->assign('uniList', $uniList);
        $this->assign('atypes', $this->atypes);
        $this->assign('actList', $actList);
        $this->assign('lists', $list);
        $this->assign('_curr', $page);
        $this->assign('_total', $total);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '出奖列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->display('logs_index');
    }

    /**
     * ajax批量操作会员状态
     */
    public function ajax_update(){
        $ids = I('ids');
        if(count($ids) > 0){
            $val   = I('nval','0','int');
            $field = I('ntype','status','trim');
            $data = array($field=>$val);
            $Goods = M('AwardLog');
            $Goods->where(array('id'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }

    /**
     * 回库/出库操作
     */
    public function savePost() {
        // 参数接收
        $id = I('id');

        if (!$id) {
            $data = array("error"=>2,"message"=>'记录信息错误');
            echo json_encode($data);die;
        }

        // 查看记录信息
        $info = M('AwardLog')->where(array('id' => array('eq', $id)))->field('status,aid')->find();
        if (!$info) {
            $data = array("error"=>2,"message"=>'记录信息错误');
        } else {
            // 更新奖品库存信息
            if ($info['status'] == 1) { // 已入库状态
                $status = 0;
            } else {
                $status = 1;
            }
            $logData = array('status' => $status);
            $is_succ = M('AwardLog')->where(array('id' => array('eq', $id)))->save($logData);
            if ($is_succ) {
                // 更新奖品库存数量
                if ($status == 1) {
                    $is_add = M('Award')->where(array('id' => array('eq', $info['aid'])))->setInc('stock');
                } else {
                    $is_add = M('Award')->where(array('id' => array('eq', $info['aid'])))->setDec('stock');
                }
                if ($is_add) {
                    $data = array("error"=>0,"message"=>'奖品库存更新成功');
                } else {
                    $data = array("error"=>3,"message"=>'奖品库存更新失败');
                }
            } else {
                $data = array("error"=>3,"message"=>'奖品信息更新失败');
            }
        }
        echo json_encode($data);die;
    }

    /**
     * 导出订单
     * @author Destiny
     */
    function outputOrders(){
        $request = I('request.');
        if ($request['actid']) { // 活动ID
            $where['actid'] = array('eq', $request['actid']);
        }
        if ($request['uniacid']) { // 公众号ID
            $where['uniacid'] = array('eq', $request['uniacid']);
        }
//        if ($request['atype']) { // 奖品类型
//            $where['atype'] = array('eq', $request['atype']);
//        }
        if ($request['stype']) { // 发送类型
            $where['status'] = array('eq', $request['stype']);
        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) { // 搜索关键字
            $where['uname|aname'] = array('like', '%'.$keyword.'%');
        }

        // 获取总数
        $where['atype'] = array('eq', 1);

        // 获取奖励列表
        $fields = 'id,uname,uid,aname';
        $list = M('AwardLog')->where($where)->field($fields)->order('id desc')->select();
        if ($list) {
            $uids = array();
            foreach ($list as $val) {
                if (!in_array($val['uid'], $uids)) {
                    $uids[] = $val['uid'];
                }
            }

            // 根据用户ID获取邮寄地址信息
            $uData = array();
            if ($uids) {
                $ufields = 'uid,name,address,postcode,tel';
                $users = M('Address')->where(array('uid' => array('in', $uids)))->field($ufields)->select();
                if ($users) {
                    foreach ($users as $uval) {
                        $uData[$uval['uid']] = $uval;
                    }
                }
            }

            // 整理数据
            foreach ($list as &$lval) {
                $lval['name'] = $lval['address'] = $lval['postcode'] = $lval['tel'] = '';
                if (isset($uData[$lval['uid']])) {
                    $lval['name'] = $uData[$lval['uid']]['name'];
                    $lval['address'] = $uData[$lval['uid']]['address'];
                    $lval['postcode'] = $uData[$lval['uid']]['postcode'];
                    $lval['tel'] = $uData[$lval['uid']]['tel'];
                }
            }
        }

        $excelLogic = D('Common/Excel','Logic');
        $xlsName = "奖品配货单";
        $xlsCell  = array(
            array('no','序号'),
            array('uname','昵称'),
            array('aname','奖品名称'),
            array('name','姓名'),
            array('address','邮寄地址'),
            array('postcode','邮政编码'),
            array('tel','联系电话'),
        );
        $xlsData = array();
        $i = 1;
        $orderStatus = C("orderStatus");
        foreach ($list as $k=>$v){
            $xlsData[$k]['no'] = $i++;
            $xlsData[$k]['uname'] = $v['uname'];
            $xlsData[$k]['aname'] = $v['aname'];
            $xlsData[$k]['name'] = $v['name'];
            $xlsData[$k]['address'] = $v['address'];
            $xlsData[$k]['postcode'] = $v['postcode'];
            $xlsData[$k]['tel'] = $v['tel'];
        }
        $excelLogic -> exportExcel($xlsName,$xlsCell,$xlsData);
    }
}