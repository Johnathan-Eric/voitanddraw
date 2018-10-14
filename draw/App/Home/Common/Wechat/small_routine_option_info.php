<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
/**
 * 设置授权方的选项信息
 * @项目名称 :project_name
 * @类名称 :SmallroutinOptionInfo
 * @类描述  :
 * @创建人 :fql
 * @创建时间 :2017年8月13日下午2:05:15
 * @修改人 :fql
 * @修改时间 ：2017年8月13日下午2:05:15
 * @修改备注：
 * @version
 */
class SmallroutinOptionInfo extends  Common_util_pub{

	public  function __construct(){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/cgi-bin/component/ api_get_authorizer_option?component_access_token='.$this->apicomment->getResult();
		$this->setParamters('component_appid', Commonconfig::$open_AppID);
		$this->setParamters('authorizer_appid', session('authorizer_appid'));
	}
	
	public function getResult($key,$value){
		$this->setParamters($key, $value);
		$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
		$data = json_decode($data,true);
		print_r($data);
		if($data['errcode']===0){
			return $data['errmsg'];
		}else{
			throw new \SDKRuntimeException($data['errmsg']);
		}
	}
}