<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
/**
 * 获取小程序帐号基本信息
 * @项目名称 :project_name
 * @类名称 :ApiCommponentToken
 * @类描述  :
 * @创建人 :fql
 * @创建时间 :2017年8月13日下午2:05:15
 * @修改人 :fql
 * @修改时间 ：2017年8月13日下午2:05:15
 * @修改备注：
 * @version
 */
class Smallroutin extends  Common_util_pub{

	public  function __construct(){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$this->apicomment->getResult();
		$this->setParamters('component_appid', Commonconfig::$open_AppID);
		$this->setParamters('authorizer_appid', session('authorizer_appid'));
	}
	
	public function getResult(){
		$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		if(isset($data['authorizer_info'])){
			return $data['authorizer_info'];
		}else{
			throw new SDKRuntimeException($data['errmsg']);
		}
	}
}