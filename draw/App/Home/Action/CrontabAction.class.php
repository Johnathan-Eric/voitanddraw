<?php
/*
 * 会员类：登录/注册/密码找回/手机验证码
 * @author xuebs
 * @date 2018-08-28
 */
namespace Home\Action;
use Think\Action;

class CrontabAction extends Action
{
    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {

    }

    /**
     * 同步用户投票总数
     */
    public function cronVote()
    {
        // 总票数信息
        $totalData = array();

        // 获取投票数据
        $voteData = M('Votedata')->where(array('is_cron' => array('eq', 0)))->field('id,rid,uniacid,openid,nickname')->select();
        $vdids = $gdids = array();
        if ($voteData) {
            foreach ($voteData as $vval) {
                $vkey = $vval['uniacid'].'_'.$vval['rid'].'_'.$vval['openid'];
                if (isset($totalData[$vkey])) {
                    $totalData[$vkey]['vote_num'] += 1;
                } else {
                    $totalData[$vkey]['name'] = $vval['nickname'];
                    $totalData[$vkey]['vote_num'] = 1;
                    $totalData[$vkey]['openid'] = $vval['openid'];
                    $totalData[$vkey]['rid'] = $vval['rid'];
                    $totalData[$vkey]['uniacid'] = $vval['uniacid'];
                }

                // 投票标识
                $vdids[] = $vval['id'];
            }
        }

        // 获取赠送礼物的投票数据
        $giftData = M('Gift')->where(array('ispay' => array('eq', 1), 'is_cron' => array('eq', 0)))->field('id,rid,uniacid,openid,nickname,giftvote')->select();
        if ($giftData) {
            foreach ($giftData as $gval) {
                $gkey = $gval['uniacid'].'_'.$gval['rid'].'_'.$gval['openid'];
                if (isset($totalData[$gkey])) {
                    $totalData[$gkey]['vote_num'] += $gval['giftvote'];
                } else {
                    $tmps = array();
                    $tmps['name'] = $gval['nickname'];
                    $tmps['vote_num'] = $gval['giftvote'];
                    $tmps['openid'] = $gval['openid'];
                    $tmps['rid'] = $gval['rid'];
                    $tmps['uniacid'] = $gval['uniacid'];
                    $totalData[$gkey] = $tmps;
                }

                // 礼物标识
                $gdids[] = $gval['id'];
            }
        }

        // 批量导入数据
        if ($totalData) {
            // 非阻塞模式处理高并发
            $fp = fopen("crontab_lock.txt", "w+");
            if(flock($fp,LOCK_EX | LOCK_NB)) { // 设置加锁
                // 添加或更新数据
                $addData = array();

                // 查看当前已有用户的投票数据
                $userList = M('Users')->select();
                if ($userList) {
                    $uData = array();
                    foreach ($userList as $uval) {
                        $ukey = $uval['uniacid'] . '_' . $uval['rid'] . '_' . $uval['openid'];
                        $uData[$ukey] = $uval;
                    }
                    foreach ($totalData as $tkey => $tval) {
                        if (isset($uData[$tkey])) {
                            $mwhere['uniacid'] = array('eq', $tval['uniacid']);
                            $mwhere['rid'] = array('eq', $tval['rid']);
                            $mwhere['openid'] = array('eq', $tval['openid']);
                            M('Users')->where($mwhere)->setInc('vote_num', $totalData[$tkey]['vote_num']);
                        } else {
                            $addData[] = $tval;
                        }
                    }
                } else {
                    $addData = array_values($totalData);
                }

                // 批量插入数据
                if ($addData) {
                    M('Users')->addAll($addData);
                }

                // 修改投票和礼物执行任务标识
                if ($vdids) {
                    M('Votedata')->where(array('id' => array('in', $vdids)))->save(array('is_cron' => 1));
                }
                if ($gdids) {
                    M('Gift')->where(array('id' => array('in', $gdids)))->save(array('is_cron' => 1));
                }

                // 解锁
                flock($fp,LOCK_UN);
            }

            // 关闭加锁处理
            fclose($fp);
        }

        echo json_encode($totalData);
    }

}