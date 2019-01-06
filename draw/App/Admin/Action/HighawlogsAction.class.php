<?php
/**
 * 高价位中奖日志
 * User: xuebangshu
 * Date: 2018/12/18
 * Time: 20:02
 */
namespace Admin\Action;
use Think\AdminBaseAction;
class HighawlogsAction extends AdminBaseAction
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
    public function high_logs() {
        $request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $where = $fwhere = $owhere = array();
        if ($request['actid']) { // 活动ID
            $where['rid'] = array('eq', $request['actid']);
            $owhere['cid'] = array('eq', $request['actid']);
            $fwhere['rid'] = array('eq', $request['actid']);
        }
        if ($request['uniacid']) { // 公众号ID
            $where['uniacid'] = array('eq', $request['uniacid']);
            $owhere['uniacid'] = array('eq', $request['uniacid']);
            $fwhere['uniacid'] = array('eq', $request['uniacid']);
        }
//        if ($request['atype']) { // 奖品类型
//            $where['atype'] = array('eq', $request['atype']);
//        }
//        if ($request['stype']) { // 发送类型
//            $where['status'] = array('eq', $request['stype']);
//        }
        $keyword	= I("keyword", '', 'trim');
        if ($keyword) { // 搜索关键字
            $where['name'] = array('like', '%'.$keyword.'%');
        }

        // 获取总数
        $total = M('Users')->where($where)->count();

        // 获取活动列表
        $actList = M('Reply')->field('rid,title,pervote')->select();
        $actData = array();
        if ($actList) {
            foreach ($actList as $aval) {
                $actData[$aval['rid']] = $aval;
            }
        }

        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');

        // 再来一次次数
        $onceList = M('Oncemore')->where($owhere)->field('num,cid,uid,uniacid')->find();
        $onceData = array();
        if ($onceList) {
            foreach ($onceList as $ov) {
                $ok = $ov['cid'].'_'.$ov['uid'];
                $onceData[$ok] = $ov['num'];
            }
        }

        // 获取所有用户的充值记录
        // SELECT openid ,nickName,SUM(fee) cc
        //FROM `ims_tyzm_diamondvote_gift` WHERE
        //  rid = 34 AND isdeal = 1 AND ispay = 1 AND is_ControlPanel = 0
        //GROUP BY openid,nickName
        //ORDER BY cc DESC
        $fwhere['isdeal'] = array('eq', 1);
        $fwhere['ispay'] = array('eq', 1);
        $fwhere['is_ControlPanel'] = array('eq', 0);
        $feeList = M('Gift')->where($fwhere)->field('rid,openid,SUM(fee) as cc')
            ->group('rid,openid')->select();
        $feeData = array();
        if ($feeList) {
            foreach ($feeList as $fval) {
                $fk = $fval['rid'].'_'.$fval['openid'];
                $feeData[$fk] = $fval['cc'];
            }
        }

        // 查询用户高价奖品记录
        $owhere['level'] = array('eq', 1);
        $highAList = M('AwardLog')->where($owhere)->field('cid,uid,count(id) as Tnum')
            ->group('cid,uid')->select();
        $highAData = array();
        if ($highAList) {
            foreach ($highAList as $hval) {
                $hk = $hval['cid'].'_'.$hval['uid'];
                $highAData[$hk] = $hval['Tnum'];
            }
        }

        // 获取奖励列表
        $list = M('Users')->where($where)->page($page, $this->listRows)->order('uid desc')->select();
        if ($list) {
            foreach ($list as &$val) {
                $val['title'] = ''; // 活动标题
                $val['lastnum'] = 0; // 剩余抽奖次数
                if (isset($actData[$val['rid']])) {
                    $val['title'] = $actData[$val['rid']]['title'];
                    $val['lastnum'] += floor($val['vote_num']/$actData[$val['rid']]['pervote']);
                }

                // 再来一次
                if (isset($onceData[$val['rid'].'_'.$val['uid']])) {
                    $val['lastnum'] += $onceData[$val['rid'].'_'.$val['uid']];
                }

                $val['paynum'] = 0; // 充值总额
                if (isset($feeData[$val['rid'].'_'.$val['openid']])) {
                    $val['paynum'] = $feeData[$val['rid'].'_'.$val['openid']];
                }

                $val['awardnum'] = 0; // 高价值奖品中奖次数
                if (isset($highAData[$val['rid'].'_'.$val['uid']])) {
                    $val['awardnum'] = $highAData[$val['rid'].'_'.$val['uid']];
                }
            }
        }

        // 返回数据
        $this->assign('sendType', $this->sendType);
        $this->assign('uniList', $uniList);
        $this->assign('atypes', $this->atypes);
        $this->assign('actList', $actList);
        $this->assign('lists', $list);
        $this->assign('_curr', $page);
        $this->assign('_total', $total);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '用户列表');
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
            $Goods = M('Users');
            $Goods->where(array('uid'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }

}