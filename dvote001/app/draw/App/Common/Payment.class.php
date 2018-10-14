<?php
// +----------------------------------------------------------------------
// | Author: wscsky <wscsky@qq.com>
// +----------------------------------------------------------------------
namespace Common;
/**
 * Dage 支付方式类
 * 实现了ORM和ActiveRecords模式
 */
class Payment{

	static  $error 			= "";             //记录最后一次的错误信息
	public $_gateway		= "";             //支付网关
	public $payment_info	= NULL;           //支付方式信息,从数据库里读取出来的数据
	public $payment_id		= 0;              //支付方式ID,payment表主键
	public $payment_code 	= NULL;           //支付方式代码如:Alipay
	public $payment_name 	= NULL;           //支付方式名称如:支付宝
	public $payment_config 	= NULL;           //支付方式参数:数组
	
	/**
	 * 实例化支付方式
	 * @param mix 	$payment_code 支付方式代码或者支付方式ID
	 * @return class;
	 */
	 static public function factory($payment_code){
		//加载支付方式参数
		$payment = D("Common/Payment","Logic")->get_payment($payment_code,is_numeric($payment_code),false,array("online"=>1,"enabled"=>1));		
		if(!$payment) return false;		
		//加载支付方式类文件
		$payment_code = ucwords($payment['payment_code']);
		$class_name = "\\Common\\Payment\\".$payment_code."Payment"; 
		$class = new $class_name();
		//初始化支付方式
		self::payment_init($payment, $class);
		return $class;		
	}
        
        /**
	 * 新的支付实例化
	 * @param mix 	$payment 支付配置
	 * @return class;
	 */
        
        static public function newIni($payment)
        {
            $class_name = "\\Common\\Payment\WxpayPayment"; 
            $class = new $class_name();
            //初始化支付方式
            self::payment_init($payment, $class);		
            return $class;	
        }


        /**
	 * 把数据转成xml
	 * @param unknown_type $data
	 * @return string
	 */
	function toxml($data){
		$xml = "<xml>";
		foreach ($data as $key=>$val)
		{
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}
	
	/**
	 * 支付方式参数的初始化
	 * @param array $payment_info 支付参数
	 * @param objec $class 支付方式类
	 */
	static function payment_init($payment_info = array(),&$class)
	{
	    $class -> payment_info		= $payment_info;
	    $class -> payment_id		= $payment_info['payment_id'];
	    $class -> payment_name		= $payment_info['payment_name'];
	    $class -> payment_code 		= $payment_info['payment_code'];
	    $class -> payment_config	= is_array($payment_info['config']) ? $payment_info['config'] : unserialize($payment_info['config']);
	    unset($class -> payment_info['config']);
	    //加载支付需要的文件
	    if(!empty($class->_import)){
	        foreach ($class->_import as $file){
	            if(is_array($file)){
	                $name 		= array_shift($file);
	                $baseUrl	= array_shift($file);
	                $ext		= array_shift($file);
	                empty($ext) && $ext = EXT;
	                import($name, $baseUrl, $ext);
	            }else{
	                import($file);
	            }
	        }
	    }
	}
	
	/**
	 * 创建支付交易
	 * @param array $order_info
	 * @return array $trade_info
	 * @author wscsky
	 */
	function create_payment_trade($order_info = array(), $payment_id = 0, $config = array()){
		if(empty($order_info) || !is_array($order_info)){
			self::$error = "订单数据为空,创建交易失败!";		
			return false;
		}
		$data = array(
				'utype'			=> $order_info['utype'],										//用户类型
				'uid'			=> $order_info['uid'],                                          //用户UID
				'shop_id'		=> intval($order_info['shop_id']),								//店铺ID
				'entity_type'	=> $order_info['entity_type'],                                   //订单类型
				'entity_id'		=> $order_info['entity_id'],                                     //订单号
				'entity_sn' 	=> $order_info['entity_sn'],                                     //订单ID
				'order_ids'		=> serialize($order_info['order_ids']),                         //多个订单ID数据				
				'order_money'	=> $order_info['order_money'],                                  //订单金额
				'add_time'		=> time(),                                                      //
				'callback'		=> is_string($order_info['callback']) ? $order_info['callback']:serialize($order_info['callback']),
				 
		);
		if(isset($order_info['payment_name'])) 	$data['payment_name'] = $order_info['payment_name'];
		if(isset($order_info['payment_id'])) 	$data['payment_id'] = $order_info['payment_id'];
		$model = M("payment_trade");
		/*查是否已存在,存在则更新*/
		$map = array(
			'uid' 			=> $data['uid'],
			'entity_type'	=> $data['entity_type'],
			'entity_id'		=> $data['entity_id'],
			'entity_sn' 	=> $data['entity_sn'],
			//'order_money'	=> $data['money'],
		);
		$_data = $model->where($map)->find();
		
		/*写入支付交易表*/
		if($_data == false or $_data['status'] == 1){
			$result 				= $model->add($data);
			$data['trade_id'] 		= $result;
		}else{
			$result = $model->where('trade_id = %d',$_data['trade_id'])->save($data);
			$data['trade_id'] = $_data['trade_id'];
		}
	
		if($result === false){
			self::$error = "生成支付交易数据失败!";
			return false;
		}
		 
		$order_info['trade_id']	= $data['trade_id'];
	
		$trade_data = array(
				'id'  => $data['trade_id'],
				'url' => $this->get_payment_trade_url($data),
				'data' => $data,
		);
		//如果选择了支付方式,刚直接到支付页面
		if($payment_id != 0){
			$pay_class 	= self::factory($payment_id);
			$html 		= $pay_class->bulidPaymentForm($order_info, $config);
			$trade_data['html'] = $html;
		}
		
		//如果自动跳转
		if($config['jump']){
			redirect($trade_data['url']);
		}
		return $trade_data;
	}
	
	/**
	 * 读取支付交易信息
	 * @author wscsky
	 * @param int $trade_id
	 * @return array $trade_info
	 *
	 */
	static function get_payment_trade($trade_id=0){
	    if($trade_id==0 or !ctype_digit(strval($trade_id))){
	        self::$error = '支付信息读取失败!';
	        return false;
	    }
	    $model = M('payment_trade');
	    $data = $model->where("trade_id = %d",$trade_id)->find();
	    return $data;
	}
	
	/**
	 * 设置交易的跳转URL
	 * @author wscsky
	 * @param array $trade_data
	 * @return string $url
	 */
	
	function get_payment_trade_url($trade_data=array()){
		if(empty($trade_data) or !is_array($trade_data)){
			self::$error = '交易数据为空,生成支付URL失败!';
			return false;
		}
	
		//依不同的支付方式设置跳支付的页面
		switch($trade_data['order_type']){
			case ENTITY_TYPE_ORDER: //会员充值订单
				$URL = U("pay/wxpay/".$trade_data['entity_id'],'',true,false);
				break;
			default:
				$URL = U("pay/wxpay/".$trade_data['entity_id'],'',true,false);
				break;
		}
		return $URL;
	}
	
	/*
	 * 读取支付日志信息
	*    @param   	string $out_trade_sn
	*    @return    array()
	*    @author    wscsky
	*/
	static function get_payment_log($out_trade_sn=null){
	    if($out_trade_sn===null) return false;
	    $modle = M("payment_log");
	    $data = $modle->where("out_trade_sn ='%s'",$out_trade_sn)->find();
	    if($data==null){
	        return false;
	    }
	    $data['append'] 		= unserialize($data['append']);
	    $data['notify_data']	= unserialize($data['notify_data']);
	    return $data;
	}
	
	/**
	 *    获取通知信息
	 *    @author    wscsky
	 *    @return    array
	 */
	function _get_notify()
	{
		/* 如果有POST的数据，则认为POST的数据是通知内容 */
		if (!empty($_POST))
		{
			return $_POST;
		}
		/* 否则就认为是GET的 */
		return $_GET;
	}
	
	/**
	 *    获取支付表单,不同支付方式需要重写该方法
	 *    @author    wscsky
	 *    @param     array $order_info
	 *    @return    array
	 */
	function get_payform()
	{
	    return $this->_create_payform('POST');
	}
	
	/**
	 *    获取商品简介
	 *    @author    wscsky
	 *    @param     array $order_info
	 *    @return    string
	 */
	function _get_subject($order_info)
	{
		if(isset($order_info['entity_sn'])){
			return $order_info['entity_sn'];
		}else{
			return create_sn($order_info['entity_type']);
    	}
	}
	
	/**
	 * 读取信息数据
	 * @param string $order_sn 订单编号
	 */
	function get_order_info($order_sn){
		$entity = substr($order_sn, 0, 2);
		switch (intval($entity)){
			case ENTITY_TYPE_CORDER:	//券订单
				$data = M("coupon_orders")->where("order_sn = '%s'", $order_sn)->find();
				//\Think\Log::log("order_info", "sql", M("coupon_orders")->getLastSQL());
				if(!$data){
					self::$error = "订单不存在,请重新发起支付！";
					return false;
				}
				if($data['pay_status'] != 0){
					self::$error = "该订单已经支付过了";
					return false;
				}
				if($data['status'] != 0){
					self::$error = "该订单状态已变，请刷新订单！";
					return false;
				}
				//整合交易需要的数据
				$trande_info = array(
						'shop_id'		=> 0,
						'utype'			=> UTYPE_AGENT,
						'uid'			=> $data['agent_id'],
						'entity_type'	=> ENTITY_TYPE_CORDER,                            //订单类型
						'entity_id'		=> $data['order_id'],                                 //订单ID
						'entity_sn' 	=> $data['order_sn'],                                 //订单SN
						'order_money'	=> $data['amount'],                                  //充值金额
						'callback'		=> array('fun'=>'\Common\Logic\CouponLogic::pay_back'),//支付成功回调
						'payment_name'	=> $this->payment_name,
						'payment_id'	=> $this->payment_id,
				);
				M("coupon_orders")->where("order_id = %d", $data['order_id'])->save(array("payment_id"=> $this->payment_id,'payment_name'=>$this->payment_name));
				return $trande_info;
				break;
			default:
				$data =  M("coupon_orders")->where("order_sn = '%s'", $order_sn)->find();
				#TODO 未完成
				break;
		}
	}
	
	/**
	 *    获取外部交易号(记录支付日志)
	 *    @author    wscsky
	 *    @param     array $order_info
	 *    @return    string
	 */
	function _get_trade_sn(&$order_info)
	{
		$member = session('member');
		$order_info['out_trade_sn'] = $this->_create_out_trade_sn($order_info['entity_sn']);
		$order_info['ip'] = get_client_ip();
		$pay_log = array(
				'payment_id' 	=> $this->payment_id,
				'payment_name'  => $this->payment_name,
				'created'   	=> time(),
				'trade_id' 		=> empty($order_info['trade_id'])?0:intval($order_info['trade_id']),
				'entity_type'	=> empty($order_info['entity_type'])? 0:intval($order_info['entity_type']),
				'entity_id' 	=> empty($order_info['entity_id'])?0:intval($order_info['entity_id']),
				'entity_sn' 	=> empty($order_info['entity_sn'])?0:trim($order_info['entity_sn']),
				'utype'			=> empty($order_info['utype'])? null : $order_info['utype'],
				'uid' 			=> empty($order_info['uid'])?0:intval($order_info['uid']),
				'shop_id' 		=> empty($order_info['shop_id'])?0:intval($order_info['shop_id']),
				'money'	   		=> floatval($order_info['order_money']),
				'ip'			=> $order_info['ip'],
				'append'		=> serialize($order_info['append']),
				'out_trade_sn'	=> $order_info['out_trade_sn'],
		);
		
		//写入支付日志
		$modle = M("payment_log");
		$result = $modle->add($pay_log);
		if($result===false){
			return($this->_get_trade_sn($order_info));
		}
		return $order_info['out_trade_sn'];
	}
	
       
    /**
     * 加载支付成功后回调需要的文件
     * @author wscsky
     */
    static function _load_backcall_file($files){
    	if(empty($files)) return;
    	$files = explode(';',$files);
    	foreach($files as $file){
    		$file = explode(",", $file);
    		$name 		= array_shift($file);
    		$baseUrl	= array_shift($file);
    		$ext		= array_shift($file);
    		empty($ext) && $ext = EXT;
    		import($name, $baseUrl, $ext);
    	}
    }
	
    /**
     * 执行回调函数
     * @author wscsky
     * @param  string $fun
     * @param  array $trade_info;
     *
     */
    static function _run_backcall($funs="",$trade_info = array()){
    	if($funs=="") return;
    	$funs = explode(";",$funs);
    	foreach($funs as $fun){
    		if(is_callable($fun)){
    			call_user_func($fun,$trade_info);
    		};
    	}
    }
    
	/*
	 * 生成随机外部交易编号
	 * @param string $order_sn 订单编号
	 * @param int $time 回调次数
	 * @return string
	 */
	function _create_out_trade_sn($entity_sn = "", $time = 0){
		$pcode 		= trim($this->_config['pcode']);
		if(empty($entity_sn)){
			$order_sn 	= $pcode.time().mt_rand(pow(10,5), pow(10,6)-1);
		}else{
			if($time > 0){
				$order_sn = "{$entity_sn}_{$time}";
			}else{
				$order_sn = $entity_sn;
			}
		}
		$model 		= M('payment_log');
		$result 	= $model->where("out_trade_sn = '%s'",$order_sn)->find();
		if($result==false){
		    $model->where("status = 0 and out_trade_sn = '%s'",$order_sn)->delete();
			return($order_sn);
		}
		return($this->_create_out_trade_sn($entity_sn,++$time));
	}
	 
	
	
	/**
	 *    获取规范的支付表单数据
	 *
	 *    @author    wscsky
	 *    @param     string $method
	 *    @param     array  $params
	 *    @return    void
	 */
	function _create_payform($method = '', $params = array())
	{
		
		return array(
				'enabled'   =>  $this->payment_info['enabled'],
				'desc'      =>  $this->payment_info['payment_desc'],
				'method'    =>  $method,
				'gateway'   =>  $this->_gateway,
				'params'    =>  $params,
		);
	}
	
	/**
	 *    获取通知地址
	 *    @author    wscsky
	 *    @param     int $pay_info
	 *    @return    string
	 */
	function _create_return_url($pay_info)
	{
		return  U("/Pay/payback",array('sn'=>$pay_info['out_trade_sn']),false,true);
	}
	
	/**
	 *    获取通知地址
	 *    @author    wscsky
	 *    @param     int $pay_info
	 *    @return    string
	 */
	function _create_notify_url($pay_info)
	{
		return  U("/pay/paynotify","",false,true) . "?sn=".$pay_info['out_trade_sn'];
	}
	
	/**
	 * 更新支付及回调数据处理
	 * @author wscsky
	 * @param array $payment_log
	 * @param array @data
	 *
	 */
	static function _update_payment($payment_log=array(),$data=array()){
	    if(empty($payment_log) or empty($data)){
	        self::$error = "更新失败:支付数据有误!";
	        return false;
	    }
	    //更新支付交易流水记录状态
	    $model_log = M("payment_log");
	    $data['notify'] = time();
	    $result = $model_log->where("id = %d",$payment_log['id'])->save($data);
	    if($result===false){
	        self::$error = "更新失败:更新支付流水记录失败!";
	        return false;
	    }
	
	    //更新支付交易状态及回调处理
	    $trade_info = self::get_payment_trade($payment_log['trade_id']);
	    if($trade_info['status']==1){
	        self::$error = "更新失败:请不要重复验证!";
	        return false;
	    }
	
	    //更新支付交易状态
	    $trade_mod = M("payment_trade");
	    $trade_mod->where("trade_id = %d",$trade_info['trade_id'])->save(
	        array(  'status'		=> 1,
	            'verify_time'	=> time(),
	            'payment_id'	=> $payment_log['payment_id'],
	            'payment_name'	=> $payment_log['payment_name'],
	        ));
	    	
	    //公司现金流数据记录日志
	    $cash_log = array(
	    	'utype'			=> $trade_info['utype'],
	    	'uid'			=> $trade_info['uid'],
	    	'uname'			=> get_uname($trade_info['uid'],$trade_info['utype']),
	    	'shop_id'		=> $trade_info['shop_id'],
	    	'entity_type'	=> $trade_info['entity_type'],
	    	'entity_id'		=> $trade_info['entity_id'],
	    	'payment'		=> $trade_info['payment_name'],
	    	'paytime'		=> time(),
	    	'remark'		=> '订单编号:'.$trade_info['entity_sn'],
	    );
		D("Common/Cash","Logic") -> add_log($trade_info['order_money'], MODE_TYPE_INC, $cash_log);
	    	
	    //回调函数处理
	    $trade_info = self::get_payment_trade($payment_log['trade_id']); //重新读取新数据
	    $trade_info['out_trade_sn'] = $payment_log['out_trade_sn'];
	
	    $callback = unserialize($trade_info['callback']);
	    unset($trade_info['callback']);
	
	    //加载回调需要的类
		$callback['import'] && self::_load_backcall_file($callback['import']);
	
	    //执行回调函数
	    $callback['fun'] 	&& self::_run_backcall($callback['fun'],$trade_info);
	
	    //查是否有报错
	    $msg = self::getError();
	    //查用户财务是否有错
	    if($msg){
				return false;
	       }
			return true;
    }
	
    /**
     * 读取最后一次的错误信息
     * @return string
     * @author wscsky
     */
	
	static function getError(){
		return self::$error;
	}
}