<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
class PreAuthCode extends  Common_util_pub{
	public $para;
	
	public  function __construct(){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$this->apicomment->getResult();
		$this->setParamters('component_appid', Commonconfig::$open_AppID);
		$this->para['component_appid']=Commonconfig::$open_AppID;
// 		$this->returnKey='pre_auth_code';
// 		$this->expire_time=500;
	}
	
	public function getpreAuthCode(){
		$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		if(isset($data['pre_auth_code'])){
			return $data['pre_auth_code'];
		}else{
			new SDKRuntimeException('获取预授权码失败!');
		}
	}

}