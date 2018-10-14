<?php
/*
 * 微信红包API
 * @author wscsky
 */
namespace Common\Api;
use Think;
class RedbagApi{
	protected $_error = NULL;			//记录错误信息
	protected $_wxpay_url  = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
	protected $_wxqy_pay   = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
	protected $_wxpay_cert = "./Public/cert/zooyoo12d1snd1#qqd/apiclient_cert.pem";
	protected $_wxpay_key  = "./Public/cert/zooyoo12d1snd1#qqd/apiclient_key.pem";
	
	
	/**
	 * 企业付款
	 * @param number $money  金额
	 * @param string $openid 收款人openID
	 */
	function wxpay($money, $openid, $log){
	    if(C('wx_fukuan_on') !=1){
	        $this->_error = "微信企业付款未开启";
	        return false;
	    }
	    if(!is_numeric($money) || !$openid){
	        $this->_error = "金额或用户信息有误";
	        return false;
	    }
	    //读取微信支付配置
	    $payment = D("Common/Payment","Logic") -> get_payment('wxpay');
	    if(!$payment || $payment['enabled'] != 1){
	        $this->_error = '微信支付未启用';
	        return false;
	    }
	    $data = array(
	        'mch_appid'        => $payment['config']['appid'],
	        'nonce_str'		   => md5(time()."wscsky"),
	        'mchid'            => $payment['config']['mch_id'],
	        'partner_trade_no' => $payment['config']['mch_id'].date("ymdHis").str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
	        'openid'           => $openid,
	        'check_name'       => 'NO_CHECK',
	        'amount'           => $money*100,
	        'desc'             => $log['desc'] ? $log['desc'] : C('site_title').'企业付款',
	        'spbill_create_ip' =>  get_client_ip(),
	    );
	    
	    $data['sign']  = $this -> getSign($data, $payment['config']['appkey']);
	    $postdata      = $this -> arrayToXml($data);
	    $rdata         = $this -> xml2array($this->curl_post_ssl($this->_wxqy_pay, $postdata));
	    if($rdata['result_code'] == "FAIL"){
	        $this->_error = "企业付款失败:".$rdata['err_code_des'];
	        return false;
	    }
	    return $rdata;
	}
	
	/**
	 * 微信支付MD5签名
	 * @param array $Obj
	 * @return string
	 */
	function getSign($Obj,$appkey) {
		$Parameters = array();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		$String = $String."&key=".$appkey;
		$String = md5($String);
		$result = strtoupper($String);
		return $result;
	}
	
	/**
	 * 签名前的数据组合
	 * @param array $paraMap
	 * @param bool $urlencode
	 * @return string
	 */
	function formatBizQueryParaMap($paraMap, $urlencode){
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if($urlencode)
			{
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = "";
		if (strlen($buff) > 0)
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	
		//以下两种方式需选择一种
	
		//第一种方法，cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,$this->_wxpay_cert);
		
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$this->_wxpay_key);
	
		//第二种方式，两个文件合成一个.pem文件
		//curl_setopt($ch,CURLOPT_SSLCERT,$this->_wxpay_cert);
	
		if( count($aHeader) >= 1 ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}
	
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		
		if($data){
			curl_close($ch);
			return $data;
		}
		else {
			$error = curl_errno($ch);
			\Think\Log::log("Redbag::curl_post_ssl", "赠送失败:调用微信接口出错", $error);
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * 数据转xml
	 * @param array $arr
	 */
	function arrayToXml($arr)
	{
		$xml = "<xml>";
		foreach ($arr as $key=>$val)
		{
			if (is_numeric($val))
			{
				$xml.="<".$key.">".$val."</".$key.">";
	
			}
			else
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
		}
		$xml.="</xml>";
		return $xml;
	}
	
	/**
	 * xml2array
	 */
	function xml2array($xml = ""){
		if(!$xml) return;
		$xml = new \SimpleXMLElement($xml);
		$data = array();
		foreach ($xml as $key => $value) {
			$data[$key] = strval($value);
		}
		return $data;
	}
   
	/**
	 * 获取错误信息
	 */
	function getError(){
		return $this->_error;
	}
}