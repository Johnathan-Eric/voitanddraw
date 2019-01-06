<?php
/*
 * 自定公用函数
 * 开发过程通用函数可以放在此
 */

/*gbk转utf8*/
function gbk2utf8($data){
	if(is_array($data)){
		return array_map("gbk2utf8", $data);
	}
	else{
		return iconv("GBK","UTF-8", $data);
	}
}

/*utf8转gbk*/
function utf82gbk($data){
	if(is_array($data)){
		return array_map("utf82gbk", $data);
	}
	else{
		return iconv("UTF-8", "GBK" , $data);
	}
}
/**
 * 数据编码转换
 * @param  $data
 * @param  $in_charset
 * @param  $out_charset array('');
 */
function do_iconv($data, $in_charset = 'GBK', $out_charset = 'UTF-8'){

	if (is_array($data))
		return array_map("do_iconv",$data);
	else
		return iconv($in_charset, $out_charset , $data);
}


/**
 * 默认的匿名用户对象
 *
 * @return stdClass
 */
global $member;
function default_anonymous_user() {
	$user = new \stdClass();
	$user->uname        = "Guest";
	$user->openid 		= null;
	$user->uid 		= 0;	//会员ID
	$user->aid   		= 0;	//员工ID
	$user->is_lock		= 0;	//是否被锁定
	$user->discount     = 1;    //价格折扣
	$user->role_name    = '游客';
	return $user;
}

/**
 * 生成 SESSION 会话ID
 */
function generate_session_id() {
	$ses_id = md5(time() . uniqid(mt_rand(), true));
	return session_id($ses_id);
}

//验证会员是否登陆
function check_login(){
	$member = session('member');
	//print_r($member);exit;
	$is_login = false;

	if(!empty($member)){
	    $is_login = true;

	if(C("IS_ADMIN")){
            if($member && $member->aid > 0) $is_login = true;		
	}elseif(C("IS_MERCHANT")){
            if($member && $member->id > 0) $is_login = true;
        }else{
            if($member && $member->uid > 0) $is_login = true;
	}
	if(!$is_login){
            if(IS_AJAX){
                    echo json_encode(array('status'=> 0, 'info'=>"您未登陆或登陆超时，请重新登陆！", 'url' => C('USER_LOGIN_URL')));
            }else{
                    //redirect(C('USER_LOGIN_URL'));
                    echo "<script type='text/javascript'>";
                    echo "var url = '".C('USER_LOGIN_URL')."';";
                    echo "if(url.indexOf('?')==-1){url+='?';}else{url+='&';}";
                    echo "var referer = 'referer='+escape(location.href);";
                    echo "location.href=url+referer";
                    echo "</script>";
            }
            exit();
	}
	return $is_login;
// 	if(!$is_login){
//             if(IS_AJAX){
//                     echo json_encode(array('status'=> 0, 'info'=>"您未登陆或登陆超时，请重新登陆！", 'url' => C('USER_LOGIN_URL')));
//             }else{
//                     //redirect(C('USER_LOGIN_URL'));
//                     echo "<script type='text/javascript'>";
//                     echo "var url = '".C('USER_LOGIN_URL')."';";
//                     echo "if(url.indexOf('?')==-1){url+='?';}else{url+='&';}";
//                     echo "var referer = 'referer='+escape(location.href);";
//                     echo "location.href=url+referer";
//                     echo "</script>";
//             }
//             exit();
// 	}else{
// 	    return $is_login;
// 	}
    }
}
//查用户密码是否已输入
function check_member_pwd($type = 2){
	if($type == 2 || $type == 3){
		if(!$_SESSION['check_member_pwd_'.$type]){
			if(IS_AJAX){
				echo json_encode(array("status"=>0,"info"=>"你需要输入{$type}级密码才可以操作","url"=> C('USER_LOGIN_URL_'.$type)));
				exit();
			}else{
				$url = C('USER_LOGIN_URL_'.$type);
				echo "<script type='text/javascript'>";
				echo "var url = '".$url."';";
				echo "if(url.indexOf('?')==-1){url+='?';}else{url+='&';}";
				echo "var referer = 'referer='+escape(location.href);";
				echo "location.href=url+referer";
				echo "</script>";
				exit();
			}
		}
	}	
}
//查用户状态情况
function check_member(){
    global $member;
    if($member -> is_lock == 1){
        //锁定的用户
        redirect(U("User/islock"));
        exit();
    }
}

/**
 * 清空目录
 */

function dir_delete($dir) {
	if (!is_dir($dir)) return FALSE;
	$handle = opendir($dir); //打开目录
	while(($file = readdir($handle)) !== false) {
		if($file == '.' || $file == '..')continue;
		$d = $dir.DIRECTORY_SEPARATOR.$file;
		is_dir($d) ? dir_delete($d) : @unlink($d);
	}
	closedir($handle);
	return @rmdir($dir);
}

/**
 * 文件大小转换成指定单位大小
 * @param mix $size
 * @param string $type
 */

function byte2unit($size, $unit="kb"){
    $size = parse_byte($size);
    if ($unit) {
        return round($size / pow(1024, stripos('bkmgtpezy', $unit{0})));
    }
    else {
        return round($size);
    }
}
/**
 * 大小转换
 * @param Number $input 数字大小
 * @param number $dec 小数位长度
 * @return string
 */
function byte_format($input, $dec=0)
{
	$prefix_arr = array("B", "K", "M", "G", "T");
	$value = round($input, $dec);
	$i=0;
	while ($value>1024)
	{
		$value /= 1024;
		$i++;
	}
	$return_str = round($value, $dec).$prefix_arr[$i];
	return $return_str;
}
/*
 * 设置分导航参数
 * @param object $page 导航连接对象
 * @param string|array $config 参数设置
 */
function page_config($page, $config = "PAGE_CONFIG"){	
	if(!is_object($page)) return $page;
	if(method_exists($page,'setConfig')){		
		$page ->lastSuffix =false;
		if(is_string($config)) $config = C($config);	
		if(is_array($config)){
			foreach ($config as $key => $val){
				$page->setConfig($key,$val);
			}
		}
	}
	return $page;
}

/**
 * 把普通生成的连接转到AJAX连接
 * @param string $html :连接代码
 * @param string $ajax_fun　:ajax处理JS函数
 * @return string
 */

function page2ajax($html, $ajax_fun = "ajax_load(this, 'content')"){
	return strtr($html, array("href"=>"href='javascript:void(0)' uri", "<a"=> "<a onclick=\"{$ajax_fun}\""));
}

/*
*function:显示某一个时间相当于当前时间在多少秒前，多少分钟前，多少小时前
*timeInt:unix time时间戳
*format:时间显示格式
*/
function time_format($timeInt,$format='Y-m-d H:i:s'){
	if(empty($timeInt)||!is_numeric($timeInt)||!$timeInt){
		return '';
	}
	$d=time()-$timeInt;
	if($d<10){
		return '刚刚';
	}else{
		if($d<60){
			return $d.'秒前';
		}else{
			if($d<3600){
				return floor($d/60).'分钟前';
			}else{
				if($d<86400){
					return floor($d/3600).'小时前';
				}else{
					if($d<259200){//3天内
						return floor($d/86400).'天前';
					}else{
						return date($format,$timeInt);
					}
				}
			}
		}
	}
}

/**
 * 检查访问权限
 * @param string $permission 权限
 * @param object $account 账号信息
 * @param bool $type 权限类型 false:会员  true:管理员  null:当前登陆类型
 * @return boolean
 */
function check_access($permission, $account = null, $type = null) {
	global $member;
	if (!isset($account)) {
		$account = $member;
		$type	 = (C("IS_ADMIN") === true ? 1 : 0);
	}
	if(is_null($type)){
		$type	 = (C("IS_ADMIN") === true ? 1 : 0);
	}
	$type	=	$type ? 1:0;
	//读取用户id
	$user_id = ($type == 1) ? $member->aid: $member->uid;
	
	static $perms = array();

	if (!isset($perms[$type][$user_id])) {
		$perms[$type][$user_id] = array();
	}

	if (isset($perms[$type][$user_id][$permission])) {
		return $perms[$type][$user_id][$permission];
	}

	$access = __check_access($permission, $account, $type);
	$perms[$type][$user_id][$permission] = $access;

	return $access;
}

/**
 * 检查用户角色
 * @param string $roles :要查的角色,多个角色中用逗号分开,有其中一个即返回true
 * @param int $type 用户类型 0:会员 1:管理员   null 按当前帐户
 * @return bool 
 */
function check_roles($roles, $type = null){
	if(empty($roles)) return false;
	global $member;
	if($type == 1 && $member -> aid == 0) return false;
	if($type == 0 && $member -> uid == 0) return false;
	$roles 	= explode(",", $roles);
	$_roles = explode(",", $member->roles);
	foreach($_roles as $val){
		if(in_array($val, $roles)) return true;
	}
}

/**
 * 处理访问权限
 * @param string $permission 权限
 * @param object $account 账号信息
 * @param string $type 权限类型
 * @return boolean
 */
function __check_access($permission, $account, $type = null) {

  if (!isset($type)) {
    $type = C('IS_ADMIN') ? 1 : 0;
  }
  //用户类型
  if($type == 1 && $account -> aid == 0) return false;
  if($type == 0 && $account -> uid == 0) return false;
  
  //读取用户所在组权限
  $group_access = D("Common/Group","Logic") -> get_group_access($account->gid, $type);
   
  if(!$group_access) return false;
  
  //有所有权限
  if($group_access['access'] == 'all') return true;
  
  $access = $group_access['access'];
   
  if(strpos(",{$access},", ",{$permission},") !== false)
  	return true;
  else
  	return false;
  
}

/**
 * 生成随机订单编号
 * @author watchman
 * @return string
 */
function _create_order_sn($pcode = "SN"){
	$order_sn 	= $pcode.date("YmdHis").mt_rand(pow(10,3), pow(10,4)-1);
	$model 		= M('orders');
	$result 	= $model->where("order_sn = '%s'",$order_sn)->find();
	if($result==false){
		return($order_sn);
	}
	return($this->_create_order_sn($pcode));
}

/**
 * 用户帐户隐藏处理,把手机号和邮箱做*号处理
 * @author wscsky
 */
function user_privacy($str){
	if (is_mobile($str) ){
		return substr($str,0,3)."****".substr($str, -4);
	}
	if(is_email($str) ){
		$arr = explode("@", $str);
		$len = mb_strlen($arr[0],"utf-8");
		if ( $len<5 ){
			$arr[0] = mb_substr($arr[0],0,1,"utf-8") . '***'. mb_substr($arr[0],$len-1,1,"utf-8");
		}else{
			$arr[0] = mb_substr($arr[0],0,2,"utf-8") . '***' . mb_substr($arr[0],$len-2,2,"utf-8");
		}
		return implode("@", $arr);
	}
	if (is_idcard($str) ){
		return substr($str,0,6)."********".substr($str, -4);
	}
	return $str;
}
/**
 * 姓名隐藏处理
 * @author wscsky
 */
function name_privacy($str){
	if(mb_strlen($str,'utf-8') == 2){
		return mb_substr($str,0,1,'utf-8')."*";
	}
	if(mb_strlen($str,'utf-8')>2){
		return mb_substr($str,0,1,'utf-8')."*".mb_substr($str,-1,1,'utf-8');
	}
	return $str;
}

/**
 * 读取用户名
 * @author wscsky
 */
function get_uname($uid, $utype = UTYPE_MEMBER){
    if(!$uid) return '';
    switch ($utype){
        case UTYPE_MEMBER:
            $uname = M("member")->where("uid = %d", $uid)->cache(30)->getField("uname");
            break;
        case UTYPE_ADMIN:
            $uname = M("admin")->where("aid = %d", $uid)->cache(30)->getField("uname");
            break;
        default:
            $uname = "";
            break;
    }
    return $uname;
}

/**
 * 获取某个月有多少天
 * @author watchman
 * @param $date 日期，格式如 20160301
 */
function get_month_days($date = null){
	if(is_null($date) && strtotime($date) == false) $date = date('Ymd');
	$firstday = date("Ym01",strtotime($date));
  	$lastday = date("Ymd",strtotime("$firstday +1 month -1 day"));
  	$days = ($lastday-$firstday)+1;
  	return $days;
}

/**
 * 显示昵称
 * @param string $nicknae Base64加密码内容
 * @param string $type 显示方式 html,text
 * @param string str
 * @author wscsky
 */

function show_uname($nickname, $type = "html"){
    if(!$nickname) return $nickname;
    $uname = base64_decode($nickname);
    if(!$uname) return $nickname;
    return emoji_show($uname, $type);
}
/**
 * 显示用户带表情的昵称
 * @param unknown $str
 */
function emoji_show($str,$type = 'html'){
   if($type == "html"){
       return emoji_unified_to_html(emoji_softbank_to_unified($str));
   }else{
       return emoji_softbank_to_unified($str);
   }
}

/**
 * 显示手机卡密码
 * @author watchman
 */
function showcardpwd($pwd, $default='--'){
	if(!$pwd) return $default;
	$aes = new \Org\Crypt\AES();
	return $aes->decrypt($pwd,CARD_PASSWD_KEY);
}

/**
 * 检测是否为手机号码
 * @author wscsky
 */
function is_mobile($mobile){
	if(preg_match("/^1[3|4|5|7|8|9]\d{9}$/",$mobile)){
		return true;
	}else
		return false;
}

/* 校验邮政编码*/
function is_postcode($code) {
	//去掉多余的分隔符
	$code = preg_replace("/[\. -]/", "", $code);
	//包含一个6位的邮政编码
	if(preg_match("/^\d{6}$/", $code)) {
		return true;
	}else{
		return false;
	}
}

/**
 * 检测是否为座机号码
 * @author wscsky
 */
function is_phone($phone){
	if(preg_match("/^0\d{2,3}(\-)?\d{7,8}$/",$phone)){
		return true;
	}else
		return false;
}

/**
 * 验证是否为手机或者座机电话号号码
 */
function is_telphone($phone){
	if(preg_match("/^0\d{2,3}(\-)?\d{7,8}$/",$phone) or preg_match("/^1[3|4|5|7|8][0-9]\d{8}$/",$phone))
		return true;
	else
		return false;
}

/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
	$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
	if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
	{
		if (preg_match($chars, $user_email))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * 查是否为身份证号码
 * @author watchman
 */
function is_idcard($str){
	$idcard = $str;
	$errors = array("验证通过!", "身份证号码位数不对!", "身份证号码出生日期超出范围或含有非法字符!", "身份证号码校验错误!", "身份证地区非法!");
	$area = array(
		11 => "北京",
		12 => "天津",
		13 => "河北",
		14 => "山西",
		15 => "内蒙古",
		21 => "辽宁",
		22 => "吉林",
		23 => "黑龙江",
		31 => "上海",
		32 => "江苏",
		33 => "浙江",
		34 => "安徽",
		35 => "福建",
		36 => "江西",
		37 => "山东",
		41 => "河南",
		42 => "湖北",
		43 => "湖南",
		44 => "广东",
		45 => "广西",
		46 => "海南",
		50 => "重庆",
		51 => "四川",
		52 => "贵州",
		53 => "云南",
		54 => "西藏",
		61 => "陕西",
		62 => "甘肃",
		63 => "青海",
		64 => "宁夏",
		65 => "新疆",
		71 => "台湾",
		81 => "香港",
		82 => "澳门",
		91 => "国外"
	);
	
	$idcard_array = array();
	$idcard_array = str_split($idcard);
	if (is_null($area[intval(substr($idcard,0, 2))])) return $errors[4];
	switch (strlen($idcard)) {
		case 15:
			if ((intval(substr($idcard, 6, 2)) + 1900) % 4 == 0 || ((intval(substr($idcard, 6, 2)) + 1900) % 100 == 0 && (intval(substr($idcard, 6, 2)) + 1900) % 4 == 0)) {
				$ereg = "/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/";
			}
			else {
				$ereg = "/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/";
			}
			if (preg_match($ereg, $idcard))
				return true;
			else
				return $errors[2];
			break;
		case 18:
			if (intval(substr($idcard, 6, 4)) % 4 == 0 || (intval(substr($idcard, 6, 4)) % 100 == 0 && intval(substr($idcard, 6, 4)) % 4 == 0)) {
				$ereg = "/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/";
			}
			else {
				$ereg = "/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/";
			}
			if (preg_match($ereg, $idcard)) {
				$S = (intval($idcard_array[0]) + intval($idcard_array[10])) * 7 + (intval($idcard_array[1]) + intval($idcard_array[11])) * 9 + (intval($idcard_array[2]) + intval($idcard_array[12])) * 10 + (intval($idcard_array[3]) + intval($idcard_array[13])) * 5 + (intval($idcard_array[4]) + intval($idcard_array[14])) * 8 + (intval($idcard_array[5]) + intval($idcard_array[15])) * 4 + (intval($idcard_array[6]) + intval($idcard_array[16])) * 2 + intval($idcard_array[7]) * 1 + intval($idcard_array[8]) * 6 + intval($idcard_array[9]) * 3;
				$Y = $S % 11;
				$M = "F";
				$JYM_X = "10X98765432";
				$JYM_x = "10x98765432";
				$M_X = substr($JYM_X, $Y, 1);
				$M_x = substr($JYM_x, $Y, 1);
				if ($M_X == $idcard_array[17] || $M_x == $idcard_array[17])
					return true;
				else
					return $errors[3];
			}
			else
				return $errors[2];
			break;
		default:
			return $errors[1];
			break;
	}
}

/**
* 检查特定表 是否存在指定值
* @param string $table
* @param array $param :array('eq',$data['attr_name'])
* @return boolean
*/
function check_table_name($table,$map){

   if(empty($table)) return false;
   $model 	= M($table);

   $data = $model->where($map)->select();
   if($data){
	   return false;
   }else{
	   return true;
   }
}

/**
 * 解析给定的字节数
 * @param string $size
 *   一个尺寸表示为一个数量的字节数和可选的SI或IEC二进制单元
 *   前缀(例如。2、3 k,5 mb,10 g,6、8个字节,直布罗陀9 mb)。
 * @return int
 */
function parse_byte($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
	$size = preg_replace('/[^0-9\.]/', '', $size);
	if ($unit) {
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
		return round($size);
	}
}

/**
 * 获得二维数据指定key的集合
 * @author Jeffreyzhu <jeffreyzhu.cn@gmail.com>
 */
if (!function_exists('array_column')){
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();
	
		if (null === $indexKey) {
			if (null === $columnKey) {
				// trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
				$result = array_values($input);
			}
			else {
				foreach ($input as $row) {
					$result[] = $row[$columnKey];
				}
			}
		}
		else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			}
			else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}
	
		return $result;
	}
}

/**
 * 对数组进行分组
 * @param array $data
 * @param string $column
 */
function array_group_key(array $data,$column){
	$group = array();
	foreach($data as $key =>$v){
		$group[$v[$column]][] = $v;
	}
	
	return $group;
}

/**
 * 对二维数组进行求和
 * @param array $data 要处理的二维数组
 * @param string $column 要处理的数组索引
 * @author wscsky
 */
function my_array_sum(array $data, $column){
    $tdata = array();
    foreach ($data as $val){
        $tdata[] = $val[$column];
    }
    return array_sum($tdata);
}
/*
 * 加载指定语言包
 * 
 */
function load_lang($lang){
	$file   =   MODULE_PATH.'Lang/'.LANG_SET.'/'.strtolower($lang).'.php';
	if (is_file($file))	L(include $file);
}

/**
 * 发送邮件
 * @param mix $address 邮件地址
 * @param unknown $title　邮箱标题
 * @param unknown $message	邮件内容
 * @param string $fromname　发送者
 */
function SendMail($address, $title, $message, $fromname = 'NONE') {
	$mail = new \Common\Org\Mail();
	$mail->IsSMTP ();
	$mail->CharSet 	= C ( 'SMTP_CHARSET' );
	$mail->AddAddress ( $address );
	$mail->Body 	= $message;
	$mail->From 	= C ( 'SMTP_ACCOUNT' );
	$mail->FromName = $fromname;
	$mail->Subject 	= $title;
	$mail->Host 	= C ( 'SMTP_SERVER' );
	$mail->SMTPAuth = true;
	$mail->Username = C ( 'SMTP_ACCOUNT' );
	$mail->Password = C ( 'SMTP_PASSWORD' );
	$mail->IsHTML ( C ( 'SMTP_HTML' ) );
	return ($mail->Send ());
}


/**
 * 转化模版的变量
 * @param string $str $a.b.c 转成 $a['b']['c'];
 */
function parse_tplvar($str){
	if(strpos($str, "(") && strpos($str, ")")){
		return $str;
	}
	if($str{0} != "$"){
		return "'".str_replace("'", "\'", $str)."'";
	}
	if($str{0} != "$") $str = "$".$str;
	$a = explode(".", $str);
	$var = array_shift($a);
	foreach ($a as $v){
		$var .="['{$v}']";
	}
	return $var;
}


/**
 * 优化json_encode不转义中文件
 */
function my_json_encode($data){
	if(version_compare(PHP_VERSION,'5.4','>'))
		return json_encode($data,JSON_UNESCAPED_UNICODE);
	else 
		return my_json_encode_pro($data);
}
/**
 * php 5.4 以下不转中文的处理
 * @param unknown $arr
 * @return string
 */
function my_json_encode_pro($arr)
{
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

/**
 * 截取字串
 */
function cutstr($str, $len = 20, $estr = "..."){
	if(empty($str)) return $str;
	if(mb_strlen($str,"utf8") <= $len){
		return $str;
	}
	return mb_substr($str, 0, $len , "UTF8").$estr;
}

/**
 * 用户积分转可用金额
 * @param int $integral 可用积分
 * @return number
 * @author wscsky
 */
function integral2money($integral = 0){
	if($integral <= 0 || C('integral2money') == 0) return 0;
	return num2money($integral * C('integral2money'));
	
}

/**
 * 金额转成需要的积分
 * @param int $money 金额
 * @return number;
 * @author wscsky
 */
function money2integral($money = 0){
	if($money <= 0 || C('integral2money') == 0) return 0;
	return intval($money / C('integral2money'));
	
}

/**
 * 计算订单邮费
 * @param number $goods_amount 商品金额
 * @param array $goods_list 商品列表
 * @return number
 * @author wscsky
 * 
 */
function get_shipping_fee($goods_amount, $goods_list = array()){
	//查是否有包邮商品
	foreach ($goods_list as $goods){
		if($goods['baoyou']) return 0;
	}
	//查是满足包邮价格
	if(C('shipping_min_amount') > 0 && $goods_amount < C('shipping_min_amount')){
		return C("shipping_fee");
	}
	return 0;
}

/**
 * 读取商品的佣金
 * @param array $goods 商品的信息
 * @param int $level   第几层的佣金
 * @return money
 * @author wscsky
 */
function get_goods_proft($goods = array(), $level = 1){
	if(empty($goods)) return "0.00";
	if(C('order_profit_type') == 3) return "0.00";
	if(C('order_profit_type')==2 || C('order_profit_type') == 1){
		$profit = $goods['price'];
	}
	if(C('order_profit_type') == 0){
		$profit = $goods['price'] - $goods['pur_price'];
	}
	if($profit < 0) $profit = 0;
	return num2money($profit*C('percentage'.$level)/100);
}

/**
 * 读取商品的折扣
 * @param array $goods 商品的信息
 * @return num
 * @author wscsky
 */
function get_goods_zhe($goods = array()){
	if(empty($goods)) return "";
	if($goods['old_price'] <= 0) return "10";
	return number_format($goods['price']*10/$goods['old_price'],1,".","");
}
/**
 * 读取我的折扣
 * @author wsccky
 */
function get_my_discount($format0 = "您是%s暂无折扣",$format = '您是%s享受<i>%1.1f</i>折优惠'){
	global $member;
	if(!$member->discount || $member->discount == 1) return sprintf($format0,$member->gname,$member->discount*10);;
	return sprintf($format,$member->gname,$member->discount*10);
}
/**
 * 读取我的价格
 * @author wscsky
 */
function get_my_price($goods=array(), $name = "您的价格:", $css = "",$number = false){
	global $member;
	if(!$member->discount || $member->discount == 1 || $goods['price'] == 0 || $goods['tejia'] == 1) return $number ? $goods['price'] : "";
	$price = $goods['price'] * $member->discount;
	$price = number_format($price,2,".","");
	return $number ? $price : "<i".($css == "" ? "" :" class='{$css}'").">{$name}¥{$price}</i>";
}
/**
 * 获取缩略URI地址
 * @author wscsky
 */
function thumb_uri($uri, $preset='thumb'){
    return D("Common/Thumb", "Logic")->thumbPath($uri, $preset);
}
/**
 * 生成缩略图
 * @author wscsky
 */
function thumb_img($uri, $preset = "thumb", $save = true){
    $thumb =  D("Common/Thumb", "Logic")->thumb($uri, $preset, $save);
    if(!$thumb)
        return $uri;
    else
        return $thumb;
}

/**
 * 检测图片网站
 * @author wscsky
 */
function check_img_url($uri, $thumb = null){
    if(!$uri || strpos($uri, "http://") ===0 || strpos($uri, "https://")===0) return $uri;
    return is_null($thumb) ? IMG_PRE . $uri : thumb_uri($uri, $thumb);
}

/**
 * 读取一行一个配置，转化为数组
 * @param string $name
 * @author wscsky
 */
function get_line_config($name){
    if(!$name) return array();
    $data = explode("\n",C($name));
    $rdata = array();
    foreach ($data as $val){
        $val = trim($val);
        if($val) $rdata[] = $val;
    }
    return $rdata;
}

/**
 * 读取拼团数据
 * @author wscsky
 */
function get_groups_data($order_pid,$num = 10000,$self = true){
    $filter['map']   = array('group_status'=>array('in','1,2,3'));
    $filter['join']  = "left join __MEMBER__ b on b.uid = a.uid";
    $filter['order'] = "a.order_id";
    $filter['field'] = "a.order_id,a.order_sn,b.uid,b.uname,b.headimg";
    if($self){
        $filter['map']["a.order_id|a.order_pid"] = $order_pid;
    }else{
        $filter['map']["a.order_pid"] = $order_pid;
    }
    return D("Common/Data","Logic","orders")->get_list($filter,1,$num);
}

/**
 * 读取拼团数据
 * @author wscsky
 */
function get_goods_groups($goods_id,$num = 2, $type = 0, $status = '1,2,3'){
    $filter['map']   = array('group_status'=>array('in',$status),'goods_id' => $goods_id);
    if($type == 1) $filter['map']['a.order_pid'] = 0;
    $filter['join']  = "left join __MEMBER__ b on b.uid = a.uid";
    $filter['order'] = "a.group_status,a.order_id asc";
    $filter['field'] = "a.order_id,a.group_num,a.group_users,a.ctime,a.group_etime,a.goods_id,a.order_sn,b.uid,b.uname,b.headimg";
    return D("Common/Data","Logic","orders")->get_list($filter,1,$num);
}

/**
 * 读取商品的阶梯价格
 * @author wscsky
 */
function get_groups_price($goods_id){
    if(!$goods_id) return array();
    $goods_info = D("Common/Goods","Logic")->get_goods($goods_id,array('goodsspec'));
    $data = $price = array();
    if(!$goods_info['goodsspec']){
        $price[] = $goods_info['group_price'];
    }else{
        foreach ($goods_info['goodsspec'] as $key => $val){
            if($val['online']){
                $price[] = floatval($val['price'] > 0 ? $val['price'] : $goods_info['price']);
            }
        }
        if(!$spec_data){
            $price[]= floatval($goods_info['price']);
        }
        sort($price);
    }
    $data =  array(
        'price' => $goods_info['price'],
        'group_pice' => $goods_info['group_price'],
        'max'        => end($price),
        'min'        => $price[0],
        'prices'     => array_reverse($price),
    );
    return $data;
}



/**
 * 显示团购时间
 */
function show_group_time($time, $type = 1){
    if(!$time) return "";
    $time = $time -  time();
    switch ($type){
        
        default:
            if($time < 0) return "已结束";
            $hour =  floor($time/3600);
            $mini =  floor($time%3600/60);
            $ss   =  $time%60;
            return "剩余{$hour}:{$mini}:{$ss}结束";
            break;
    }
}

/**
 * 获取客户端浏览器类型
 * @param  string $glue 浏览器类型和版本号之间的连接符
 * @return string|array 传递连接符则连接浏览器类型和版本号返回字符串否则直接返回数组 false为未知浏览器类型
 */
function get_client_browser($glue = null) {
    $browser = array();
    $agent = $_SERVER['HTTP_USER_AGENT']; //获取客户端信息
    
    /* 定义浏览器特性正则表达式 */
    $regex = array(
        'ie'      => '/(MSIE) (\d+\.\d)/',
        'chrome'  => '/(Chrome)\/(\d+\.\d+)/',
        'firefox' => '/(Firefox)\/(\d+\.\d+)/',
        'opera'   => '/(Opera)\/(\d+\.\d+)/',
        'safari'  => '/Version\/(\d+\.\d+\.\d) (Safari)/',
    );

    foreach($regex as $type => $reg) {
        preg_match($reg, $agent, $data);
        if(!empty($data) && is_array($data)){
            $browser = $type === 'safari' ? array($data[2], $data[1]) : array($data[1], $data[2]);
            break;
        }
    }

    return empty($browser) ? false : (is_null($glue) ? $browser : implode($glue, $browser));
}


function GetIpLookup($ip = ''){  
    if($ip == ''){
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip=json_decode(file_get_contents($url),true);
        $data = $ip;
    }else{
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(file_get_contents($url));   
        if((string)$ip->code=='1'){
           return false;
        }
        $data = (array)$ip->data;
    }
    
    return $data;  
}  

//数组分页
function getPageData($data,$start,$length)
{
    $arr =array();
    $stop = $start+$length;
    for ($i = $start; $i < $stop; $i++) {
        if(isset($data[$i])) {
            $arr[] = $data[$i];
        }else{
            break;
        }
    }
    return $arr;
}

function getJson($code, $msg, $data = null)
{
    $result = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data));
    return $result;
}

function responseJson($code, $msg, $data = null)
{
    var_dump($msg);
    die(getJson($code, $msg, $data));
}

/**
 * 获取当时时间，精确到毫秒
 * @author              wangxiaoxiao  17-12-6
 * @example             getMicrotime();
 */
function getMicrotime() {
    $time = explode(" ", microtime());
    return ($time[1] * 1000) + ($time[0] * 1000);
}


/**
 * 获取小程序模板消息发送id
 * @author              xiaoxiao  18-01-12
 * @example             formId();
 */
function formId($openid,$formId){
    $datas['ctime']  = time();
    $datas['openid'] = $openid;
    if(isset($formId) && $formId != NULL){
        $datas['formid'] = $formId;
        $datas['type']  = 1;
        M('SendMess')->add($datas);
    }
}


//验证会员是否登陆
function check_admin_login(){
	$admin = session('admin');
	$is_login = false;
	if($admin && $admin->id > 0) $is_login = true;
        
	if(!$is_login){
            if(IS_AJAX){
                    echo json_encode(array('status'=> 0, 'info'=>"您未登陆或登陆超时，请重新登陆！", 'url' => C('USER_LOGIN_URL')));
            }else{
                    //redirect(C('USER_LOGIN_URL'));
                    echo "<script type='text/javascript'>";
                    echo "var url = '".C('USER_LOGIN_URL')."';";
                    echo "if(url.indexOf('?')==-1){url+='?';}else{url+='&';}";
                    echo "var referer = 'referer='+escape(location.href);";
                    echo "location.href=url+referer";
                    echo "</script>";
            }
            exit();

	}
	return $is_login;
}

function is_md5($password) {
    return preg_match("/^[a-z0-9]{32}$/", $password);
}

/**
 * Ajax方式返回数据到客户端
 * @param int $code     返回码：-1：错误，0：成功，1：用户未登录，2：用户没有权限，3：非法请求 4：非法用户
 * @param string $msg   返回码提示信息：错误,成功,不登录,未经许可,非法请求,非法用户
 * @param int $time     执行时间
 * @param array $data   返回数据
 * @param int $type     AJAX返回数据格式：0：json；1：xml；2：jsonp；3：eval
 * @param string $callback callback跨域问题；
 * @return string       返回指定格式字符串
 * @author              liwenqiang 2016/12/19
 * @example             returnData();
 */
function returnData($code=-1, $msg='', $time=0, $data=array(), $type=0, $callback="") {
    $code = (int)$code;
    $type = (int)$type;
    $callback = isset($callback) ? trim($callback) : ''; //jsonp回调参数，必需
    $new_data = array('code' => $code, 'msg' => $msg, 'datetime' => $time.' ms');
    if(is_array($data) && !empty($data)) {
        //还原html字符 liwenqiang 2016-12-19
        foreach($data as $k => $v) {
            if(is_string($v)) {
                $data[$k] = htmlspecialchars_decode($v, ENT_NOQUOTES);
            }
        }
        if(is_array($data['dataList'])) {
            foreach($data['dataList'] as $k1 => $v1) {
                if (is_array($v1)) {
                    foreach ($v1 as $k2 => $v2) {
                        if(is_string($v2)) {
                            $data['dataList'][$k1][$k2] = htmlspecialchars_decode($v2, ENT_NOQUOTES);
                        }
                    }
                }
            }
        }
        $new_data['data'] = $data;
    } else {
        $new_data['data'] = array();
    }
    switch($type) {
        case 0:
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $new_data = json_encode($new_data);
            break;
        case 1:
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            $new_data = zwj_xml_encode($new_data);
            break;
        case 2:
            // 返回JSON数据格式到客户端 callback跨域问题
            header('Content-Type:application/json; charset=utf-8');
            $new_data = $callback."(".json_encode($new_data).")";
            break;
        case 3:
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            break;
    }
    echo $new_data;
    exit;
}

/** 
* 计算剩余天时分。 
* $unixEndTime string 终止日期的Unix时间 
* @author yuDingXuan 
* @version 2018年4月17日 
*/  
function ShengYu_Tian_Shi_Fen($unixEndTime=0)  
{  
   if ($unixEndTime <= time()) { // 如果过了活动终止日期  
       return '0天0时0分';  
   }  

   // 使用当前日期时间到活动截至日期时间的毫秒数来计算剩余天时分  
   $time = $unixEndTime - time();  

   $days = 0;  
   if ($time >= 86400) { // 如果大于1天  
       $days = (int)($time / 86400);  
       $time = $time % 86400; // 计算天后剩余的毫秒数  
   }  

   $xiaoshi = 0;  
   if ($time >= 3600) { // 如果大于1小时  
       $xiaoshi = (int)($time / 3600);  
       $time = $time % 3600; // 计算小时后剩余的毫秒数  
   }  

   $fen = (int)($time / 60); // 剩下的毫秒数都算作分  
   
   return $days.'天'.$xiaoshi.':'.$fen.':'.$time%60;  
}

/**
* @version information(版本信息): v1.0
* @author(作者): xiaoxiao
* @deprecated(简要说明): 生成订单编号
* @param
* @write_time(创建时间): 2017-12-13
*   */
function getmerchat() {

   srand((double)microtime()*1000000);//create a random number feed.
   $ychar="0,1,2,3,4,5,6,7,8,9";
   $list=explode(",",$ychar);
   for($i=0;$i<6;$i++){
       $randnum=rand(0,9); // 10+26;
       $authnum.=$list[$randnum];
   }

   return time().$authnum;
}

/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function substr_cut($user_name){
    $strlen     = mb_strlen($user_name, 'utf-8');
    $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}

/**
 * 对二维数组进行排序
 * @param $array
 * @param $keyid 排序的键值
 * @param $order 排序方式 'asc':升序 'desc':降序
 * @param $type  键值类型 'number':数字 'string':字符串
 */
function sortArray(&$array, $keyid, $order = 'asc', $type = 'number') {
        if (is_array($array)) {
        foreach($array as $val) {
            $order_arr[] = $val[$keyid];
        }
        $order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
        $type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;
        return array_multisort($order_arr, $order, $type, $array);
    } else {
        return array();
    }
}

/**
 * 生成随机数
 * @param int $length 位数
 * @return int 6位数
 **/
function randCode($length = 6) {
    return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

/**
 * 密码生成（md5）
 **/
function makePass($password) {
	return md5(MEMBER_LOGIN_KEY . trim($password));
}

/**
 * 生成订单编号
 **/
function makeOrderSn() {
	srand((double)microtime()*1000000);//create a random number feed.
	$ychar="0,1,2,3,4,5,6,7,8,9";
	$list=explode(",",$ychar);
	for($i = 0; $i < 6; $i ++){
		$randnum = rand(0, 9); // 10+26;
		$authnum .= $list[$randnum];
	}
	return time().$authnum;
}

/**
 * 记录信息到文件中（做调试使用）
 * @param array $data 数组
 * @param string $fileName 文件名
 **/
function fileLog($data, $fileName) {
    file_put_contents('./Uploads/'.$fileName, date('Ymd H:i:s').'--'.json_encode($data).PHP_EOL, FILE_APPEND);
}

