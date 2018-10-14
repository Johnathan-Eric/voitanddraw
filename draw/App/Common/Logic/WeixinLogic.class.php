<?php
/*
 * 微信接口API
 * @author wscsky
 */
namespace Common\Logic;
use Think;
class WeixinLogic{
	protected $_error = NULL;			//记录错误信息
    protected $access_token = null;		//当前access_token
    protected $data		= array();		//微信返回
    protected $zycode_time = 86400;      //zycode形式登陆有效期 默认一天
    
    /**
     * 读取微信接口请求时的xml数据
     * @param bool $back 是否返回结果
     * @author wscsky
     */
    function get_request($back=false){
    		$xml = file_get_contents("php://input");
    		if(!$xml) return;
			$xml = new \SimpleXMLElement($xml);
	        foreach ($xml as $key => $value) {
	        	$this->data[$key] = strval($value);
	        }
	        if($back) return $this->data;
    }
    /**
     * 返回API回调数据
     */
    function get_data(){
    	return $this->data;    	
    }
	/**
	 * 验证公众号信息
	 * @param bool $back 是否返回true/false
	 */
	function auth(){
	    $result = $this->_auth();
		if($result){
			if($_GET['echostr']){
				ob_end_clean();
				echo ($_GET['echostr']);
				exit();
			}
		}
		else{
			exit("微信验证失败");
		}
	}
	
	/**
	 * 验证用户微信用户
	 * @param bool $redirect 是否直接跳转
	 * @return bool 是否登陆成功
	 * 
	 */
	function check_user($redirect = true){
		$data = $_GET;
		if($data['code']){
			$wxdata = $this->get_api_openid($data['code']);
			if($wxdata['errcode']) return $this->goto_weixin_auth($redirect);
			$model 	=  D("Common/member", "Logic");
			$uinfo  =  $model-> get_member_by_openid($wxdata['openid'],true);
			$puid 	 = I("puid",0,"intval");
			$cpuid	 = intval(cookie("puid"));
			$puid 	 = $puid > 0 ? $puid : $cpuid > 0 ? $cpuid : MEMBER_DEFAULT_PUID;
			if(C('write_weixinlog') == 1) \Think\Log::log("check_user","puid",$puid);
			//如果没有用户信息。添加新用户
			if(!$uinfo){
				$uid 	 = $this -> add_weixin_member($wxdata, $puid);
				$uinfo   = $model -> get_member_by_openid($wxdata['openid'],true);
			}
			return $uinfo;
		}
		return $this->goto_weixin_auth($redirect);
	}
	
	/**
	 * 添加微信用户
	 * @param array $wxdata
	 * @param string $puid
	 * @return int $uid
	 * @author wscsky
	 */
	function add_weixin_member($wxdata, $puid, $referer = 1, &$rdata=array()){
	    if(is_array($wxdata)){
	        $openid = $wxdata['openid'];
	        $access_token = $wxdata['access_token'];
	    }else{
	        $openid = $wxdata;
	        $access_token = "";
	    }
        $wxuinfo = $this->get_wxuser_info($openid, $access_token);
		if($wxuinfo['errcode']){
			$this->get_access_token(true);
			$wxuinfo = $this->get_wxuser_info($openid, $access_token);
		}
		$model 	=  D("Common/member", "Logic");
		//读取原用户信息
		$uinfo 	= M("member")->where("openid = '%s'",$openid)->find();
		//记录原数据
		if($uinfo){
    		$uinfo['old'] = array(
    			'status'			=> $uinfo['status'],
    			'subscribe_time'	=> $uinfo['subscribe_time'],
    		);
		}
		$sex_data = array(1=> "男", 2 => "女");
		$uinfo['sex']       = $sex_data[$wxuinfo['sex']];
		$uinfo['openid']	= $openid;
		$uinfo['unionid']   = $wxuinfo['unionid'];
		$uinfo['referer']	= $uinfo['referer'] ? $uinfo['referer'] : $referer;
		$subscribe = $uinfo['status']	= intval($wxuinfo['subscribe']);
		$uinfo['uname']		= $wxuinfo['nickname'];
		$uinfo['nickname']  = base64_encode($wxuinfo['nickname']);
		$uinfo['headimg']	= $wxuinfo['headimgurl'];
		
		if(!$uinfo['puid'] && $puid > 0){
		    $puinfo = $model -> get_member_field($puid,'uid,uname');
		    if($puinfo){
		        $uinfo['puid']    = $puinfo['uid'];
		        $uinfo['puname']  = $puinfo['uname'];
		    }
		}
		//不能自己推荐自己
		if($uinfo['uid'] == $uinfo['puid']) unset($uinfo['puid'], $uinfo['puname']);
		if($wxuinfo['subscribe_time']>0){
		    $uinfo['subscribe_time'] = $wxuinfo['subscribe_time'];
		}
		$msg     = $msg2 = "";
		$uid 	 = $model -> add_member($uinfo, $msg, $msg2);
		if($uid){
			$uinfo = M("member")->where("openid = '%s'",$openid)->find();
			//准备关注者消息
			$message =  D("Common/Message","Logic") -> read("subscribe_msg",array(1,2,3,5,6,7));
			if(is_string($message)){
			    C("LAYOUT_ON",false);
			     \Think\Think::instance('Think\View')->assign($uinfo);
			     $rdata['Content'] = \Think\Think::instance('Think\View')->fetch("",$message['Content'].$msg);
			}else{
			    $rdata = $message;
			}
			//发送推荐者
			if($uinfo['puid'] > 0){
			    //$model -> check_member_vip($uinfo['puid']);  //检查推荐者的升级
			    $puinfo = M("member")->find($uinfo['puid']);
			    if(!empty($msg)) $msg = ",".$msg;
			    if($uinfo['referer'] == 2)
			        $content = "【{$uinfo['uname']}】通过你的二维码关注了".C('wx_site_name')."，成为您的".C('my_member').$msg2;
			    else
			        $content = "【{$uinfo['uname']}】通过你的链接关注了".C('wx_site_name')."，成为您的".C('my_member').$msg2;
			    if($subscribe == MEMBER_STATUS_FOCUS) $this->send_wx_msg($puinfo['openid'],$content);
			    //伯乐奖
			    $mnum   = M("member")->where("puid = %d and status = 1", $uinfo['puid'])->count();
			    if($puinfo['bole'] == 0 && $puinfo['puid'] > 0 && $mnum >= C('bole_step',null,100) && C('bole_profit') >0 ){
			        $log = array(
			            'type'          => BALANCE_TYPE_BOLE,
			            'remark'        => "你的好友{$puid['uname']}推荐人数达到".C('bole_step',null,100)."人，获得伯乐奖".C('bole_profit')."元",
			        );
			        $rr = D("Common/Finance","Logic")->change_balance(C('bole_profit'), MODE_TYPE_INC, $puinfo['puid'] , $log);
			        if($rr){
			            M("member")->where("uid = %d", $puinfo['uid'])->save(array('bole' => 1));
			            $ppinfo = M("member")->find($puinfo['puid']);
			            $this->send_wx_msg($ppinfo['openid'],$log['remark']);
			        }
			    }
			    
			}
				
		}
		return $uid;
	}
	
	
	/**
	 * 读取微信用户基本信息
	 */
	function get_wxuser_info($openid, $access_token = ""){
	    if($access_token){
	          $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
	    }else{
		      $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->get_access_token()."&openid={$openid}&lang=zh_CN";
	    }
		$data = $this->curl_http($url);
		if($data) return $data = json_decode($data, true);
		return $data;
	}
	
	/**
	 * 通过返回的code读取用户的openid
	 * @param string $code 
	 * @return string $openid
	 * @author wscsky
	 */
	function get_api_openid($code){
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".C("wxAPPID")."&secret=".C('wxAppSecret')."&code={$code}&grant_type=authorization_code";
		$data = $this->curl_http($url);
		if($data){
			$data = json_decode($data,true);
			$data['token_time'] = time();
			$_SESSION['access_token'] = $data; 
			return $data;
		}
		return false;
	}
	
	/**
	 * 批量读取用户信息
	 * @param array $openids 用户的openids列表
	 * @author wscsky
	 */
	function get_wx_members($openids = array()){
	   if(!$openids) return false;
	   if(!is_array($openids)){
	       $ids = array($openids);
	   }else{
	       $ids = $openids;
	   }
	   //组合数据
	   $udata = array();
	   foreach ($ids as $openid){
	       $udata['user_list'][] = array(
	           'openid' => $openid,
	           'lang'   => 'zh-CN',
	       );
	   }
       $url ="https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . self::get_access_token();
       $data = self::curl_http($url, "POST", $udata);
	}
	
	/**
	 * 跳转到微信页面
	 */
	function goto_weixin_auth($redirect = true){
		$this->set_puid_cookie();
		$backurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if(C('wxAccoutType') == 1){
		    //订阅号
		    if(C('open_site_login')){
		        $data = array('puid' => I("puid"));
		        if(stripos($backurl, 'zycode') > 0){
		            $backurl = substr($backurl, 0, stripos($backurl, 'zycode'));
		        }
		        if($backurl) $data['referer'] = base64_encode($backurl);
		        redirect(U('Open/oauth',$data));
		        exit();
		    }
	        return ;
		}else{
		    $backurl = urlencode($backurl);
		    //服务号
		    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C("wxAPPID")."&redirect_uri={$backurl}&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
		}
		if($redirect){
			redirect($url);
			exit();
		}
		return $url;
	}
	
	/**
	 * 读取微信 access_token
	 * @param bool $force 是否强制刷新
	 */
	function get_access_token($force = false){
		if($this->access_token && !$force) return $this->access_token;
		$token = C("wxTOKEN");
		$access_token = S("access_token_{$token}");
		if($force || !$access_token){
			$url 	= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C('wxAppID')."&secret=".C('wxAppSecret');
			$data 	= json_decode(self::curl_http($url),true);
			if($data['access_token']){
				$access_token = $data['access_token'];
				S("access_token_{$token}",$access_token, $data['expires_in']);
			}else{
				S("access_token_{$token}",null);
				$this->_error = "errcode:{$data['errcode']}";
			}
		}
		$this->access_token = $access_token;
		return $access_token;
	}
	
	/**
	 * 读取微信 get_auth2_access_token
	 * @param bool $force 是否强制刷新
	 */
	function get_auth2_access_token($force = false, $scope="snsapi_base"){
		if($this->access_token && !$force) return $this->access_token;
		$token = C("wxTOKEN");
		$access_token = S("access_token_{$token}");
		if($force || !$access_token){
			$url 	= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C('wxAppID')."&secret=".C('wxAppSecret');
			$data 	= json_decode(self::curl_http($url),true);
			if($data['access_token']){
				$access_token = $data['access_token'];
				S("access_token_{$token}",$access_token, $data['expires_in']);
			}else{
				S("access_token_{$token}",null);
				$this->_error = "errcode:{$data['errcode']}";
			}
		}
		$this->access_token = $access_token;
		return $access_token;
	}
	
	/**
	 * 微信公众号验证
	 * @param string $token
	 * @return boolean
	 */
	private function _auth($token = "")
	{
		$token == "" && $token = C("wxTOKEN");
		$data = array(
				$_GET['timestamp'],
				$_GET['nonce'],
				$token
		);
		$sign = $_GET['signature'];
		sort($data,SORT_STRING);
		$signature = sha1(implode($data));
		return $signature === $sign;
	}
	
	/**
	 * 读取我的海报
	 * @author wscsky
	 */
	function get_my_haibao($uid = 0){
	    $member = session('member');
	    $uid = $uid > 0 ? $uid : $member->uid;
	    $data = M("member_haibao")->find($uid);
	    //无数据生成数据
	    if(!$data){
	        $uinfo = M("member") -> find($uid);
	        $hb_uri = self::create_haibao($uinfo);
	        $data = array(
	            'uid' => $uid,
	            'uri' => $hb_uri,
	            'created' => time(),
	        );
	        M("member_haibao")->add($data);
	    }
	    //发送文件到微信服务器
	    if(!$data['sendtime'] || time() - $data['sendtime'] > 24*60*60*3){
	        $file = $this->media_upload($data['uri']);
	        if($file){
	            $data['mediaid']  = $file['media_id'];
	            $data['sendtime'] = $file['created_at'];
	            M('member_haibao') -> where("uid = %d", $uid)->save($data);
	        }
	    }
	    return $data;
	}
	
	/**
	 * 读取用户永久二维码推广信息
	 * @param int $uid 	用户的$uid/$openid
	 * @param bool $force 强制生成
	 * @return array()
	 * @author wscsky
	 */
	function get_qrcode_data($uid, $force=false){
		if(empty($uid)) return false;
		$needsave = false;		
		$uinfo = M("member") -> where((is_numeric($uid) ? "uid = '%d'" : "openid = '%s'"), $uid)->find();
		if(!$uinfo) {
			$this->_error = "";
			\Think\Log::log("get_qrcode_data", "失败:未找到用户信息！", "uid:{$uid}");
			return false;
		}
		$qrdata = M("member_qrcode") -> where("uid = '%d'", $uinfo['uid'])->find();
		if(!$qrdata){
			$qrdata = array(
				'uid' 		=> $uinfo['uid'],
				'created'	=> time()	
			);
			$qrdata['id'] = M("member_qrcode") -> add($qrdata);
		}
		
		if(!$qrdata['id']){
			$this->_error = "服务繁忙，请稍候重试！";
			\Think\Log::log("get_qrcode_data", "失败:服务繁忙！", $qrdata);
			return false;
		}
		
		if(is_null($qrdata['ticket']) || empty($qrdata['ticket'])){				
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->get_access_token();
			$jsondata = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.intval($qrdata['id']).'}}}';
			$data = json_decode($this->post_json($url, $jsondata),true);
			if($data['errcode']){
				$this->_error = "生成二维码遇到问题。请重试！";
				\Think\Log::log("get_qrcode_data", "失败:生成二维码遇到问题", $data);
				return false;
			}
			$qrdata['ticket'] = $data['ticket'];
			$qrdata['url'] = $data['url'];
			$qrdata['created'] = time();
			$needsave = true;
		}
		//生成文件 
		if(!$qrdata['uri'] || $force){
			$qrdata['uri'] = $this->create_qrcode($uinfo,$qrdata);
		}

		//发送文件到微信服务器
		if(time() - $qrdata['sendtime'] > 24*60*60*3){
			$file = $this->media_upload($qrdata['uri']);
			if($file){
				$qrdata['mediaid'] = $file['media_id'];
				$qrdata['sendtime'] = $file['created_at'];
				$needsave = true;
			}
		}
		$needsave && M("member_qrcode")->save($qrdata);
		return $qrdata;
	}
	
	public function getAddress() {
		
	}
	/**
	 * 获取临时二维码数据
	 * @param string $type 类型 goods
	 * @param int $goods_id 商品ID
	 * @param int $uid 用户UID
	 * @param bool $save_img 是否保存图片
	 * @param int $expire_seconds 有效期
	 * @return array()
	 * @author wsccsky
	 */
	function get_qrcode($goods_id, $type="goods", $uid = 0, $save_img = true, $expire_seconds = 604800){
		$member = session('member');
		$uid = $uid == 0 ? $member->uid : $uid;
		if($goods_id <=0) return false;
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->get_access_token();
		$qrdata = M("qrcodes")->where("uid = %d and data_id = %d and type='%s'", array($uid, $goods_id, $type))->find();
		if(!$qrdata){
			$qrdata = array("uid"=> $uid, 'data_id'=>$goods_id,"type"=>$type);
			$qrdata['id'] = M("qrcodes")->add($qrdata);
		}elseif($qrdata['expire'] > time()){
			//在有效期内直接返回数据
			return $qrdata;
		}
		$jsondata = '{"expire_seconds":'.$expire_seconds.',"action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.intval($qrdata['id']).'}}}';
		$data = json_decode($this->post_json($url, $jsondata),true);
		if($data['errcode']){
			$this->_error = "生成二维码遇到问题。请重试！";
			return false;
		}
		$qrdata['ticket'] 	= $data['ticket'];
		$qrdata['expire'] 	= $data['expire_seconds']+time()-60;
		$qrdata['url'] 		= $data['url'];
		//获取图片数据
		if($save_img){
			$qrcode_uri = "./Uploads/scene/".$qrdata['id'];
			$qrurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$qrdata['ticket']}";
			$qrdata['uri'] = D("Common/File", "Logic") -> get_weixin_qrcode($qrurl,$qrcode_uri);
		}
		//保存数据
		M("qrcodes")->save($qrdata);
		return $qrdata;
	}
	/**
	 * 读取用户永久二维码数据
	 * @param string $ticket
	 * @return array
	 * @author wscsky
	 */
	function read_member_qrcode($ticket){
		if(empty($ticket)) return false;
		M("member_qrcode") -> where("ticket = '%s'", $ticket)->find();
	}
	/**
	 * 读取临时二维码数据
	 * @author wscsky
	 */
	function read_qrcode($ticket = ""){
		return M("qrcodes")->where("ticket = '%s'", $ticket)->find();
	}

	/**
	 * 生成用户推广海报
	 * @author wscsky
	 */
	function create_haibao($uinfo){
	    $url= 'http://'.C('site_url'). U('/index/hb','puid='.$uinfo['uid']);
	    vendor("phpqrcode.phpqrcode");
	    $haibao_cfgs = C('haibao_cfgs');
	    
	    $qrcode_uri = "./Uploads/haibao/".$uinfo['uid'].".jpg";
	    $qr_uri = "./".RUNTIME_PATH . "Temp/hbqr_{$uinfo['uid']}.png";
	    \QRcode::png($url, $qr_uri, 3, 4, 1);
	    
	    $Image 			= new \Think\Image();
	    $Image -> open($qr_uri) -> thumb(C('hb_qr_w',null,200), C('hb_qr_w',null,200), 3)->save($qr_uri);
	    
	    //处理头像
	    if(in_array(1,$haibao_cfgs)){
    	    if(is_null($uinfo['headimg']) || empty($uinfo['headimg'])){
    	        $logo_uri = C('hb_logo_default');
    	    }else{
    	        $logo_uri = D("Common/File", "Logic") -> get_member_img($uinfo['headimg'],$uinfo['openid'],'ulogo');
    	    }
	       $Image -> open($logo_uri) -> thumb(C('hb_logo_w'), C('hb_logo_h'),3)->save($logo_uri);
	    }
	    
	    $nickname = show_uname($uinfo['nickname'],"text");
	    if(!$nickname) $nickname = $uinfo['uname'];
	    if(!$nickname) $nickname = "会员号{$uinfo['uid']}";
	    $bguri = ".". ltrim(C('hb_bg_uri'),".");
	    //合成图片
	    $Image  ->open($bguri);
	    if(in_array(1,$haibao_cfgs)) $Image -> water($logo_uri,array(C('hb_logo_x'),C('hb_logo_y')),C('hb_logo_alpha'));
	    if(in_array(2,$haibao_cfgs)) $Image -> text($uinfo['uname'], C('hb_name_font'), C('hb_name_size'), C('hb_name_color') ,array(C('hb_name_x'),C('hb_name_y')));
	    if(in_array(4,$haibao_cfgs)) $Image -> water($qr_uri,array(C('hb_qr_x'),C('hb_qr_y')),C('hb_qr_alpha'));
        $Image->save($qrcode_uri);
	    return $qrcode_uri;
	}
	
	/**
	 * 生成用户推广二维码
	 * @author wscsky
	 */
	function create_qrcode($uinfo, $data){
	    vendor("phpqrcode.phpqrcode");
	    $haibao_cfgs = C('haibao_cfgs');
	    $url = $data['url'];
	    if(!$uinfo || !$url) return '';
	    $dir = C('UPLOAD_FILE_PATH') . "qrcode" ;
	    if($dir{0} == "/") $dir = ".".$dir;
	    !is_dir($dir) && mkdir($dir, 0777, true);
	    $qrcode_uri = $dir."/".$uinfo['uid'].".jpg";
	    
	    $qr_uri = "./".RUNTIME_PATH . "Temp/qrcode_{$uinfo['uid']}.png";
	    \QRcode::png($url, $qr_uri, 3, 4, 1);
	    $Image 			= new \Think\Image();
	    $Image -> open($qr_uri) -> thumb(C('hb_qr_w',null,200), C('hb_qr_w',null,200), 3)->save($qr_uri);
	     
	    //处理头像
	    if(in_array(1,$haibao_cfgs)){
	        if(is_null($uinfo['headimg']) || empty($uinfo['headimg'])){
	            $logo_uri = C('hb_logo_default');
	        }else{
	            $logo_uri = D("Common/File", "Logic") -> get_member_img($uinfo['headimg'],$uinfo['openid'],'ulogo');
	        }
	        if(!file_exists($logo_uri)){
	           $logo_uri = C('hb_logo_default');
	        }else{
	           $Image -> open($logo_uri) -> thumb(C('hb_logo_w'), C('hb_logo_h'),3)->save($logo_uri);
	        }
	    }
	     
	    $nickname = show_uname($uinfo['nickname'],"text");
	    if(!$nickname) $nickname = $uinfo['uname'];
	    if(!$nickname) $nickname = "会员号{$uinfo['uid']}";
	    $bguri = ".". ltrim(C('hb_bg_uri'),".");
	    //合成图片
	    $Image  ->open($bguri);
	    if(in_array(2,$haibao_cfgs)) $Image -> text($uinfo['uname'], C('hb_name_font'), C('hb_name_size'), C('hb_name_color') ,array(C('hb_name_x'),C('hb_name_y')));
	    if(in_array(4,$haibao_cfgs)) $Image -> water($qr_uri,array(C('hb_qr_x'),C('hb_qr_y')),C('hb_qr_alpha'));
	    if(file_exists($logo_uri))  if(in_array(1,$haibao_cfgs)) $Image -> water($logo_uri,array(C('hb_logo_x'),C('hb_logo_y')),C('hb_logo_alpha'));
	    $Image->save($qrcode_uri);
	    return $qrcode_uri;
	}
	
	/**
	 * POST提交JSON请求
	 * @author wscsky
	 */
     function post_json($url, $data){
	    if(!is_string($data)) $data = my_json_encode($data);
	    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
	    
	    $ch = curl_init() ;
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    if($ssl){
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    }
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}
	
	/**
	 * curl 请求 
	 * @param stirng $url  		要请求的网址
	 * @param string $method	请求方式 get post
	 * @param string $data		post 请求的时数据
	 * @return mixed			返回结果
	 */
	
	function curl_http($url, $method = 'get', $data = '')
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
	 * curl 下载多媒体文件
	 * @author watchman
	 * @param string $url 请求的url地址
	 * @return array
	 */
	function curl_down($url, $dirname = "./wximages/"){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);    
        curl_setopt($ch, CURLOPT_NOBODY, false);    //对body进行输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        $res = json_decode($package, true);
        if($res['errcode']){
        	$this->_error = "errcode:{$res['errcode']},errmsg:{$res['errmsg']}";
			return false;
        }
        $media = array_merge(array('mediabody' => $package), $httpinfo);
        
        //求出文件格式
        preg_match('/\w\/(\w+)/i', $media["content_type"], $extmatches);
        $fileext = $extmatches[1];
        $filename = time().rand(100,999).".{$fileext}";
        if(!file_exists($dirname)){
            mkdir($dirname,0777,true);
        }
        file_put_contents($dirname.$filename, $media['mediabody']);
        return $dirname.$filename;
    }
	
	function data2xml(&$xml, $data, $item = 'item') {
		foreach ($data as $key => $value) {
			/* 指定默认的数字key */
			is_numeric($key) && $key = $item;
	
			/* 添加子元素 */
			if(is_array($value) || is_object($value)){
				$child = $xml->addChild($key);
				$this->data2xml($child, $value, $item);
			} else {
				if(is_numeric($value)){
					$child = $xml->addChild($key, $value);
				} else {
					$child = $xml->addChild($key);
					$node  = dom_import_simplexml($child);
					$node->appendChild($node->ownerDocument->createCDATASection($value));
				}
			}
		}
	}
	
	/**
	 * 上传多媒体文件
	 * @author watchman
	 * @param array $files 媒体文件信息
	 * @param string $type 媒体文件类型
	 * @param bool   $forever 是否是永久素材
	 * @return array
	 */
	function media_upload($files, $type = "image", $forever = false, $fdata = array()){
		if(!is_array($files)) $_files = array($files);
		if(!in_array($type, array('image', 'voice', 'video', 'thumb'))){
			$this->_error = '媒体文件类型有误！';
			return false;
		}
		if($forever){
		      $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->get_access_token()."&type={$type}";
		}else{
		      $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->get_access_token()."&type={$type}";
		}
		foreach ($_files as &$file){
			if($file{0} != "@") $file = "@{$file}";
			if(C('write_weixinlog') == 1) \Think\Log::log("weiapi","media_upload","{$file}");
			$data=array(
			    'media' => $file,
			);
			if($forever){
			    $data['description'] = json_encode($fdata);
			}
			$result = $this->curl_http($url, 'POST', $data);
			$result = json_decode($result, true);
			if(C('write_weixinlog') == 1) \Think\Log::log("weiapi","upload_result",$result);
			$file = "";
			if($result['errcode']){
				$file = "";
			}else{
				$file = $result;
			}
		}
		if(is_string($files))
			return $_files[0];
		else 
			return $_files;
	}
	
	/**
	 * 删除永久素材
	 * @package mixed $media_id
	 * @author wscksy
	 */
	function media_delete($media_id){
	    if(is_array($media_id)){
	        return array_map(array("self","media_delete"), $media_id);
	    }
	    $url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=".$this->get_access_token();
	    $rdata =  json_decode($this->curl_http($url, 'POST',json_encode(array("media_id" => $media_id))),true);
	    if($rdata['errcode']){
	       return false;
	    }else{
	       return true;
	    }
	}
	
	/**
	 * 下载多媒体文件
	 * @author watchman
	 * @param array $data 媒体文件信息
	 * @param string $type 媒体文件类型
	 * @return array
	 */
	function media_get($media_id){
		if(!$media_id){
			$this->_error = '媒体文件ID不能为空！';
			return false;
		}
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->get_access_token()."&media_id={$media_id}";
		$result = $this->curl_down($url);
		if(!$result){
			return false;
		}
	
		return $result;
	}
	
	/**
	 * 客服帐号管理（增删改）
	 * @author watchman
	 * @param array $data 客服信息
	 * @param string $operate 操作方法
	 * @return bool
	 */
	function kf_manage($data, $operate){
		if(!is_array($data) || count($data) == 0){
			$this->_error = '客服信息不能为空';
			return false;
		}
		if(!in_array($operate, array('add', 'update', 'del'))){
			$this->_error = '客服信息操作参数有误！';
			return false;
		}
		$url = "https://api.weixin.qq.com/customservice/kfaccount/{$operate}?access_token=".$this->get_access_token();
		$result = $this->post_json($url, $data);
		$result = json_decode($result, true);
		if($result['errcode'] == 0){
			return true;
		}else{
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
	}
	
	/**
	 * 设置客服帐号的头像
	 * @author watchman
	 * @param string $kf_account 客户账号
	 * @
	 */
	function kfheadimg($kf_account, $file){
		if(!$kf_account){
			$this->_error = '客服帐号不能为空！';
			return false;
		}
		$url = "http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=".$this->get_access_token()."&kf_account={$kf_account}";
		if($file{0} != "@") $file = "@{$file}";
		$result = $this->curl_http($url, 'POST', array("media" => $file));
		$result = json_decode($result, true);
		if($result['errcode'] == 0){
			return true;
		}else{
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
	}
	
	/**
	 * 获取所有客服账号
	 * @author watchman
	 * @return array
	 */
	function getkflist(){
		$url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=".$this->get_access_token();
		$result = $this->curl_http($url);
		$result = json_decode($result, true);
		if($result['errcode']){
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
		return $result;
	}
	
	/**
	 * 客服发消息
	 * @author watchman
	 * @param array $data 要发送的消息
	 */
	function kf_send($data){
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->get_access_token();
		$result = $this->post_json($url, $data);
		$result = json_decode($result, true);
		if($result['errcode'] == 0){
			return true;
		}else{
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
	}
	
	/**
	 * 发消息给指定的用户
	 * @param array $wxUser	要发送的用户的openid
	 * @param array $data 要发送的消息,如果发文本可以直接传内容
	 * @author wscsky
	 */
	function send_wx_msg($wxUser = array(), $data = array()){
	    if(empty($wxUser)) return;
	    if(is_string($wxUser)) 	$wxUser = array($wxUser);
	    if(is_string($data)) 	$data = array("msgtype"=>"text","text"=>array("content"=>$data));
	    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->get_access_token();
	    foreach ($wxUser as $openid){
	        $data['touser'] = $openid;
	        $rdata = $this->post_json($url, $data);
	    }
	}
	
	/**
	 * 微信菜单创建
	 * @author watchman
	 * @param array $data 要创建的菜单数据
	 */
	function create_menu($data){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->get_access_token();
		$result = $this->post_json($url, $data);
		\Think\Log::log("create_menu", "rback",$result);
		$result = json_decode($result, true);
		if($result['errcode'] == 0){
			return true;
		}else{
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
	}
	
	/**
	 * 删除微信菜单
	 * @author watchman
	 */
	function del_menu(){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->get_access_token();
		$result = $this->curl_http($url);
		$result = json_decode($result, true);
		if($result['errcode'] == 0){
			return true;
		}else{
			$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
			return false;
		}
	}
	

	
	/**
	 * 长链接转短链接接口
	 * @author watchman
	 */
	function long2short($long_url){
 		$site_url = "http://mp.weixin.qq.com/";
 		if(strpos($long_url, $site_url) != false){
 			$url =  str_replace($site_url, '', $long_url);
 		}else{
 			if(strpos($url, 'http://') != false){
 				$url =  str_replace('http://', '', $url);
 			}else{
 				$url = $long_url;
 			}
 		}
 		
 		//查数据库中是否当前长链接生成短链接记录，有则直接返回，无则调微信接口生成并保存到数据库
 		$short_url = M('shorturl')->where("url = '%s'", $url)->getField('short_url');
 		if($short_url){
 			return $short_url;
 		}else{
 			$get_url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=".$this->get_access_token();
 			$data = array("action" => "long2short", "long_url" => $long_url);
 			$result = $this->post_json($get_url, $data);
 			$result = json_decode($result, true);
 			
 			if($result['errcode'] == 0){
 				$data = array('url'=>$url, 'full_url'=>$long_url, 'short_url'=>$result['short_url'], 'addtime'=>time());
 				M('shorturl')->add($data);
 				return $result['short_url'];
 			}else{
 				$this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
 				return false;
 			}
 		}
	}
	
	/**
	 * 读取微信联系地址签名
	 * @author wscsky
	 */
	function get_address_sign(){
	    $access_token = $this->get_member_access_token();
	    if(!$access_token) return false;
		$data = array(
			'appId' 	=> C("wxAPPID"),
			'url'		=> 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
			'timeStamp' => time(),
			'nonceStr'  => create_sn(10),
			'accessToken' => $access_token
		);		
		
		$sign_data =  array();
		foreach ($data as $key => $val){
			$sign_data[strtolower($key)] = $val;
		}
		ksort($sign_data);
		foreach ($sign_data as $key => &$val){
			$val = "{$key}={$val}";
		}
		unset($data['accessToken']);
		$sign = implode("&", $sign_data);
		$data['addrSign'] = sha1($sign);
		return $data;
	}
	
	/**
	 * 读取用户的 access_token
	 * @author wscsky
	 */
	function get_member_access_token(){
	    $member = session('member');
	    if(!$_SESSION['access_token']) return false;
	    $token_data = $_SESSION['access_token'];
	    if(time() - $token_data['token_time'] > $token_data['expires_in']){
	    	return $this->get_access_token_byrefresh_token($member->openid, $token_data['refresh_token']);
	    }else{
	    	if($this->chk_member_access_token($member->openid, $token_data['access_token'])){
	    		return $token_data['access_token'];
	    	}else 
	    	    return false;
	    }
	    
	}
	
	/**
	 * 通过refresh_token读取用户的access_token
	 * @author wscsky
	 */
	function get_access_token_byrefresh_token($openid = "", $refresh_token = ""){
	    if(empty($openid) || empty($refresh_token)) return false;
		$url  = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".C("wxAPPID")."&grant_type=refresh_token&refresh_token=".$refresh_token;
		$data = json_decode($this->curl_http($url),true);
		if($data['errcode']){
			return false;
		}else{
			$_SESSION['access_token'] = $data;
			return $data['access_token'];
		}
	}
	
	/**
	 * 检查用户的access_token是否有效
	 * @author wscsky
	*/
	function chk_member_access_token($openid = "", $access_token = ""){
	    if($openid == "" || $access_token == "") return false;
		$url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$openid}";
		$data = json_decode($this->curl_http($url),true);
		if($data['errcode'] == "0"){
			return true;
		}else{
		    \Think\Log::log("chk_access_token", "用户access_token验证失败",$url);
			return false;
		}
	}
	
	
	/**
	 * 设置公众账号服务所处行业：需要选择2个，每月可更改1次
	 * @author watchman
	 * @param $data 要设置的2个行业的行业代码（可在文档中查询行业代码）
	 * @return bool
	 */
	function set_industry($data = array(1, 31)){
	    if(count($data) != 2 || !is_numeric($data[0]) || !is_numeric($data[0])){
	        $this->_error = '参数有误！';
	        return false;
	    }
	
	    $post_data = array(
	        "industry_id1" => $data[0],
	        "industry_id2" => $data[1]
	    );
	    $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=".$this->get_access_token();
	
	    $result = $this->post_json($url, $post_data);
	    $result = json_decode($result, true);
	    if($result['errcode'] == 0){
	        return true;
	    }else{
	        $this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
	        return false;
	    }
	}
	
	/**
	 * 获取消息模板ID
	 * @author watchman
	 * @param $template_id_short 模板库中模板的编号（有“TM**”和“OPENTMTM**”等形式）
	 * @return string $template_id 消息模板ID
	 */
	function get_template_id($template_id_short){
	    if(!$template_id_short){
	        $this->_error = "请输入模板的编号！";
	        return false;
	    }
	
	    $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$this->get_access_token();
	    $result = $this->post_json($url, array('template_id_short' => $template_id_short));
	    $result = json_decode($result, true);
	    if($result['errcode'] == 0){
	        return $result['template_id'];
	    }else{
	        $this->_error = "errcode:{$result['errcode']},errmsg:{$result['errmsg']}";
	        return false;
	    }
	}
	
	/**
	 * 发送模板消息到用户
	 * @author watchman
	 * @param array $wxUser	要发送的用户的openid
	 * @param int $template_id 消息模板ID
	 * @param string $url 消息链接
	 * @param string $topcolor 链接颜色
	 * @param array $data 要发送的消息（消息体格式请看文档）
	 */
	function send_template_msg($wxUser = array(), $template_id, $url, $topcolor = '#FF0000', $data = array()){
	    if(empty($wxUser)){
	        $this->_error = '请输入要发送的用户的openid！';
	        return false;
	    };
	    if(is_string($wxUser)){
	        $wxUser = array($wxUser);
	    }
	    $post_data['touser'] = $wxUser;
	    if(!$template_id){
	        $this->_error = '请输入您选择的消息模板！';
	        return false;
	    }else{
	        $post_data['template_id'] = $template_id;
	    }
	    if(!$url){
	        $this->_error = '请输入消息链接！';
	        return false;
	    }else{
	        $post_data['url'] 		= $url;
	        $post_data['topcolor']	= $topcolor;
	    }
	    if(empty($data)){
	        $this->_error = '请输入您要发送的消息！';
	        return false;
	    }else{
	        $post_data['data'] = $data;
	    }
	
	    $post_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->get_access_token();
	    foreach ($wxUser as $openid){
	        $post_data['touser'] = $openid;
	        $this->post_json($post_url, $post_data);
	    }
	}
	/*
	 * 获得JS-SDK签名所需的jsapi_ticket
	* @author eva
	* */
	function get_jsapi_ticket($force=false){
	    if($this->jsapi_ticket && !$force) return $this->jsapi_ticket;
	    $jsapi_ticket = S("jsapi_ticket_kdjsk");
	    if($force || !$jsapi_ticket){
	        $url	= "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->get_access_token()."&type=jsapi";
	        $data 	= json_decode(self::curl_http($url),true);
	        if($data['ticket']){
	            $jsapi_ticket = $data['ticket'];
	            S("jsapi_ticket_kdjsk",$jsapi_ticket, $data['expires_in']);
	        }else{
	            S("jsapi_ticket_kdjsk",null);
	            $this->_error = "errcode:{$data['errcode']}";
	        }
	    }
	    $this->jsapi_ticket = $jsapi_ticket;
	    return $jsapi_ticket;
	}
	
	
	/**
	 * 获得jssdk的签名数据
	 * @return array(
	 * timestamp: , // 必填，生成签名的时间戳
	 * nonceStr: '', // 必填，生成签名的随机串
	 * signature: '',/);
	 * 
	 */
	function get_jssdk_sign(){
	    $timestamp = time();
	    $data	= array(
	        'noncestr'		=> md5($timestamp."wscsky"),
	        'jsapi_ticket'	=> $this->get_jsapi_ticket(),
	        'timestamp'		=> $timestamp,
	        'url'			=> "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
	    );
	    return $this->get_sign($data);
	}
	
	/**
	 * 获得签名
	 * @author eva
	 */
	function get_sign($data){
	    if(!is_array($data))	return false;
	    foreach($data as $key=>$value){
	        $signParam[strtolower($key)] = $value;
	    }
	    ksort($signParam,SORT_STRING);
	    $string1 = "";
	    foreach($signParam as $key=>$value){
	        $string1 .=$key."=".$value."&";
	    }
	
	    $string1	= substr($string1,0,-1);
	    $data['signature']	= sha1($string1);
	    return $data;
	}
	
	/**
	 * 调用推荐者puid cookie数据
	 */
	function set_puid_cookie($puid = 0){
		$member = session('member');
		$cpuid = cookie('puid');
		$puid = $puid ? $puid : I("puid",0,"intval");
		if($puid > 0 && $puid != $cpuid && $puid != $member->uid){
			cookie("puid", $puid, 60*60*C('puid_cookie_expire')); //3天有效
		}
	}
	/**
	 * 同步指定用户的微信数据
	 * @author wscsky
	 */
	function sync_member_info($openid){
	    $data = self:: get_wxuser_info($openid);
	    \Think\Log::log("wxapi", "sync_member_info", $data);
	    $sex_data = array(0=> null, 1=> "男", 2 => "女");
	    if(!$data['errcode']){
	        if($data['subscribe'] ==1){
	            $ndata = array(
	                'uname'     => show_uname($data['nickname'],'text'),
	                'nickname'  => base64_encode($data['nickname']),
	                'headimg'   => $data['headimgurl'],
	                'status'    => 1,
	                'sex'       => $sex_data[(int)$data['sex']],
	                'ansy_time' => time(),
	                'unionid'   => $data['unionid'],
	                'subscribe_time' => $data['subscribe_time']
	            );
	        }else{
	            $ndata = array('status' => 0, 'ansy_time' => time(),'unionid' => $data['unionid']);
	        }
	        return M('member') -> where("openid = '%s'", $openid) -> save($ndata);
	    }else{
	        M('member') -> where("openid = '%s'", $openid) -> save(array('ansy_time' => time()));
	        return FALSE;
	    }
	}
	
	
	/**
	 * 获取zycode
	 */
	function get_zycode($openid){
	    if(empty($openid)) return "";
	    $time 	= time();
	    $md5key = md5($openid . MD5_KEY. $time);
	    $zycode = base64_encode($openid ."{@}". $md5key ."{@}".$time);
	    return $zycode;
	}
	/**
	 * 通过zycode来进行用户登陆
	 */
	function login_by_zycode($zycode){
	    $member = session('member');
	    if(empty($zycode)) return false;
		unset($_GET['zycode']);
	    $zycode = base64_decode($zycode);
	    list($openid, $key, $time, ) = explode("{@}", $zycode);
	    if(!$time || $this->zycode_time > 0 && (time() - $time) > $this->zycode_time) return false;  //半小时有效
	    $md5key = md5($openid .MD5_KEY . $time);
	    if($md5key == $key){
	        $udata = D("Common/member", "Logic")->get_member_by_openid($openid,true);
	        if($udata) $member = (object)$member;
	        return $member;
	    }else{
	        return false;
	    }
	}
	/**
	 * 获取错误信息
	 */
	function getError(){
		return $this->_error;
	}
}