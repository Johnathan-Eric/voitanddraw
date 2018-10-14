<?php
/**
 * 聊天机器人接口
 * @author wscsky
 */
namespace Common\Api;
class ChatApi{
	private $error 	= null;
    private  $user_id   = null;	
	/**
	 * 聊天机器人配置
	 * @param int $type 配置编号 
	 */
	function config($type = null){
		$data = array(
				"tuling"	=> array(
				        "name"		=> "图录机器人",
						'api_fun'	=> "chat_tuling",							//执行函数
						'api_uri'	=> "http://www.tuling123.com/openapi/api",	//URL地址
						'api_key'   => "a7958a11709320f6e3d1ecbda79e936e",      //APIKEY
						'api_secret'=> "a0c8c2e7f2332684",                      //密钥
					),
		);
		return is_null($type) ? $data : $data[$type];
	}
	
	/**
	 * 执行聊天机器人
	 * @param string $keyword 关键字
	 * @param string $openid  用户openid
	 * @author wscsky
	 */
	function chat($keyword = null, $openid = "123123"){
	    $this->user_id = md5($openid);
	    if(!$keyword) return false;
	    if($rdata = self::get_def_answer($keyword)) {
	        return $rdata;
	    }
	    $chat_boy = self::config(C("chat_robot_mode"));
	    if(!$chat_boy) return false;
	    $rdata = $this->$chat_boy['api_fun']($keyword,$chat_boy);
	    if(is_string($rdata)){
	        return array(
	            'MsgType' => 'text',
			    'Content' =>  $rdata
	        );
	    }
	    return $rdata;
	}
	
	/**
	 * curl 请求
	 * @param stirng $url  		要请求的网址
	 * @param string $data		post 请求的时数据
	 * @param string $method	请求方式 get post
	 * @return mixed			返回结果
	 */
	
	function curl_http($url, $data = '',$method = 'post')
	{
	    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
	    $ch = curl_init();
	    $headers = array('Accept-Charset: utf-8');
	    if($ssl){
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    }
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	    if(strtoupper($method)=="POST"){
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $content = curl_exec($ch);
	    curl_close($ch);
	    return $content;
	}
	
	/**
	 * 图灵机器人聊天
	 * @param string $keyword 关键字
	 * @param array $config   配置
	 */
	private function chat_tuling($keyword, $config = array()){
	    $pdata = array(
	        'key'  => $config['api_key'],
	        'info' => $keyword,
	        'userid' => $this->user_id
	    );
	    $data = json_decode(self::curl_http($config['api_uri'], http_build_query($pdata)),true);
	    //处理返回结果
	    switch($data['code']){
				case 100000://文本类数据
					$rdata = array(
					   'MsgType' => 'text',
					   'Content' =>  $data['text'],
					   );
					break;
				case 200000://网址类数据
				    $rdata = array(
				        'MsgType' => 'text',
				        'Content' =>  $data['text'].'，<a href="'.$data['url'].'">点击查看</a>。',
				    );
					break;
				case 302000://新闻
					$item      =   $data['list'];
					$icount    =   count($item);
					$datas     = array();
					for($i=0; $i<$icount; $i++){
						if(count($datas)>=8) break;
					    $datas[] = array(
					        'Title' 		=> $item[$i]['article'],
					        'Description' 	=> "新闻来源:{$item[$i]['source']}",
					        'PicUrl' 		=> $item[$i]['icon'] ? $item[$i]['icon'] :"",
					        'Url' 			=> $item[$i]['detailurl'],
					    );
					}
					if($datas){
					    $rdata = array(
					        'MsgType'      => 'news',
					        'ArticleCount' => count($datas),
					        'Articles'     => $datas,
					    );
					}
					break;
				case 308000://菜谱
					$item      =   $data['list'];
					$icount    =   count($item);
					$datas     = array();
					for($i=0; $i<$icount; $i++){
						if(count($datas)>=8) break;
					    $datas[] = array(
					        'Title' 		=> $item[$i]['name']."做法",
					        'Description' 	=> $item[$i]['info'],
					        'PicUrl' 		=> $item[$i]['icon'] ? $item[$i]['icon'] :"",
					        'Url' 			=> $item[$i]['detailurl'],
					    );
					}
					if($datas){
					    $rdata = array(
					        'MsgType'      => 'news',
					        'ArticleCount' => count($datas),
					        'Articles'     => $datas,
					    );
					}
					break;
				case 40001://key的长度错误(32位)
					$rdata='key的长度错误(32位)';
					break;
				case 40002://请求内容为空
					$rdata='请求内容为空';
					break;
				case 40003://key错误或帐号未激活
					$rdata='key错误或帐号未激活';
					break;
				case 40004://当天请求次数已用完
					$rdata='当天请求次数已用完';
					break;
				case 40005://暂不支持该功能
					$rdata='暂不支持该功能';
					break;
				case 40006://服务器升级中
					$rdata='服务器升级中';
					break;
				case 40007://服务器数据格式异常
					$rdata='服务器数据格式异常';
					break;
				default:
					$rdata='微校君也被你问到了～～[呵呵]';
					break;
			}
		return $rdata;
	}
	/**
	 * 常用配置回复
	 * @author wscsky
	 */
    private function get_def_answer($keyword){
	    switch ($keyword){
	        case "你是":case "你是?":case "你是谁":case "你叫":case "你叫什么":case "你叫什么?":
	            return array(
	                   'MsgType' => 'text',
	                   'Content' =>  C('chat_robot_name'),
	               );
	            break;
	        case "你父母是谁":
            case "你爸爸是谁":
            case "你妈妈是谁":
                return array(
                    'MsgType' => 'text',
                    'Content' =>  "主人，我是由[卓云科技]创建的大学服务平台，当然我也是您的[亲亲]!"
                );
                break;
            #TODO 可以考虑读取数据配置
            default:
                return false;
	            break;
	    }
	}
	
	/*
	 * 获取最后一次错误信息
	 * @author wscsky
	 */
	function getError(){
		return $this->error;
	}

}