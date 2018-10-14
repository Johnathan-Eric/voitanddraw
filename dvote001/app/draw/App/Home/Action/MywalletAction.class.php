<?php
/*
 * 会员类：我的钱包
 * @author xuebs
 * @date 2018-08-30
 */
namespace Home\Action;
use Think\Action;

class MywalletAction extends Action {
    /**
    * 接受客户端传过来的参数
    **/
    private $_ClientData = NULL;
    private $_Code = array('success' => 200, 'fail' => 402);
    private $_MemMod,$_GoodsMod,$_OrdersMod,$profitType;

    /**
     * 构造函数
     * 初始化数据
     **/
    public function _initialize() {
    	$client_data = file_get_contents("php://input");
    	if(!empty($client_data)) {
            try{
                $this->_ClientData = json_decode($client_data,TRUE);
                if(empty($this->_ClientData)) {
                    throw new \Think\Exception("Error Processing Request");
                }
            }catch(\Think\Exception $e){
                $ErrString = $e->getMessage();
                $this->ajaxReturn(array('code' => $this->_Code['fail'], 'msg' => $ErrString),'JSON');
                die();
            }
    	}
        $this->_MemMod = D('Member');
        $this->_GoodsMod = D('Goods');
        $this->_OrdersMod = D('Orders');
        $this->profitType = C('profitType');
    }

    /**
     * 钱包首页
     **/
    public function index() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 分页
        $page = isset($client_data['page']) ? $client_data['page'] : 1;

        // 根据请求参数对会员信息
        $fields = 'total_income,total_fee,total_money';
        $data['info'] = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data['info'])) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 收益方式
        $profitType = $this->profitType;
        $data['pTypeList'] = array();
        foreach ($profitType as $pid => $pname) {
            $tmp = array();
            $tmp['pid'] = $pid;
            $tmp['pname'] = $pname;
            $data['pTypeList'][] = $tmp;
        }

        // 查询用户标识
        $uids[] = $client_data['uid'];

        // 查询当前用户和其下级和下级的用户uid
        $uwhere['puid'] = array('eq', $client_data['uid']);
        $uwhere['ppuid'] = array('eq', $client_data['uid']);
        $uwhere['_logic'] = 'or';
        $uList = $this->_MemMod->getList($uwhere, 'uid', 0);
        if (!empty($uList)) {
            foreach ($uList as $uval) {
                if (!in_array($uval['uid'], $uids)) {
                    $uids[] = $uval['uid'];
                }
            }
        }

        // 收益数据列表
        $where['uid'] = array('in', $uids);
        $where['buid'] = array('eq', $client_data['buid']);
        $data['list'] = $this->_MemMod->getAssetsList($where,'uname,mtype,ptype,price,pamount,dateline', $page);
        if (!empty($data['list'])) {
            foreach ($data['list'] as &$value) {
                $value['mtype'] = $value['mtype'] == 1 ? '+' : '-';
                $value['ptype'] = $this->profitType[$value['ptype']];
                $value['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
            }
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 绑定银行卡
     **/
    public function bindBookcard() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['bank_name'] || !$client_data['bank_address'] || !$client_data['account_name'] || !$client_data['account_number']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 查询此卡是否已经绑定
        $bankInfo = $this->_MemMod->getBankInfo(array('account_number' => array('eq', $client_data['account_number'])), 'id');
        if (!empty($bankInfo)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '此银行卡已绑定！'));
            return;
        }

        // 银行卡数据
        $bank = $client_data;
        $bank['add_time'] = time();

        // 执行保存
        $res = $this->_MemMod->addBankData($bank);
        if ($res) {
            $return = array('code' => 200, 'msg' => '保存成功');
        } else {
            $return = array('code' => 203, 'msg' => '保存失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 提现累计
     **/
    public function withdrawList() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 分页
        $page = isset($client_data['page']) ? $client_data['page'] : 1;

        // 根据请求参数对会员信息
        $fields = 'total_income,total_fee,total_money';
        $data['info'] = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data['info'])) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 数据列表
        $where['uid'] = array('eq', $client_data['uid']);
        $where['buid'] = array('eq', $client_data['buid']);
        $data['list'] = $this->_MemMod->getWithdrawList($where,'amount,dateline', $page);
        if (!empty($data['list'])) {
            foreach ($data['list'] as &$value) {
                $value['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
            }
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 我的业绩-员工
     **/
    public function achieveList() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 分页
        $page = isset($client_data['page']) ? $client_data['page'] : 1;

        // 根据请求参数对会员信息
        $fields = 'total_income';
        $data['info'] = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data['info'])) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 查询用户标识
        $uids[] = $client_data['uid'];

        // 查询当前用户和其下级和下级的用户uid
        $uwhere['puid'] = array('eq', $client_data['uid']);
        $uwhere['ppuid'] = array('eq', $client_data['uid']);
        $uwhere['_logic'] = 'or';
        $uList = $this->_MemMod->getList($uwhere, 'uid', 0);
        if (!empty($uList)) {
            foreach ($uList as $uval) {
                if (!in_array($uval['uid'], $uids)) {
                    $uids[] = $uval['uid'];
                }
            }
        }

        // 收益数据列表
        $where['uid'] = array('in', $uids);
        $where['buid'] = array('eq', $client_data['buid']);
        $data['list'] = $this->_MemMod->getAssetsList($where,'uname,mtype,ptype,price,pamount,dateline', $page);
        if (!empty($data['list'])) {
            foreach ($data['list'] as &$value) {
                $value['mtype'] = $value['mtype'] == 1 ? '+' : '-';
                $value['ptype'] = $this->profitType[$value['ptype']];
                $value['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
            }
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 提现页面
     **/
    public function withdrawView() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 根据请求参数对会员信息
        $data['info'] = $this->_MemMod->getInfo($client_data['uid'], 'total_fee');
        if (empty($data['info'])) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 银行卡信息
        $data['bank'] = $this->_MemMod->getBankInfo(array('uid' => array('eq', $client_data['uid'])), 'bank_name,account_number');

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 提现
     **/
    public function doWithdraw() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['fee_num']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 判断提现余额 必须大于50rmb
        if ($client_data['fee_num'] < 50) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '提现金额不能小于50RMB！'));
            return;
        }

        // 根据请求参数对会员信息
        $info = $this->_MemMod->getInfo($client_data['uid'], 'total_fee,name');
        if (empty($info)) {
            $this->ajaxReturn(array('code' => 204, 'msg' => '用户信息错误！'));
            return;
        }

        // 判断提现金额是否足够
        if ($client_data['fee_num'] > $info['total_fee']) {
            $this->ajaxReturn(array('code' => 205, 'msg' => '提现金额不足！'));
            return;
        }

        // 提现数据
        $wdata['wcn'] = makeOrderSn(); // 提现编号
        $wdata['uid'] = $client_data['uid'];
        $wdata['buid'] = $client_data['buid'];
        $wdata['uname'] = $info['name'];
        $wdata['amount'] = $client_data['fee_num'];
        $wdata['dateline'] = time();

        // 执行保存
        $res = $this->_MemMod->addWithdrawData($wdata);
        if ($res) {
            $return = array('code' => 200, 'msg' => '提现成功');
        } else {
            $return = array('code' => 203, 'msg' => '提现失败');
        }
        
        // 返回数据
        $this->ajaxReturn($return);
        return;
    }
}