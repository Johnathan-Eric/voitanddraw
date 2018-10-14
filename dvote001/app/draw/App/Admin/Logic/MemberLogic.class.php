<?php
//会员处理逻辑模块
namespace Admin\Logic;

class MemberLogic{
	
	protected  $_error = null;
	
	/**
	 * 读取会员列表
	 * @author	eva
	 * @param 	array 	$where 查询条件
	 * @param	int 	$page		列表页数
	 * @param	int 	$listRows	每页数量
	 * @param	int 	$count		符合条件记录总数
	 * @return	array 	$list		会员列表
	 */
    public function getLists($where = array(), $p = 1, $listRows = 10,$order=' m.uid asc ') {
        $model = M("Member");
        $total = 0;
        if ($listRows) {
			$total = $model->alias("m")->where($where)->count();
	        if ($total == 0) {
	            return array(
	                'total' => 0,
	                'lists' => array()
	            );
	        }
        }
        
        //$listRows==0时 查全部
        $fields = 'uid,name,phone,region,created_at,status,type,grade,total_income,total_fee,total_money,is_head,puid,ppuid,position';
        if ($listRows==0) {
        	$lists = $model->alias("m")->where($where)->order($order)->field($fields)->select();
        } else {
        	$lists = $model->alias("m")->where($where)->page($p,$listRows)->field($fields)->order($order)->select();
        }
        return array(
            'total' => $total,
            'lists' => $lists
        );
    }
	
	/**
	 * 保存编辑的会员信息
	 * @author	watchman
	 * @param	array	$data	从表单读过来的会员数据
	 * @return	bool
	 */
	function save($data = array()){
		$model = D("Member");
		
		if(!$model->create($data)){//验证数据
			$this->_error = $model->getError();
			return false;
		}else{
			//处理密码
			if(empty($model->password)){
				unset($model->password);
			}else{
				$model->password = md5(MEMBER_LOGIN_KEY . $model->password);
			}
			if(empty($model->password2)){
				unset($model->password2);
			}else{
				$model->password2 = md5(MEMBER_LOGIN_KEY2 . $model->password2);
			}
			if(empty($model->password3)){
				unset($model->password3);
			}else{
				$model->password3 = md5(MEMBER_LOGIN_KEY3 . $model->password3);
			}
			
			//处理昵称，昵称不填则默认为其真实姓名
			$model->nickname ? '' : $model->nickname = $model->real_name;
			
			if($model->uid == 0){//新增
				unset($model->uid);
				$model->gid = MEMBER_DEFAULT_GID;//将用户放在默认用户组
				$result 	= $model->add();
			}else{//编辑
				//编辑时unset不需要修改的数据
				unset($model->gid);
				unset($model->uname);
				unset($model->puid);
				unset($model->puname);
				
				$result = $model->save();
			}
				
			if($result === false){
				$this->_error = L("DO_FAIL");
				return false;
			}
			return true;
		}
	}
	
	/**
	 * 获取下级或下下级用户信息
	 **/
	public function getpuids($where, $fields) {
		return M("Member")->where($where)->field($fields)->select();
	}
}