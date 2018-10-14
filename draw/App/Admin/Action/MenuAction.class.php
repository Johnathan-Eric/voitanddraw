<?php
namespace Admin\Action;
defined("THINK_PATH") or die("error");
use Think\BaseAction;
class MenuAction extends BaseAction
{
	protected $menuType,$model;
	//权限设置
	function accessRules(){
		return array(
			'index'		=> true,
			'get_menu'	=> true,
			'add'		=> 'menu_add',
			'saveadd'	=> 'menu_add',
			'edit'		=> 'menu_edit',
			'saveedit'	=> 'menu_edit',
			'delete'	=> 'menu_del',
			'listorder'	=> 'menu_listorder',
		);
		
	}
	
	public function __construct()
	{
		parent::__construct();
		$this->model     = D('Menu','Model');
		$this->menuType  = array('0'=>'后台菜单','1'=>'会员菜单');
		$this->assign('menuType',$this->menuType);
		$this->assign("page_title","菜单管理");
	}

	public function index()
	{
		$model = D("Menu","Logic");
		$menu  = $model -> get_menu(I('type'),'',true);

		$subnav = array(
			array(
					"name"	=> L("ADD_MENU"),
				 	"url"	=> U("Menu/add","type=".I("type")."&menuid=".I("menuid")))
		);

		$menus 		= list_to_tree($menu,"id","parentid");
		$select_id  = I('get.select_id','0','intval');
		
		if(!empty($select_id))
		{
			foreach ($menus as $key=>$value)
			{
				if($value['id']==$select_id)
				{
					$menuStr[] = $value;
				}
			}
			$menus_graph= graph_tree_list($menuStr,"id");
			
			$this->assign('menuList',$menus);
			$this->assign('menus',$menuStr);
			$this->assign('menus_graph',$menus_graph);
		}
		else{
			$menus_graph= graph_tree_list($menus,"id");
			
			$this->assign('menus',$menus);
			$this->assign('menus_graph',$menus_graph);
		}
		$this->assign('type',I('type'));
		$this->assign("subnav", $subnav);
		$this->assign("select_id",$select_id);
		$this->display();
	}

	function add()
	{
		$data  			= is_array(I('get.'))?I('get.'):'';
		$type  			= isset($data['type'])?$data['type']:0;
		$menu 			= $this->get_menu($type,'',true);
		$menus 			= list_to_tree($menu,"id","parentid");
		$vo['type'] 	= $data['type'];
		$vo['parentid'] = $data['parentid'];

		if($type == 1){
			$is_admin = false;
		}else{
			$is_admin = true;
		}
		
		$subnav = array(
				array(
						"name"	=> L("ADD_MENU"),
						"url"	=> U("Menu/add","type=".I("type")."&menuid=".I("menuid")))
		);
		$model = D("Access","Logic");
		//读取权限分组
		$access_list = $model -> get_group($is_admin);
		//读取每个分组下的权限列表
		foreach ($access_list as &$group){
			$group['access'] = $model -> get_access_list($is_admin, array("id" => $group['id']));
		}
		$this->assign("access_list", $access_list);
		$this->assign("subnav", $subnav);
		$this->assign('parentid',$data['parentid']);
		$this->assign('menus',$menus);
		$this->assign('action_name','add');
		$this->assign('vo',$vo);
		$this->display('Menu/edit');
	}

	function saveadd()
	{
		$data  		     = I('post.');
		$menuid 		 = $data['menuid'];
		unset($data['id'],$data['menuid']);
		if(is_array($data['access'])){
			$data['access'] = implode(",", $data['access']);
		}else{
			$data['access'] = "";
		}
		$data['model']   = trim($data['model']);
		$data['action']  = trim($data['action']);
		$data['subname'] = empty($data['subname'])?$data['name']:$data['subname'];
		$result          = $this->model->addData($data);
		F("menus_all_".I("type"),null); //清除缓存
		F("menus_".I("type"),null); //清除缓存
		$this->showResult($result,U('Menu/index',"type=".I("type")."&menuid=".$menuid));
	}

	function edit() {

		$data 	  = I('get.');
		$menu     = $this->get_menu($data['type'],'',true);
		$menus    = list_to_tree($menu,"id","parentid");
		$vo 	  = $menu[$data['id']];
		$subnav = array(
				array(
						"name"	=> L("ADD_MENU"),
						"url"	=> U("Menu/add","type=".I("type")."&menuid=".I("menuid")))
		);
		$model = D("Access","Logic");
		if($vo['type'] == 1){
			$is_admin = false;
		}else{
			$is_admin = true;
		}
		//读取权限分组
		$access_list = $model -> get_group($is_admin);
		//读取每个分组下的权限列表
		foreach ($access_list as &$group){
			$group['access'] = $model -> get_access_list($is_admin, array("id" => $group['id']));
			$is_all = true;
			foreach ($group['access'] as &$val){
				if(strpos(",{$vo['access']},",",{$val['access']},") !== false || $vo['access'] == 'all'){
					$val['checked'] = true;
				}else{
					$is_all = false;
				}
			}
			$group['access'] && $group["checked"] = $is_all;
		}
		$this->assign("access_list", $access_list);
		$this->assign("subnav", $subnav);
		$this->assign ('vo', $vo );
		$this->assign('menus',$menus);
		$this->display ();
	}

	function saveedit()
	{
		$data   		 = I('post.');
		$menuid 		 = $data['menuid'];
		$id 			 = $data['id'];
		unset($data['id'],$data['menuid']);
		if(is_array($data['access'])){
			$data['access'] = implode(",", $data['access']);
		}else{
			$data['access'] = "";
		}
		$data['model']   = trim($data['model']);
		$data['action']  = trim($data['action']);
		$data['subname'] = empty($data['subname'])?$data['name']:$data['subname'];
		$result 		 = $this->model->updateMenuById($id,$data);
		F("menus_all_".I("type"),null); //清除缓存
		F("menus_".I("type"),null); //清除缓存
		$this->showResult($result,U('Menu/index',"type=".I("type")."&menuid=".$menuid));
	}

	function delete()
	{
		$id 	= intval(I('get.id'));
		if(!is_numeric($id))
			$this->error(L("INPUT_DATA_ERROR"));
 		$result = $this->model->deleteByCondition("id={$id} or parentid={$id}");
 		F("menus_all_".I("type"),null); //清除缓存
		F("menus_".I("type"),null); //清除缓存
 		$referer = $_SERVER["HTTP_REFERER"];
 		if($referer == "") $referer = U("Menu/index","menuid=".I('get.menuid'));
		$this->showResult($result,$referer);
	}

	function listorder()
	{
		$ids 	= I('post.listorders');
		foreach ($ids as $key=>$value)
		{
			$this->model->updateMenuById($key,array('listorder'=>$value));
		}
		F("menus_all_".I("type"),null); //清除缓存
		F("menus_".I("type"),null); //清除缓存
		$this->showResult(true);
	}

	private function showResult($result,$jumpUrl='')
	{
		if($result===true){
			$this->success(L("do_success"),$jumpUrl);
			exit();
		}else{
			$this->error(L("do_fail"));
		}
	}
	function get_menu($type = 0, $parentid = NULL, $ALL = false)
	{
		$model = D("Menu","Logic");
		$menu = $model -> get_menu($type, $parentid, $ALL);
		foreach ($menu as $v)
		{
			$menus[$v['id']]  = $v;
		}
		return $menus;
	}

}
?>