<?php
/**
 * 添加访问控制机制的Action
 * @author wscsky <wscsky@qq.com>
 *
 */
namespace Think;
class MerbaseAction extends Controller{

	/**
	 * 用户对象信息
	 * @var user
	 */
	protected $member   = null;

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
		C('site_title','微智服商户管理');
                // 加载user用户对象
		if(!empty(session('member'))){
		    session('member')->store_name = session('member')->mobile;
		}
                $GLOBALS['member'] = session('member');
		if (isset($GLOBALS['member'])) {
			$this->member = &$GLOBALS['member'];
		}
		else {
			$this->member = default_anonymous_user();
		}
                //免登陆访问方法数组
                $avoid_landing = array(
                    'logout',
                    'forgetpassword',
                    'createShop',
                    'register',
                    'verify',
                    'login',
                    'getRegisterCode'
                );
                $member = (array) $GLOBALS['member'];
                if(!in_array(ACTION_NAME, $avoid_landing) && $member['is_lock'] == 0){
                    $this->error('您还未登录，即将跳转登录页面!','/Home/Publics/register.html?type=login',3);
                }
                $mLv = $member['level'];
                $levelMenu = C("menu_level");
                $menuInfo = $levelMenu[$mLv];
                switch ($menuInfo['code']){
                    case "message":
                        $showLv = 1;
                        break;
                    case "all":
                        $showLv = 10;
                        break;
                    default :
                        $showLv = 1;
                }
                $this->assign("showlv",$showLv);
		$this->assign("member",(array)$this->member);
		//检查用户状态
                //check_member();
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
				$data       =	 isset($args[2])?$args[2]:NULL;
			}
			return $data;
		}
	}
	
	/**
	 * 查模版是否存在
	 * @param string $tpl 模版名字
	 * @return bool
	 * @author wscsky
	 */
	function _check_tpl($tpl=""){
		$templateFile = \Think\Think::instance('Think\View') -> parseTemplate($tpl);
		// 模板文件不存在直接返回
		if(is_file($templateFile))
			return true;
		else
			return false;
	}
	
	
	function _empty(){
		header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
		$this->display("Public:404");
	}
}
