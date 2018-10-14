<?php
/**
 * 会员组模型
 * @author zc
 *
 */
namespace Admin\Model;
use Think\Model;
class MemberGroupModel extends Model{
	
    protected $tableName = 'member_group';    //数据表名

    protected $_validate 	= array();	//数据自动验证
    protected $_map         = array();  //字段映射定义
    protected $_auto		= array();	//自动填写
    protected $_scope		= array();  //命名范围定义
    //protected $patchValidate = true;
    public $error;

    //该函数会在实列化时自动执行
    function _initialize(){
        $this->_auto = array(
                array('add_time','time',1,'function'),			//新增数据时自动设置添加时间
        );
        $this->_validate = array(
            array('name', 'require', '会员组名称必填', 1, '', 3),
            array('integral_num', 'number', '积分满足点为整数', 1, '', 3),
            array('discount', '0,1', '享受折扣0~1两位小数', 1, 'between', 3),
            array('money', 'is_numeric', '免运费标准为数字', 1, 'function', 3),
        );
    }

    public function getLists($where = array(), $p = 1, $listRows = 10,$order=' integral_num asc ')
    {
        $total = $this->where($where)->count();
        if ($total == 0) {
            return array(
                'total' => 0,
                'lists' => array()
            );
        }
        $lists = $this-> where($where)->page($p,$listRows)->order($order)->select();
        return array(
            'total' => $total,
            'lists' => $lists
        );
    }
	
    public function saveData($date)
    {
        if($this->create($date)){
            if($date['id']){
                unset($this->data['add_time']);
                $re = $this->where(array('gid'=>$date['id']))->save();
            } else {
                unset($this->data['id']);
                $this->data['listorder'] = '255';
                $re = $this->add();
            }
        }else{
            $this->error = $this->getError();
            return FALSE; 
        }
        if($re){
            return TRUE;
        } else {
            $this->error = "操作失败";
            return FALSE;
        }
    }
    
    public function getInfo($id)
    {
        return $this->where(array('gid'=>$id))->find();
    }
	
	
    
}