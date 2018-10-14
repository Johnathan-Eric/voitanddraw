<?php
// 本类由系统自动生成，仅供测试用途
namespace Admin\Action;
defined("THINK_PATH") or die("error");
use Think\AdminBaseAction;
class IndexAction extends AdminBaseAction{
    
    public function index(){
        $admin = session('admin');
        $this->assign('user',(array)$admin);
        $this->display();
    }

    /**
     * 后台首页
     */
    public function main() {
    	$admin = session('admin');
        $this->assign('admin',(array)$admin);
        $this->assign('newHtml', 'yes');
        $this->display('main');
    }


    // 更换密码
    public function password() {
    	if (IS_POST) {
    		$oldpassword 	= I("oldpassword");
    		$password		= I("password");
    		$repassword		= I("repassword");
    		$verify			= I("verify");

    		if ($verify == "") {
    			$this->error(L('verify_empty'));
    		}
    		if ($password != $repassword) {
    			$this->error(L('password_repassword'));
    		}
    		$user = D("Admin","Logic");
    		$result = $user -> modify_password($oldpassword, $password, $verify);
    		if($result){
    			$this->success(L("do_success"));
    		}else{
    			$this->error($user->getError());
    		}
    	} else {
    		$this->display();
    	}
    }

    // 修改资料
    public function profile() {
       $admin = session('admin');
        if (IS_POST) {
            $oldpassword 	= I("oldpassword");
            $password		= I("password");
            $repassword		= I("repassword");
            if ($password != $repassword) {
                $this->error(L('password_repassword'));
            }
            $user = D("Admin","Logic");
            $result = $user -> modify_password($oldpassword, $password);
            if($result == '1'){
                $this->success($result['msg']);
            }  else {
                $this->error($result['msg']);
            }
    	} else {
            $this->assign('user', (array)$admin);
            $this->assign('newHtml', 'yes');
            $this->display('Index/profile');
    	}
    } 

    public function cache() {
    	dir_delete(RUNTIME_PATH . 'Cache');
    	dir_delete(RUNTIME_PATH . 'Data');
    	if (is_file(RUNTIME_PATH . '~runtime.php'))
    		@unlink(RUNTIME_PATH . '~runtime.php');
    	if (is_file(RUNTIME_PATH . '~allinone.php'))
    		@unlink(RUNTIME_PATH . '~allinone.php');
    	F("site_*",null);

    	\Think\Log::log("clear cache", "清除系统缓存");
    	$forward = $_GET['forward'] ? $_GET['forward'] : U('Index/main');
    	$this->assign('jumpUrl', $forward);
    	$this->success(L('do_success'));
    }


    /*
     * 查看系统日志
     */
    public function log()
    {
    	$member = session('member');

    	$limit = 20;					//每页显示
    	$type 	= I("type","","trim");	//日志类型
    	$Model   = D('Logs');
    	$map = array();
    	if($type) $map["type"]  = $type;
    	$opt = I("get.opt", "", "trim");
    	//清除操作
    	if(IS_POST && $opt != "search")
    	{
    		//查权限
    		if(!check_access("system_log_clear")){
    			$this->success("你无权操作删除系统日志");
    			exit();    			
    		}
    		$day 		= I("get.day",0,"intval");
    		if(!in_array($day, array(30,90)) && (APP_DEBUG && $day != 0)){
    			$this->error(L('DELETE_FAIL'));
    			exit();
    		}

    		if(APP_DEBUG && $day == 0){
    			$map['addtime'] = array("LT", time());
    		}else{
    		     $map['addtime'] = array("LT", time()-$day*86400);
    		}
    		$result  = $Model->where($map)->delete();
			if($result===false)
				$this->error(L('DELETE_FAIL'));
			else
			{
				\Think\Log::log("clear logs", ($day == 0 ? "清空所有日志" :"清除{$day}天前系统日志"));
				$this->success($day == 0 ? "清空所有日志成功" :"清除{$day}天前系统日志成功");
			}
			exit();
    	}


    	$sdate	= I("sdate","","trim");			//开始日期
    	$edate	= I("edate","","trim");			//结束日期
    	$keyword= I("keyword","","trim")==L('PLEASE_INPUT_KEYWORD')?'':I("keyword","","trim");		//关键字
    	$page	= I("p",1,"intval");
    	$page < 1 && $page = 1;
    	if($keyword) 		 $map['type|uname|message'] = array("like", "%{$keyword}%");
    	
    	if($sdate && $edate){
    		$map['addtime']	= array(array('egt',strtotime($sdate)),array('elt',strtotime($sdate." 23:59:59")),'and');
    	}elseif($sdate){
    		$map['addtime']	= array('egt',strtotime($sdate));
    	}elseif($edate){
    		$map['addtime']	= array('elt',strtotime($sdate." 23:59:59"));
    	}
    	$total = $Model->where($map)->count();
    	$logs = $Model -> where($map) -> page("$page,$limit") -> order('logid desc') ->select();
    	$Page = new \Think\Page($total, $limit);
    	$Page = page_config($Page);
    	$show = $Page->show();

    	$this->assign("show_logs_detail",C('show_logs_detail'));
    	$this->assign('keyword',	$keyword);
    	$this->assign('starttime',	$sdate);
    	$this->assign('endtime',	$edate);
    	$this->assign('page',		$show);
		$this->assign('logs',		$logs);
	    $this->display();
    }
    
    /*
     * 会员分享日志
    */
    public function sharelog()
    {
    	$member = session('member');
    
    	$limit = 20;					//每页显示
    	$type 	= I("type","","trim");	//日志类型
    	$Model  = D('ShareLog');
    	$map = array();
    	if($type) $map["type"]  = $type;
    
    	$sdate	= I("sdate","","trim");			//开始日期
    	$edate	= I("edate","","trim");			//结束日期
    	$keyword= I("keyword","","trim");		//关键字
    	$page	= I("p",1,"intval");
    	$page < 1 && $page = 1;
    	if($keyword) $map['uname|action'] = array("like", "%{$keyword}%");
    	 
    	if($sdate && $edate){
    		$map['logtime']	= array(array('egt',strtotime($sdate)),array('elt',strtotime($edate." 23:59:59")),'and');
    	}elseif($sdate){
    		$map['logtime']	= array('egt',strtotime($sdate));
    	}elseif($edate){
    		$map['logtime']	= array('elt',strtotime($edate." 23:59:59"));
    	}
    	$total = $Model->where($map)->count();
    	$logs = $Model ->where($map) -> page("$page,$limit") -> order('id desc') ->select();
    
    	//echo $Model->getLastSQL();
    
    	$Page = new \Think\Page($total, $limit);
    	$Page = page_config($Page);
    	$show = $Page->show();
    $this->assign("show_logs_detail",C('show_logs_detail'));
    	$this->assign("page_title","会员分享记录");
    	$this->assign('keyword',	$keyword);
    	$this->assign('starttime',	$sdate);
    	$this->assign('endtime',	$edate);
    	$this->assign("type", $type);
    	$this->assign('page',		$show);
    	$this->assign('logs',		$logs);
    	$this->display();
    }
    
    
    /**
     * 查查看菜单的权限
     * @param string $access
     * @return bool
     */
    private function check_access($access){
    	if(empty($access) || is_null($access)) return true;   
    	$_access = explode(",", $access);
    	foreach ($_access as $val){
    		if(check_access($val,null,true)) return true;
    	}  	
    }
}

