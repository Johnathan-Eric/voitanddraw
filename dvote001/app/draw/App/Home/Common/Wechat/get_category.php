<?php
/**
 * 
 * @项目名称 :project_name
 * @类名称 :GetCategory
 * @类描述  :获取授权小程序帐号的可选类目
 * @创建人 :fql
 * @创建时间 :2017年10月14日下午3:18:10
 * @修改人 :fql
 * @修改时间 ：2017年10月14日下午3:18:10
 * @修改备注：
 * @version
 */
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
require_once 'api_component_token.php';
require_once 'SDKRuntimeException.php';
require_once 'authorizer_access_token.php';

class GetCategory extends  Common_util_pub{
    public  function __construct($appid=null){
        $authoid= new \AuthorizerAccessToken();
        $this->url="https://api.weixin.qq.com/wxa/get_category?access_token=".$authoid->getInfo();
    }
    public function getCategory(){
        $data = $this->curls('',$this->url,30, 0, 'json');
        $data = json_decode($data,true);
        return $data;
    }
}