<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
require_once 'authorizer_access_token.php';
class SmallroutineCommitCode extends  Common_util_pub{
	
	public function __construct($app=null){
		//$extString='{"extAppid":"'.session('authorizer_appid').'"}';
	    $extString='{"extEnable": true,"extAppid":"'.session('authorizer_appid').'","ext":{"name":"'.session('authorizer_appid').'"}}';
		$authoid= new \AuthorizerAccessToken();
		$this->url="https://api.weixin.qq.com/wxa/commit?access_token=".$authoid->getInfo();
		$this->setParamters('template_id', empty($app['template_id'])?0:$app['template_id']);
		$this->setParamters('ext_json',empty($app['ext_json'])?$extString:$app['ext_json']);
		$this->setParamters('user_version', empty($app['user_version'])?'v1.0':$app['user_version']);
		$this->setParamters('user_desc',empty($app['user_desc'])?'test':$app['user_desc']);
		
	}
	
	public function commit(){
	    $data = $this->curls(json_encode($this->paramters,JSON_UNESCAPED_UNICODE),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		if($data['errcode']===0){
			return $data['errmsg'];
		}else{
		    return $data;
			//throw new \SDKRuntimeException($data['errmsg']);
		}
	}
}