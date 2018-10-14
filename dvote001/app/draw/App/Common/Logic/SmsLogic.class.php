<?php
//手机验证发送及验证模块
namespace Common\Logic;
class SmsLogic{
	
	protected $_error = null;
	protected $_error_num = 0;
	/**
	 * 发送短信参数
	 */
	private $max_time = 15 ; //验证码最长有效期 单:分钟
	
	private function get_sms_config(){
		/**
		 *  短信发送所需的配置参数
		 *  server:远程短信接口地址
		 *  sname:提交用户
		 *  spwd:提交密码
		 */
		return array(
				'server'	=> 'http://106.ihuyi.cn/webservice/sms.php?method=Submit',
				'sname'		=> C('ihuyi_uname'),
				'spwd'		=> C('ihuyi_pwd'),
	);
	}
	
	/**
	 * 发送短信到手机[可用于同时发送到多个手机号]
	 * @author	watchman
	 * @param	mix		$mobile		接受短信的手机号码，多个手机号以数组的形式传参
	 * @param	string	$content	短信内容
	 * @return	bool
	 */
	function sendSms($mobile, $content){
		if(is_array($mobile)){
			foreach ($mobile as $key => $_mobile){
				if(!is_mobile($_mobile)) unset($mobile[$key]);
			}
			if(count($mobile) == 0){
				$this->_error = "手机号码不正确！";
				return false;
			}
			if(count($mobile) > 100){
				$this->_error = "每批发送的手机号数量不得超过100个!";
				return false;
			}
			$mobile = join(",", $mobile);
		}else{
			if(!is_mobile($mobile)){
				$this->_error = "手机号码不正确！";
				return false;
			}
		}
		
		if(!$content){
			$this->_error = "短信内容为空！";
			return false;
		}
		
		$uri 	= "https://api.miaodiyun.com";
		
		$timestamp 		= date("YmdHis", time());
		$sig_parameter 	= md5(C('qmy_account_sid').C('qmy_auth_token').$timestamp);
		$url 			= $uri.'/'.C('qmy_soft_version') . '/affMarkSMS/sendSMS';
			
		$data = array(
		        'accountSid'        => C('qmy_account_sid'),
		        'smsContent'        => $content,
				'to'				=> $mobile,
		        'timestamp'         => $timestamp,
		        'sig'               => $sig_parameter,
		        'respDataType'      => 'JSON'
		);
// 		echo $url;
// 		dump($data);	
		$result = $this->post_json($url, $data);
		$result = json_decode($result, true);
		$rcode = $result['respCode'];
		$state = $result['respCode'] === '00000' ? true : false;
		//结果处理
		if($state){
			return true;
		}else{
			$this->_error = "发送失败{$rcode}！";
			return false;
		}
	}
	
	/**
	 * 发送手机验证码到用户手机
	 * @author	watchman
	 * @param	string	$mobile		接收号码
	 * @param	mixed	$code		验证码或者长度(小于10)
	 * @param	array	$where		用户或管理员ID查询条件
	 * @param	string	$type  		验证码用途 auth:认证  pay:支付等
	 * @param	string	$check		查发送时间是否过短
	 * @param	string	$smstype	短信模版
	 */
	function sendSmsCode($mobile, $code = 6, $where = array(), $type='auth', $check = true, $smstype = 'SmsCode'){
		$member = session('member');
		if(!is_mobile($mobile) || !is_array($where) || empty($where) || count($where) > 1 || !is_numeric($code) || $code > 10){
			$this->_error = '数据有误，发送失败！';
			return false;
		}
		//清除长时未验证数据
		self::clearSmsCode();
		//组合查询条件
		$where['uid'] && $map['uid'] = intval($where['uid']);
		$where['aid'] && $map['aid'] = intval($where['aid']);
		
		$map['mobile'] 	= $mobile;
		$map['type']	= $type;
		
		$model = M("sms");
		//查是否发送过于频繁
		if($check){
		    switch ($smstype){
		        case "qimayun":
		            $min_send_time = C('qmy_min_send_time') > 1 ? intval(C('qmy_min_send_time')) : 1;
		            break;
		        default:
		            $min_send_time = 2;
		            break;
		    }
		    
			$map['send_time'] = array("gt", strtotime("-$min_send_time minutes"));
			$result = $model -> where($map) -> count();  
			if($result > 0){
				$this->_error = "发送过于频繁，请稍候再试！";
				return false;
			}
		}
	
		//如果之前已发短信未被认证使用,再次下发
		$map['send_time'] = array("lt", strtotime("-2 minutes"));
		$sms_log = $model	-> where($map)
							-> field("id, code")
							-> order("send_time desc")
							-> find();
		if($sms_log){
			$code = $sms_log['code'];
		}else{
			if($code < 4) $code = 4;
			$code = self::randNumber($code);
		}
	
		$smstype = C('sms_api_type');
		switch ($smstype){
			case "ihuyi":
				return self::ihuyi_code($code, $mobile, $type, $where, $sms_log);
				break;
			case "qimayun":
				return self::qimayun_code($code, $mobile, $type, $where, $sms_log);				
				break;
				
			default:
				$this->_error = "未开启短信接口";
				return false;
				break;
		}
	}
	
	/**
	 * ihuyi发送接口验证码
	 * @author wscsky
	 */
	function ihuyi_code($code, $mobile, $type, $where, $sms_log){
		$member = session('member');
		$model = M("sms");
		
		$config = array(
				'server'	=> 'http://106.ihuyi.cn/webservice/sms.php?method=Submit',
				'sname'		=> C('ihuyi_uname'),
				'spwd'		=> C('ihuyi_pwd'),
		);
		$mb 	= C('ihuyi_sms');
		$mb 	= str_replace("【变量】", $code, $mb);
		
		$url 	= $config['server']."&account=".$config['sname']."&password=".md5($config['spwd'])."&mobile={$mobile}&content={$mb}";
		$rdata 	= file_get_contents($url);
		$xml = new \SimpleXMLElement($rdata);
		$rdata = array();
		foreach ($xml as $key => $value) {
			$rdata[$key] = strval($value);
		}
		//结果处理
		if($rdata['code'] == 2){
			//成功保存数据
			if($sms_log){
				$update_data = array(
						"id" 		=> $sms_log['id'],
						"send_time" => time(),
						"smsid"		=> $rdata['smsid'],
				);
				$model->data($update_data)->save();
			}else{
				$insert_data = array(
						'mobile'	=> $mobile,
						'uid'		=> intval($member->uid),
						'aid'		=> intval($member->aid),
						'code'		=> $code,
						'type'		=> $type,
						'send_time' => time(),
						"smsid"		=> $rdata['smsid'],
				);
				$insert_data = array_merge($insert_data, $where);
				$model->data($insert_data)->add();
			}
			return true;
		}else{
			\Think\Log::log("Sms","短信接口发送失败",$rdata);
			$this->_error = "发送失败:". $rdata['msg'];
			return false;
		}
	}
	
	/**
	 * 轻码云的配置
	 * @author wscsky
	 */
	function qimayun_code($code, $mobile, $type, $where, $sms_log){
		
		$member = session('member');
		$model  = M("sms");
		$uri 	= "https://api.miaodiyun.com";
		
		$timestamp 		= date("YmdHis", time());
		$sig_parameter 	= md5(C('qmy_account_sid').C('qmy_auth_token').$timestamp);
		$url 			= $uri.'/'.C('qmy_soft_version') .'/industrySMS/sendSMS';
			
		$service_tel = C('service_tel');
		$smsContent = '【'.C('qmy_sign').'】'.str_replace('{1}', $code, C('qmy_code_mb'));
		$data = array(
		    'accountSid'      => C('qmy_account_sid'),
		    'smsContent'      => $smsContent,
		    'to'              => $mobile,
		    'timestamp'       => $timestamp,
		    'sig'             => $sig_parameter,
		    'respDataType'    => 'JSON'
		);
			
		$result = $this->post_json($url, $data);
		$result = json_decode($result, true);
		$rcode = $result['respCode'];
		$state = $result['respCode'] === '00000' ? true : false;
		//结果处理
		if($state){
			//成功保存数据
			if($sms_log){
				$update_data = array(
						"id" 		=> $sms_log['id'],
						"send_time" => time(),
				);
				$model->data($update_data)->save();
			}else{
				$insert_data = array(
						'mobile'	=> $mobile,
						'uid'		=> intval($member->uid),
						'aid'		=> intval($member->aid),
						'code'		=> $code,
						'type'		=> $type,
						'send_time' => time(),
				);
				$insert_data = array_merge($insert_data, $where);
				$model->data($insert_data)->add();
			}
			return true;
		}else{
			$this->_error = "发送失败{$rcode}！";
			return false;
		}
	}
	/**
	 * 
	 * 清除过期手机验证码
	 */
	function clearSmsCode(){
		$model = M("sms");
		$model->where("send_time < " . strtotime("-{$this->max_time} minutes"))->delete();
	}
	
	/**
	 * 验证手机验证码
	 * @author	watchman
	 * @param 	string	$mobile		手机号码
	 * @param 	string 	$code		验证码
	 * @param 	string 	$type  		验证码用途 auth:认证  pay:支付 等
	 * @param 	bool 	$is_del  	验证成功后是否删除此验证码
	 * @param 	array 	$where		用户或管理员ID查询条件
	 * @param	bool	$is_del		验证后是否删除，默认删除
	 * @return 	bool
	 */
	function checkSmsCode($mobile, $code, $type = "auth", $where = array(), $is_del = TRUE){
		
		
		self::clearSmsCode();
		if(!is_mobile($mobile) || empty($code)){
			$this->_error = '手机号或验证码有误,验证失败!';
			return false;
		}
		
		//组合查询条件
		$where['uid'] && $map['uid'] = intval($where['uid']);
		$where['aid'] && $map['aid'] = intval($where['aid']);
		
		$map['mobile'] 	= $mobile;
		$map['type']	= $type;
		$map['code']	= addslashes($code);
		
		$model = M("sms");
		
		$result = $model -> where($map) -> field("id") -> find();
		//echo $model -> getLastSQL();
		if($result){
			if($is_del){
				$model -> where("id = {$result['id']}") -> delete();
			}
			return true;
		}
		else{
			$this->_error = "验证码不正确";
			return false;
		}
	}
	
	/**
	 * 邮件发送
	 * @author	watchman
	 * @param	string	$email   	接收人邮件地址
	 * @param 	string 	$subject	邮件标题
	 * @param 	string 	$content	邮件内容
	 * @param 	string 	$fromname  	发件人
	 * @return	bool
	 */
	function sendMail($email, $subject, $content, $fromname)
	{
		$this->_error_num = 0;
		if(is_array($email)){
			foreach ($email as $_email){
				if(is_email($_email)){
					if(!SendMail($_email, $subject, $content, $fromname))
						$this->_error_num++;
				}
			}
			return true;
		}elseif(is_email($email)){
			return SendMail($email, $subject, $content, $fromname);
		}else{
			$this->_error = sprintf ( "%s 不是合法的email地址", $email );
			$this->_error_num++;
			return false;
		}
	}	
	
	/**
	 * 生成一个随机数字
	 * @param 	int		$len 随机数长度
	 * @return 	string
	 */
	function randNumber($len=6) {
		return sprintf("%0".$len."d", mt_rand(1,pow(10,$len)-1));
	}
	
	/**
	 * POST提交JSON请求
	 */
	private function post_json($url, $data){
// 		if(!is_string($data)) $data = json_encode($data);
		$data = http_build_query ( $data, '&' );
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		$ch = curl_init() ;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		if($ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','charset:utf-8','Accept:application/json','Content-Length: ' . strlen($data)));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	/**
	 * 获取错误信息
	 * @author watchman
	 */
	function getError(){
		return $this->_error;
	}
	
	/**
	 * 读取邮件发送失败数
	 * @author wscsky
	 */
	function getErrorNum(){
		return $this->_error_num;
	}
	
}