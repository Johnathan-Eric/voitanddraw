<?php
/**
 * @version infomation(版本信息): v1.0
 * @author(作者): xiaoxiao
 * @deprecated(简单说明): 微信模板信息公用类
 * @param:
 * @write_time(创建时间): 2017-12-28
 *   */
class template {
    private $access_token;
    public function __construct($appid,$secret) {
        
        if(!empty($appid) && !empty($secret)) {
            try{
                //获取token值
                $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
                $getArr=array();
                //判断token是否获取
                $tokenArr=json_decode(D("Common/Weixin","Logic")->curl_http($tokenUrl),true);
                $this->access_token = $tokenArr['access_token'];
                if(empty($tokenArr)) {
                    throw new \Think\Exception("Error Processing Request");
                }
            }catch(\Think\Exception $e){
                $ErrString = $e->getMessage();
                $this->ajaxReturn(array('code'=>$this->_Code['fail'],'message'=>$ErrString),'JSON');
                die();
            }
        }
        
    }
    
    
    
     /**
	 * curl 请求 
	 * @param stirng $url  		要请求的网址
	 * @param string $method	请求方式 get post
	 * @param string $data		post 请求的时数据
	 * @return mixed			返回结果
	 */
	
	function curl_http($url, $method = 'get', $data = '')
	{
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		
		$ch = curl_init();
		$headers = array('Accept-Charset: utf-8');
		if($ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		if(strtoupper($method)=="POST"){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	
	
	/**
	 * @version infomation(版本信息): v1.0
	 * @author(作者): xiaoxiao
	 * @deprecated(简单说明): 获取小程序模板库标题列表
	 * @param:string $access_token     获取的access_token值
	 * @param:string $offset           分页开始数据
	 * @param:string $count            分页结束数据(最多20条)
	 * @write_time(创建时间): 2017-12-28
	 *   */
	public function titleList($offset,$count){
	    
	    //请求参数
	    $title['offset'] = !isset($offset)?0:$offset;
	    $title['count']  = !isset($count)?5:$count;
	    
	    
	    //请求网口
	    $titleUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=".$this->access_token;
	    //请求接口,获取官网对应模板列表
	    $res = json_decode($this->curl_http($titleUrl,'post',json_encode($title)),true);
	    if($res['errcode'] == 0 && $res['errmsg'] == 'ok'){
	        return $res;
	    }else{
	        return $res;
	    }
	}
	
	
	/**
	 * @version infomation(版本信息): v1.0
	 * @author(作者): xiaoxiao
	 * @deprecated(简单说明): 获取小程序模板标题下关键词库
	 * @param:string $access_token     获取的access_token值
	 * @param:string $id               官网模板id(目前供选择的三种AT10007,AT0008,AT0009)
	 * @write_time(创建时间): 2017-12-29
	 *   */
    public function keyTitle($access_token,$id){
        if(empty($access_token) || empty($id)){
            return false;
        }
        
        //请求参数
        $data['id'] = $id;
        
        //请求网口
        $keyUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=".$access_token;
        $res = json_decode($this->curl_http($keyUrl,'post',json_encode($data)),true);
        if($res['errcode'] == 0 && $res['errmsg'] == 'ok'){
	        return $res;
	    }else{
	        return $res;
	    }
    }
    
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): xiaoxiao
     * @deprecated(简单说明): 组合模板并添加至帐号下的个人模板库
     * @param:string id               官网模板id(目前供选择的三种AT10007,AT0008,AT0009)
     * @param:array  keyword_id_list             用户拼接好的关键字组合
     * @write_time(创建时间): 2017-12-29
     *   */
    public function combinationTitle($list=array()){
        if(!is_array($list)){
            return false;
        }
        //请求网口
        $combinationUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=".$this->access_token;
        foreach($list as $k => $v){
            //请求参数
            $data['id'] = $k;
            $data['keyword_id_list'] = $v;
            $res = json_decode($this->curl_http($combinationUrl,'post',json_encode($data)),true);
            if($res['errcode'] == 0 && $res['errmsg'] == 'ok'){
                $datas[] = $res['template_id'];
            }else{
                return $res;
            }
        }
        return $datas;
    }
    
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): xiaoxiao
     * @deprecated(简单说明): 获取帐号下已存在的模板列表
     * @param:string $access_token     获取的access_token值
	 * @param:string $offset           分页开始数据
	 * @param:string $count            分页结束数据(最多20条)
     * @write_time(创建时间): 2017-12-29
     *   */
    public function userTitleList($access_token,$offset,$count){
        if(empty($access_token)){
            return false;
        }
        
        //请求参数
        $title['offset'] = !isset($offset)?0:$offset;
        $title['count']  = !isset($count)?5:$count;
        
        //请求网口
        $userUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=".$access_token;
        $res = json_decode($this->curl_http($userUrl,'post',json_encode($title)),true);
        if($res['errcode'] == 0 && $res['errmsg'] == 'ok'){
            return $res;
        }else{
            return $res;
        }
    }
    
    
    /**
     * @version infomation(版本信息): v1.0
     * @author(作者): xiaoxiao
     * @deprecated(简单说明): 删除帐号下的某个模板
     * @param:string $access_token     获取的access_token值
     * @param:string $template_id      用户账号下模板id
     * @write_time(创建时间): 2017-12-29
     *   */
    public function delTitle($access_token,$template_id){
        if(empty($access_token) || empty($template_id)){
            return false;
        }
        
        //请求数据
        $data['template_id'] = $template_id;
        
        //请求网口
        $delUrl = "https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=".$access_token;
        $res = json_decode($this->curl_http($delUrl,'post',json_encode($data)),true);
        if($res['errcode'] == 0 && $res['errmsg'] == 'ok'){
            return $res;
        }else{
            return $res;
        }
    }
}

