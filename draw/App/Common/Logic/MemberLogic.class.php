<?php
//会员相关逻辑代码
namespace Common\Logic;
class MemberLogic{
	
	protected $error = null;

	/**
	 * 通过用户的openid读取用户信息
	 * 找不到返回false
	 * @param string $openid 用户openid
	 * @param string $login  是否直接登陆 
	 * @return array
	 * @author wscsky
	 */
	
	function get_member_by_openid($openid, $login = true){
		if(empty($openid)) return false;		
		$model           = D("Common/Member");
		$uinfo = $model-> scope("login")->where("openid = '%s'", $openid)->find();
		if($login && $uinfo){
			$member = session('member');
			$member = (object)$uinfo;
			self::update_login_info(); //更新用户登陆信息
			generate_session_id();
			session_commit();
		}
		return $uinfo;
	}
	
	/**
	 * 读取用户的上级uid信息
	 * @param mix $uid 用户的uid 或者openid
	 * @return array(puid,puid2,puid3)
	 * @author wscsky
	 */
	function get_member_puids($uid){
		if(!$uid) return array(0,0,0);
		$data = M("member")-> where((is_numeric($uid) ? "uid = '%d'" : "opendid = '%s'"),$uid)
						   ->field("puid,puid2,puid3,puname,uid,uname,master")
						   ->find();
		if(!$data) $data = array(0,0,0);
		return $data;
	}
	
	/**
	 * 读取的下级会员列表
	 * @author watchman
	 * @param int $uid 会员ID
	 * @param int $level 第几级下级
	 * @return array $list
	 */
	function get_sub_list($uid, $level, $page = 1, $listRows = 10, &$count = 0){
		$member = session('member');
		
		if(!$uid || !is_numeric($uid))	$uid = $member->uid;
		
		$model 	= M("member");
		$map 	= array();
		switch ($level){
			case 2:
				$map['puid2'] = $uid;
				break;
			case 3:
				$map['puid3'] = $uid;
				break;
			default:
				$map['puid'] = $uid;
				break;
		}
		//只显示已关注或者是代理的会员
		$map['master|status'] = 1;
		//数据统计
		$count 	= $model	-> where($map)-> count();
		$model	-> where($map)-> page("{$page}, {$listRows}");
		$list 	= $model->order("uid")->field("uid, gid, uname, nickname, headimg,master,status, reg_time, subscribe_time")->select();
		//echo $model->getLastSQL();
		return $list;
	}
	
	/**
	 * 读取的下级代理列表
	 * @author eva
	 * @param int $uid 会员ID
	 * @param int $level 第几级下线代理
	 * @param bool $flag true【计算代理人数】 false【我的会员数】
	 * @param string str [puid] [ruid] 
	 * @return array $list
	 */
	function get_sub_list2($uid, $level, $page = 1, $listRows = 10, &$count = 0,$flag=false,$str='puid'){
		$member = session('member');
	
		if(!$uid || !is_numeric($uid))	$uid = $member->uid;
	
		$model 	= M("member");
		$map 	= array();
		if($level<=0 || !$level){
			$level = 1;
		}
		$model	=  new \Think\Model();
		$field	= "uid,uname,headimg,status,gid,subscribe_time";
		$sql	= "select ".$field." from ".C('DB_PREFIX')."member where master = 1 and ".$str." in (";
		$sql2	= "select uid from ".C('DB_PREFIX')."member where master = 1 and ".$str." in (";
		if($level==1){
			$sql .= $uid.")";
		}else{
			$num	= $level-1;
			for($i=1;$i<=$num;$i++){
				$sql .=$sql2;
			}
			$sql .=$uid;
			for($i=1;$i<=$level;$i++){
				$kuohao	.=")";
			}
			$sql .= $kuohao;
		}
		$list_total	= $model->query($sql);
		
		foreach ($list_total as $k=>$v){
			if($flag){
				$is_agent	= D("Common/Group","Logic")->is_agent($v['gid']);
				if(!$is_agent){
					unset($list_total[$k]);
				}
			}
		}
		$count	= count($list_total);
		$startCount =($page-1)*$listRows;
		$sql .= " order by uid limit ".$startCount.", $listRows";
		$list	= $model -> query($sql);
		foreach ($list as $k=>$v){
			if($flag){
				$is_agent	= D("Common/Group","Logic")->is_agent($v['gid']);
				if(!$is_agent || $v['master']){
					unset($list[$k]);
				}
			}
		}
		
		if(!$list){
			$this->error = $model->getDbError();
			return false;
		}
		return $list;
	}
	
	/**
	 * 读取我的会员统计
	 * @param int $puid	推荐者ID
	 */
	function get_mymember_static($puid = 0){
		$member = session('member');
		$puid = $puid > 0 ? $puid : $member->uid;
		$model = M("member");
		$field = "sum(case WHEN puid = {$puid} then 1 else 0 end) as level1,
				  sum(case WHEN puid2 = {$puid} then 1 else 0 end) as level2,
				  sum(case WHEN puid3 = {$puid} then 1 else 0 end) as level3";
		return $model -> where("master = 1 or status =1") -> field($field)->find();
	}
	
	/**
	 * 读取用户二维码上数据的用户uid
	 * @param int $id 场景值
	 * @param string 二维码 ticket
	 * @author wscsky
	 */
	function get_qrcode_uid($id, $ticket = ""){
		//先用ticke查临时二维码数据
		if($ticket){
			$uid = M("qrcodes")->where("ticket = '%s'", $ticket)->getField("uid");
			if($uid > 0) return $uid; 
		}
		if(!is_numeric($id)) return 0;
		$uid = M("member_qrcode") -> where("id = '%d'", $id) -> getField("uid");
		return $uid ? $uid : 0;
	}
	
	/**
	 * 添加用户信息
	 * @param array $data 用户信息
	 * @param tring $msg  送积分返回消息
	 * @author wscsky
	 */
	function add_member($data, &$msg = "", &$msg2 = ""){
		if(empty($data) || !$data['openid']) return false;
		$model = D("Member");
		$isnew = true;
		if($data['uid']){
			$uid = $data['uid'];unset($data['uid']);
			if($data['old']['subscribe_time'] > 0) $isnew = false;
			if($data){
				if($data['puid'] == $uid){
					$data['puid']=$data['puid2']=$data['puid3']=0;
					$data['puname'] = "";
				}
				$data = $model -> create($data);
				if($data)
					$model -> where("uid = %d",$uid) -> save($data);
				else 
					return $uid;			
			}else{
				return $uid;
			}
		}else{
			unset($data['master']);
			$data = $model->create($data);
			if($data){
				$uid   = $model -> add($data);
			}else{
				return false;
			}
		}
		if(!$uid){
			\Think\Log::log("adduerror", "添加/修改用户失败", $model->getError());
			return false;
		}
		//统计上级用户推荐数据
		if($data['puid'] > 0)  self::update_pmember_stat($data['puid']);
		if($data['puid2'] > 0) self::update_pmember_stat($data['puid2']);
		if($data['puid3'] > 0) self::update_pmember_stat($data['puid3']);
		//关注赠送积分
		if($isnew && $data['status'] == MEMBER_STATUS_FOCUS && C('reg_integral')>0){
			$log = array(
					'type'			=> INTEGRAL_TYPE_PREG,
					'entity_type'	=> ENTITY_TYPE_MEMBER,
					'entity_id'		=> $uid,
					'remark'		=> "关注赠送积分",
			);
			D("Common/Finance", "Logic") -> change_integral(C('reg_integral'),MODE_TYPE_INC, $uid, $log);	
			$msg2 = "关注赠送您".C('reg_integral')."积分";
		}
		//关注给推荐者送积分
		if(C('reg_give_integral') > 0 && $isnew && $data['puid'] > 0 && $data['status'] == MEMBER_STATUS_FOCUS){
			$log = array(
				'type'			=> INTEGRAL_TYPE_PREG,
				'entity_type'	=> ENTITY_TYPE_MEMBER,
				'entity_id'		=> $uid,
				'remark'		=> "推荐[{$data['uname']}]用户注册送积分",
			);
			if(D("Common/Finance", "Logic") -> change_integral(C('reg_give_integral'),MODE_TYPE_INC, $data['puid'], $log)){
				$msg = "您获得".C('reg_give_integral')."积分";
			}	
		}
 		return $uid;		
	}
	
	/**
	 * 更新用户的下线统计
	 * @author wscsky
	 */
	function update_pmember_stat($puid){
		$data = $this->get_mymember_static($puid);
		$model = M("member");
		M("member_finance")->where("uid = %d", $puid) -> save($data);
	}
	
	/**
	 * 通过微信openid读取登陆
	 * 登陆成功返回uid,不成功返回false
	 */
	function wxlogin($openid = ""){
		$member = session('member');
		if(empty($openid) && $member->uid > 0){
			return $member -> uid;
		}
		if(empty($openid)){
			$this -> error	= "openid为空!";
			return false;
		}
		$model = M('member');
		$user_info = $model-> scope("login")->where("openid = '%s'", $openid)->find();
		if(!$user_info){
			$member = default_anonymous_user();
			$this -> error	= "未找到用户信息!";
			return false;
		}
		else{
			$member = (object)$user_info;
			self::update_login_info(); //更新用户登陆信息
		}
		generate_session_id();
		session_commit();
		return $member->uid;
	}

	/**
	 * 会员登陆模块
	 * @param string $uname	用户名
	 * @param string $password 密码
	 * @param string $verify_code 验证码
	 * @author wscsky
	 */
	function login($uname, $password, $verify_code=''){
		if(empty($uname) || empty($password)){
			$this->error = "用户名和密码不能为空!";
			return false;
		}
		//如果开启验证码,验证
		if(C("MEMBER_LOGIN_VERIFY")){
			if($verify_code == ""){
				$this->error = "登陆失败,验证为空!";
				return false;
			}
			$verify = new \Think\Verify();
			$result = $verify->check($verify_code);
			if(!$result){
				$this->error = "验证码不正确!";
				return false;
			}
		}
	
		$model           = D("Common/Member");
		$map             = array();
		$map['m.uname']  = $uname;
		$map['m.password']	= md5(MEMBER_LOGIN_KEY . $password);

		$user_info = $model-> scope("login")->where($map)->find();
		
		if(!$user_info){
			$GLOBALS['member'] = default_anonymous_user();
			$this -> error	= "用户名或者密码有误!";
			return false;
		}
		else{		
			$GLOBALS['member'] = (object)$user_info;
			self::update_login_info(); //更新用户登陆信息
		}
		generate_session_id();
		session_commit();
		return true;
	}
	
	/**
	 * 用户退出登陆
	 * @author wscsky
	 */
	function logout(){
		\Think\Log::log("logout", "退出系统");
		session_destroy();
		return;
	}
	/**
	 * 保存编辑的用户信息
	 * @author	watchman
	 * @param	array	$data		从表单读过来的数据
	 * @param	bool	$is_update	更新用户登陆信息
	 * @return	void
	 */
	function save($data = array(), $is_update = true){
		$member = session('member');
		$model = D("Common/Member");
		$model->startTrans();//开启事务
		unset($data['master']);
		$_data = $model->create($data);
		
		if(!$_data){//验证数据
			$this->error = $model->getError();
			return false;
		}else{
			//处理昵称，昵称不填则默认为其真实姓名
			$model->nickname ? '' : $model->nickname = $model->real_name;
			
			$uid = $model->uid;
			if($uid == 0){//新增
				unset($model->uid);

				//注册时处理用户的用户组（会员等级）
				$new_gid = $_data['gid'] ? $_data['gid'] : 0;
				$_data['gid'] = MEMBER_DEFAULT_GID;//注册时用户的等级设为默认
				
				$result = $model->add($_data);
				if(!$result){
					$this->error = $model->getError();
				}else{
					$uid 	= $result;
					$gid	= MEMBER_DEFAULT_GID;
				}
				
			}else{
				//编辑时unset不需要修改的数据
				unset($_data['puid'],$_data['gid'],$_data['puname'],$_data['uname']);
				$result = $model->save($_data);
				if(!$result){
					$this ->error = $model->getError();
				}else{
					$gid	= $member->gid;
					
				}
			}

			if($result === false){
				$model->rollback();
				return false;
			}
			
			if($is_update === true){
				$user_info = $model->scope("login")->where("m.uid = %d", $uid)->find();
				if(!$user_info){
					$GLOBALS['member'] = default_anonymous_user();
					return false;
				}
				else{
					$GLOBALS['member'] = (object)$user_info;
					self::update_login_info(); //更新用户登陆信息
				}
				generate_session_id();
				session_commit();
			}
			
			$model->commit();
			return $uid;
		}
	}
	
	/**
	 * 更新用户登陆信息
	 * @author wscsky
	 */
	function update_login_info(){
		$member = session('member');
		if(!$member || $member->uid == 0) return;
		$data = array(
				'last_time'		=> array("exp","last_online"),
				'last_online'	=> NOW_TIME,
				'login_time'	=> array("exp","login_time + 1"),
				'last_ip'		=> get_client_ip(),
		);
		M("member") -> where("uid = %d", $member->uid)->save($data);
		//记录日志
		//\Think\Log::log("login", "登陆系统");
	}
	
	/**
	 * 读取用户信息
	 * @access	public
	 * @param	int		$uid/$openid 	会员UID
	 * @param	array	$link	会员关系数据 如: group:会员组; auth 认证; finance 财务信息
	 * @return	array	$user_name	会员产帐户
	 *
	 */
	function get_member_info($uid = 0, $link= array())
	{		
		if(!$uid) return false;
		$model 	= D("Common/Member");
		$map	= array();
		if(is_numeric($uid)){
			$map['uid'] 		= $uid;
		}else{
			$map['openid']	= uid;
		}
		$model -> where($map);
		if(!empty($link)) $model -> relation($link);
		$uinfo = $model->find();
// 		echo $model->getLastSQL();
		return $uinfo;
	}
	
	/**
	 * 读取用户实体信息[get_entity函数调用]
	 * @param int $uid 用户ID
	 * @return array()
	 */
	function get_entity($uid){
		if(!is_numeric($uid)) return false;
		$data = D("Common/Member","Logic")->get_member_info($uid,"");
		return $data;
	}
	
	/**
	 * 读取用户表指定数据
	 * @param mix $uid 用户UID/openid
	 * @param string $field 要读取的字段,多个字段用,分开
	 * @return mix 如果是单个之段返回字串,如果是多个返回数组;
	 * @author wscsky
	 */
	function get_member_field($uid, $field = "openid"){
	    $model  = M("member");
	    if(is_numeric($uid)){
        	$result = $model-> field($field)->where('uid = %d',$uid)->find();
	    }else{
	    	$result = $model -> where("openid = '%s'", $uid)->field($field) -> find();
	    }
        if($result){
        	if(count($result) == 1)
        	    return $result[$field];
            else 
                return $result;
        }
        return false;
	}
	
	/**
	 * 读取用户推荐者的指定数据
	 * @param mix $uid 被推荐用户UID/用户帐户
	 * @param string $field 要读取的字段,多个字段用,分开
	 * @return mix 如果是单个之段返回字串,如果是多个返回数组;
	 * @author wscsky
	 */
	function get_pmember_field($uid, $field = "uname"){
	    $puid	= self::get_member_field($uid,"puid");
	    if(!$puid) return false;
	    return self::get_member_field($puid, $field);
	}
	
	/**
	 * 读取用户的上层用户数据
	 * @param mix $uid 用户UID/用户帐户
	 * @param string $field 要读取的字段,多个字段用,分开
	 * @return mix 如果是单个之段返回字串,如果是多个返回数组;
	 * @author wscsky
	 */
	function get_player_field($uid, $field = "uname"){
		$puid	= self::get_member_field($uid,"layer_puid");
		if(!$puid) return false;
		return self::get_member_field($puid, $field);
	}
	
	/**
	 * 我的推广
	 * 统计会员 所有下线的所有订单数
	 * 统计 会员 的 已成为分销的下线数
	 * $uid	用户uid 或者 openID
	 * @author eva
	 */
	function statistics_orders($uid){
		$member = session('member');
		if(!$uid){$uid = $member->uid;} 
		
		$model			=  new \Think\Model();
		$ordersnum		= $model -> query("SELECT COUNT(order_id) as count FROM ".C("DB_PREFIX")."orders WHERE uid IN (SELECT uid  FROM ".C("DB_PREFIX")."member WHERE puid=".$uid." OR puid2=".$uid." OR puid3=".$uid.") AND order_status IN (0,1,2,3,4)");
		if($ordersnum===false){
			$this->error	= $model->getDbError();
			return false;
		}
		
		$map['puid|puid2|puid3']	= $uid;
		$map['master']				= MEMBER_MASTER_YES;
		$next_members		= M("member")->where($map)->count();
		return array('ordersnum'=>$ordersnum[0]['count'],'next_members'=>$next_members);
	}
	
	/**
	 * 读取用户的左/右线最后一名用户UID
	 * @param int $uid	:用户UID
	 * @param string $line_type :左/右线 left,right
	 * @author wscsky
	 */
	function get_last_line_uid($uid, $line_type = "left"){
		if($line_type != "left" && $line_type != "right") return false;
		$line_uid = self::get_member_field($uid,"{$line_type}_uid");
		if($line_uid == 0)
			return $uid;
		else 
			return self::get_last_line_uid($line_uid, $line_type);	
	}
	
	/**
	 * 读取用户直推用户数量
	 * @param int 	$puid 	:推荐者UID
	 * @param bool	$contain:是否包含普通会员,即没有投资的用户
	 * @return int 推荐的人数统计
	 * @author wscsky
	 */
	function get_mymember_num($puid, $contain = false){
		if($contain){
			return M("member") -> where("status = 1 and puid = %d", $puid) -> count();
		}else{
			return M("member") -> where("status = 1 and gid != '". MEMBER_DEFAULT_GID ."' and puid = %d", $puid) -> count();
		}
	}
	
	/**
	 * 查验证信息是否存在
	 * @param string $auth 数据
	 * @param string $type 查询类型 email mobile idcrad
	 * @param int $uid 排除的UID
	 * @param bool $is_auth
	 * @return bool true可用　flase不可用
	 * @return string $where 其它查询条件
	 */
	function check_auth_info($auth, $type = "email", $uid = 0, $is_auth = false, $where = array()){
		switch ($type){
			case "mobile":
				if(!is_mobile($auth)) return false;
				$field = "mobile";
				break;
			case "email":
				if(!is_email($auth)) return false;
				$field = "email";
				break;
			case "idcard":
				if(!is_idcard($auth)) return false;
				$field = "idcard";
				break;
			default:
				return false;
				break;			
		}
		$member = session('member');
		if($uid == 0) $uid = $member->uid;
		$map[$field] = $auth;
		if($uid > 0 ) $map['uid'] = array("neq", $uid);
		if($is_auth){
			$map[$field."_t"] =  array("exp","is not null");
		}
		if(is_array($where) && count($where) != 0){
			$map = array_merge($map, $where);
		}
		$count = M("member_auth")->where($map)->count();
		if($count == 0)
			return true;
		else
			return false;
	}
	
	/**
	 * 改变用户审核状态
	 * @author	watchman
	 * @param	int		$uid	用户ID
	 * @param	int		$status	要修改为的用户状态值
	 * @param	string	$info	操作信息：审核失败或成功信息;为false时，不保存审核信息
	 * @param	string	$clean_up	是否清空认证信息数据
	 * @return	bool
	 */
	function change_auth_status($uid, $status, $info = false, $clean_up = false){
		$model = M("Member_auth");
		if($model->where("uid = %d", $uid)->save(array('idcard_status'=>$status, 'idcard_t'=>time())) !== false){
			
			$data = array();
	
			if($clean_up !== false){
				$data = array(
						'real_name' 		=> NULL,
						'idcard'			=> NULL,
						'idcard_t'			=> NULL,
						'idcard_validity'	=> NULL,
						'body_photo'		=> NULL,
						'idcard_facade'		=> NULL,
						'idcard_obverse'	=> NULL,
						'idcard_info'		=> NULL,
				);
	
				//删除之前的文件使用绑定并把其设置为未使用状态
				$file_data = array(
						FILE_TYPE_AUTH. "_body_photo",
						FILE_TYPE_AUTH. "_idcard_facade",
						FILE_TYPE_AUTH. "_idcard_info",
				);
	
				$file_model  = D("Common/File","Logic");
				if($file_model -> delete_file_use(0, $uid, $file_data) === false){
					return false;
				};
			}
			
			if($info !== false){
				$data = array("idcard_info" => $info);
			}
	
			$result = $model -> where("uid = {$uid}") -> save($data);
			if($result !== false){
				return true;
			}else{
				return false;
			}
	
		}else{
			return false;
		}
	}
	
	/**
	 * 保存邮箱验证信息
	 * @param string $email 邮箱地址
	 * @param int $uid 用户UID
	 * @return bool
	 * @author wscsky
	 */
	function save_email_auth($email, $uid=0){
	    $member = session('member');
		$uid == 0 && $uid = $memer->uid;
		if(!is_email($email)){
			$this->error = "无效的电子邮箱地址!";
			return false;
		}
		if(self::check_auth_info($email,"email",$uid,true)){
			M("member_auth") -> where("email = '%s'",$email) -> save(array("email"=> null, 'email_t'=> null));
			if(M("member_auth")->where("uid = %d", $uid)->count() == 0){
				M("member_auth") -> add(array("uid"=> $uid, "email"=> $email, 'email_t'=> time()));
			}else{
				M("member_auth") -> where("uid = %d",$uid) -> save(array("email"=> $email, 'email_t'=> time()));
			}
			return true;
		}else{
		    $this->error = "该邮箱已认证过了,请不要重复验证!";
		    return false;
		}
		
	}
	
	/**
	 * 保存手机号码验证
	 * @param string $mobile 手机号码
	 * @param string $code 手机验证码
	 * @package int $uid 用户ID
	 * @return bool
	 */
	function save_mobile_auth($mobile, $code, $uid = 0){
		$member = session('member');
		if(!is_mobile($mobile)){
			$this->error = "无效的手机号码";
			return false;
		}
		if($code == ""){
			$this->error = "验证码为空";
			return false;
		}
		if($uid == 0) $uid = $member->uid;
		if($uid == 0){
			$this->error = "用户信息丢失";
			return false;
		}
		$model = D("Common/Sms","Logic");
		$result = $model -> checkSmsCode($mobile, $code, "auth");
		if(!$result){
			$this->error = "绑定失败，验证码有误！";
			return false;
		}
		//查是否已被绑定
		if(self::check_auth_info($mobile,'mobile',$uid,true)){
			M("member_auth") -> where("mobile = '%s'",$mobile) -> save(array("mobile"=> null, 'mobile_t'=> null));
			if(M("member_auth")->where("uid = %d", $uid)->count() == 0){
				M("member_auth") -> add(array("uid"=> $uid, "mobile"=> $mobile, 'mobile_t'=> time()));
			}else{
				M("member_auth") -> where("uid = %d",$uid) -> save(array("mobile"=> $mobile, 'mobile_t'=> time()));
			}
			return true;
		}else{
			$this->error = "绑定失败，该手机号已被使用！";
			return false;			
		}
	}
	
	/**
	 * 查用户是否可以成为东家/VIP
	 * 返回结果为 是否为东家、VIP
	 * 如果用户是东家。直接返回true
	 * @param int $uid
	 * @return bool
	 */
	function check_member_vip($uid){
		$uinfo = $this->get_member_info($uid, array("finance"));
		if(!$uinfo) return false;
		if($uinfo['master'] == 1) return true;
		$master = false;
		if(C('vip_rank_integral')>0 && $uinfo['finance']['rank_integral'] >= C('vip_rank_integral')){
		    $master = true;
		}
		if(!$master && C('vip_needs_num') > 0){
		    $count = M("member") -> where("puid = %d", $uid)->count();
		    if($count >= C('vip_needs_num')){
		        $master = true;
		    }
		}
		if($master){
			M("member")->where("uid = %d", $uid)->save(array("master"=>1));
			return true;			
		}
		return false;
	}
	/**
	 * 自动处理用户等级
	 * @param int $uid 用户UID
	 * @param array $log 日志内容
	 * @author wscsky
	 * @return $gid
	 */
	function check_member_group($uid, $log = array()){
	    $uinfo = $this->get_member_info($uid, array("finance"));
	    if(!$uinfo) return false;
	    $model = M("group");
	    $rank_integral = $uinfo['finance']['rank_integral'];
	    $newgroup = $model -> where("rank_integral <= %d",$rank_integral)->order("rank_integral desc")->find();
	    $nowgroup = $model -> where("gid = %d", $uinfo['gid'])->find();
	    if(!$newgroup || $nowgroup['rank_integral'] > $newgroup['rank_integral']){
	    	return $uinfo['gid'];
	    }
	    $gid = $newgroup['gid'];
	    if(M('member')->where("uid = %d", $uid) -> save(array("gid"=>$gid))){
            $log = array(
                'uid'   => $uid,
                'gid'   => $uinfo['gid'],
                'new_gid' => $newgroup['gid'],
                'status'  => MEMBERLOG_STATUS_AUDITED,
                'pay_type'=> MEMBERLOG_PAY_AUTO,
                'apply_time' => time(),
                'audit_time' => time(),
                'audit_aid'  => 0,
                'audit_aname' => 'system',
            	'remark' => "您成长值达到{$newgroup['rank_integral']},自动升级到{$newgroup['name']}",
            );
            M("member_log") -> add($log);
            return $gid;
	    }
	    return $uid['gid'];
	}
	/**
	 * 会员升级记录
	 * 修改 或者 插入一条会员升级记录
	 * @param int uid 会员编号
	 * @param floatval money 升级所需费用
	 * @param int logid 会员升级记录自增ID
	 * @param array log 数据 例如array('gid'=>,'new_gid'=>,'pay_type'=>,'status'=>....); 
	 * @author eva
	 * 
	 */
	function member_log($uid, $money, $logid=0, $log=array()){
		$member = session('member');
		$member->uid>0 && $uid=$member->uid;
		if(!is_numeric($uid) || $uid<=0){
			$this->error = "用户UID参数有误";
			return false;
		}
		if(!is_numeric($log['gid']) || $log['gid']<=0){
			$this->error = "用户组ID参数有误";
			return false;
		}
		if(!is_numeric($log['new_gid']) || $log['new_gid']<=0){
			$this->error = "新用户组ID参数有误";
			return false;
		}
		//if(!is_numeric($money) || $money<=0){
		//	$this->error = "升级所需费用不是数字或者小于0";
		//	return false;
		//}
		if(!in_array($log['pay_type'],array(MEMBERLOG_PAY_ONLINE,MEMBERLOG_PAY_OFFLINE))){
				$this->error = "该支付方式不存在";
				return false;
		}
		if(!in_array($log['status'],array(MEMBERLOG_STATUS_UNPAY,MEMBERLOG_STATUS_AUDITED,MEMBERLOG_STATUS_CANCEL))){
			$this->error = "该升级状态不存在";
			return false;
		}
		
		$data	= array();
		$data['uid']	= $uid;
		$data['money'] 	= $money;
		isset($log['gid'])			&& 	$log['gid']			&& $data['gid']	= $log['gid'];
		isset($log['ruid'])			&& 	$log['ruid']		&& $data['ruid']	= $log['ruid'];
		isset($log['new_gid'])		&& $log['new_gid']		&& $data['new_gid']= $log['new_gid'];
		isset($log['sn'])			&& $log['sn']			&& $data['sn']= $log['sn'];
		is_numeric($log['wxpay'])   && $log['wxpay']>0  	&& $data['wxpay']	= $log['wxpay'];
		is_numeric($log['balance']) && $log['balance']>0	&& $data['balance']	= $log['balance'];
		
		isset($log['status'])		&& $data['status'] = $log['status'];
		isset($log['pay_type'])		&& $data['pay_type']=$log['pay_type'];
		
		$log['operator_aid']	?	$data['operator_aid']  = $log['operator_aid']:$data['operator_aid']=$member->aid;
		$log['operator_aname']	? 	$data['operator_aname']= $log['operator_aname']:$data['operator_aname']=$member->uname;
		$logid==0 && $data['apply_time']	= time();
		if(isset($log['audit_time']) && $log['audit_time']){
			$data['audit_time'] = $log['audit_time'];
		}else{
			$logid && $data['audit_time'] = time();
		}
		
		isset($log['remark']) && $log['remark'] && $data['remark'] = $log['remark'];
		if($logid){
			$result = M("member_log")->where("logid=%d",$logid)->save($data);
		}else{
			$result = M("member_log")->add($data);
		}
		
		return $result; 
	}
	
	/**
	 * 会员升级 给代理推荐人 加提成
	 * @param int uid 会员uid
	 * @param int ruid 推荐人uid
	 * @param int money 升级所需费用
	 * @param array data  array('entity_id'=>,'new_group_name'=>,'old_group_name'=>)
	 * @author eva
	 */
	function recommend_brokerage($uid,$ruid,$money,$status=PROFIT_STATUS_UNTAKE,$data=array()){
		return true; //暂无此功能
	    if(!is_numeric($uid) || $uid<0 || !is_numeric($ruid) || $ruid<0 || !is_numeric($money) || $money<0){
			$this->error = "参数有误";
			return false;
		}
		if(!in_array_case($status, array(PROFIT_STATUS_UNCONFIRMED,PROFIT_STATUS_CONFIRMED,PROFIT_STATUS_INVALID,PROFIT_STATUS_UNTAKE))){
			$this->error = "该收益状态不存在";
			return false;
		}
		$model 		= D("Common/Group","Logic");
		$model_finance	= D("Common/Finance",'Logic');
		
		//给直接推荐人的提成
		$data['brokerage1']	= $money*C('first_referrer') *0.01;
		$data['brokerage2']	= $money*C('second_referrer') *0.01;
		$data['brokerage3']	= $money*C('third_referrer') *0.01;
		
		//查看会员原来是否有推荐人
		$self_memb	= 	self::get_member_info($uid);
		if(!$self_memb['ruid']){
			$memb1 	= self::get_member_info($ruid);
			$result	= M("member")->where("uid=%d",$uid)->save(array('ruid'=>$memb1['uid']));
			if(!$result){
				$this->error = '插入推荐人uid失败';
				return false;
			}
		}
			//获得会员的代理推荐人的uid
			$referrers	= self::get_referrers($ruid);
			foreach($referrers as $k=>$v){
				$memb = self::get_member_info($v);
				$profit_log	= array(
						'uname'			=> $memb['uname'],
						'entity_id' 	=> $data['entity_id'],
						'entity_type'	=> ENTITY_TYPE_MEMBERLOG,
						'remark'		=> "作为".$self_memb['uname']."[".$data['old_group_name']."->".$data['old_group_name']."]的第".$k."推荐人",
				);
				$balance_log	= array(
						'uname'			=> $memb['uname'],
						'type'			=> BALANCE_TYPE_REF,
						'entity_id'		=> $data['entity_id'],
						'entity_type'	=> ENTITY_TYPE_MEMBERLOG,
						'mode'			=> MODE_TYPE_INC,
						'remark'		=> "作为".$self_memb['uname']."[".$data['old_group_name']."->".$data['old_group_name']."]的第".$k."推荐人",
				);
				switch($k){
					case 1:
						$change_money			= $data['brokerage1'];
						$balance_log['money']	= $data['brokerage1'];
						$balance_log['balance']	= $memb['balance'] + $data['brokerage1'];
						break;
					case 2:
						$change_money			= $data['brokerage2'];
						$balance_log['money']	= $data['brokerage2'];
						$balance_log['balance']	= $memb['balance'] + $data['brokerage2'];
						break;
					case 3:
						$change_money			= $data['brokerage3'];
						$balance_log['money']	= $data['brokerage3'];
						$balance_log['balance']	= $memb['balance'] + $data['brokerage3'];
						break;
				}
				$change_money > 0 && $result = $model_finance->change_balance($change_money, MODE_TYPE_INC, $memb['uid'], $balance_log);
				
				$result 		= $model_finance->insert_profit_log($memb['uid'],PROFIT_TYPE_REF,$change_money,$status,$profit_log);
				
			}
			return true;
		
	}
	/**
	 * 获得某用户 的是代理商的 上三级以内推荐人 
	 * @author eva
	 */
	function get_referrers($ruid){
		$member1	= self::get_member_info($ruid);
		if(!$member1){
			$this->error = "未找到该直接推荐人信息";
			return false;
		}
		$is_agent = D("Common/Group","Logic")->is_agent($member1['gid']);
		if(!$is_agent){
			$this->error = "直接推荐人不是代理商，不能做推荐人";
			return false;
		}
		
		$data	= array();
		$data['1'] = $member1['uid'];
		$member2	= self::get_member_info($member1['ruid']);
		if($member2){
			$is_agent = D("Common/Group","Logic")->is_agent($member2['gid']);
			if($is_agent){
				$data['2'] = $member2['uid']; 
				
				$member3	= self::get_member_info($member2['ruid']);
				if($member3){
					$is_agent = D("Common/Group","Logic")->is_agent($member3['gid']);
					if($is_agent){
						$data['3'] = $member3['uid'];
					}
				}
				
			}
		}
		return $data;
		
	}
	/**
	 * 读取错误信息
	 */
	function getError(){
		return $this->error;		
	}
	
        
        function count_user()
        {
            $return = array();
            $member = session('member');
            $where = array('shanghu_uid'=>array('eq',$member->id));
            $return['total'] = $this->num_count($where);
            $daytime = strtotime(date('Y-m-d'),strtotime('+1 day'));
            $dayWhere = $where;
            $dayWhere['bindtime'] = array('gt',$daytime); //当天数据条件
            $return['day_num_count'] = $this->num_count($dayWhere); //当天总订单
            $yesterdaytime = strtotime(date("Y-m-d",strtotime("-1 day")));
            $yesterdayWhere = $where;
            $yesterdayWhere['bindtime'] = array(array('gt', $yesterdaytime),array('lt', $daytime));
            $return['yday_num'] = $this->num_count($yesterdayWhere);
            $monthtime = strtotime(date("Y-m-d",strtotime("-1 month")));
            $monthWhere = $where;
            $monthWhere['bindtime'] = array('gt',$monthtime); //当天数据条件
            $return['month_num'] = $this->num_count($monthWhere);
            return $return;
        }
        
        function num_count($where = array())
        {
            $model = D("Common/Member");
            $info = $model->field("count(*) as total")->where($where)->find(); //订单总额
            return $info['total'];
        }
}