<?php
namespace Home\Model;
use Think\Model\RelationModel;
class SmsModel extends RelationModel {
   /**
      function:用户表对象
   **/
   private $_Sms = NULL;
   
   /**
      function:用户表映射关系
   **/
   private $_SmsObject = array('sms'=>'sms');

   /**
      function:
   **/
   public function __construct() {
   		/**
           function:在构造函数中实例化数据库对象
   		**/
   		if($this->_Sms == NULL) {
   			$this->_Sms = M($this->_SmsObject['sms']);
   		}
   }

   /**
    * 获取信息
    * @param string $phone 手机号码
    * @param string $fields 查询字段
    **/
   public function getInfoByphone($mobile, $fields = '*') {
      return $this->_Sms->where(array('mobile' => array('eq', trim($mobile))))->field($fields)->find();
   }
}