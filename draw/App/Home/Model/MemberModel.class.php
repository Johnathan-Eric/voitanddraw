<?php
namespace Home\Model;
use Think\Model\RelationModel;

/**
    function:获取用户相关信息
**/
class MemberModel extends RelationModel {
   /**
      function:用户表对象
   **/
   private $_Member = NULL;
   private $_Memgrade = NULL;
   private $_Memassets = NULL;
   private $_Membank = NULL;
   private $_Memwdraw = NULL;
   
   /**
      function:用户表映射关系
   **/

   private $_MemberObject = array(
      'member'  => 'member',
      'grade'   => 'member_grade',
      'assets'  => 'member_assets',
      'bank'    => 'member_bank',
      'withdraw'  => 'member_withdraw'
   );

   private $_resultObj = NULL;
   
   /**
      function:
   **/
   public function __construct() {
   		/**
           function:在构造函数中实例化数据库对象
   		**/
   		if($this->_Member == NULL) {
   			$this->_Member = M($this->_MemberObject['member']);
   		}
      if($this->_Memgrade == NULL) {
        $this->_Memgrade = M($this->_MemberObject['grade']);
      }
      if($this->_Memassets == NULL) {
        $this->_Memassets = M($this->_MemberObject['assets']);
      }
      if($this->_Membank == NULL) {
        $this->_Membank = M($this->_MemberObject['bank']);
      }
      if($this->_Memwdraw == NULL) {
        $this->_Memwdraw = M($this->_MemberObject['withdraw']);
      }
   }
   
   ###### 会员信息
   /**
    * 获取用户信息
    * @param string $uid uid
    * @param string $fields 查询字段
    **/
   public function getInfo($uid, $fields = '*') {
      $info = $this->_Member->where(array('uid' => array('eq', $uid)))->field($fields)->find();
      return $info;
   }

   /**
    * 获取用户列表信息
    * @param array $where 查询条件
    * @param string $fields 查询字段
    **/
   public function getList($where, $fields = '*', $page = 1, $listRows = 10) {
      if ($page == 0) {
          $info = $this->_Member->where($where)->field($fields)->select();
      } else {
          $info = $this->_Member->where($where)->field($fields)->page($page, $listRows)->select();
      }
      return $info;
   }

   /**
    * 获取用户信息
    * @param string $phone 手机号码
    * @param string $fields 查询字段
    **/
   public function getInfoByphone($phone, $fields = '*') {
      return $this->_Member->where(array('phone' => array('eq', trim($phone))))->field($fields)->find();
   }

   /**
    * 获取用户信息
    * @param string $phone 手机号码
    * @param string $fields 查询字段
    **/
   public function getInfoByOpenid($openid, $fields = '*') {
      return $this->_Member->where(array('openid' => array('eq', trim($openid))))->field($fields)->find();
   }

   /**
    * 获取用户信息
    * @param string $code 邀请码
    * @param string $fields 查询字段
    **/
   public function getInfoByCode($code, $fields = '*') {
      return $this->_Member->where(array('user_code' => array('eq', trim($code))))->field($fields)->find();
   }

   /**
    * 保存数据
    **/
   public function saveData($where, $data) {
      return $this->_Member->where($where)->save($data);
   }

   /**
    * 添加一用户
    **/
   public function addData($data) {
      return $this->_Member->add($data);
   }

   ###### 会员等级信息
   /**
    * 获取会员等级信息
    * @param string $grade 等级ID
    * @param string $fields 查询字段
    **/
   public function getGradeInfo($grade, $fields = '*') {
      $info = $this->_Memgrade->where(array('id' => array('eq', $grade)))->field($fields)->find();
      return $info;
   }

   /**
    * 会员等级列表
    * @param array $where 查询条件
    * @param string $fields 查询字段
    **/
   public function getGradeList($where, $fields = '*') {
      $list = $this->_Memgrade->where($where)->field($fields)->order('id asc')->select();
      return $list;
   }

   ###### 会员收益信息
   /**
    * 会员收益列表
    * @param array $where 查询条件
    * @param string $fields 查询字段
    **/
   public function getAssetsList($where, $fields = '*', $page = 1, $listRows = 10) {
      $list = $this->_Memassets->where($where)->field($fields)->page($page, $listRows)->order('id desc')->select();
      return $list;
   }

   /**
    * 添加收益信息
    **/
   public function addAssetsData($data) {
      return $this->_Memassets->add($data);
   }

   ###### 会员提现信息
   /**
    * 会员提现列表
    * @param array $where 查询条件
    * @param string $fields 查询字段
    **/
   public function getWithdrawList($where, $fields = '*', $page = 1, $listRows = 10) {
      $list = $this->_Memwdraw->where($where)->field($fields)->page($page, $listRows)->order('id desc')->select();
      return $list;
   }

   /**
    * 添加提现信息
    **/
   public function addWithdrawData($data) {
      return $this->_Memwdraw->add($data);
   }

   ###### 会员银行信息
   /**
    * 添加一银行卡信息
    **/
   public function addBankData($data) {
      return $this->_Membank->add($data);
   }

   /**
    * 获取会员银行卡信息
    * @param string $where 查询条件
    * @param string $fields 查询字段
    **/
   public function getBankInfo($where, $fields = '*') {
      $info = $this->_Membank->where($where)->field($fields)->find();
      return $info;
   }

}