<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'pre_auth_code.php';
require_once 'refresh_authorizer_access_token.php';
require_once 'SDKRuntimeException.php';
class AuthorizerAccessToken extends  Common_util_pub{
	public  function __construct($authorization_code=null){
		$this->apicomment = new ApiCommponentToken();
		$this->url='https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$this->apicomment->getResult();
		$this->setParamters('component_appid', Commonconfig::$open_AppID);
		$this->setParamters('authorization_code',$authorization_code);
		$this->returnKey='authorizer_access_token';
		$this->expire_time=7000;
	}
	
	public function getInfo($appid=null){
	    if(!empty($appid)){
	        session('authorizer_appid',$appid);
	    }
		$this->setAccessToken(session('authorizer_appid'));
		$this->setRefreshToken(session('authorizer_appid'));
		if(!empty($appid)){
		    $mini = D('mini_program')->where(array('appid'=>session('authorizer_appid')))->find();
		    if(!empty($mini)){
		        if(empty(S($mini['appid'].'_freshaccesstoken'))){
		            	  S($mini['appid'].'_freshaccesstoken',$mini['auth_refresh_token']);
		        }
		    }
		   
		}
		
		if(!S($this->refreshaccesstoken) && !S($this->accesstoken)){
			if(empty($this->paramters['authorization_code'])){
				throw new \SDKRuntimeException("缺少必须参数authorization_code!");
			}
			$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
			$data = json_decode($data,true);
			if(isset($data['authorization_info']) && isset($data['authorization_info']['authorizer_access_token'])){
				$this->setAccessToken($data['authorization_info']['authorizer_appid']);
				$this->setRefreshToken($data['authorization_info']['authorizer_appid']);
				S($this->refreshaccesstoken,$data['authorization_info']['authorizer_refresh_token']);
				S($this->accesstoken,$data['authorization_info']['authorizer_access_token'],7000); 
				session('refreshaccesstoken',$data['authorization_info']['authorizer_refresh_token']);
				session('authorizer_appid',$data['authorization_info']['authorizer_appid']);
				//session_commit();
				//print_r(session('appInfo'));echo '=====';exit;
				return S($this->accesstoken);
			}else{
				throw new SDKRuntimeException('获取accesstoken失败!');
			}
			
			
		}else{
			if(!S($this->accesstoken) && S($this->refreshaccesstoken)){
				$fresh = new RefreshAuthorizerAccessToken();
				$re = $fresh->refreshAuthorizerAccessToken();
				if($re) return S($this->accesstoken);
			}else{
				return S($this->accesstoken);
			}
		}
	}
	
	
}