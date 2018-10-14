<?php
/**
 *  本类由空控制器提示页面
 *  @author Jeffreyzhu.cn@gmail.com
 *  访问 ： http://scm.dage.nc:8090/index.php/Home/haha
 */
namespace Admin\Action;
use Think\BaseAction;
class EmptyAction extends BaseAction {
    public function index(){
	    $cityName = CONTROLLER_NAME;
        $this->empty_Act($cityName);
    }
    
    public function empty_Act($name){
    	header("Content-type: text/html; charset=utf-8");
    	echo '您调用了一个不存在的Action:' . $name;
    }
}