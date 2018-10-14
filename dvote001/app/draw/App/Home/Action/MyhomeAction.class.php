<?php
/*
 * 会员类：个人信息/会员信息/我的订单/我的钱包/我的业绩/我的推荐
 * @author xuebs
 * @date 2018-08-29
 */
namespace Home\Action;
use Think\Action;
class MyhomeAction extends Action {
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
     * 首页
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

        // 根据请求参数对会员信息
        $fields = 'name,headimg,user_code,total_income,total_fee,total_money,grade,phone,position,email,weixinnum,company,address,comment';
        $data = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data)) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '用户信息错误！'));
            return;
        }

        // 获取权限信息
        $data['grade_info'] = '';
        if ($data['grade']) {
        	$gradeInfo = $this->_MemMod->getGradeInfo($data['grade'], 'info');
        	$data['grade_info'] = $gradeInfo['info'];
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 保存用户信息
     **/
    public function saveInfo() {
    	// 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['headimg'] || !$client_data['name'] || !$client_data['position'] || !$client_data['phone'] || !$client_data['email']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 根据请求参数对会员信息
        $fields = 'name';
        $data = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 整理数据
        $save['name'] = $client_data['name'];
        $save['headimg'] = $client_data['headimg'];
        $save['position'] = $client_data['position'];
        $save['phone'] = $client_data['phone'];
        $save['weixinnum'] = $client_data['weixinnum'];
        $save['email'] = $client_data['email'];
        $save['company'] = $client_data['company'];
        $save['address'] = $client_data['address'];
        $save['comment'] = $client_data['comment'];

        // 执行保存
        $res = $this->_MemMod->saveData(array('uid' => array('eq', $client_data['uid'])), $save);
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
     * 上传头像图片
     **/
    public function uploadHeadimg() {

    }

    /**
     * 会员升级列表
     **/
    public function gradeList() {
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
        $fields = 'grade,headimg,name';
        $data['info'] = $this->_MemMod->getInfo($client_data['uid'], $fields);
        if (empty($data)) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户信息错误！'));
            return;
        }

        // 获取会员等级列表
        $gwhere['buid'] = array('eq', $client_data['buid']);
        $gwhere['status'] = array('eq', 0);
        $gwhere['id'] = array('gt', $data['info']['grade']);
        $data['list'] = $this->_MemMod->getGradeList($gwhere, 'id,name,price,info,thumb');

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 我的推荐-老板
     **/
    public function recomList() {
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

        // 查询当前用户和其下级和下级的用户uid
        $uwhere['buid'] = array('eq', $client_data['buid']);
        $uwhere['type'] = array('eq', 1); // 员工
        $uwhere['_string'] = 'puid = '.$client_data['uid'].' or ppuid = '.$client_data['uid'];
        $data = $this->_MemMod->getList($uwhere, 'uid,name,total_income,gnum', $page);

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 添加员工
     **/
    public function addEmployee() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['name'] || !$client_data['phone'] || !$client_data['password']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 密码长度判断
        $passLen = strlen($client_data['password']);
        if ($passLen < 6) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '密码长度不能小于6位字符！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 整理数据
        $save['name'] = $client_data['name'];
        $save['phone'] = $client_data['phone'];
        $save['puid'] = $client_data['uid'];
        $save['password'] = makePass($client_data['password']);
        $save['type'] = 1;

        // 执行保存
        $res = $this->_MemMod->addData($save);
        if ($res) {
            $return = array('code' => 200, 'msg' => '添加成功');
        } else {
            $return = array('code' => 204, 'msg' => '添加失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }

    /**
     * 我的推荐-老板-游客列表
     **/
    public function touristList() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 分页
        $page = isset($client_data['page']) ? $client_data['page'] : 1;

        // 查询当前用户和其下级和下级的用户uid
        $uwhere['buid'] = array('eq', $client_data['buid']);
        $uwhere['type'] = array('eq', 0); // 游客
        $data = $this->_MemMod->getList($uwhere, 'uid,name,headimg', $page);

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 添加游客为我的员工
     **/
    public function addTourist() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['tuid'] || !$client_data['name'] || !$client_data['phone'] || !$client_data['password']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 未登录
        if (!$client_data['uid']) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '用户未登录！'));
            return;
        }

        // 密码长度判断
        $passLen = strlen($client_data['password']);
        if ($passLen < 6) {
            $this->ajaxReturn(array('code' => 202, 'msg' => '密码长度不能小于6位字符！'));
            return;
        }

        // 验证手机号码
        $is_true = is_telphone($client_data['phone']);
        if (!$is_true) {
            $this->ajaxReturn(array('code' => 203, 'msg' => '手机号码格式错误！'));
            return;
        }

        // 整理数据
        $save['name'] = $client_data['name'];
        $save['phone'] = $client_data['phone'];
        $save['puid'] = $client_data['uid'];
        $save['password'] = makePass($client_data['password']);
        $save['type'] = 1;

        // 执行保存
        $res = $this->_MemMod->saveData(array('uid' => array('eq', $client_data['tuid'])), $save);
        if ($res) {
            $return = array('code' => 200, 'msg' => '添加成功');
        } else {
            $return = array('code' => 204, 'msg' => '添加失败');
        }

        // 返回数据
        $this->ajaxReturn($return);
        return;
    }
}