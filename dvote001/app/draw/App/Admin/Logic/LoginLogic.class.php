<?php
//微信消息管理员组逻辑模块
namespace Admin\Logic;

class LoginLogic{
	public  function checkLogin($post){
	    $re = D('Merchant')->field('mobile,pwd')->where($post)->find();
	    if($re){
	       return $re;
	   }else{
	       return false;
	   } 
	}
}


