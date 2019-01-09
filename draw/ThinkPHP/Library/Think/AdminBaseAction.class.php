<?php
/**
 * 添加访问控制机制的Action
 * @author wscsky <wscsky@qq.com>
 *
 */
namespace Think;
class AdminBaseAction extends Controller{

	/**
	 * 用户对象信息
	 * @var user
	 */
	protected $admin  = null;
        
        //忽略权限控制方法
	protected function neglect(){
	    return array('ajax_update','ajax_update_field','catlist','goods_allot','outputbooks','exceldata');
	}

        /**
	 * 架构函数 取得模板对象实例
	 * @access public
	 */
	function __construct(){
        parent::__construct();
        $admin = session('admin');

        // 加载user用户对象
        if ($admin) {
            $this->admin = $admin;
        }else {
            $this->admin = default_anonymous_user();
        }
        //验证会员登陆
        $this->assign("admin",(array)$this->admin);

        //检查用户状态
        check_admin_login();
        
        //查访问权限
        if(!self::_check_access()){
            $user = D("Admin","Logic");
            if(IS_AJAX){
                echo json_encode(array('status'=> 0, 'info'=>"你无权访问该页面", 'url' => C('USER_LOGIN_URL')));
            }else{
                $this->error("你无权访问该页面",U("User/login"));die();
            }
            exit();
        }

        // if(session('initMenu')){
        //     self::_init_menu();
        //     //$this->assign('menu', (array) session('initMenu'));
        // } else {
        //     self::_init_menu();
        // }
        
        // menu
        self::_init_menu();

        //获取当前操作子操作
        self::_get_access_child();
	}
	
	/**
	 * 用户权限验证
	 * @return boolean
	 * @author wscsky
	 */
	
	private function _check_access(){
        $return = FALSE;
        if($this->admin->role_auth_ids){
            $ac_name = strtolower(CONTROLLER_NAME.'-'.ACTION_NAME);
            $all_ac_name = strtolower($this->admin->role_auth_ac);
            $all_ac_arr = explode(',', $all_ac_name);
            if(in_array($ac_name, $all_ac_arr) || $all_ac_name == 'all'){
                $return = TRUE;
            }
            if(in_array(strtolower(ACTION_NAME), $this->neglect())){
                $return = TRUE;
            }
        }
        return $return;
	}
        
    private function _get_access_child()
    {
        //获取当前操作id
        $info = M("admin_role_auth")->field("id") ->where(array('auth_c' => CONTROLLER_NAME,'auth_a' => ACTION_NAME))->find();
        if($info){
            $where = array();
            $where['auth_pid'] = array('eq',$info['id']);
            if($this->admin->role_auth_ids != 'all'){
                $where['id'] = array('in', $this->admin->role_auth_ids);
            }
            $child_acc_lists = M("admin_role_auth")->field("id,auth_name,auth_pid,auth_path,auth_a") ->order(' sort asc, id asc ') ->where($where)->select();

            $acc = array();
            if($child_acc_lists){  
                foreach ($child_acc_lists as $k=>$v){
                    $acc[$v['auth_a']] = $v;
                }
            }
            $this->assign('acc', $acc);
        }
    }

        

    private function _init_menu()
    {
        //超级管理员只出现在总店.
        $where = array();
        $where['is_menu'] = array('eq',1); 
        if($this->admin->role_auth_ids != 'all'){
            $where['id'] = array('in', $this->admin->role_auth_ids);
        }
        $menuLists = M("admin_role_auth")->field("id,auth_name,auth_pid,auth_path") ->order('sort asc,id asc ') ->where($where)->select();
        $tree = new \Think\Tree($menuLists,array('id','auth_pid'));
        $menu = $tree->leaf(0); //读取pid为0的所有数据生成菜单
        $initMenu = (object)$menu;
        session('initMenu',$initMenu);
        $this->assign('menu', $menu);
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