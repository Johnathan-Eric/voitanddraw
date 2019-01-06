<?php
/**
 * 补充原有Action机制,增加原版一些操作方法
 * @author wscsky <wscsky@qq.com>
 *
 */
namespace Think;
class Action extends Controller{
	
	/**
	 * 用户对象信息
	 * @var user
	 */
	protected $member   = null;
	protected $menuid   = null;
	protected $query    = array();

	//权限配置
	protected function accessRules(){
	    return array();
	}

	/**
	 * 架构函数 取得模板对象实例
	 * @access public
	 */
	function __construct(){
	    parent::__construct();
        //检查是否到期
//        if(C('EXPIRE_TIME') !== 0 && time() > strtotime(C('EXPIRE_TIME'))){
//                $this->display(THINK_PATH.'Tpl/expire.html');
//                exit();
//        }
		// 加载user用户对象
		if (isset($GLOBALS['member'])) {
			$this->member = &$GLOBALS['member'];
		}
		else {
			$this->member = default_anonymous_user();
		}
		if(C("SITE_TYPE") == "API") return;
		//需要微信方式
		if(C("SITE_TYPE") == "weixin"){
		    //jssdk签名
		    $this->assign("jssdk",D("Common/Weixin","Logic")->get_jssdk_sign());
			//用户验证
			if($this->member->uid == 0){
				if(C('system_mode') == "test" || I('test_mode') == 1){
					D("Common/Member", "Logic")->get_member_by_openid(C('test_openid') ,true);
				}else{
 					$result = D("Common/Weixin", "Logic") -> check_user();
				}
				$this->member = &$GLOBALS['member'];
			}
		}
		$this->assign("member",(array)$this->member);
	}
	
	/**
	 * 用户权限验证
	 * @return boolean
	 * @author wscsky
	 */
	
	private function _check_access(){
		$accessRules = $this->accessRules();
		if(!empty($accessRules) && isset($accessRules[ACTION_NAME])){
			if(C("IS_ADMIN") === true){
				$user_id	= $this->member->aid;
			}else{
				$user_id	= $this->member->uid;
			}
			//登陆就可以访问的权限
			if($accessRules[ACTION_NAME] === true && $user_id > 0) return true;
			//所有人能访问的权限 
			if($accessRules[ACTION_NAME] === "*") return true;
			
			//按设置的权限
			if(is_string($accessRules[ACTION_NAME])){
				$type		 = null;
				$need_access = explode(",", $accessRules[ACTION_NAME]);
			}else{
				if(empty($accessRules[ACTION_NAME])) return true;
				
				//所有人可以访问
				if($accessRules[ACTION_NAME]['access'] === "*"){
					return true;					
				}
				
				//登陆就可以访问
				if($accessRules[ACTION_NAME]['access'] === true){
					if($user_id > 0) return true;
				}
				
				//指定用户角色访问
				$roles		 = isset($accessRules[ACTION_NAME]['roles']) ? $accessRules[ACTION_NAME]['roles'] : NULL;
				if($roles && $this->member->roles){
					$roles 	= explode(",", $roles);
					$_roles = explode(",", $this->member->roles);
					foreach($_roles as $val){
						if(in_array($val, $roles) && $user_id > 0) return true;
					}
				}

				//设置了权限按权限			
				$need_access = explode(",", $accessRules[ACTION_NAME]['access']);
			}
			foreach ($need_access as $access){
				if(check_access($access, null, $type)){
					return true;
					break;
				}
			}
			//以上都不满足,则无权限
			return false;
		}
		//没有设置直接返回
		return true;		
	}

	
	/**
	 * 魔术方法 有不存在的操作的时候执行
	 * @access public
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method,$args) {
		if( 0 === strcasecmp($method,ACTION_NAME.C('ACTION_SUFFIX'))) {
            if(method_exists($this,'_empty')) {
                // 如果定义了_empty操作 则调用
                $this->_empty($method,$args);
            }elseif(file_exists_case($this->view->parseTemplate())){
                // 检查是否存在默认模版 如果有直接输出模版
                $this->display();
            }else{
                E(L('_ERROR_ACTION_').':'.ACTION_NAME);
            }
        }else{
			switch(strtolower($method)) {
				// 判断提交方式
				case 'ispost'   :
				case 'isget'    :
				case 'ishead'   :
				case 'isdelete' :
				case 'isput'    :
					return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
					// 获取变量 支持过滤和默认值 调用方式 $this->_post($key,$filter,$default);
				case '_get'     :   $input =& $_GET;break;
				case '_post'    :   $input =& $_POST;break;
				case '_put'     :   parse_str(file_get_contents('php://input'), $input);break;
				case '_param'   :
					switch($_SERVER['REQUEST_METHOD']) {
						case 'POST':
							$input  =  $_POST;
							break;
						case 'PUT':
							parse_str(file_get_contents('php://input'), $input);
							break;
						default:
							$input  =  $_GET;
					}
					if(C('VAR_URL_PARAMS')){
						$params = $_GET[C('VAR_URL_PARAMS')];
						$input  =   array_merge($input,$params);
					}
					break;
				case '_request' :   $input =& $_REQUEST;   break;
				case '_session' :   $input =& $_SESSION;   break;
				case '_cookie'  :   $input =& $_COOKIE;    break;
				case '_server'  :   $input =& $_SERVER;    break;
				case '_globals' :   $input =& $GLOBALS;    break;
				default:
					E(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
			}
			if(!isset($args[0])) { // 获取全局变量
				$data       =   $input; // 由VAR_FILTERS配置进行过滤
			}elseif(isset($input[$args[0]])) { // 取值操作
				$data       =	$input[$args[0]];
				$filters    =   isset($args[1])?$args[1]:C('DEFAULT_FILTER');
				if($filters) {// 2012/3/23 增加多方法过滤支持
					$filters    =   explode(',',$filters);
					foreach($filters as $filter){
						if(function_exists($filter)) {
							$data   =   is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
						}
					}
				}
			}else{ // 变量默认值
				$data       =	 isset($args[2]) ? $args[2]:NULL;
			}
			return $data;
		}
	}
	
	/**
	 * 得到URI里的参数
	 * @return array()
	 * @author wscsky
	 */
	function _get_uri($rstr = "", &$query = array()){
		$uri = $_SERVER[C('URL_REQUEST_URI')];
		$this->query = array();
		//增加兼容性读取
		if($pos = strpos($uri, "?s=")){
			$uri = substr($uri, $pos+3);
		}
		if($uri){
			if(false !== strpos($uri,'?')) { // 解析参数
				list($uri,$query) = explode('?',$uri,2);
				$_query = array();
				$query && parse_str($query,$_query);
				$query  = $_query;
				$this->query = $query;
			}
				
			$uri = str_ireplace("index.php", "", $uri);
			if($rstr && stripos($uri, $rstr)){
				$uri = substr($uri,stripos($uri, $rstr)+strlen($rstr));
			}
			if(C("URL_HTML_SUFFIX") && stripos($uri, C("URL_HTML_SUFFIX"))){
				$uri = substr($uri,0,stripos($uri, C("URL_HTML_SUFFIX"))-1);
			}
			$uri  = trim($uri, "/");
			if($uri)
				return explode("/", $uri);
			else
				return array();
	
		}else{
			return array();
		}
	}
	
	/**
	 * 查模版是否存在
	 * @param string $tpl 模版名字
	 * @return bool
	 * @author wscsky
	 */
	function _check_tpl($tpl=""){
		if(empty($tpl)) return false;
		$templateFile = \Think\Think::instance('Think\View') -> parseTemplate($tpl);
		// 模板文件不存在直接返回
		if(is_file($templateFile))
			return true;
		else
			return false;
	}
	
	function _empty(){
		header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
		$this->assign("error","您访问的页面不存在！");
		$this->display("Publics:404");
	}
	
	/**
	 * 返回404
	 */
	function _404(){
	    header("http/1.1 404 not found");
	    header("status: 404 not found");
	    $this->assign("error","您访问的页面不存在！");
	    $this->display("Publics:404");
	    exit();
	}
	
	
	function FetchRepeatMemberInArray($array){
	   
	    // 获取去掉重复数据的数组
	   
	    $unique_arr = array_unique($array);
	   
	    //获取重复数据的数组
	   
	    $repeat_arr = array_diff_assoc($array,$unique_arr);
	   
	    return $repeat_arr;
	    
	}
	
	
	
}