<?php
/*
 * 会员类：登录/注册/密码找回/手机验证码
 * @author xuebs
 * @date 2018-08-28
 */
namespace Home\Action;
use Think\Action;
//use Think\Cache\Driver\Redis;

class IndexAction extends Action {
    private $_awNum,$_haKey,$_aKey,$_redis;

    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {
        $this->_awNum = 9;
        $this->_haKey = 'highA_';
        $this->_redis = new \Redis();
        $this->_redis->connect(C('REDIS_HOST'));
    }

    /**
     * 首页
     **/
    public function index() {
        $request = I("request.");

        // 当前时间
        $nowTime = time();

        // 查询活动是否过期
        $rwhere['rid'] = array('eq', $request['actid']);
        $fields = 'rid,starttime,endtime,pervote,uniacid';
        $actInfo = M('Reply')->where($rwhere)->field($fields)->find();
//        if (!$actInfo) {
//            echo '<script type="application/javascript">alert("活动不存在！");window.history.go(-1);</script>';
//            exit;
//        }
//
//        // 时间判断
//        if ($nowTime < $actInfo['starttime']) {
//            echo '<script type="application/javascript">alert("活动未开始！");window.history.go(-1);</script>';
//            exit;
//        }
//        if ($nowTime > $actInfo['endtime']) {
//            echo '<script type="application/javascript">alert("活动已结束！");window.history.go(-1);</script>';
//            exit;
//        }

        // 查询奖品数据
        $request['uniacid'] = $actInfo['uniacid'];
        $request['index'] = 1; // 首页获取数据
        $data['awList'] = $this->getList($request);
        if (empty($data['awList'])) {
            echo '<script type="application/javascript">alert("奖品未配置！");window.history.go(-1);</script>';
            exit;
        }

        // 获取活动顶部图片
        $actImg = M('ReplyBase')->where(array('actid' => array('eq', $request['actid'])))->find();

        // 抽奖记录
        $where['uniacid'] = array('eq', $actInfo['uniacid']);
        $where['cid'] = array('eq', $request['actid']);
        $data['logs'] = M('AwardLog')->where($where)->field('uid,uname,aname,addtime')->order('addtime desc')->select();

        // 获取用户抽奖次数信息
        $request['pervote'] = $actInfo['pervote'];
        $uawInfo = $this->getAwNum($request);

        // 返回数据
        $this->assign('homeUrl', substr($_SERVER['HTTP_HOST'], 5).'/app/index.php?i='.$actInfo['uniacid'].'&c=entry&rid='.$request['actid'].'&do=index&m=tyzm_diamondvote');
        $this->assign('awardUrl', '/Home/Index/index/actid/'.$request['actid'].'/openid/'.$request['openid']);
        $this->assign('myUrl', '/Home/Index/awardLog/uid/'.$uawInfo['uid'].'/actid/'.$request['actid']);
        $this->assign('homeClass', 'activebg');
        $this->assign('homePng', 'award_active');
        $this->assign('myClass', '');
        $this->assign('myPng', 'my');
        $this->assign('user', $uawInfo);
        $this->assign('data', $data);
        $this->assign('request', $request);
        $this->assign('actImg', $actImg);
        $this->display('index');
    }

    /**
     * 显示奖品信息并记录点击次数
     */
    public function showPic() {
        // 接收参数
        $request = I("request.");
        $adlog['aid'] = $request['aid'];
        $adlog['actid'] = $request['actid'];
        $adlog['uniacid'] = $request['uniacid'];

        // 记录日志
        // 查看公众号活动下的奖品点击次数信息
        $awhere['uniacid'] = array('eq', $request['uniacid']);
        $awhere['actid'] = array('eq', $request['actid']);
        $awhere['aid'] = array('eq', $request['aid']);
        $aDataInfo = M('award_data')->where($awhere)->find();
        if ($aDataInfo) {
            $isTrue = M('award_data')->where($awhere)->setInc('num', 1);
        } else {
            $adlog['num'] = 1; // 点击次数
            $isTrue = M('award_data')->add($adlog);
        }

        // 返回数据
        if ($isTrue) {
            $return = array('code' => 200, 'msg' => '');
        } else {
            $return = array('code' => 201, 'msg' => '');
        }

        // 返回结果
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 获取用户的抽奖次数
     */
    private function getAwNum($request) {
        // 获取投票次数数据
        if (!isset($request['pervote'])) {
            $actInfo = M('Reply')->where(array('rid' => array('eq', $request['actid'])))->field('pervote')->find();
            $pervote = $actInfo['pervote'];
        } else {
            $pervote = $request['pervote'];
        }

        // 获取用户信息
//        $uwhere['uniacid'] = array('eq', $request['uniacid']);
//        $uwhere['rid'] = array('eq', $request['actid']);
//        if (isset($request['uid']) && $request['uid']) {
//            $uwhere['uid'] = array('eq', $request['uid']);
//        } else {
//            $uwhere['openid'] = array('eq', $request['openid']);
//        }
//        $user = M('Users')->where($uwhere)->field('uid,name,vote_num,status,nextnum,total_num')->find();
        $user = $this->getUser($request);

        // 获取抽奖机会次数（再累一次）
        $owhere['uniacid'] = array('eq', $request['uniacid']);
        $owhere['cid'] = array('eq', $request['actid']);
        $owhere['uid'] = array('eq', $user['uid']);
        $onceData = M('Oncemore')->where($owhere)->field('num')->find();
        $totalNum = isset($onceData['num']) ? $onceData['num'] : 0;

        // 用户的投票数计算的抽奖次数
        $totalNum += floor($user['vote_num']/$pervote);

        // 返回数据
        return array('uniacid' => $request['uniacid'], 'nickname' => $user['name'], 'totalNum' => $totalNum, 'uid' => $user['uid'], 'pervote' => $pervote, 'user' => $user);
    }

    /**
     * 获取奖品信息
     **/
    private function getList($request) {
        // 获取活动的奖品信息
        $skey = $this->_haKey.$request['actid'];
//        $isHave = $this->_redis->get($skey);
//        if ($isHave) {
//            return json_decode($isHave, true);
//        }

        // 当前时间
        $nowTime = time();

        // 查询条件
        $where['uniacid'] = array('eq', $request['uniacid']);
        $where['actid'] = array('eq', $request['actid']);
        $where['online'] = array('eq', 1);
        $fields = 'id,name,thumb,stock,pronum,atype,stime,etime,hurl,sendnum,winnum,level,limitnum,paynum';
        $order = 'listorder asc';
        $return = M('Award')->where($where)->field($fields)->order($order)->limit(9)->select();
        $xxArr = $lastData = $highAward = array();
        if ($return) {
            foreach ($return as $value) {
                if ($value['stime'] > $nowTime || $value['etime'] < $nowTime) {
                    unset($value);
                } else {
                    if ($value['atype'] == '2') {
                        $xxArr = $value;
                        continue;
                    } else if ($value['atype'] == '3') {
                        $value['thumb'] = '/Public/images/next.jpeg';
                    }
                    $lastData[] = $value;
                }
            }
        }

        // 总数
        $awTotal = count($lastData);
        if ($awTotal < $this->_awNum) {
            for($i = $awTotal; $i < $this->_awNum; $i ++) {
                $tmp = $xxArr;
                $tmp['atype'] = '2';
                $tmp['thumb'] = '/Public/images/thanks.jpeg';
                $lastData[$i] = $tmp;
            }
        }

        // 存储奖品信息
        $this->_redis->set($skey, json_encode($lastData), C('DATA_CACHE_TIME'));

        return $lastData;
    }

    /**
     * 获取用户信息
     * @param $request
     * @return mixed
     */
    private function getUser($request) {
        // 获取用户信息
        $uwhere['uniacid'] = array('eq', $request['uniacid']);
        $uwhere['rid'] = array('eq', $request['actid']);
        if (isset($request['uid']) && $request['uid']) {
            $uwhere['uid'] = array('eq', $request['uid']);
        } else {
            $uwhere['openid'] = array('eq', $request['openid']);
        }
        return M('Users')->where($uwhere)->field('uid,name,vote_num,status,nextnum,total_num')->find();
    }

    /**
     * 获取高价奖品的ID信息
     **/
    private function getHighArr($request) {
        // 返回信息
        $return = array();

        // 获取高价奖品信息
        $skey = $this->_haKey.$request['actid'];
        $highAward = json_decode($this->_redis->get($skey), 1);

        // 获取用户信息
        $user = $this->getUser($request);

        // 查看中奖记录
        $lwhere['uniacid'] = array('eq', $request['uniacid']);
        $lwhere['cid'] = array('eq', $request['actid']);
        $lwhere['uid'] = array('eq', $request['uid']);
        $lwhere['level'] = array('eq', 1);
        $awLogs = M('AwardLog')->where($lwhere)->field('aid,level')->select();
        if ($awLogs) {
            if ($user['status'] == '1') { // 高价奖品只能获取一次,除非为用户设置允许再次获取奖品
                return $return;
            } else { // 允许中奖,但是,下次抽奖次数必须大于总抽奖次数
                if ($user['status'] == '2' && $user['nextnum'] < $user['total_num']) {
                    return $return;
                }
            }
        }

        // 存储高价奖品
        if (!isset($request['index']) && $highAward) {
            $highArr = array();
            foreach ($highAward as $hval) {
                if ($hval['level'] == 1) {
                    $highArr[] = $hval;
                }
            }

            // 排序
            sortArray($highArr, 'paynum');

            if ($highArr) {
                // 获取所有充值总计数据
                $fwhere['uniacid'] = array('eq', $request['uniacid']);
                $fwhere['rid'] = array('eq', $request['actid']);
                $fwhere['isdeal'] = array('eq', 1);
                $fwhere['ispay'] = array('eq', 1);
//                $fwhere['is_ControlPanel'] = array('eq', 0);
                $feeList = M('Gift')->where($fwhere)->field('openid,SUM(fee) as cc')
                    ->group('openid')->select();
                if ($feeList) {
                    $feeData = $aPaynumArr = array();

                    // 整理用户充值统计数据
                    foreach ($feeList as $fval) {
                        $feeData[$fval['openid']] = $fval['cc'];
                    }

                    if (isset($feeData[$request['openid']])) {
                        // 高价奖品的充值数额区间数据
                        $aPaynumArr = array();

                        // 高价奖品总数
                        $hNum = count($highArr);
                        foreach ($highArr as $k => $hval) {
                            if ($k < $hNum) {
                                if (!isset($highArr[$k+1])) {
                                    $lastNum = '1000000000000';
                                } else {
                                    $lastNum = $highArr[$k+1]['paynum'];
                                }
                                $aPaynumArr[$k] = array($hval['paynum'], $lastNum);
                            } else {
                                break;
                            }
                        }

                        // 比对奖品
                        $apNumArr = array();
                        foreach ($feeList as $fval) {
                            // $fval['cc'] += 5000;
                            foreach ($aPaynumArr as $k => $aval) {
                                if ($fval['cc'] >= $aval[0] && $fval['cc'] < $aval[1]) {
                                    if (isset($apNumArr[$k])) {
                                        $apNumArr[$k] += 1;
                                    } else {
                                        $apNumArr[$k] = 1;
                                    }
                                }
                            }
                        }

                        // 根据符合充值总额满足条件的用户获取奖品的ID
                        if ($apNumArr) {
                            foreach ($apNumArr as $ak => $aknum) {
                                if (isset($highArr[$ak]) && $highArr[$ak]['limitnum'] >= $aknum) {
                                    $return[] = $highArr[$ak]['id'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $return;
    }

    /**
     * 抽奖
     **/
    public function doAward() {
        // 接收参数
        $request = I("request.");

        // 缓存key
        $skey = $this->_haKey.$request['actid'];

        ###### 获取奖品信息
        // 抽奖次数
        $num = $request['num'];

        // 获取的所有奖品信息
        if (!$num) {
            // 返回结果
            $this->ajaxReturn(array('code' => 201, 'msg' => '抽奖次数错误！'));
            return;
        }

        // 抽奖次数验证
        $awInfo = $this->getAwNum($request);
        if ($awInfo['totalNum'] < $num) {
            // 返回结果
            $this->ajaxReturn(array('code' => 202, 'msg' => '抽奖次数不足！'));
            return;
        }

        // 剩余抽奖次数
        $lastNum = $awInfo['totalNum'] - $num;

        // 查看用户已中奖的记录
        $lwhere['uniacid'] = array('eq', $request['uniacid']);
        $lwhere['cid'] = array('eq', $request['actid']);
        $lwhere['uid'] = array('eq', $request['uid']);
        $awLogs = M('AwardLog')->where($lwhere)->field('aid,level')->select();
        $awNums = $hawNums = array();
        if ($awLogs) {
            foreach ($awLogs as $aval) {
                if (isset($awNums[$aval['aid']])) {
                    $awNums[$aval['aid']] += 1;
                } else {
                    $awNums[$aval['aid']] = 1;
                }
                if ($aval['level'] == 1) {
                    if (isset($hawNums[$aval['aid']])) {
                        $hawNums[$aval['aid']] += 1;
                    } else {
                        $hawNums[$aval['aid']] = 1;
                    }
                }
            }
        }

        // 查看用户再来一次的记录
        $onceLogs = M('Oncemore')->where($lwhere)->field('num')->count();

        // 获奖信息
        $dmsg = '';

        // 非阻塞模式处理高并发
        $fp = fopen("lock.txt", "w+");
        if(flock($fp,LOCK_EX | LOCK_NB)) { // 设置加锁
            // 根据次数循环出奖信息
            $rid = $onceNum = $isHaveHigh = 0;
            $logArr = $rdata = $stockNum = array();
            for ($i = 0; $i < $num; $i++) {
                // 查询奖品数据
                $awList = $this->getList($request);

                $newawList = array(); // 奖品池数据

                // 查询是否满足高价奖品信息
                $hArr = $this->getHighArr($request);
                if (empty($hArr)) {
                    foreach ($awList as $k => $awval) {
                        if ($awval['level'] == 0) {
                            $newawList[$k] = $awval;
                        }
                    }
                } else {
                    foreach ($awList as $hk => $awval) {
                        if ($awval['level'] == 1 && in_array($awval['id'], $hArr)) {
                            $newawList[$hk] = $awval;
                        }
                    }
                }

                // 奖品ID对于的中奖率
                $arr = $awData = array();
                $atype2 = 0;
                foreach ($newawList as $k => $value) {
                    if ($value['stock'] > 0) {
                        $arr[$k] = (float)$value['pronum'];
                        $awData[$k] = $value;
                        if ($value['atype'] == '2') {
                            $atype2 = $k;
                        }
                    }
                }

//                fileLog($newawList, 'arr.txt');
//                fileLog($arr, 'arr.txt');

                // 计算实际概率
                $data = $this->getRand($arr);

                $rid = $data['key'];

//                fileLog(array('rid' => $rid), 'arr.txt');

                // 奖品ID
                $aid = $awData[$rid]['id'];
                $level = $awData[$rid]['level'];

                // 判断中奖数量限制
                $winnum = $awData[$rid]['winnum'];
                if ($level == 1) { // 高价奖品次数判断
                    if ($hawNums && $awInfo['user']['status'] == '1') { // 高价奖品只能获取一次,除非为用户设置允许再次获取奖品
                        continue;
                    } else { // 允许中奖,但是,下次抽奖次数必须大于总抽奖次数
                        if ($awInfo['user']['status'] == '2' && $awInfo['user']['nextnum'] < $awInfo['user']['total_num']) {
                            continue;
                        }
                    }
                } else { // 普通奖品中奖次数判断
                    if ($winnum > 0) {
                        if (isset($awNums[$awData[$rid]['id']]) && $awNums[$aid] >= $winnum) {
                            continue;
                        }
                    }
                }

                // 日志数据
                if ($awData[$rid]['atype'] != 2) {
                    $log['uid'] = $request['uid'];
                    $log['uname'] = $request['nickname'];
                    $log['cid'] = $request['actid'];
                    $log['addtime'] = time();
                    $log['uniacid'] = $request['uniacid'];
                    $log['aid'] = $aid;
                    $log['aname'] = $awData[$rid]['name'];
                    $log['thumb'] = $awData[$rid]['thumb'];
                    $log['atype'] = $awData[$rid]['atype'];
                    $log['level'] = $awData[$rid]['level'];
                    $logArr[] = $log;

                    // 库存数据
                    if (isset($stockNum[$aid])) {
                        $stockNum[$aid] += 1;
                    } else {
                        $stockNum[$aid] = 1;
                    }

                    // 再来一次数据
                    if ($awData[$rid]['atype'] == 3) {
                        $onceNum += 1;
                    }
                }

                // 奖品信息 只显示普通商品信息（过滤谢谢关注，再来一次）
                if ($awData[$rid]['atype'] == 1) {
                    if (isset($rdata[$aid])) {
                        $rdata[$aid]['num'] += 1;
                    } else {
                        $rdata[$aid]['num'] = 1;
                        $rdata[$aid]['aname'] = $awData[$rid]['aname'];
                        $rdata[$aid]['thumb'] = $awData[$rid]['thumb'];
                    }
                }

                // 获取高价奖品
                if ($level == 1) {
                    $isHaveHigh = 1;
                }

                // 更新奖品库存数据(缓存)
                foreach ($awList as &$awval) {
                    if ($aid == $awval['id'] && $awval['stock'] > 0) {
                        $awval['stock'] -= 1;
                    }
                }
                $this->_redis->set($skey, json_encode($awList), C('DATA_CACHE_TIME'));
            }
            if ($logArr) {
                M('AwardLog')->addAll($logArr);
            }

            // 减少库存
            if ($stockNum) {
                foreach ($stockNum as $aid => $anum) {
                    M('Award')->where(array('id' => array('eq', $aid), 'stock' => array('gt', 0)))->setDec('stock', $anum);
                }
            }

            // 添加再来一次数据
            if ($onceNum) {
                if ($onceLogs) {
                    M('Oncemore')->where($lwhere)->setInc('num', $onceNum);
                } else {
                    $onceArr['uid'] = $request['uid'];
                    $onceArr['num'] = $onceNum;
                    $onceArr['cid'] = $request['actid'];
                    $onceArr['uniacid'] = $request['uniacid'];
                    M('Oncemore')->add($onceArr);
                }

                // 剩余抽奖次数
                $lastNum += $onceNum;
            }

            // 条件
            $uwhere['rid'] = array('eq', $request['actid']);
            $uwhere['uid'] = array('eq', $request['uid']);

            // 扣除抽奖机会次数 首先，查询再来一次数据；其次，查询投票的总数；
            $onceInfo = M('Oncemore')->where($lwhere)->field('num')->find();
            if ($onceInfo && $onceInfo['num'] >= $num) {
                M('Oncemore')->where($lwhere)->setDec('num', $num);
            } else {
                $decNum = $num * $awInfo['pervote'];
                M('Users')->where($uwhere)->setDec('vote_num', $decNum);
            }

            // 设置用户的下次允许抽中状态为不允许
            if ($isHaveHigh) {
                M('Users')->where($uwhere)->save(array('status' => 1));
            }

            // 更新用户总抽奖次数
            M('Users')->where($uwhere)->setInc('total_num', $num);
            
            // 获奖信息
            if ($rdata) {
                foreach ($rdata as $rv) {
                    $dmsg .= '<div class="drawall"><div class="drawallimg">';
                    $dmsg .= '<img src="' . $rv['thumb'] . '" /></div>';
                    $dmsg .= '<div class="dracenter">' . $rv['aname'] . '</div>';
                    $dmsg .= '<div class="draright">x' . $rv['num'] . '</div>';
                    $dmsg .= '</div>';
                }
            } else {
                $rid = $atype2;
                $dmsg .= '<div class="drawall" style="text-align: center;">谢谢关注，继续努力吆！</div>';
            }

            // 解锁
            flock($fp,LOCK_UN);
        } else {
            $dmsg .= '<div class="drawall" style="text-align: center;">您抽奖太快了！</div>';
        }

        // 关闭加锁处理
        fclose($fp);

        // 返回结果
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'rid' => $rid, 'data' => $dmsg, 'onceNum' => $onceNum, 'lastNum' => $lastNum));
        return;
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

    /**
     * 我的中奖列表
     **/
    public function awardLog() {
        $request = I("request.");

        // 获取我的邮寄地址信息
        $addInfo = M('Address')->where(array('uid' => array('eq', $request['uid'])))->find();

        // 获取用户信息
        $user = M('Users')->where(array('uid' => array('eq', $request['uid'])))->field('openid')->find();

        // 抽奖记录
        $where['cid'] = array('eq', $request['actid']);
        $where['uid'] = array('eq', $request['uid']);
        $where['aid'] = array('neq', 0);
        $where['aname'] = array('neq', 'thanks');
        $logs = M('AwardLog')->where($where)->field('aname,thumb')->select();

        // 获取活动顶部图片
        $actImg = M('ReplyBase')->where(array('actid' => array('eq', $request['actid'])))->find();

        // 查询活动是否过期
        $rwhere['rid'] = array('eq', $request['actid']);
        $fields = 'rid,starttime,endtime,pervote,uniacid';
        $actInfo = M('Reply')->where($rwhere)->field($fields)->find();

        // 返回数据
        $this->assign('homeUrl', substr($_SERVER['HTTP_HOST'], 5).'/app/index.php?i='.$actInfo['uniacid'].'&c=entry&rid='.$request['actid'].'&do=index&m=tyzm_diamondvote');
        $this->assign('awardUrl', '/Home/Index/index/actid/'.$request['actid'].'/openid/'.$user['openid']);
        $this->assign('myUrl', '/Home/Index/awardLog/uid/'.$request['uid'].'/actid/'.$request['actid']);
        $this->assign('homeClass', '');
        $this->assign('homePng', 'award');
        $this->assign('myClass', 'activebg');
        $this->assign('myPng', 'my_active');
        $this->assign('logs', $logs);
        $this->assign('addInfo', $addInfo);
        $this->assign('uid', $request['uid']);
        $this->assign('actid', $request['actid']);
        $this->assign('actImg', $actImg);
        $this->display('awardLog');
    }

    /**
     * 保存收件地址信息 is_mobile is_postcode
     **/
    public function saveAddress() {
        // 接收参数
        $request = I("request.");

        // 验证参数
        if (!$request['uid'] || !$request['name'] || !$request['tel'] || !$request['address']) {
            // 返回结果
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误'));
            return;
        }

        // 验证手机号码
        $isTrue = is_mobile($request['tel']);
        if (!$isTrue) {
            // 返回结果
            $this->ajaxReturn(array('code' => 202, 'msg' => '手机号码格式错误'));
            return;
        }

        // 验证邮政编码
        if ($request['postcode']) {
            $isPostcode = is_postcode($request['postcode']);
            if (!$isPostcode) {
                // 返回结果
                $this->ajaxReturn(array('code' => 203, 'msg' => '邮政编码格式错误'));
                return;
            }
        }

        // 查看是否已经设置地址信息
        $addInfo = M('Address')->where(array('uid' => array('eq', $request['uid'])))->find();
        if (empty($addInfo)) {
            $request['addtime'] = time();
            $is_succ = M('Address')->add($request);
            if ($is_succ) {
                $return = array('code' => 200, 'msg' => '添加成功');
            } else {
                $return = array('code' => 204, 'msg' => '添加失败');
            }
        } else {
            $save['tel'] = $request['tel'];
            $save['name'] = $request['name'];
            $save['address'] = $request['address'];
            $save['postcode'] = $request['postcode'];
            $is_succ = M('Address')->where(array('uid' => array('eq', $request['uid'])))->save($save);
            if ($is_succ) {
                $return = array('code' => 200, 'msg' => '更新成功');
            } else {
                $return = array('code' => 205, 'msg' => '信息未变化');
            }
        }

        // 返回结果
        $this->ajaxReturn($return);
        return;
    }
}