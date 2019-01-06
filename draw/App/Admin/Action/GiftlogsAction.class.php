<?php
/**
 * 充值日志
 * User: xuebangshu
 * Date: 2018/12/30
 * Time: 21:26
 */
namespace Admin\Action;
use Think\AdminBaseAction;
class GiftlogsAction extends AdminBaseAction
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
    }

    /**
     * 充值日志
     **/
    public function gift_index()
    {
        $request = I("request.");

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");

        // 查询条件
        $fwhere = array();
        if ($request['uniacid']) { // 公众号ID
            $fwhere['uniacid'] = array('eq', $request['uniacid']);
        }
        if ($request['actid']) { // 活动ID
            $fwhere['rid'] = array('eq', $request['actid']);
        }
        if ($request['openid']) { // openid
            $fwhere['openid'] = array('eq', $request['openid']);
        }
        $keyword    = I("keyword", '', 'trim');
        if ($keyword) { // 搜索关键字
            $fwhere['nickname'] = array('like', '%'.$keyword.'%');
        }

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

        // 获取所有用户的充值记录
        // SELECT openid ,nickName,SUM(fee) cc
        //FROM `ims_tyzm_diamondvote_gift` WHERE
        //  rid = 34 AND isdeal = 1 AND ispay = 1 AND is_ControlPanel = 0
        //GROUP BY openid,nickName
        //ORDER BY cc DESC
        $fwhere['isdeal'] = array('eq', 1);
        $fwhere['ispay'] = array('eq', 1);
        $fwhere['is_ControlPanel'] = array('eq', 0);

        // 获取总数
        $total = M('Gift')->where($fwhere)->count();
    
        // 获取数据列表
        $list = M('Gift')->where($fwhere)
            ->field('rid,nickname,fee,avatar,gifttitle,giftcount,gifticon,createtime')
            ->order('id desc')->page($page, $this->listRows)->select();
        if ($list) {
            foreach ($list as &$val) {
                $val['title'] = ''; // 活动标题
                if (isset($actData[$val['rid']])) {
                    $val['title'] = $actData[$val['rid']]['title'];
                }
            }
        }

        // 返回数据
        $this->assign('uniList', $uniList);
        $this->assign('actList', $actList);
        $this->assign('lists', $list);
        $this->assign('_curr', $page);
        $this->assign('_total', $total);
        $this->assign('_limit', $this->listRows);
        $this->assign('page_title', '充值列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->display('gift_index');
    }
}