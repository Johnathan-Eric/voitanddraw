<?php
/*
 * 微信付款逻辑代码
 * @autor wscsky
 */
namespace Common\Wechat;
//微信支付类
class WeixinPayWechat
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识
	protected $APPID = '';//填写您的appid。微信公众平台里的

	//拉起支付的小程序唯一标识
	protected $SUB_APPID = '';

	protected $APPSECRET = '';

	//服务商商ID，身份标识
	protected $MCHID = '';//服务商 商户id

	//收款商户ID，身份标识
	protected $SUB_MCHID = '';//收款 商户id

	//商户支付密钥Key
	protected $KEY = '';

	//签名算法
	protected $SIGN_TYPE = 'MD5';

	//回调通知接口
	protected $APPURL =   '';

	//交易类型
	protected $TRADETYPE = '';

	//微信支付接口
	protected $PAY_API = '';

	//商品类型信息
	protected $BODY = '';

	//商品附加信息 可带可不带
	protected $ATTACH = '';

	//支付实体
	protected $PARAMETEERS = '';

	//支付错误
	protected $ERROR = '';

	//微信支付类的构造函数
	function __construct($config = array())
	{
		$conf = new WeixinConfWechat($config);
		$this->APPID = $conf::APPID;
		$this->APPSECRET = $conf::APPSECRET;
		$this->SUB_APPID = $conf::SUB_APPID;
		$this->MCHID = $conf::MCHID;
		$this->SUB_MCHID = $conf::$SUB_MCHID;
		$this->KEY = $conf::KEY;
		$this->SIGN_TYPE = $conf::SIGN_TYPE;
		$this->APPURL = $conf::$APPURL;
		$this->TRADETYPE = $conf::TRADETYPE;
		$this->PAY_API = $conf::PAY_API;
		//禁止xml解析实体
		libxml_disable_entity_loader(true);
	}

	/**
	 * @param 下
	 * @return array  
	 * ["appId"] 	微信分配的公众账号ID
	 * ["timeStamp"] 交易时间
	 * ["nonceStr"] 微信返回的随机字符串
	 * ["package"] 就是 prepay_id，格式如 “prepay_id= prepay_id_item“。否则会导致错误。
	 * ["signType"] 加密方式
	 * ["paySign"] 对以上数据进行相应处理并加密
	 */

	//微信支付类向外暴露的支付接口
	public function pay($sub_openid,$outTradeNo,$totalFee,$goodsType='',$attach='')
	{
		$this->sub_openid = $sub_openid;//小程序下用户openid
		$this->outTradeNo = $outTradeNo; //商户系统内部订单号
		$this->totalFee = $totalFee; //总价
		$this->BODY = $goodsType; //商品类型信息
		$this->ATTACH = $attach; //附加信息
		$result = $this->weixinapp();
		$result['parameters'] = $this->PARAMETEERS;
		return $result;
	}

	//对微信统一下单接口返回的支付相关数据进行处理
	private function weixinapp()
	{
		$unifiedorder=$this->unifiedorder();
		$parameters = array();
		if (!is_array($unifiedorder)) 
		{
			$parameters['unifiedorder'] = $unifiedorder;
		}
		elseif ($unifiedorder['return_code']!='SUCCESS') 
		{
			$parameters['unifiedorder'] = $unifiedorder;
		}
		elseif ($unifiedorder['result_code']!='SUCCESS') 
		{
			$parameters['unifiedorder'] = $unifiedorder;
		}
		else
		{
			$timeStamp = time();
			$parameters=array(
				'appId'=>$this->SUB_APPID,//服务商appID
				'nonceStr'=>$this->createNoncestr(),//随机串
				'package'=>'prepay_id='.$unifiedorder['prepay_id'],//数据包
				'signType'=>'MD5',//签名方式
				'timeStamp'=>(string)$timeStamp//时间戳
			);
			$parameters['paySign']=$this->getSign($parameters);
			$parameters['unifiedorder'] = $unifiedorder;
		}
		return $parameters;
	}

	/*
	*请求微信统一下单接口
	*/
	private function unifiedorder()
	{
		$parameters = array(
			'appid' => $this->APPID,//服务商appid
			'attach' => $this->ATTACH,//备注信息
			'body' => $this->BODY, //商品信息
			'mch_id' => $this->MCHID,//服务商 商户id
			'nonce_str' => $this->createNoncestr(),//随机字符串
			'notify_url' => $this->APPURL, //通知地址
			'out_trade_no' => $this->outTradeNo,//商户订单编号
			'sign_type' => $this->SIGN_TYPE, //签名算法
			'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],//终端ip
			'sub_appid' => $this->SUB_APPID,//小程序id
			'sub_mch_id' => $this->SUB_MCHID,//收款 商户id
			'sub_openid' => $this->sub_openid,//用户openid
			'total_fee' => $this->totalFee,//floatval($this->totalFee), //总金额
			'trade_type' => $this->TRADETYPE,//交易类型
		);
		$parameters['sign'] = $this->getSign($parameters);
		$this->PARAMETEERS = $parameters;
		$xmlData = $this->arrayToXml($parameters);
		$xml_result = $this->postXmlCurl($xmlData, $this->PAY_API);
		$result = $this->xmlToArray($xml_result);
		return $result;
	}

	//数组转字符串方法
	protected function arrayToXml($arr)
	{
		$xml = "<xml>";
		foreach ($arr as $key=>$val)
		{
			$xml.="<".$key.">".$val."</".$key.">";
			/*if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}*/
		}
		$xml.="</xml>";
		return $xml;
	}

	//xml转数组
	protected function xmlToArray($xml)
	{
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	//发送xml请求方法
	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param int $second   url执行超时时间，默认30s
	 */

	
	private static function postXmlCurl($xml, $url, $second = 30)
	{
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
		//设置header
		$header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
		curl_setopt($ch, CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) 
		{
			curl_close($ch);
			return $data;
		}
		else 
		{
			$error = curl_errno($ch);
			curl_close($ch);
			return $error;
		}
	}

	/*
	* 对要发送到微信统一下单接口的数据进行签名
	*/
	protected function getSign($Obj)
	{
		foreach ($Obj as $k => $v)
		{
			if ($v) 
			{
				$Parameters[$k] = $v;
			}
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->KEY;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		return $result_;
	}

	/*
	*排序并格式化参数方法，签名时需要使用
	*/
	protected function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if($urlencode)
			{
				$v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0)
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}

	/*
	* 生成随机字符串方法
	*/
	protected function createNoncestr($length = 32 )
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ ) 
		{
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
}