<?php
/**
 * 用户提现
 * @author wscsky
 *
 */
namespace Home\Action;
defined("THINK_PATH") or die();
use Think\BaseAction;
class WithdrawAction extends BaseAction {
	
	/**
	 * 权限设置
	 * @see \Think\Action::accessRules()
	 */
	function accessRules(){
		return array(
			
		);
		
	}
	function _initialize(){
		$this->menuid = 181;
	}
	
	/**
	 * 我的资料 
	 */
	public function index(){	
		$this->assign("page_title","资金提现");
 		$this->display();
    }
    
   
    
}

