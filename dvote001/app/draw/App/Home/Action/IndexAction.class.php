<?php
/*
 * 会员类：登录/注册/密码找回/手机验证码
 * @author xuebs
 * @date 2018-08-28
 */
namespace Home\Action;
use Think\Action;

class IndexAction extends Action {
    private $_awNum;

    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {
        $this->_awNum = 9;
    }

    /**
     * 首页
     **/
    public function index() {
        // 当前时间
        $nowTime = time();

        // 查询奖品数据
        $where['online'] = array('eq', 1);
        $where['stock'] = array('gt', 0);
        $fields = 'id,name,thumb';
        $order = 'listorder asc';
        $data['awList'] = M('Award')->where($where)->field($fields)->order($order)->limit(9)->select();
        $data['awTotal'] = count($data['awList']);

        $this->assign('data', $data);
        $this->display('index');
    }

    /**
     * 抽奖
     **/
    
}