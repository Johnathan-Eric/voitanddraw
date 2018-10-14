<?php
/**
 * 微信支付
 * @author wscsky
 *
 */
namespace Common\Payment\Wxpay;
class Wxpay{
	

	/**
	 * 支付方式基本信息
	 */
	function base_info(){
		return array(
			'payment_name'	=> "微信支付",
			'payment_code' 	=> "Wxpay",
			'payment_desc'	=> "微信支付",	
			'online'		=> 1,
			'enable'		=> true,
		);		
	}
	
	/**
	 * 支付方式的配置设置
	 */
	function get_config(){
	   return array(
	   		array(
			   		'lable'		=> '公众账号ID',
			   		'name'      => 'appid',
			   		'type'      => 'text',
			   		'validate'  => 'required:true',
			   		'help'      => '微信分配的公众账号ID，必须和【网站参数→系统设置】里的【微信AppID】一致。否则不能支付！'),
	   		array(
	   				'lable'		=> '商户号 ',
	   				'name'      => 'mch_id',
	   				'type'      => 'text',
	   				'validate'  => 'required:true',
	   				'help'      => '微信支付分配的商户号 '),
	   	
	   		array(
	   				'lable'		=> '商家APPKEY',
	   				'name'      => 'appkey',
	   				'type'      => 'text',
	   				'validate'  => 'required:true',
	   				'help'      => '32位英文大小写和数字组合，在微信支付平台设置'),
	   		
	   		array(
	   				'lable'		=> '签名方式',
	   				'name'      => 'signtype',
	   				'type'      => 'select',
	   				'validate'  => 'required:true',
	   				'data'		=> array(array('value'=>"md5",'name'=>'MD5加密')),//array('value'=>"sha1",'name'=>'SHA1加密'),
	   				'help'      => ''	),
	   );
	     
	}
}