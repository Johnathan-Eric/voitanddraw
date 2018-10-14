<?php
/*
 * 会员类：通讯录
 * @author xuebs
 * @date 2018-08-31
 */
namespace Home\Action;
use Think\Action;
class MaillistAction extends Action {
    /**
    * 接受客户端传过来的参数
    **/
    private $_ClientData = NULL;
    private $_Code = array('success' => 200, 'fail' => 402);
    private $_MemMod,$profitType;

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
        $this->profitType = C('profitType');
    }

    /**
     * 我的合伙人（下级）
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

        // 查询当前用户和其下级和下级的用户uid
        $uwhere['buid'] = array('eq', $client_data['buid']);
        $uwhere['puid'] = array('eq', $client_data['uid']); // 下级
        $data = $this->_MemMod->getList($uwhere, 'uid,name,total_income,gnum', $page);

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 我的粉丝（下下级）
     **/
    public function fansList() {
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
        $uwhere['ppuid'] = array('eq', $client_data['uid']); // 下下级
        $data = $this->_MemMod->getList($uwhere, 'uid,name,total_income,gnum', $page);

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 会员收入列表
     **/
    public function assetsList($exData = array()) {
        // 针对导出判断
        if (empty($exData)) {
            // 接收参数
            $client_data = $this->_ClientData;

            // 验证参数
            if (!$client_data['buid'] || !$client_data['uid']) {
                $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
                return;
            }

            // 分页
            $page = isset($client_data['page']) ? $client_data['page'] : 1;
        } else {
            $client_data = $exData;
            $page = 0;
        }
        
        // 根据请求参数对会员信息
        $fields = 'total_income,headimg';
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

        // 返回导出数据
        if (!empty($exData)) {
            return $data['list'];
        }

        // 返回数据
        $this->ajaxReturn(array('code' => 200, 'msg' => '成功', 'data' => $data));
        return;
    }

    /**
     * 导出收益列表
     **/
    public function exportData() {
        // 接收参数
        $client_data = $this->_ClientData;

        // 验证参数
        if (!$client_data['buid'] || !$client_data['uid']) {
            $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
            return;
        }

        // 获取数据
        $data = $this->assetsList($client_data);
        if (!empty($data)) {
            // 处理数据
            $lastData = array();
            foreach ($data as $value) {
                $tmp = array();
                $tmp['uname'] = $value['uname'];
                $tmp['ptype'] = $value['ptype'];
                $tmp['price'] = '¥'.$value['price'];
                $tmp['pamount'] = $value['mtype'].'¥'.$value['pamount'];
                $tmp['dateline'] = $value['dateline'];
                $lastData[] = $tmp;
            }

            // 执行导出
            if (!empty($lastData)) {
                $excelModel = D("Common/Excel","Logic");
                $filename = '收入明细';
                $excelConf = array(
                    array('headname'=>'姓名'),
                    array('headname'=>'收入方式'),
                    array('headname'=>'商品价格'),
                    array('headname'=>'收益金额'),
                    array('headname'=>'日期'),
                );
                $excelModel->getExcel($filename, $newData, $excelConf);
            }
        } else {
            // 返回数据
            $this->ajaxReturn(array('code' => 202, 'msg' => '暂无收入明细！'));
            return;
        }
    }
}