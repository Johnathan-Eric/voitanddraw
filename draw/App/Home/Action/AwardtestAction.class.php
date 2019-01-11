<?php
/**
 * 抽奖测试
 * User: xuebangshu
 * Date: 2019/1/9
 * Time: 20:27
 */

namespace Home\Action;
use Think\Action;
use Think\Cache\Driver\Redis;

class AwardtestAction extends Action
{
    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {
        $this->_awNum = 9;
        $this->_haKey = 'highA_';
        $this->_redis = new Redis();
    }

    /**
     * 首页
     */
    public function index()
    {
        // 获取公众号数据
        $uModel = new \Think\Model();
        $uniList = $uModel->query('SELECT uniacid,name FROM `ims_uni_account`');

        // 返回数据
        $this->assign('uniList', $uniList);
        $this->display();
    }

    /**
     * 获取活动列表
     */
    public function getActList()
    {
        // 接收参数
        $request = I("request.");

        // 验证参数
        if (!$request['uniacid']) {
            // 返回结果
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误'));
            return;
        }

        // 获取列表数据
        $fields = 'rid,title';
        $where['uniacid'] = array('eq', $request['uniacid']);
        $list = M('Reply')->where($where)->field($fields)->order('id desc')->select();
        if ($list) {
            $data = '<option value="-1">请选择</option>';
            foreach ($list as $val) {
                $data .= '<option value="'.$val['rid'].'">'.$val['title'].'</option>';
            }
            $return = array('code' => 200, 'msg' => '更新成功', 'list' => $data);
        } else {
            $return = array('code' => 205, 'msg' => '暂无活动数据');
        }

        // 返回结果
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 获取用户列表
     */
    public function getUsersList()
    {
        // 接收参数
        $request = I("request.");

        // 验证参数
        if (!$request['uniacid'] || !$request['rid']) {
            // 返回结果
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误'));
            return;
        }

        // 获取活动兑换投票数
        $act = M('Reply')->where(array('rid' => array('eq', $request['rid'])))->field('pervote')->find();

        // 获取列表数据
        $fields = 'uid,name,vote_num';
        $where['uniacid'] = array('eq', $request['uniacid']);
        $where['rid'] = array('eq', $request['rid']);
        $list = M('Users')->where($where)->field($fields)->select();
        if ($list) {
            // 获取活动的奖品信息
            unset($where['rid']);
            $where['actid'] = array('eq', $request['rid']);
            $where['online'] = array('eq', 1);
            $awardList = M('Award')->where($where)->field('name,stock,atype,level')->order('listorder asc')->select();
            $awards = '';
            if ($awardList) {
                foreach ($awardList as $aval) {
                    $awards .= '<div>级别：';
                    switch ($aval['level']) {
                        case 0:
                            $awards .= '低价奖品，';
                            break;
                        case 1:
                            $awards .= '<font style="color: red;">高价奖品</font>，';
                            break;
                    }

                    switch ($aval['atype']) {
                        case 1:
                            $awards .= '名称：'.$aval['name'].'，';
                            $awards .= '类型：普通，';
                            break;
                        case 2:
                            $awards .= '名称：谢谢关注，';
                            $awards .= '类型：谢谢关注，';

                            break;
                        case 3:
                            $awards .= '名称：再来一次，';
                            $awards .= '类型：再来一次，';
                            break;
                    }

                    $awards .= '库存：'.$aval['stock'].'；';
                    $awards .= '</div>';
                }
            }

            $data = '<option value="-1">请选择</option>';
            foreach ($list as $val) {
                $data .= '<option value="'.$val['uid'].'">'.$val['name'].'</option>';
            }
            $return = array('code' => 200, 'msg' => '更新成功', 'list' => $data, 'pervote' => $act['pervote'], 'awards' => $awards);
        } else {
            $return = array('code' => 205, 'msg' => '暂无用户数据');
        }

        // 返回结果
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 获取用户信息
     */
    public function getUsers()
    {
        // 接收参数
        $request = I("request.");

        // 验证参数
        if (!$request['uniacid'] || !$request['rid'] || !$request['uid']) {
            // 返回结果
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误'));
            return;
        }

        // 获取活动兑换投票数
        $act = M('Reply')->where(array('rid' => array('eq', $request['rid'])))->field('pervote')->find();

        // 获取列表数据
        $fields = 'vote_num,total_num,openid';
        $where['uniacid'] = array('eq', $request['uniacid']);
        $where['rid'] = array('eq', $request['rid']);
        $where['uid'] = array('eq', $request['uid']);
        $info = M('Users')->where($where)->field($fields)->find();
        if ($info) {
            // 中奖记录
            unset($where['rid']);
            $where['cid'] = array('eq', $request['rid']);
            $awardLog = M('AwardLog')->where($where)->field('aname,level,addtime,num')->order('id desc')->select();
            $awards = $numLogs = '';
            if ($awardLog) {
                $numArr = array();
                foreach ($awardLog as $alval) {
                    // 中奖记录
                    $awards .= '<div>抽 '.$alval['num'].' 次，';
                    $awards .= '级别：';
                    switch ($alval['level']) {
                        case 0:
                            $awards .= '低价奖品，';
                            break;
                        case 1:
                            $awards .= '<font style="color: red;">高价奖品</font>，';
                            break;
                    }
                    $awards .= '名称：'.$alval['aname'].'，';
                    $awards .= '时间：'.date('Y-m-d H:i:s', $alval['addtime']).'；';
                    $awards .= '</div>';

                    // 次数信息
                    if (isset($numArr[$alval['num']])) {
                        $numArr[$alval['num']] += 1;
                    } else {
                        $numArr[$alval['num']] = 1;
                    }
                }

                // 抽奖次数标识记录
                if ($numArr) {
                    ksort($numArr);
                    foreach ($numArr as $k => $nv) {
                        $numLogs .= '<div>';
                        $numLogs .= '抽奖'.$k.'次，抽了'.$nv.'次';
                        $numLogs .= '</div>';
                    }
                }
            }

            // 剩余次数
            $info['lastNum'] = getAwardNum($info['vote_num'], $act['pervote']);
            $info['tvoteNum'] = $info['vote_num'] + ($info['total_num'] * $act['pervote']);
            $info['totalNum'] = $info['total_num'] + $info['lastNum'];
            $return = array('code' => 200, 'msg' => 'succ', 'info' => $info, 'awards' => $awards, 'numLogs' => $numLogs);
        } else {
            $return = array('code' => 205, 'msg' => '暂无用户数据');
        }

        // 返回结果
        $this->ajaxReturn($return);
        return;
    }
}