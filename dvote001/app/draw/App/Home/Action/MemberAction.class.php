<?php
/*
 * 会员类：登录/注册/密码找回/手机验证码
 * @author xuebs
 * @date 2018-08-17
 */
namespace Home\Action;
use Think\Action;
require_once APP_PATH.'Home/Common/Wechat/small_routine_getopenid.php';
require_once APP_PATH.'Home/Common/Sms/Sms.php';

class MemberAction extends Action {
    /**
    * 接受客户端传过来的参数
    **/
    private $_ClientData = NULL;
    private $_Code = array('success' => 200, 'fail' => 402);
    private $_MemMod,$_SmsMod;

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
        $this->_SmsMod = D('Sms');
    }

    /**
     * 登录
     **/
    public function login() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['phone'] || !$client_data['password']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 根据请求参数对会员信息验证
        $fields = 'uid,user_code,name,phone,position,email,weixinnum,company,address,comment,grade,headimg,password';
        $info = $this->_MemMod->getInfoByphone($client_data['phone'], $fields);
        if (empty($info)) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '用户不存在！'));
            return;
        }
        
        // 密码验证
        $checkPass = makePass($client_data['password']);
        if ($checkPass != $info['password']) {
            $this->ajaxReturn(array('code' => 204, 'msg' => '登录密码错误！'));
            return;
        }
        unset($info['password']);

        // 登录成功返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '登录成功', 'data' => $info));
        return;
    }

    /**
     * 发送手机短信验证码 
     **/
    public function sendSms() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['phone']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 执行短信验证码发送
        $res = \Sms::sendYzm($client_data['phone']);
        if ($res) {
            $return = array('code' => 200, 'msg' => '发送成功', 'data' => $res);
        } else {
            $return = array('code' => 203, 'msg' => '发送失败');
        }
        
        // 登录成功返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 忘记密码
     **/
    public function forgetPassword() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['phone'] || !$client_data['password'] || !$client_data['verifyCode'] || !$client_data['password2']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 验证密码的长度
        $passLen = strlen($client_data['password']);
        if ($passLen < 6) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '密码长度不能小于6个字符！'));
            return;
        }

        // 验证2次密码是否一致
        if ($client_data['password'] != $client_data['password2']) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '两次密码不一致！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 204, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 验证用户是否存在
        $fields = 'uid';
        $info = $this->_MemMod->getInfoByphone($client_data['phone'], $fields);
        if (empty($info)) {
            $this->ajaxReturn(array('code' => 205, 'msg' => '用户不存在！'));
            return;
        }

        // 验证手机验证码是否存在
        $smsInfo = $this->_SmsMod->getInfoByphone($client_data['phone'], 'code');
        if (empty($smsInfo)) {
            $this->ajaxReturn(array('code' => 206, 'msg' => '短信验证码不存在！'));
            return;
        }

        // 验证验证码是否正确
        if ($smsInfo['code'] != $client_data['verifyCode']) {
            $this->ajaxReturn(array('code' => 206, 'msg' => '手机验证码错误！'));
            return;
        }

        // 执行密码修改
        $newPass = makePass($client_data['password']);
        $is_succ = $this->_MemMod->saveData(array('phone' => array('eq', $client_data['phone'])), array('password' => $newPass));
        if ($is_succ) {
            $return = array('code' => 200, 'msg' => '修改成功');
        } else {
            $return = array('code' => 203, 'msg' => '修改失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 注册
     **/
    public function register() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['phone'] || !$client_data['password'] || !$client_data['verifyCode'] || !$client_data['password2'] || !$client_data['name'] || !$client_data['inviteCode']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 验证密码的长度
        $passLen = strlen($client_data['password']);
        if ($passLen < 6) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '密码长度不能小于6个字符！'));
            return;
        }

        // 验证2次密码是否一致
        if ($client_data['password'] != $client_data['password2']) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '两次密码不一致！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 204, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 验证用户是否存在
        $fields = 'uid';
        $info = $this->_MemMod->getInfoByphone($client_data['phone'], $fields);
        if (!empty($info)) {
            $this->ajaxReturn(array('code' => 205, 'msg' => '用户已注册！'));
            return;
        }

        // 验证手机验证码是否存在
        $smsInfo = $this->_SmsMod->getInfoByphone($client_data['phone'], 'code');
        if (empty($smsInfo)) {
            $this->ajaxReturn(array('code' => 206, 'msg' => '手机验证码不存在！'));
            return;
        }

        // 验证验证码是否正确
        if ($smsInfo['code'] != $client_data['verifyCode']) {
            $this->ajaxReturn(array('code' => 207, 'msg' => '手机验证码错误！'));
            return;
        }

        // 验证邀请码
        $uInfo = $this->_MemMod->getInfoByCode($client_data['inviteCode'], 'uid,puid');
        if (empty($uInfo)) {
            $this->ajaxReturn(array('code' => 208, 'msg' => '邀请码不存在！'));
            return;
        }

        // 执行注册
        $add['name'] = $client_data['name'];
        $add['phone'] = $client_data['phone'];
        $add['password'] = makePass($client_data['password']);
        $add['puid'] = $uInfo['uid'];
        $add['ppuid'] = $uInfo['puid'];
        $add['created_at'] = time();
        $add['user_code'] = randCode();
        $newUid = $this->_MemMod->addData($add);
        if ($newUid) {
            $return = array('code' => 200, 'msg' => '注册成功');
        } else {
            $return = array('code' => 209, 'msg' => '注册失败');
        }
        
        // 返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 获取openid
     **/
    public function getOpenid() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['js_code']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }
        $data['js_code'] = $client_data['js_code'];

        $getOpenid = new \SmallroutineGetOpenid();
        $res = $getOpenid->getResult($data);
        if ($res) {
            $return = array('code' => 200, 'msg' => '成功', 'data' => $res);
        } else {
            $return = array('code' => 202, 'msg' => '失败');
        }
        
        // 登录成功返回数据
        $this->ajaxReturn($return);
        return;
    }
}