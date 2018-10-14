<?php
defined('IN_IA') or exit('Access Denied');
load()->classs('weixin.account');
load()->func('communication');
class customservice extends WeiXinAccount {
	public $account = null;
	public function __construct($acid = '') {
		$this->account_api = self::create($acid);
		$this->account = $this->account_api->account;
	}

	public function getAccessToken() {
		return $this->account_api->getAccessToken();
	}
	//获取客服基本信息列表
	public function GetKflist() {
		$access_token = $this->getAccessToken();
		if (is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token={$access_token}";
		$response = $this->requestApi($url);
		if (is_error($response)) {
			return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
		}
		return $response;
	}

	//获取客服状态列表
	public function GetOnlineKflist() {
		$access_token = $this->getAccessToken();
		if (is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token={$access_token}";
		$response = $this->requestApi($url);
		return $response;
	}
	/**
	 * 创建客服会话
	 * @param array kf_account 完整客服帐号,openid 粉丝的openid
	 * @return array
	 */
	public function KfsessionCreate($data) {
		$access_token = $this->getAccessToken();
		if (is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/customservice/kfsession/create?access_token={$access_token}";
		$data = json_encode($data);
		$response = $this->requestApi($url, $data);
		if ($response['errno'] != 0) {
			return error(-1, $this->CustomErrorCode($response['errno']));
		}
		return true;
	}
	/**
	 * 关闭会话
	 * @param array kf_account 完整客服帐号,openid 粉丝的openid
	 * @return array
	 */
	public function KfsessionClose($data) {
		$access_token = $this->getAccessToken();
		if (is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/customservice/kfsession/close?access_token={$access_token}";
		$data = json_encode($data);
		$response = $this->requestApi($url, $data);
		if ($response['errno'] != 0) {
			return error(-1, $this->CustomErrorCode($response['errno']));
		}
		return true;
	}
	/**
	 * 获取客户会话状态
	 * @param str openid 粉丝的openid 
	 * @return array 如果不存在，则kf_account为空。
	 */
	public function GetSession($openid) {
		$access_token = $this->getAccessToken();
		if (is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/customservice/kfsession/getsession?access_token={$access_token}&openid={$openid}";
		$response = $this->requestApi($url);
		if ($response['errno'] != 0) {
			return $this->CustomErrorCode($response['errno']);
		}
		return $response;
	}

	public function CustomErrorCode($errcode) {
		$errors = array(
			'65400' => 'API不可用，即没有开通/升级到新版客服功能',
			'65401' => '无效的客服帐号',
			'65402' => '帐号尚未绑定微信号，不能投入使用',
			'65413' => '不存在对应用户的会话信息',
			'65414' => '客户正在被其他客服接待',
			'40003' => '非法的openid'
		);
		return $errors[$errcode] ? $errors[$errcode] : false;
	}
}
