<?php
require_once 'SDKRuntimeException.php';
class Common_util_pub{
	protected $paramters;
	protected $url;
	protected $returnKey; //微信接口返回的关键字
	protected $expire_time;//返回结果过期时间
	protected $accesstoken;
	protected $refreshaccesstoken;
	
	public function setParamters($key,$value){
		$this->paramters[$key]=$value;
	}
	
	public function setAccessToken($authappid){
		$this->accesstoken=$authappid."_accesstoken";
	}
	
	public function setRefreshToken($authappid){
		$this->refreshaccesstoken=$authappid."_freshaccesstoken";
	}
	public function combileAccessToken($authappid){
		return $authappid."_accesstoken";
	}
	
	public function combileRefreshToken($authappid){
		return $authappid."_freshaccesstoken";
	}
	
	/**
	 * 发起curl请求
	 * @param unknown $data
	 * @param unknown $url
	 * @param number $second
	 * @param unknown $ispost
	 * @param unknown $data_type
	 * @return unknown|boolean
	 */
	function curls($data, $url, $second = 30, $ispost, $data_type) {
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		switch ($ispost) {
			case 0:
				break;
			case 1:
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
			default :
				break;
		}
		switch ($data_type) {
			case "json":
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json;charset=utf-8',
				'Content-Length: ' . strlen($data))
				);
				break;
			default :
				break;
		}
		
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error" . "<br>";
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * 获取接口结果
	 * @return boolean|mixed|object
	 */
	public function getResult(){
		if(!S($this->returnKey)){
			$data = $this->curls(json_encode($this->paramters),$this->url,30, 1, 'json');
			$return=json_decode($data,true);
			if(isset($return[$this->returnKey])){
				if(!empty($this->expire_time)){
					S($this->returnKey,$return[$this->returnKey],$this->expire_time);
				}else{
					S($this->returnKey,$return[$this->returnKey]);
				}
				
			}else{
				throw new SDKRuntimeException($return['errmsg']);
			}
		}
		return S($this->returnKey);
		
	}
	
}