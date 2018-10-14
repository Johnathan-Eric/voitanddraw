<?php
//会员管理
namespace Admin\Action;
use Think\AdminBaseAction;
class MemberAction extends AdminBaseAction{
    protected $model,$levList,$memType,$listRows,$admin;
    
    /**
     * 初始化设置
     */
    function _initialize() {
        $this->model = D("Member", "Logic");

        // 获取用户等级数据
        $gradeList = D('Common/Mgrade', 'Logic')->getList();
        if (!empty($gradeList['list'])) {
            foreach ($gradeList['list'] as $lval) {
                $this->levList[$lval['id']] = $lval['name'];
            }
        }

        // 用户类型
        $this->memType = C('memType');
        $this->listRows = 10; // 每页显示条数

        // 管理员信息
        $this->admin = session('admin');
    }
    
    //会员列表
    public function index($expData = array()) {
        $request = I("request.");

        // 每页显示条数
        $listRows = $this->listRows;

        //组合查询条件
        $where = array();

        //获取表单提交的数据
        $uname      = I("uname", '', 'trim');
        $reg_time   = I("reg_time", '', "trim");
        if (isset($expData['uname'])) {
            $uname = $expData['uname'];
            $listRows = 0;
        }
        if (isset($expData['reg_time'])) {
            $reg_time = $expData['reg_time'];
            $listRows = 0;
        }
        $uname  && $where['m.name'] = array('like',"%".$uname."%");
        $reg_time && $where['m.created_at'] = array('between', array(strtotime($reg_time), strtotime($reg_time)+24*60*60));

        //页码、每页显示记录条数
        $page   = I("curr", 1, "intval");
        $order = $reg_time ? " m.created_at ASC" : " m.created_at DESC";

        //统计及读取数据
        $where['type'] = array('in', array(2, 3));

        // 获取数据列表
        $data = $this->getList($where, $page, $listRows, $order);

        // 导出数据
        if (!$listRows) {
            return $data['lists'];
        }

        // 返回数据
        $this->assign('memType', $this->memType);
        $this->assign('levList', $this->levList);
        $this->assign('lists', $data['lists']);
        $this->assign('_curr', $page);
        $this->assign('_total', $data['total']);
        $this->assign('_limit', $listRows);
        $this->assign('page_title', '用户列表');
        $this->assign('page_header', '数据列表');
        $this->assign('newHtml', 'yes');
        $this->assign('request', $request);
        $this->display('index');
    }
    
    // 查看详情
    public function view() {
        $request = I("request.");
        if($request['uid']){
            $where = array('uid'=> array('eq', $request['uid']));
            $info = D('Member')->where($where)->find();
            if($info){
                $info['grade'] = $this->levList[$info['grade']];
                $info['type'] = $this->memType[$info['type']];

                // 获取上级信息
                $info['pname'] = '';
                if ($info['puid']) {
                    $pinfo = D('Member')->where(array('uid' => array('eq', $info['puid'])))->field('name')->find();
                    if ($pinfo) {
                        $info['pname'] = $pinfo['name'];
                    }
                } else {
                    $info['puid'] = '';
                }

                // 根据uids获取下级总数和下下级总数
                $lData = $this->getLData(array($request['uid']));
                $info['lNum'] = isset($lData['lNums'][$request['uid']]) ? $lData['lNums'][$request['uid']] : 0;
                $info['llNum'] = isset($lData['llNums'][$request['uid']]) ? $lData['llNums'][$request['uid']] : 0;

                ###### 获取该用户下的所有的员工信息
                // 分页
                $page  = empty($request['curr']) ? 1 : intval($request['curr']);
                $order = $reg_time ? " m.created_at ASC" : " m.created_at DESC";

                //统计及读取数据
                $dwhere['type'] = array('eq', 1);
                $dwhere['puid'] = array('eq', $request['uid']);
                $data = $this->getList($dwhere, $page, $listRows, $order);

                // 返回数据
                $this->assign('list',$data['lists']);
                $this->assign('info', $info);
                $this->assign('newHtml', 'yes');
                $this->assign('_curr', $page);
                $this->assign('_total', $data['total']);
                $this->assign('_limit', $this->listRows);
                $this->assign('request', $request);
                $this->display('member_views');
            }
        }
    }

    /**
     * 查看用户列表数据
     **/
    private function getList($where, $page, $listRows, $order) {
        // 查询数据
        $data = $this->model->getLists($where, $page, $listRows, $order);
        
        // 整理数据
        if ($data['lists']) {
            $uids = array();
            foreach ($data['lists'] as $val) {
                $uids[] = $val['uid'];
            }

            // 根据uids获取下级总数和下下级总数
            $lData = $this->getLData($uids);
            
            // 整理数据
            foreach ($data['lists'] as &$v) {
                $v['lNum'] = $v['llNum'] = '0';
                if (isset($lData['lNums'][$v['uid']])) {
                    $v['lNum'] = $lData['lNums'][$v['uid']];
                }
                if (isset($lData['llNums'][$v['uid']])) {
                    $v['llNum'] = $lData['llNums'][$v['uid']];
                }

                // 清除多余的数据
                unset($v['puid']);
                unset($v['ppuid']);
            }
        }

        return $data;
    }

    /**
     * 查看下级或下下级总数
     **/
    private function getLData($uids = array()) {
        // 返回数据
        $return = array(
            'lNums'     => 0, // 下级总数
            'llNums'    => 0 // 下下级总数
        );
        if (!$uids) {
            return $return;
        }

        // 查询条件
        $gwhere['puid'] = array('in', $uids);
        $gwhere['ppuid'] = array('in', $uids);
        $gwhere['_logic'] = 'OR';
        $fields = 'uid,puid,ppuid';
        $gdata = $this->model->getpuids($gwhere, $fields);
        if ($gdata) {
            foreach ($gdata as $gval) {
                if ($gval['puid']) {
                    if (isset($lNums[$gval['puid']])) {
                        $lNums[$gval['puid']] += 1;
                    } else {
                        $lNums[$gval['puid']] = 1;
                    }
                }
                
                if ($gval['ppuid']) {
                    if (isset($llNums[$gval['ppuid']])) {
                        $llNums[$gval['ppuid']] += 1;
                    } else {
                        $llNums[$gval['ppuid']] = 1;
                    }
                }
            }

            $return['lNums'] = $lNums;
            $return['llNums'] = $llNums;
        }
        return $return;
    }

    /**
     * ajax批量操作会员状态
     */
    public function ajax_update(){
        $ids = I('ids');
        if(count($ids) > 0){
            $val   = I('nval','1','int');
            $field = I('ntype','status','trim');
            $data = array($field=>$val);
            $Member = M('Member');
            if ($field == 'is_head') { // 首页只能显示一个推广会员的信息
                $mwhere['buid'] = array('eq', $this->admin->buid);
                $mwhere['is_head'] = array('eq', 2);
                $Member->where($mwhere)->save(array('is_head' => 1));
            }
            $Member->where(array('uid'=>array('in', $ids)))->save($data);
            $date = array("error"=>0,"message"=>'操作成功');
        }else{
            $date = array("error"=>2,"message"=>'操作失败');
        }

        echo json_encode($date);die;
    }

    //会员信息导出
    public function exportExcel() {
        // 请求参数
        $request = I("request.");
        $param['uname'] = $request['uname'];
        $param['reg_time'] = $request['reg_time'];
        $data = $this->index($param);
        if ($data) {
            foreach ($data as &$val) {
                $val['created_at'] = date('Y-m-d H:i:s', $val['created_at']);
                $val['status'] = $val['status'] == 1 ? '启用' : '关闭';
                $val['type'] = $this->memType[$val['type']];
                $val['grade'] = $this->levList[$val['grade']];
                $val['is_head'] = $val['is_head'] == 1 ? '否' : '是';
            }
        }

        // 执行导出
        $excelModel = D("Common/Excel","Logic");
        $filename = '用户信息';
        $excelConf = array(
            array('headname'=>'用户ID'),
            array('headname'=>'姓名'),
            array('headname'=>'手机号码'),
            array('headname'=>'区域'),
            array('headname'=>'注册时间'),
            array('headname'=>'启用状态'),
            array('headname'=>'类型'),
            array('headname'=>'等级'),
            array('headname'=>'总收入'),
            array('headname'=>'余额'),
            array('headname'=>'已提现'),
            array('headname'=>'是否设为首页'),
            array('headname'=>'下级总数'),
            array('headname'=>'下下级总数'),
        );
        $excelModel->getExcel($filename, $data, $excelConf);
    }
}
