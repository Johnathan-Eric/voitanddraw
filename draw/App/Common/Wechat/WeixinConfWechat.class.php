<?php
/**
* 	微信支付配置账号信息
*/
namespace Common\Wechat;
class WeixinConfWechat
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 * 
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 * 
	 * SUB_APPID：当前调起支付的小程序APPID
	 * 
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 * 
	 * SUB_MCHID : 服务商商户号有父子绑定关系的子商户号
	 * 
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * 
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	//const APPID = 'wx199b08c010821803';//微小服支付 公众号appid
	const APPID = 'wxc035702bfdb17bcf';//龙门支付 公众号appid
	//const SUB_APPID = 'wxc1b73480393f0b1b';//微小服企业版小程序 appid
	const SUB_APPID = 'wxde913170fc1c67ed';//龙门小程序 appid
	//const SUB_APPID = 'wx5df0db98abeeb356';//微shop8小程序 appid
	//const MCHID = 1500892601;//支付服务号 商户id
	const MCHID = 1510036441;//支付服务号 商户id
	//const SUB_MCHID = '1508391221';//特约商户id
	public static $SUB_MCHID = '';
	const KEY = 'npswc8H3f1FszBjCLfDaLw5q9qhO5QaY';//api密钥
	const APPSECRET = 'a904ce1f6af6d33fd122bfd3b1984164';//微小服
	//const SUB_APPSECRET = '14b5761d21a5a6ff820628e6a52ffca7';//微shop8
	const SUB_APPSECRET = 'e57a77afa090759ed84bce3634f85b34';
	const SIGN_TYPE = 'MD5';
	
	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const SSLCERT_PATH = 'Public/cert/apiclient_cert.pem';// 无/的绝对路径
	const SSLKEY_PATH = 'Public/cert/apiclient_key.pem';// 无/的绝对路径
	
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const CURL_PROXY_PORT = 0;//8080;
	
	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

	//=======【接口信息配置】===================================
	/**
	 * TODO：本系统提供的接口信息
	 * 
	 * PAY_API : 付款API接口
	 * 
	 * REFUND_API : 退款API接口
	 *
	 * CALLBACK_API：支付状态回调程序接口
	 * 
	 * TRADETYPE：调用支付类型 JSAPI
	 * @var url
	 */
	const PAY_API = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
	const REFUND_API = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
	const TRADETYPE = 'JSAPI';
	const CALLBACK_API = '/Orders/payCallback';
	public static $APPURL = 'https://zhsy.longmenshuju.com/Orders/payCallback';

	function __construct($config=array())
	{
		/*self::$APPURL = (	(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || 
							(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
						)
						? 'https://' : 'http://'
						. $_SERVER['HTTP_HOST'] . self::CALLBACK_API;*/
		self::$SUB_MCHID = $config['sub_mchid'];

	}
}
