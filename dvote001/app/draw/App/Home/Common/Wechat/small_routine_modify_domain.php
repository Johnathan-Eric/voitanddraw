<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
require_once 'authorizer_access_token.php';
class SmallroutineModifyDomain extends  Common_util_pub{
	
	public function __construct(){
		$authoid= new \AuthorizerAccessToken();
		$this->url="https://api.weixin.qq.com/wxa/modify_domain?access_token=".$authoid->getInfo();
		$this->setParamters('action', 'set');
		$this->setParamters('requestdomain',['https://'.Commonconfig::$modifydomain]);
		$this->setParamters('wsrequestdomain', ['wss://'.Commonconfig::$modifydomain]);
		$this->setParamters('uploaddomain', ['https://'.Commonconfig::$modifydomain]);
		$this->setParamters('downloaddomain',['https://'.Commonconfig::$modifydomain]);
		
	}
	
	public function modifyDomain(){
		$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		if($data['errcode']===0){
			return $data['errmsg'];
		}else{
			throw new \SDKRuntimeException($data['errmsg']);
		}
	}
}