<?php
//管理员登陆
namespace Admin\Action;
defined("THINK_PATH") or die();
use Think\Action;
class UserAction extends Action {

    /*
     * 该方法会在加载该Action自动执行
     */
    function _initialize(){

    }

    /**
     *
     */
    function index(){
        $this->login();
    }

    /**
     * 会员登陆
     * @author wscksy
 *
     */
    function login(){
        //如果已经登陆转到首页
        $admin = session('admin');
        $reload = I("reload");
        if($reload){
            D("Admin","Logic")->logout();
        }

        if(IS_AJAX){
            if($admin && $admin->id !=0){
                echo json_encode(array('status'=>1,'info'=>'已登录','url'=>U("Index/index")));
                exit();
            }
        }  else {
            if($admin && $admin->id !=0){
                $this->redirect(U("Index/index"));
            }
        }

        //登陆操作
        if(IS_POST){
            $user = D("Admin","Logic");
            if(M()->autoCheckToken($_POST)){
                    $uname 	= $this -> _post("uname");
                    $password	= $this -> _post("password");
                    $verify_code= $this -> _post("verify");
                    $result 	= $user->login($uname,$password,$verify_code);
                    //登陆失败
                    if($result['status'] === false){
                        echo json_encode(array('status'=>2,'info'=>$result['msg']));
                        exit();
                    }else{
                        echo json_encode(array('status'=>1,'info'=>'已登录','url'=>U("Index/index")));
                        exit();
                    }
            }else{
                    $this->assign("error", "访问验证有误,请刷新页面后登陆!");
            }
        }
        $this->assign('page_title', '登录');
        $this->assign("referer",I("referer"));
        $this->display('minilogin');
    }

    /*
     * 会员登出
     * @author wscsky
     */
    function logout(){
        $user 	= D("Admin","Logic");
        $result = $user -> logout();
        $this -> success(L("LOGOUT_SUCCESS"),U("Index/index"));
    }


    /**
     * 用户已被锁定
     *
     */
    function islock(){
        $member = session('member');
        if(!$member || $member->uid == 0){
                redirect(U("User/login"));exit();
        }else{
                if($member->status == 1){
                        redirect(U("Index/index"));exit();		
                }
        }
        $this->assign("uname", $member -> uname);
        $this->display();
    }
}
