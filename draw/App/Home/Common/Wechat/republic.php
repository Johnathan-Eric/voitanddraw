<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
require_once 'authorizer_access_token.php';
class Republic extends  Common_util_pub{
	
	public function __construct($app=null){
		$authoid= new \AuthorizerAccessToken();
		$this->url="https://api.weixin.qq.com/wxa/release?access_token=".$authoid->getInfo();
	}
	
	public function republish(){
	    $data = $this->curls('{}',$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		return $data;
// 		if($data['errcode']===0){
// 			return $data['errmsg'];
// 		}else{
// 			//throw new \SDKRuntimeException($data['errmsg']);
// 		}
	}
}