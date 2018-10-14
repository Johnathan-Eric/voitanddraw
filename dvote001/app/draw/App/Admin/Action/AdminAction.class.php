<?php
//管理员管理
namespace Admin\Action;
defined("THINK_PATH") or die("error");
use Think\AdminBaseAction;
class AdminAction extends AdminBaseAction{
	
	protected $adminMod;
    // protected $distriMod;
    private $pagesize;
	
	function _initialize(){
        $this->adminMod = D("Admin","Logic");
        // $this->distriMod = D("Distributor","Logic");
        $this->pagesize = 10;
	}
        
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 角色管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     *   */
    public function role_index(){
        $request = I("request.");
        $curr  = empty($request['curr']) ? 1 : intval($request['curr']);
        $filter = array();
        $request['uname'] != "" && $filter['r.role_name|r.rid'] = array('like',"%".$request['uname']."%");
        $request['d_type'] != "" && $filter['r.d_type'] = array('eq',$request['d_type']);
        $date  = $this->adminMod->getRoleLists($filter, $curr, $this->pagesize);
        $this->assign('page_title','角色管理');
        $this->assign('page_header','数据列表');
        $this->assign('_total', $date['total']);
        $this->assign('_curr', $curr);
        $this->assign('_limit', $this->pagesize);
        $this->assign('lists',$date['lists']);
        $this->assign('requ',$request);
        $this->display('role_index');
    }
        
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     *   */
    public function role_add(){
        $request = I("request.");
        if(IS_POST){
            $re = $this->adminMod->save_role($request);
            if($re){
                $return = array(
                    'status' => 1,
                    'msg'    => "操作成功"
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => $this->adminMod->_error
                );
            }
            echo json_encode($return);
            die();
        } else {
            // $dis_lists = $this->distriMod->getAllLists();
            // $this->assign('dlists',$dis_lists);
            $this->display('role_view');
        }
    }
        
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     **/
    public function role_edit(){
        $request = I("request.");
        $info = $this->adminMod->getRoleInfo($request['id']);
        if(IS_POST){
            $re = $this->adminMod->save_role($request);
            if($re){
                $return = array(
                    'status' => 1,
                    'msg'    => "操作成功"
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => $this->adminMod->_error
                );
            }
            echo json_encode($return);
            die();
        } else {
            // $dis_lists = $this->distriMod->getAllLists();
            // $this->assign('dlists',$dis_lists);
            $this->assign('info',$info);
            $this->display('role_view');
        }
    }
        
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     **/
    public function role_allot(){
        $request = I("request.");
        $filter = array();
        //$request['d_type'] != "" && $filter['d_type'] = array('eq',$request['d_type']);
        if($request['raids'])
        {
            $idArr = explode(',', $request['raids']);
            $this->assign('idarr',$idArr);
        }else{
            if($request['rid'])
            {
                $info = $this->adminMod->getRoleInfo($request['rid']);
                if($info){
                    if($info['role_auth_ids'] == 'all'){
                        $idArr = $info['role_auth_ids'];
                    } else {
                        $idArr = explode(',', $info['role_auth_ids']);
                    }
                    $this->assign('idarr',$idArr);
                }
            }
        }
        if(IS_POST){
            $re = $this->adminMod->saveRoleInfo($request['ids']);
            if($re){
                $return = array(
                    'status' => 1,
                    'date' => $re
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            die();
        } else {
            $lists = $this->adminMod->getRoleAuthLists($filter);
            $this->assign('lists',$lists);
            $this->display('role_allot');
        }
    }
        
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     *   */
    public function user_index(){
        $admin = session('admin');
        $request = I("request.");
        $curr  = empty($request['curr']) ? 1 : intval($request['curr']);
        $filter = array();
        $filter['a.is_del'] = array('eq',0);
        // $request['uname'] != "" && $filter['a.uname|a.aid|a.real_name'] = array('like',"%".$request['uname']."%");
        // $request['rid'] != "" && $filter['r.rid'] = array('eq',$request['rid']);
        $date  = $this->adminMod->getLists($filter, $curr, $this->pagesize);
        $this->assign('page_title','管理员管理');
        $this->assign('page_header','数据列表');
        $this->assign('_total', $date['total']);
        $this->assign('_curr', $curr);
        $this->assign('_limit', $this->pagesize);
        $this->assign('lists',$date['lists']);
        $this->assign('requ',$request);
        $this->display('user_index');
    }
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     *   */
    public function user_add(){
        $admin = session('admin');
        // if($admin->ds_id < 0){
        //     $error = array(
        //         'error' => 1,
        //         'msg'   => "请配置商户设置，并通过总店审核才能添加分销商账号"
        //     );
        //     echo json_encode($error);
        //     die();
        // }
        $request = I("request.");
        if(IS_POST){
            $re = $this->adminMod->save($request);
            if($re){
                $return = array(
                    'status' => 1,
                    'msg'    => "操作成功"
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => $this->adminMod->_error
                );
            }
            echo json_encode($return);
            die();
        } else {
            $roleList = $this->adminMod->getRoleListsByDtype();
            $this->assign('roleList',$roleList);
            $this->display('user_view');
        }
    }
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 员工管理
     * @param:
     * @write_time(创建时间): 2018-5-21
     **/
    public function user_edit(){
        $request = I("request.");
        $filter = array();
        $request['id'] != "" && $filter['a.id'] = array('eq',$request['id']);
        $info = $this->adminMod->getInfo($filter);
        if(IS_POST){
            $re = $this->adminMod->save($request);
            if($re){
                $return = array(
                    'status' => 1,
                    'msg'    => "编辑成功"
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => $this->adminMod->_error
                );
            }
            echo json_encode($return);
            die();
        } else {
            $roleList = $this->adminMod->getRoleListsByDtype();
            $this->assign('roleList',$roleList);
            $this->assign('info',$info);
            $this->display('user_view');
        }
    }
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): YuDingXuan
     * @deprecated(简单说明): 分配角色
     * @param:
     * @write_time(创建时间): 2018-5-21
     **/
    public function user_role(){
        $request = I("request.");
        $filter = array();
        $request['id'] != "" && $filter['a.aid'] = array('eq',$request['id']);
        $info = $this->adminMod->getInfo($filter);
        if(IS_POST){
            unset($request['id']);
            $re = $this->adminMod->edit_role($request);
            if($re){
                $return = array(
                    'status' => 1,
                    'msg'    => "编辑成功"
                );
            }else{
                $return = array(
                    'status' => 2,
                    'msg'    => $this->adminMod->_error
                );
            }
            echo json_encode($return);
            die();
        } else {
            if($info['ds_id'] == 0){
                $d_type = 1;
            }else{
                $d_type = 2;
            }
            $role_lists = $this->adminMod->getRoleListsByDtype($d_type);
            $this->assign('rlists',$role_lists);
            $this->assign('info',$info);
            $this->display('acc_role');
        }
    }
        
	/**
	 * 删除用户
	 * @author watchman
	 */
	function user_del(){
            $id 	= I('id', 0, "intval");
            $model 	= D("Admin");
            $val   = I('nval','1','int');
            $field = I('ntype','is_del','trim');
            $data = array($field=>$val);
            $data['status'] = 0;
            $re = $model->where(array('id'=>$id))->save($data);
            if($re !== false){
                $date = array("status"=>1,"msg"=>'操作成功');
            }else{
                $date = array("status"=>2,"msg"=>'操作失败');
            }
            echo json_encode($date);die;
	}
	
	
	/**
	 * 删除部门
	 * @author watchman
	 */
	function role_del(){
	    $id 	= I('id', 0, "intval");
	    $model 	= D("Admin");
	    $result	= $model->where(array('rid'=>$id,'status'=>1))->count();
	    if($result > 0){
	        $date = array("state"=>2,"message"=>'删除失败,此部门还存在人员');
	    }else{
	        $re	= M('Admin_role')->where('rid = %d', $id)->delete();
	        if($result == 0 && $re !== false){
	           $date = array("status"=>1,"msg"=>'删除成功');
	        }else{
	           $date = array("status"=>2,"msg"=>'操作失败');
	        }
	    }
	    echo json_encode($date);die;
	}

	
	/**
	 * 用于远程检查用户是否存在
	 * @author watchman
	 */
	function check_uname()
	{
            $uname = I('uname','','trim');
            $back = array('valid'=>true, 'msg'=>'用户名可用');
            if(isset($uname) && !empty($uname)){
                    $result = $this->model->get_info(0, $uname);
                    if ($result){
                            $back['valid'] = false;
                            $back['msg'] = '该用户已存在';
                    }
            }
            echo json_encode($back);
	}
        
     /**
	 * ajax修改信息
	 */
	public function ajax_update(){
            $id = I('id');
            if(!$this->check_access($id)){
                echo json_encode(array("error"=>2,"message"=>'无权操作'));die;
            }
            if($id > 0){
                $val   = I('nval','0','int');
                $field = I('ntype','status','trim');
                $data = array($field=>$val);
                $admin = M('admin');
                $admin->where('aid = %d', $id)->save($data);
                $date = array("error"=>0,"message"=>'操作成功');
            }else{
                $date = array("error"=>2,"message"=>'操作失败');
            }
            echo json_encode($date);die;
	}
        
     /**
	 * ajax修改信息
	 */
	public function check_access($aid){
            $admin = session('admin');
            $info = $this->model->get_info($aid);
            if(!$info){
                return FALSE;
            }
            switch ($admin->gid){
                case '1':
                    if($info['gid'] == 1){
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                    break;
                case '2':
                    if($info['gid'] <= 2){
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                    break;
                case '6':
                    if($info['gid'] == 1){
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                    break;
                case '7':
                    if($info['gid'] == 1){
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                    break;
                default :
                    return FALSE;
                    break;
            }
	}
        
        /**
         * @version infomation(版本信息): v1.0
         * @author(作者): YuDingXuan
         * @deprecated(简单说明): 员工管理
         * @param:
         * @write_time(创建时间): 2018-5-21
         *   */
        public function store_index(){
            $admin = session('admin');
            if($admin->ds_id != 0){
                echo "只有总店，才能操作";
                die();
            }
            $request = I("request.");
            $curr  = empty($request['curr']) ? 1 : intval($request['curr']);
            $this->pagesize = 10;
            $filter = array();
            $filter['a.is_del'] = array('eq',0);
            $filter['a.is_store'] = array('eq',1);
            $request['uname'] != "" && $filter['a.uname|a.aid|a.real_name'] = array('like',"%".$request['uname']."%");
            $request['rid'] != "" && $filter['r.rid'] = array('eq',$request['rid']);
                        //判断代理商如果是就查询分销商是否包含在这个代理商内
            if($admin->is_agency == 1){
                $reids = empty($admin->reids) ? 0 : $admin->reids;
                $filter['d.province'] = array('exp', " IN (".$reids.") OR a.ds_id = -1");
            }
            $date  = $this->adminMod->getLists($filter, $curr, $this->pagesize);
            $this->assign('page_title','店长管理');
            $this->assign('page_header','数据列表');
            $this->assign('_total', $date['total']);
            $this->assign('_curr', $curr);
            $this->assign('_limit', $this->pagesize);
            $this->assign('lists',$date['lists']);
            $this->assign('requ',$request);
            $this->assign('is_store',1);
            $this->display('Admin/user_index');
        }
        
        /**
         * @version infomation(版本信息): v1.0
         * @author(作者): YuDingXuan
         * @deprecated(简单说明): 员工管理
         * @param:
         * @write_time(创建时间): 2018-5-21
         *   */
        public function store_save(){
            $admin = session('admin');
            if($admin->ds_id != 0){
                $error = array(
                    'error' => 1,
                    'msg'   => "只有总店，才能操作"
                );
                echo json_encode($error);
                die();
            }
            $request = I("request.");
            if(IS_POST){
                $date = $request;
                $date['d_type'] = 2;
                $date['rid'] = '5';
                $date['ds_id'] = '-1';
                $date['is_store'] = 1;
                $date['d_name'] = $request['d_name'];
                $re = $this->adminMod->save($date);
                if($re){
                    $return = array(
                        'status' => 1,
                        'msg'    => "操作成功"
                    );
                }else{
                    $return = array(
                        'status' => 2,
                        'msg'    => $this->adminMod->error
                    );
                }
                echo json_encode($return);
                die();
            } else {
                $info = $this->adminMod->getInfo(array('aid'=>$request['id']));
                $this->assign('info',$info);
                $this->assign('s_save',TRUE);
                $this->display('user_view');
            }
        }
        
        /**
	 * 删除用户
	 * @author watchman
	 */
	function store_del(){
            $id 	= I('id', 0, "intval");
            $model 	= D("Admin");
            $val   = I('nval','1','int');
            $field = I('ntype','is_del','trim');
            $data = array($field=>$val);
            $data['status'] = 0;
            $re = $model->where(array('aid'=>$id))->save($data);
            if($re !== false){
                $date = array("status"=>1,"msg"=>'操作成功');
            }else{
                $date = array("status"=>2,"msg"=>'操作失败');
            }
            echo json_encode($date);die;
	}
        
}
