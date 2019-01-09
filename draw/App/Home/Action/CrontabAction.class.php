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
        $request = I("request.");
        if (!isset($request['uniacid']) || !$request['rid']) {
            exit('参数错误！');
        }

        // 总票数信息
        $totalData = array();

        // 获取投票数据
        $where['uniacid'] = array('eq', $request['uniacid']);
        $where['rid'] = array('eq', $request['rid']);
        $where['is_cron'] = array('eq',0);
        $voteData = M('Votedata')->where($where)->field('id,rid,uniacid,openid,nickname')->select();
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
        $where['ispay'] = array('eq', 1);
        $giftData = M('Gift')->where($where)->field('id,rid,uniacid,openid,nickname,giftvote')->select();
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

        echo '总条数：'.count($totalData);exit;
    }

    /**
     * 模版消息 - 获取ACCESS_TOKEN
     */
    public function getAccessToken()
    {
        $code = $_GET['code'];
        $appid = 'wx2dbeeba3fa3a3b25';
        $appsecret = 'df2a9fd1acd19d6e2311ac1660631b3c';
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        echo $data;
    }

    /**
     * 模版消息 - 向关注用户发送模版消息
     */
    public function sendTemMsg()
    {
        $ACCESS_TOKEN = "替换你的ACCESS_TOKEN";//ACCESS_TOKEN
        //openid数组
        $touser = array('ouD7BuHpIKRXPIz7pdrwI9IwDRCU','ouD7BuI36wSUZgteyiydmDrldQLU','ouD7BuLejq7R4Vbuyh41bH778cg0');
        //模板消息请求URL
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $ACCESS_TOKEN;

        //遍历发送微信消息
        foreach ($touser as $value) {
            $data = $this->getDataArray($value);
            $json_data = json_encode($data);//转化成json数组让微信可以接收
            $res = $this->https_request($url, urldecode($json_data));//请求开始
            $res = json_decode($res, true);
            if ($res['errcode'] == 0 && $res['errcode'] == "ok") {
                echo "发送成功！<br/>";
            }
        }
    }

    //获取发送数据数组
    private function getDataArray($value)
    {
        $data = array(
            'touser' => $value, //要发送给用户的openid
            'template_id' => "mfopDNUlvoBGGsPLB-d_nrfL8Je92xnTq5vk5ZBxL-w",//改成自己的模板id，在微信后台模板消息里查看
            'url' => "http://mp.weixin.qq.com/s/8UWPqHVa8PReWZp-No0ebA", //自己网站链接url
            'data' => array(
                'first' => array(
                    'value' => "亲爱的同学，您有考试提醒，请查阅。",
                    'color' => "#000"
                ),
                'keyword1' => array(
                    'value' => "2017下半年教师资格证面试",
                    'color' => "#f00"
                ),
                'keyword2' => array(
                    'value' => "2018-1-6",
                    'color' => "#173177"
                ),
                'keyword3' => array(
                    'value' => "请看您的准考证",
                    'color' => "#3d3d3d"
                ),
                'keyword4' => array(
                    'value' => "教师资格证试讲",
                    'color' => "#3d3d3d"
                ),
                'keyword5' => array(
                    'value' => "答辩，选题，结构化",
                    'color' => "#3d3d3d"
                ),
                'remark' => array(
                    'value' => "\n现在是打印准考证时间，请您在考试前打印准考证，戳进来可以查看详情>>>",
                    'color' => "#3d3d3d"
                ),
            )
        );
        return $data;
    }

    //curl请求函数，微信都是通过该函数请求
    private function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}