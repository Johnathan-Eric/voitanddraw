<?php

namespace Admin\Model;
use Think\Model;
class MenuModel extends Model{

	protected $tableName = 'menu';

	public function getMenuByParent($parentid)
	{
		$sql1 	= "SELECT id FROM ".C("DB_PREFIX")."menu where id={$parentid} or parentid={$parentid} group by id";
		$sql 	= "select * from ".C("DB_PREFIX")."menu where id in({$sql1}) or parentid IN({$sql1})";
		$model 	= $this->query($sql);
		foreach ($model as $k=>$v)
		{
			$menudata[$v['id']] 			= $v;
		}
		return $menudata;
	}

	public function deleteByCondition($condition)
	{
		$result  = $this->where($condition)->delete();
		if( $result=== false){
			return $this->getDbError();
		}
		return true;
	}

	public function updateMenuById($id,$data)
	{
		if(!is_numeric($id))
			return L("INPUT_DATA_ERROR");
		$result = $this->where("id={$id}")->save($data);
		if( $result=== false){
			return $this->getDbError();
		}
		return true;
	}

	function addData($data)
	{
		$listorder = $this->where("parentid=%d and type=%d",array($data['parentid'],$data['type']))->max('listorder');
		if($listorder===false)
			return $this->getDbError();
		$data['listorder']	= $listorder+1;
		$result    = $this->data($data)->add();
		if( $result=== false){
			return $this->getDbError();
		}
		return true;
	}

}