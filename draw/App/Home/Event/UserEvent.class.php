<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Event;
//use Think\Controller;
class UserEvent{
	public function index(){
		echo "Hello, world!";
	}
	
    public function login(){
        echo 'login event';
    }
    public function logout(){
        echo 'logout event';
    }
}