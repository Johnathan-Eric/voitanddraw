<?php
//一些网络相关接口
namespace Common\Api;

class NetApi{
    
    /**
     * 查检是否手机访问
     * @return boolean
     */
    function is_mobile($chk_domain = true) {
       
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        $is_mobile = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        if($chk_domain){
            $domain = strtolower($_SERVER['HTTP_HOST']);
            if(C('mobile_domain') == $domain)
                $is_mobile = true;
        }
        return $is_mobile;
    }
    /**
     * 检查是否微信
     * @return boolean
     */
    function is_weixin()
    {
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
    
    
    /**
     * 查是否是搜索引擎
     * @author wscsky
     */
    function is_robot($useragent = '')
    {
        static $kw_spiders = array('bot', 'crawl', 'spider' ,'slurp', 'sohu-search', 'lycos', 'robozilla');
        static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');
    
        $useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
        if(strpos($useragent, 'http://') === false && $this->strpos($useragent, $kw_browsers)) return false;
        return $this->strpos($useragent, $kw_spiders, true);
    }
    
   function strpos($string, $arr, $returnvalue = false)
    {
        if(empty($string)) return false;
        foreach((array)$arr as $v) {
            if(strpos($string, $v) !== false) {
                $return = $returnvalue ? $v : true;
                return $return;
            }
        }
        return false;
    }
    
    /**
     * 查是搜索引擎来源和关键字
     * @param string $referer 来源
     * @param string $from 返回来源
     * @return boolean
     */
    public function robot_from($referer = '')
    {
        $referer = empty($referer) ?  strip_tags($_SERVER['HTTP_REFERER']) : $referer;
        if(strstr($referer, 'baidu.com')){ //百度
            preg_match("/baidu.+wo?r?d=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'baidu';
        }else if(strstr($referer, 'google.com') or strstr($referer, 'google.cn')){ //谷歌
            preg_match("/google.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'google';
        }else if(strstr($referer, 'so.com')){ //360搜索
            preg_match("/so.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = '360';  
        }else if(strstr($referer, 'sogou.com')){ //搜狗
            preg_match("/sogou.com.+query=([^\&]*)/is", $referer, $match);
            $q = urldecode($tmp[1]);
            $from = 'sogou';    
        }else if(strstr($referer, 'soso.com')){ //搜搜
            preg_match("/soso.com.+w=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'soso';
        }else if(strstr($referer, 'bing.com')){ //bing
            preg_match("/bing.com.+q=([^\&]*)/is", $referer, $match);
            $q = urldecode($match[1]);
            $from = 'bing';
        }else {
            return false;
        }
        return array('q'=>$q,'from'=>$from);
    }       
		
}


