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
class SmallroutineGetOpenid extends  Common_util_pub{

	public  function __construct(){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/sns/jscode2session?';
		$this->setParamters('access_token',$this->apicomment->getResult());
		$this->setParamters('appid', Commonconfig::$open_AppID);
		$this->setParamters('secret', Commonconfig::$open_AppID_Secrect);
		$this->setParamters('grant_type', 'authorization_code');
	}
	
	public function getResult($paramters){
	    $url=$this->url;
	    $url.='appid='.$this->paramters['appid'];
	    $url.='&secret='.$this->paramters['secret'];
	    $url.='&grant_type='.$this->paramters['grant_type'];
	    $url.='&js_code='.$paramters['js_code'];
	    $data = $this->curls('',$url,30, 1, 'json');
		$data = json_decode($data,true);
		if($data['errmsg']!='invalid code'){
			return $data;
		}else{
			throw new SDKRuntimeException($data['errmsg']);
		}
	}
}