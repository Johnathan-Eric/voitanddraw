<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
require_once 'authorizer_access_token.php';
class SmallroutineGetQrcode extends  Common_util_pub{
	
	public function __construct($appid=null){
		$authoid= new \AuthorizerAccessToken();
		$this->url="https://api.weixin.qq.com/wxa/get_qrcode?access_token=".$authoid->getInfo($appid);

		
	}
	
	public function getQrcode(){
		$data = $this->curls(null,$this->url,30, 0, null);
		//$data = json_decode($data,true);
		return $data;
	}
}