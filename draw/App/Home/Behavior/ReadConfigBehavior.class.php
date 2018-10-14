<?php
namespace Home\Behavior;
defined('THINK_PATH') or exit("Access Denied");
//读取数据库参数配置
class ReadConfigBehavior {
	public function run(&$params){

		//读取网站配置参数
		$configs 	= F(C("CONFIG_CACHE_NAME"));

		if(!$configs){
			$configs  	= M("Config")->order("groupid,listorder")->select();
			APP_DEBUG or F(C("CONFIG_CACHE_NAME"), $configs);
		}
		foreach ($configs as $config){
			C($config['name'], unserialize($config['value']));
		}
		
		//读取语言包设置		
		$configs 	= F(C("LANG_CACHE_NAME"));
		$langSet 	= C('DEFAULT_LANG');
		$langList 	= C('LANG_LIST',null,'zh-cn');
		if(false === stripos($langList,$langSet)) { // 非法语言参数
			$langSet = C('DEFAULT_LANG');
		}
		
		if(!$configs || !$configs[$langSet]){
			$configs 			= array();
			$configs[$langSet] 	= M("lang")->where("type = '%s'", $langSet)->select();
			APP_DEBUG or F(C("LANG_CACHE_NAME"), $configs);
		}
		
		foreach ($configs[$langSet] as $config){
			L($config['name'], $config['value']);
		}
	}
}