<?php
require_once 'commonConfig.php';
require_once 'Common_util_pub.php';
/**
 * 
 * @项目名称 :微信第三方平台代授权及实现业务 获取 access_token
 * @类名称 :ApiCommponentToken
 * @类描述  :该类实现第三方平台的access_token,并缓存到缓存
 * @创建人 :fql
 * @创建时间 :2017年8月13日上午10:26:36
 * @修改人 :fql
 * @修改时间 ：2017年8月13日上午10:26:36
 * @修改备注：
 * @version
 */
class ApiCommponentToken extends  Common_util_pub{

    public  function __construct(){
        $this->url='https://api.weixin.qq.com/cgi-bin/token';
        $this->url .= '?grant_type=client_credential';
        $this->url .= '&appid='.Commonconfig::$open_AppID;
        $this->url .= '&secret='.Commonconfig::$open_AppID_Secrect;
        $this->returnKey='access_token';
        $this->expire_time=7000;
    }
}