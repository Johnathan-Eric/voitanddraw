<?php
//用户相关逻辑
namespace Admin\Logic;
class AdminLogic{
	
        public $error = "";
        
        /**
	 * 获取员工（管理员）列表
	 * @author	ydx
	 * @access	public
	 * @param	int 	$p		列表页数
	 * @param       int 	$listRows	每页数量
	 * @param       int 	$where          查询搜索条件
         * @param       string  $order           排序 
	 * @return	array 	$list		管理员列表
	 */
        public function getLists($where = array(), $p = 1, $listRows = 10,$order=' a.add_time desc ')
        {
            $adminMod = D("admin/admin");
            $join = "left join ".C("DB_PREFIX")."admin_role as r on r.rid = a.rid";
            $total = $adminMod->alias("a")->where($where)->join($join)->count();
            if ($total == 0) {
                return array(
                    'total' => 0,
                    'lists' => array()
                );
            }
            $lists = $adminMod->alias("a")
                    ->field("a.*,r.role_name,r.role_auth_ids,r.role_auth_ac")
                    ->where($where)
                    ->join($join)
                    ->page($p,$listRows)->order($order)->select();
            if($lists){
                foreach ($lists as $k=>$v){
                    if($v['role_auth_ids'] == 'all'){
                        $lists[$k]['auth_name'] = "全部权限";
                    }else{
                        $lists[$k]['auth_name'] = $this->getAuthByIds($v['role_auth_ids']);
                    }
                }
            }
            return array(
                'total' => $total,
                'lists' => $lists
            );
        }
        
        public function getAuthByIds($str){
            $araMod = M("admin_role_auth");
            $where = array();
            $where['id'] = array('in',$str);
            $alists =  $araMod->field("id,auth_name,describe,auth_pid,auth_c,auth_a,auth_level,is_menu")->where($where)->select();
            $tree = new \Think\Tree($alists,array('id','auth_pid'));
            $menu = $tree->leaf(0); 
            if($menu){
                return $this->chlidStr($menu);
            } else {
                return FALSE;
            }
        }
        
        private function chlidStr($arr) {  
            $totalStr = "";
            foreach ($arr as $v){
                if($v['child']){
                    $childStr = $this->chlidStr($v['child']);
                    if($v['auth_level'] == 1){
                        $totalStr .= $v['auth_name']."-[".$childStr."];";
                    } else {
                        $totalStr .= $v['auth_name'].":(".$childStr.");";
                    }
                    
                }else{
                    $totalStr .= $v['auth_name'].';';
                }
            }
            return $totalStr;
        }  

        /**
	 * 保存管理员信息
	 * @author	watchman
	 * @param	array	$user_data	用户数据
	 * @return	bool
	 */
	function save($user_data = array()){
		$model 	= D("Admin");
		if(!$model->create($user_data)){
			$this->error = $model -> getError();
			return false;
		}else{
			//处理密码
			if(is_md5($model->password)){
                unset($model->password);
			}else{
                $model->password = md5(ADMIN_LOGIN_KEY . $model->password);
			}
			if($model->id == 0){
                $model->add_time = time();
                $model->status = 1;
				unset($model->id);
				$result = $model -> add();
			}else{
				//编辑时unset不需要修改的数据
                unset($model->uname);
				// unset($model->rid);
				$result = $model->save();
			}
				
			if($result === false){
				$this->error = $model -> getError();
				return false;
			}
			return true;
		}
	}
        
         /**
	 * 修改角色
	 * @author	watchman
	 * @param	array	$user_data	用户数据
	 * @return	bool
	 */
	function edit_role($user_data = array()){
		$model 	= D("Admin");
		if(!$model->create($user_data)){
			$this->error = $model -> getError();
			return false;
		}else{
			if($model->aid == 0){
                            $model->status = 1;
                            unset($model->aid);
                            $result = $model -> add();
			}else{
                            //编辑时unset不需要修改的数据
                            unset($model->uname);
                            $result = $model->save();
			}
				
			if($result === false){
				$this->error = $model -> getError();
				return false;
			}
			return true;
		}
	}
        
        public function getInfo($where)
        {
            $adminMod = D("admin/admin");
            $join = "left join ".C("DB_PREFIX")."admin_role as r on r.rid = a.rid";
            return $adminMod->alias("a")
                ->field("a.*,r.role_name,r.role_auth_ids,r.role_auth_ac")
                ->where($where)
                ->join($join)->find();
        }


        /**
	 * 读取管理员信息
	 * @author	watchman
	 * @param	int		$aid 	管理员ID
	 * @param	string	$uname	管理员帐户
	 * @param	array	$link	管理员员关系数据 如: group:会员组;
	 * @return	array	$info	管理员帐户信息
	 */
	function getRoleListsByDtype()
	{
            $model = M("Admin_role");
            $where['rid'] = array('neq', 1);
            return $model->where($where)->select();
	}
	
	/*
	 * 后台管理员登陆
	 * @param string $uname	用户名
	 * @param string $password 密码
	 * @param string $verify_code 验证码
	 * @author wscsky
	 */
	function login($uname, $password, $verify_code=''){
            $back_info = array('status'=>false, 'msg'=>'登陆失败!');
            if(empty($uname) || empty($password)) return $back_info;
            //如果开启验证码,验证
            if(C("ADMIN_LOGIN_VERIFY")){
                if($verify_code == ""){
                    $back_info['msg'] = "登陆失败,验证为空!";
                    return $back_info;
                }
                $verify = new \Think\Verify();
                $result = $verify->check($verify_code);
                if(!$result){
                    $back_info['msg'] = "验证码不正确!";
                    return $back_info;
                }
            }
            $model   = D("Admin");
            $model   -> scope("login");
            $map['m.username'] = $uname;
            $map['m.password']	= md5(ADMIN_LOGIN_KEY . $password);
            $model -> field("m.*,r.role_name,r.role_auth_ids,r.role_auth_ac");
            $user_info = $model->where($map)->find();
            if(!$user_info){
                $GLOBALS['admin'] = default_anonymous_user();
                $back_info['msg']	= "用户名或者密码有误!";
                return $back_info;
            }elseif($user_info['status'] !=1){
                $GLOBALS['admin'] = default_anonymous_user();
                $back_info['msg']	= "您的帐户已锁定";
                return $back_info;
            }else{
                $GLOBALS['admin'] = (object)$user_info;
                self::update_login_info(); //更新用户登陆信息
            }
            session('admin', $GLOBALS['admin']);
            return array("status" => true);
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
		 * 检查管理员登陆密码
		 * @param string $uname 用户名
		 * @param string $password  密码
		 * @param bool
		 */
		function check_password($password){
			if(empty($password)) return false;
			$model = D("Admin");
			$model -> scope("login");
			$map   = array();
			$map['m.password']		= md5(ADMIN_LOGIN_KEY . $password);
			$result = $model -> where($map) -> find();
			if(!$result){
				return false;
			}else{
				return true;
			}
		}
            /**
             * 修改陆密码
             * @param string $oldpwd 旧密码
             * @param string $newpwd 新密码
             * @param string $verify 验证码
             */
            function modify_password($oldpwd, $newpwd, $date){
                    $admin = session('admin');

                    if(!$admin || $admin -> id == 0 ){
                            $this->error = "您未登陆,或者登陆超时!";
                            return array(
                                'status' => 2,
                                'msg'    => $this->error
                            );
                    }
                    if($oldpwd == "" || $newpwd == "" ){
                            $this->error = "您提交的数据不有误!";
                            return array(
                                'status' => 2,
                                'msg'    => $this->error
                            );
                    }
                    //查旧密码
                    if(!self::check_password($oldpwd)){
                            $this->error = "您的旧密码不正确";
                            return array(
                                'status' => 2,
                                'msg'    => $this->error
                            );
                    }
                    $date["password"] = md5(ADMIN_LOGIN_KEY . $newpwd);
                    $model = M("admin");
                    $result = $model->where("id = %d", $admin->id)->save($date);
                    if($result === false){
                            $this->error = "登陆密码修改失败,请稍候重试!";
                            return array(
                                'status' => 2,
                                'msg'    => $this->error
                            );
                    }
                    return array(
                        'status' => 1,
                        'msg'   => '成功'
                    );		
            }
                
			
	/**
	 * 更新用户登陆信息
	 */
	function update_login_info(){
		$admin = $GLOBALS['admin'];
		if(!$admin || $admin->aid == 0) return;
		$data = array(
                    'last_time'		=> array("exp","last_online"),
                    'last_online'	=> NOW_TIME,
                    'login_time'	=> array("exp","login_time + 1"),
                    'last_ip'		=> get_client_ip(),
		);
		M("Admin") -> where("id = %d", $admin->aid)->save($data);
		//记录日志
		\Think\Log::log("login", "登陆系统");
	}
	

	/**
	 * 读取最后一条错误信息
	 */
	function getError(){
		return $this-> error;
	}

         /**
	 * 获取角色列表
	 * @author	ydx
	 * @access	public
	 * @param	int 	$p		列表页数
	 * @param       int 	$listRows	每页数量
	 * @param       int 	$where          查询搜索条件
         * @param       string  $order           排序 
	 * @return	array 	$list		管理员列表
	 */
        public function getRoleLists($where = array(), $p = 1, $listRows = 10,$order=' r.rid desc ')
        {
            $model = M("Admin_role");
            $total = $model->alias("r")->where($where)->count();
            if ($total == 0) {
                return array(
                    'total' => 0,
                    'lists' => array()
                );
            }
            $lists = $model->alias("r")
                    ->where($where)
                    ->page($p,$listRows)->order($order)->select();
            if($lists){
                foreach ($lists as $k=>$v){
                    if($v['role_auth_ids'] == 'all'){
                        $lists[$k]['auth_name'] = "全部权限";
                    }else{
                        $lists[$k]['auth_name'] = $this->getAuthByIds($v['role_auth_ids']);
                    }
                }
            }
            return array(
                'total' => $total,
                'lists' => $lists
            );
        }
        
         /**
	 * 获取角色列表
	 * @author	ydx
	 * @access	public
	 * @param	int 	$p		列表页数
	 * @param       int 	$listRows	每页数量
	 * @param       int 	$where          查询搜索条件
         * @param       string  $order           排序 
	 * @return	array 	$list		管理员列表
	 */
        public function getRoleAuthLists($where = array())
        {
            $model = M("Admin_role_auth");
            $alists =  $model->field("id,auth_name,describe,auth_pid,auth_c,auth_a,auth_level,is_menu")->where($where)->select();
            $tree = new \Think\Tree($alists,array('id','auth_pid'));
            $menu = $tree->leaf(0); 
            return $menu;
        }
        
        public function getRoleInfo($rid)
        {
            $model = M("Admin_role");
            $lists = $model->where("rid = %d", $rid)->find();
            return $lists;
        }
        
        public function saveRoleInfo($ids)
        {
            $model = M("Admin_role_auth");
            $idstr = implode(',', $ids);
            $lists =  $model->field("id,auth_name,auth_pid,auth_c,auth_a,auth_level,is_menu")->where(array('id'=>array('in',$idstr)))->select();
            $ac_str = "";
            $p_ids = array();
            foreach ($lists as $k=>$v){
                if($v['auth_a']){
                    $ac_str .= $v['auth_c']."-".$v['auth_a'].",";    
                }
                if(!in_array($v['auth_pid'], $ids)){
                    if($v['auth_pid'] != 0){
                        $p_ids[] = $v['auth_pid'];
                    }
                }
            }
            $pStr = implode(',', array_unique($p_ids));
            if($pStr){
                $idstr .= ",".$pStr;
            }
            return array(
                'idstr' => $idstr,
                'ac_str' => $ac_str
            );
        }
        
        /**
        * 保存管理员信息
        * @author	watchman
        * @param	array	$user_data	用户数据
        * @return	bool
        */
        function save_role($user_data = array()) {
            $model = M("Admin_role");
            if(!$user_data['rid']){
                unset($user_data['rid']);
                $result = $model->add($user_data);
            }else{
                $result = $model->save($user_data);
            }
            if($result === false){
                $this->error = "参数错误";
                return false;
            }
            return true;
        }
    /**
    * 总店获取分店数量
    * @author	watchman
    * @param	array	$user_data	用户数据
    * @return	bool
    */
    function allCount() {
        $model = M("distributor");
        return $model->where(array('status'=>2))->count();
    }
    
}