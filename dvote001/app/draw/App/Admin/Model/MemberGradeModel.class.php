<?php
/**
 * 会员等级Model
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class MemberGradeModel extends RelationModel{
	protected $tableName = 'member_grade';		  //数据表名
    
    /**
     * 添加数据
     * @param array $data 添加数据
     * @return boolean true/false
     **/
    public function add($data) {
        // 执行更新
        return M($this->tableName)->add($data);
    }

    /**
     * 更新数据
     * @param array $data 更新数据
     * @param array $where 更新条件
     * @return boolean true/false
     **/
    public function save($data, $where) {
        return M($this->tableName)->where($where)->save($data);
    }

    /**
     * 获取数据
     * @param array $where 查询条件
     * @return array $data 返回数据
     **/
    public function getInfo($where) {
        return M($this->tableName)->where($where)->find();
    }
}