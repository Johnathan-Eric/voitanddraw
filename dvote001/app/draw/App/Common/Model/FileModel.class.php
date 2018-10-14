<?php
/**
 * 文件资源MODEL
 *
 */
namespace Common\Model;
use Think\Model;
class FileModel extends Model{
	
	protected $tableName = 'file';		  //数据表名
	
	protected $_validate 	= array();	//数据自动验证
	protected $_map         = array();  // 字段映射定义
  	protected $_scope		= array();  // 命名范围定义
	
  	//该函数会在实列化时自动执行
	function _initialize(){
		
	}
	
		
    
}