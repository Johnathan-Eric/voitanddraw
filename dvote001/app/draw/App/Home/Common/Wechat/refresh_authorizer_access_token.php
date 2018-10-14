<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'pre_auth_code.php';
require_once 'SDKRuntimeException.php';
class RefreshAuthorizerAccessToken extends  Common_util_pub{
	public  function __construct(){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$this->apicomment->getResult();
		$this->setParamters('component_appid', Commonconfig::$open_AppID);
		if(empty(session('authorizer_appid'))){
			throw new \SDKRuntimeException('缺少必须参数authorizer_appid!');
		}
		$this->setParamters('authorizer_appid',session('authorizer_appid'));
		$this->setRefreshToken(session('authorizer_appid'));
		$this->setParamters('authorizer_refresh_token',S($this->refreshaccesstoken));
		$this->returnKey='authorizer_access_token';
		$this->expire_time=7000;
	}
	
	public function refreshAuthorizerAccessToken(){
		$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		if(isset($data['authorizer_access_token']) && isset($data['authorizer_refresh_token'])){
			$this->setAccessToken($this->paramters['authorizer_appid']);
			$this->setRefreshToken($this->paramters['authorizer_appid']);
			S($this->accesstoken,$data['authorizer_access_token'],7000);
			S($this->refreshaccesstoken,$data['authorizer_refresh_token']);
		}else{
			throw new \SDKRuntimeException('刷新accesstoken失败!');
		}
		return true;
	}
	
}