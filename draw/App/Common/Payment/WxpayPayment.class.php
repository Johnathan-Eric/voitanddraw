<?php
//微信支付方式
//@author wscsky
namespace Common\Payment;
use Common\Payment;
class WxpayPayment extends Payment{

	var $_gateway   = 'https://api.mch.weixin.qq.com/pay/unifiedorder';       //外部处理网关
    var $_code      = 'wxpay';       							   			  //付方式唯一标识
    var $_charset	= 'utf-8';    											  //编码
    var $_params	= array();
    var $returnParameters = array(); //验证返回数据
    
    protected $_wxpay_return 	= "https://api.mch.weixin.qq.com/secapi/pay/refund";	//退款网关
    protected $_wxpay_cert 		= "./Public/cert/zooyoo12d1snd1#qqd/apiclient_cert.pem";			//证书
    protected $_wxpay_key 		= "./Public/cert/zooyoo12d1snd1#qqd/apiclient_key.pem";				//公钥
    
    //支付接口所要的文件或函数
    var $_import = array(
		//"Common.Payment.Wcpay.CommonUtil"
	);
    
   /*返回支付订单表单HTML代码
   *    @author    wscsky
   *    @param     array $order_info
   *    @param     string $method
   *    @param	   string $submit_name
   *    @return    HTML
   */
  function bulidPaymentForm($order_info, $config = array()){
  	$member = session('member');
  	$trade_type = in_array($config['trade_type'], array("NATIVE","JSAPI","APP")) ? $config['trade_type'] : "JSAPI";
  	$this->_params = array(
  			'openid'			=> $member->openid,
  			'body'           	=> $this->_get_subject($order_info),
  			'out_trade_no'      => $this->_get_trade_sn($order_info),
  			'total_fee'         => intval($order_info['order_money']*100),   //需要支付金额
  			'notify_url'        => $this->_create_notify_url($order_info),
  			'trade_type'		=> $trade_type,									//交易类型
  			'spbill_create_ip'	=> $_SERVER['REMOTE_ADDR'],					//终端ip
  	);
  	//\Think\Log::log("bulidPaymentForm", "支付信息", $this->_params);
  	//读取预支付信息
  	$prepayData = $this->getPrepay();
  	switch ($trade_type){
  		case "NATIVE":	//招码支付
  			return $prepayData;
  			break;
  		case "APP":
  			return $prepayData;
  			break;
  		default:
  			//组合JSAPI数据
  			return $this->getJSAPI_Parameters($prepayData);
  			break;
  		}
  }
  
  /**
   * 执行退款操作
   * @param array $pay_log 支付记录
   * @param array $order_info 订单数据
   * @author wscsky
   */
  function refund($pay_log = array(), $order_info = array()){
  	empty($order_info) && $order_info = D("Common/Orders", "Logic")->get_orders_info($return_data['order_id']);
  	$r_money = $pay_log['return_money'] ? $pay_log['return_money'] : $pay_log['money']; 
  	$refund_data = array(
  			'appid'				=> $this->payment_config['appid'],
  			'mch_id'           	=> $this->payment_config['mch_id'],
  			'nonce_str'      	=> $this->create_noncestr(),
  			'out_trade_no'		=> $pay_log['out_trade_sn'],
  			'out_refund_no'		=> 'R'.$pay_log['out_trade_sn'],
  			'total_fee'         => intval($pay_log['money']*100),   //订单金额
  			'refund_fee'        => intval($r_money*100),            //退款金额
  			'op_user_id'        => $this->payment_config['mch_id'],
  	);
  	//签名
  	$refund_data['sign'] = $this->getSign($refund_data);
  	$rdata = $this->xmlToArray($this->curl_post_ssl($this->_wxpay_return, $this->arrayToXml($refund_data)));
  	$data  = array();
  	if($rdata['return_code'] == "SUCCESS"){
  		if($rdata['result_code'] == "SUCCESS"){
  			$data = array(
  			        'status' => 2,
  			        'refund_remark'      => '微信退款成功',
  			        'refund_data' => array(
  					     'id' 	       => $rdata['refund_id'],
  					     'status'	   => 1,
  			             'time'        => time(),
  			        ),
  				);
  			//公司现金流数据记录日志
  			$cash_log = array(
				'uid'			=> $order_info['uid'],
				'uname'			=> get_uname($order_info['uid'],UTYPE_MEMBER),
				'entity_type'	=> ENTITY_TYPE_ORDER,
				'entity_id'		=> $order_info['order_id'],
				'payment'		=> "微信支付",
				'paytime'		=> time(),
				'remark'		=> '订单编号:'.$order_info['order_sn']." 微信退款",
  			);
  			D("Common/Cash","Logic") -> add_log($r_money, MODE_TYPE_DEC, $cash_log);
  		}else{
  		    $data = array(
  		        'status' => 3,
  		        'refund_remak' => "{$rdata['err_code']}:{$rdata['err_code_des']}",
  		        'refund_data' => array(
  		            'id' 	       => $rdata['refund_id'],
  		            'status'	   => 2,
  		            'time'        => time(),
  		        ),
  		    );
  		}
  	}else{
            if($rdata){
                $data = array(
                    'status'       => 3,
                    'refund_remak' =>"微信退款API调用失败:{$rdata['return_msg']}",
                    'refund_data'  => array(
                        'id' 	       => $rdata['refund_id'],
                        'status'	   => 2,
                        'time'        => time(),
                    ),
                );
                \Think\Log::log("wxpay_refund", "微信退款API调用失败:{$rdata['return_msg']}", $rdata);
            }  else {
                $data = array(
                    'status'       => 4,
                    'refund_remak' =>"微信退款数据错误，无法退款"
                );
                \Think\Log::log("wxpay_refund", "微信退款数据错误，无法退款");
            }
  	}
  	if(!empty($data)){
  	    $data['refund_data'] = my_json_encode_pro($data['refund_data']);
  		M('payment_log') -> where("id = %d", $pay_log['id'])->save($data);
  	}
  	return $data;
 }
  
  /**
   * 微信签名SSL连接
   * @param string $url
   * @param array $vars
   * @param int $second
   * @param array $aHeader
   * @return mixed|boolean
   */
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
  		\Think\Log::log("wxpay:curl_post_ssl", "退款失败:调用微信接口出错", $error);
  		curl_close($ch);
  		return false;
  	}
  }
  
  /**
   * 创建扫码支付交易
   * @author wscsky
   */
  function create_native_trade($data = array()){
  	if(empty($data) || !array_key_exists("openid", $data) || !array_key_exists("product_id", $data))
  	{
  		self::$error = "回调数据异常";
  		return false;
  	}
  	$openid 	= $data["openid"];
  	$order_sn 	= $data["product_id"];
  	//读取支付数据
  	
  	$order_info = $this->get_order_info($order_sn);
  	if(!$order_info) return false;
  	$trade_data = $this->create_payment_trade($order_info);
  	if(!$trade_data) return false;
  	$trade_info = $trade_data['data'];
  	$this->_params = array(
  			'openid'			=> $openid,
  			'body'           	=> $this->_get_subject($trade_info),
  			'out_trade_no'      => $this->_get_trade_sn($trade_info),
  			'total_fee'         => intval($trade_info['order_money']*100),   //需要支付金额
  			'notify_url'        => $this->_create_notify_url($trade_info),
  			'trade_type'		=> "NATIVE",									//交易类型
  			'spbill_create_ip'	=> $_SERVER['REMOTE_ADDR'],					//终端ip
  			'time_start'		=> date("YmdHis"),
  			'time_expire'		=> date("YmdHis", time() + 600),
  	);
  	
  	\Think\Log::log("bulidPaymentForm", "支付信息", $this->_params);
  	//读取预支付信息
  	$prepayData = $this->getPrepay();
  	\Think\Log::log("bulidPaymentForm", "predata", $prepayData);
  	return $prepayData;
  }

  /**
   * 扫码支付生成二维码
   */
  function getQrcode_pay($order_info = array()){
  	$data = array(
  			"appid"			=> $this->payment_config['appid'],
  			'mch_id' 		=> $this->payment_config['mch_id'],
  			'nonce_str'		=> $this->create_noncestr(),
  			'product_id'	=> $order_info['entity_sn'],
  			'time_stamp'	=> time(),
  		);
  	$data['sign'] = $this->getSign($data);
  	$xmlData = $this->arrayToXml($this->_params);
  	$qrdata = "weixin://wxpay/bizpayurl?appid={$data['appid']}&mch_id={$data['mch_id']}&nonce_str={$data['nonce_str']}&product_id={$data['product_id']}&time_stamp={$data['time_stamp']}&sign={$data['sign']}";
  	return $qrdata;
  }
  
  /**   微信支付返回通知结果
   *    @author    wscsky
   *    @param     array $order_info
   *    @param     bool  $focre
   *    @return    array
   */
  function verify_notify($payment_log, $focre = false)
  {
  	//\Think\Log::log("wxpay","verify_notify",$payment_log);
  	if($focre === false){
	  	$xml 	= $GLOBALS['HTTP_RAW_POST_DATA'];
	  	$rdata 	= $this->xmlToArray($xml);
	  	//\Think\Log::log("wxpay","rdata",$rdata);
	  	$notice = $this->checkSign($rdata);
	  	if($notice == FALSE){
	  		$this->setReturnParameter("return_code","FAIL");//返回状态码
	  		$this->setReturnParameter("return_msg","签名失败");//返回信息
	  	}else{
	  		$this->setReturnParameter("return_code","SUCCESS");//设置返回码
	  	}
	  	$returnXml = $this->returnXml();
	  	echo $returnXml;
	  	if($notice == false){
	  		\Think\Log::log("wxpay","微信回调验证失败",$rdata);
	  		return false;
	  	}
  	}else{
  		//强制执行
  		$member = session('member');
  		if($member->aid <= 0){
  			$rdata = array("uname"=> $member->uname, "aid"=>$member->aid,"remark"=>"后台手动操作更新支付状态");
  			self::$error = "你无权执行该操作";
			return false;  			
  		}
  	}

  	/*更新支付日志状态及把返回数据验证存入数据库*/
  	$data = array();
  	$data['notify_data'] = serialize($rdata);
  	$data['status'] = 1;
  	$payment_log['remark'] = "微信支付";
  	//更新支付
  	return $this->_update_payment($payment_log, $data);
  
  }
  
  /**
   * 读取预支付数据
   * @return array $prepay
   */
  function getPrepay(){
  	
  	$this->_params['appid'] 		= $this->payment_config['appid'];
  	$this->_params['mch_id'] 		= $this->payment_config['mch_id'];
  	$this->_params['nonce_str'] 	= $this->create_noncestr();
  	$this->_params["sign"] 			= $this->getSign($this->_params);//签名
  	
  	$xmlData = $this->arrayToXml($this->_params);
  	
  	$model 				= D("Common/Weixin", "Logic");
  	$xmlStr 			= $model->curl_http($this->_gateway,"POST",$xmlData);
  	if(!$xmlStr) return array();
  	$xml 				= new \SimpleXMLElement($xmlStr);
  	$data 				= array();
  	foreach ($xml as $key => $value) {
  		$data[$key] = strval($value);
  	}
  	if($data['result_code'] == "FAIL"){
  	    \Think\Log::log("wxpay","创建支付失败", $data);
  	}
  	return $data;
  }
  
  /**
   * 	作用：设置jsapi的参数
   */
  public function getJSAPI_Parameters($prepayData = array())
  {
  	$jsApiObj["appId"] 		= $this->payment_config['appid'];
  	$timeStamp 				= time();
  	$jsApiObj["timeStamp"] 	= "$timeStamp";
  	$jsApiObj["nonceStr"] 	= $this->create_noncestr();
  	$jsApiObj["package"] 	= "prepay_id=".$prepayData['prepay_id'];
  	$jsApiObj["signType"] 	= "MD5";
  	$jsApiObj["paySign"] 	= $this->getSign($jsApiObj);
  	$josnData	  	 		= json_encode($jsApiObj);
  	return $josnData;
  }
   
    
    /**
     *    验证签名是否可信
     *    
     *    @author    wscsky
     *    @param     array $notify
     *    @return    bool
     */
    function _verify_sign($notify)
    {
    	$local_sign = $this->_get_sign($notify);    
    	return ($local_sign == $notify['sign']);
    }
    
    /**
     * 微信支付参数trim
     * @param value
     * @return
     */
    function trimString($value){
    	$ret = null;
    	if (null != $value) {
    		$ret = $value;
    		if (strlen($ret) == 0) {
    			$ret = null;
    		}
    	}
    	return $ret;
    }
    
    function create_noncestr( $length = 32 ) {
    	$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    	$str ="";
    	for ( $i = 0; $i < $length; $i++ )  {
    		$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    		//$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    	}
    	return $str;
    }
   
    function formatQueryParaMap($paraMap, $urlencode){
    	$buff = "";
    	ksort($paraMap);
    	foreach ($paraMap as $k => $v){
    		if (null != $v && "null" != $v && "sign" != $k) {
    			if($urlencode){
    				$v = urlencode($v);
    			}
    			$buff .= $k . "=" . $v . "&";
    		}
    	}
    	$reqPar = "";
    	if (strlen($buff) > 0) {
    		$reqPar = substr($buff, 0, strlen($buff)-1);
    	}
    	return $reqPar;
    }
    
    
    function formatBizQueryParaMap($paraMap, $urlencode){
    	$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if(!$v) continue;
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = "";
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
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
     * 	将XML转为array
     */
    public function xmlToArray($xml)
    {
    	$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    	return $array_data;
    }
    
    function checkSign($data)
    {
    	$wxsign = $data['sign'];
    	unset($data['sign']);
    	$sign = $this->getSign($data);//本地签名
    	if ($wxsign == $sign) {
    		return TRUE;
    	}
    	return FALSE;
    }
    
    /**
     * 设置返回微信的xml数据
     */
    function setReturnParameter($parameter, $parameterValue)
    {
    	$this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }
    
    /**
     * 生成接口参数xml
     */
    function createXml()
    {
    	return $this->arrayToXml($this->returnParameters);
    }
    
    /**
     * 将xml数据返回微信
     */
    function returnXml()
    {
    	$returnXml = $this->createXml();
    	return $returnXml;
    }
    
    /**
     * 微信支付MD5签名
     * @param array $Obj
     * @return string
     */
    function getSign($Obj) {
    	$Parameters = array();
    	foreach ($Obj as $k => $v)
    	{
    		$Parameters[$k] = $v;
    	}
    	//签名步骤一：按字典序排序参数
    	ksort($Parameters);
    	$String = $this->formatBizQueryParaMap($Parameters, false);
    	//echo '【string1】'.$String.'</br>';
    	//签名步骤二：在string后加入KEY
    	$String = $String."&key=".$this->payment_config['appkey'];
    	//echo "【string2】".$String."</br>";
    	//签名步骤三：MD5加密
    	$String = md5($String);
    	//echo "【string3】 ".$String."</br>";
    	//签名步骤四：所有字符转为大写
    	$result = strtoupper($String);
    	//echo "【result】 ".$result."</br>";
    	return $result;
	}
	
	function _create_notify_url($pay_info)
	{
		$url =  U("/pay/wxnotify/".$pay_info['out_trade_sn'].WAPDM,"",true,true);
		$url =  str_replace("/index.php?s=", '', $url);
		return $url;
	}
	
}
