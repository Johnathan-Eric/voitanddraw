<?php
/**
 * Session处理文件
 * Mysql数据库方式存放 Session。
 * @author wscsky <wscsky@qq.com>
 */
namespace Think\Session\Driver;
defined('THINK_PATH') or exit();

class Session{
  /**
   * 数据表前缀
   * @var string
   */
  protected $table_prefix;
  /**
   * Session数据表
   * @var string
   */
  protected $session_table_name;

  /**
   * 数据库链接资源
   * @var resource
   */
  protected $resource;

  /**
   * 当前链接的Session名称
   * @var string
   */
  protected $session_name;

  /**
   * HTTPS链接下的HTTP Session Name
   * @var string
   */
  protected $insecure_session_name = '__==__';

  /**
   * 原Session数据
   * @var array
   */
  protected $old_session = array();
  
  /**
   * 构造Session
   */
  public function __construct() {
    defined('IS_HTTPS') or define('IS_HTTPS', is_ssl() || ini_get('session.cookie_secure'));

    // 使用Session Cookie
    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cache_limiter', 'none');
    ini_set('session.cookie_httponly', '1');

   // session.cookie_lifetime
    // 设置session name
    if (IS_HTTPS) {
      ini_set('session.cookie_secure', TRUE);
    }
    $prefix = IS_HTTPS ? 'SSLID' : 'SESID';
    $session_name = ini_get('session.cookie_domain');
    empty($session_name) && $session_name = session_name();
    
    //session_name($prefix .substr(md5($session_name), 0, 8));
    
    $this->session_name = session_name();
    if (IS_HTTPS) {
      $this->insecure_session_name = substr($this->session_name, 1);
    }
  }

  /**
   * 规避安全字符
   * @param string $value
   * @return string
   */
  protected function escapeString($value) {
    return mysql_real_escape_string($value, $this->resource);
  }

  /**
   * 生成Session ID
   * @return string
   */
  protected function generate_sid() {
    return md5(time().uniqid(mt_rand(), true));
  }

  /**
   * 设置Session Cookie
   * @param string $name
   * @param boolean $secure
   */
  private function set_cookie($name, $value) {
    $params = session_get_cookie_params();
    $expire = $params['lifetime'] ? NOW_TIME + $params['lifetime'] : 0;
    setcookie($name, $value, $expire, $params['path'], $params['domain'], FALSE, $params['httponly']);
    $_COOKIE[$name] = $value;
  }

  /**
   * 删除Session Cookie
   * @param string $name
   * @param boolean $secure
   */
  private function del_cookie($name, $secure = null) {
    if (isset($_COOKIE[$name]) || (!IS_HTTPS && $secure === true)) {
      $params = session_get_cookie_params();
      if ($secure !== null) {
        $params['secure'] = $secure;
      }
      setcookie($name, '', NOW_TIME - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
      unset($_COOKIE[$name]);
    }
  }

  /**
   * 打开Session
   * @return boolean
   */
  public function open() {

  	$dsn = C('DB_HOST') .(C('DB_PORT') ? ':'. C('DB_PORT') : '');
    $resource  = mysql_connect($dsn, C('DB_USER'), C('DB_PWD'));
    if(!$resource || !mysql_select_db(C('DB_NAME'), $resource))
       return false;

    mysql_query("SET NAMES '".C('DB_CHARSET')."'", $resource);

    $this->table_prefix  		=  C("DB_PREFIX");
    $this->session_table_name   =  C("DB_PREFIX") .(C('SESSION_TABLE') ? C('SESSION_TABLE') : 'sessions');
    $this->resource = $resource;
    return true;
    
  }

  /**
   * 关闭Session
   */
  public function close() {
      mysql_close($this->resource);
  }

  /**
   * 读取Session数据
   * @param string $sid
   * @return string
   */
 public function read($ses_id) {
  	global $member;
  	if (!isset($_COOKIE[$this->session_name]) && !isset($_COOKIE[$this->insecure_session_name])) {
        // 第一次访问的游客不读取Session数据
        $member = default_anonymous_user();
        return '';
      }

      $ses_id = $this->escapeString($ses_id);
      // 如果Session被设置，则会存储Session数据到数据库中。
      $sql = "Select * from {$this->session_table_name}";
      if(IS_HTTPS){
      	$sql .= " where ses_id = '{$ses_id}'";
      	if (isset($_COOKIE[$this->insecure_session_name])) {
      		$ssl_id = $this->escapeString($_COOKIE[$this->insecure_session_name]);
      		$sql .= " OR (ssl_id = '{$ses_id}' AND ses_id= '' AND uid = 0 AND suid = 0) ORDER BY session_time DESC LIMIT 0, 1";
      	}
      }
      else {
      		$sql .= " where ses_id = '{$ses_id}'";
      }
      $result = mysql_query($sql, $this->resource);
      
      $session_data = "";
      //读取数据库Session      
      if($result){
      	$member = mysql_fetch_object($result);
      	if ($member) {
      		$session_data = $member->session_data;
      		$this->old_session['ses_id']         = $member->ses_id;
      		$this->old_session['ssl_id']         = $member->ssl_id;
      		$this->old_session['session_data']   = $member->session_data;
      		$this->old_session['session_time']   = $member->session_time;
      		unset($member->ses_id, $member->ssl_id, $member->session_data, $member->session_time);
      	}      	
      }
      //如果已登陆，读取用户信息
      if($member && ($member->aid > 0 || $member->uid > 0 || $member->mid > 0)){
      	//加载管理员
      	if(C("IS_ADMIN")){
            $model = D("Admin/Admin");
            $model -> scope("login") 
               -> where("m.aid = %d", $member->aid);
        }elseif (C("IS_MERCHANT")){
            $model = D("Merchant/Merchant");
            $model -> where("id = %d", $member->mid);
        }else{
            //加载会员
            $model = D("Common/Member");
            $model -> scope("login")
                       -> where("m.uid = %d", $member->uid);
         }      	 
      	$member = $model->find();
      	if(!$member){
      	//读取访客默认设置
      		$member = default_anonymous_user();
      	}else{
      		$member = (object)$member;
      	}
      	//var_dump($member,$session_data);exit;
      }else{
      	//读取访客默认设置
      	$member = default_anonymous_user();      	
      }
      return $session_data;
  }

  /**
   * 写入Session数据
   * @param string $ses_id
   * @param string $data
   */
  public function write($ses_id, $data) {
        global $member;
        if(C("IS_ADMIN")){
            if(empty($member->aid) && empty($data)) {
                    return ;
            }
        }elseif (C("IS_MERCHANT")){
            if(empty($member->mid) && empty($data)) {
          	return ;
            }
        }else{      
            if(empty($member->uid) && empty($data)) {
                   return ;
            }
        }
      
      $uid 		= $member -> uid + 0;
      $aid		= $member -> aid + 0;
      $mid		= $member -> mid + 0;
      $ses_id 	= $this->escapeString($ses_id);
      $ip		= get_client_ip();
      
      //如果没有旧Session新建
      if(empty($this->old_session['ses_id'])) {
      	$sql = "INSERT INTO {$this->session_table_name}
      			(uid, aid, mid, ses_id, ip, session_time, session_data) VALUES
      			({$uid}, {$aid}, {$mid}, '{$ses_id}', '{$ip}', ". NOW_TIME .", '{$data}')";
      	mysql_query($sql, $this->resource);
      	$this->set_cookie($this->session_name, $ses_id);
      }
      //Session资源已被回收或者过期时重新写入
      elseif((!IS_HTTPS && $this->old_session['ses_id'] != $ses_id) || (IS_HTTPS && $this->old_session['ssl_id'] != $ses_id)
          || NOW_TIME - $this->old_session['session_time'] >= ini_get('session.gc_maxlifetime')){

      	$fields = array(
      			'uid' 			=> $uid,
      			'aid'			=> $aid,
                        'mid'			=> $mid,
      			'ip' 			=> "'{$ip}'",
      			'session_time' 	=> NOW_TIME,
      			'session_data' 	=> "'{$data}'");        
        if (IS_HTTPS) {
          $fields['ses_id'] 	= "'". (isset($_COOKIE[$this->insecure_session_name]) ? $_COOKIE[$this->insecure_session_name] : $this->old_session['ses_id'])."'";
          $fields['ssl_id'] 	= "'{$ses_id}'";
        }
        else {
          $fields['ses_id'] 	= "'{$ses_id}'";
          $fields['ssl_id'] 	= "'". (isset($this->old_session['ssl_id']) ? $this->old_session['ssl_id'] : '')."'";
        }
        

        mysql_query('DELETE FROM '. $this->session_table_name ." WHERE ses_id = '". $this->old_session['ses_id'] ."'"
                    ." AND ssl_id = '". $this->old_session['ssl_id'] ."'", $this->resource);
        mysql_query('INSERT INTO '. $this->session_table_name .' ('. implode(', ', array_keys($fields)) .')'
            .' VALUES ('. implode(', ', $fields) .')', $this->resource);
        
        // 设置会话Cookie
        $this->set_cookie($this->session_name, $ses_id);
      }
      //Session改变时更新
      elseif ($this->old_session['session_data'] != $data) {
        $sql = "UPDATE {$this->session_table_name} SET session_data = '{$data}', ip = '{$ip}', session_time = ".NOW_TIME;
        
        if (IS_HTTPS) {
          $sql .= " WHERE ssl_id = '{$ses_id}'";
        }
        else {
          $sql .= " WHERE ses_id = '{$ses_id}'";
        }
        mysql_query($sql, $this->resource);
      }
      
      return true;
  }

  /**
   * 销毁Session
   * @param string $ses_id
   */
  public function destroy($ses_id) {
    mysql_query('DELETE FROM '. $this->session_table_name .' WHERE '. (IS_HTTPS ? 'ssl_id' : 'ses_id') ." = '{$ses_id}'", $this->resource);

    $_SESSION = array();
    
    $GLOBALS['member'] = default_anonymous_user();

    if (IS_HTTPS) {
      $this->del_cookie($this->session_name);
      $this->del_cookie($this->insecure_session_name, false);
    }
    else {
      $this->del_cookie($this->session_name);
      $this->del_cookie('S'. $this->session_name, true);
    }
    self::gc(C("SESSION_OPTIONS.expire"));
  }

  /**
   * 回收Session资源
   * @param int $lifeTime
   */
  public function gc($lifeTime) {
    mysql_query('DELETE FROM '. $this->session_table_name .' WHERE session_time < '. (NOW_TIME - $lifeTime), $this->resource);
  }
}

?>