<?php
require_once 'curl.func.php';
class Sms{
    public static function sendYzm($mobile){
        $appkey = C('Sms.appkey');//你的appkey
        $yzm = rand(100000,999999);
        $name = $mobile.'_'.$yzm;
        // S($name,$yzm,1200);
        // $content=urlencode($yzm.'微智慧账号短信验证码，20分钟有效。如非本人操作，请忽略本短信。【微小服】');
        // $url = "http://api.jisuapi.com/sms/send?appkey=$appkey&mobile=$mobile&content=$content";
        // $result = curlOpen($url);
        // $jsonarr = json_decode($result, true);
        // if($jsonarr['status'] != 0)
        // {
        //     return $jsonarr['msg'];
        //     exit();
        // }
        //$result = $jsonarr['result'];
        $date = array(
            'mobile' => $mobile,
            'code' => $yzm,
            'type' => 'auth',
            'send_time' => time()
        );
        $SmsMod = M('sms');
        $SmsInfo = $SmsMod->where(array('mobile' => array('eq', $mobile)))->field('code')->find();
        if ($SmsInfo) {
            $SmsMod->where(array('mobile' => array('eq', $mobile)))->save(array('code' => $yzm));
        } else {
            $SmsMod->add($date);
        }
        
        // return $jsonarr;
        return $yzm;
    }
 
 public static function express($number,$type=null){
     $appkey = C('Sms.appkey');//你的appkey
     $url = "http://api.jisuapi.com/express/query?appkey=$appkey";
     if(empty($type)){
         $type='auto';
        }
     $post = array('type'=>$type,
         'number'=>$number
     );
     $result = curlOpen($url, array('post'=>$post));
     
     $jsonarr = json_decode($result, true);
     //exit(var_dump($jsonarr));
     
     if($jsonarr['status'] != 0)
     {
         echo $jsonarr['msg'];
         exit();
     }
     
     $result = $jsonarr['result'];
     if($result['issign'] == 1) echo '已签收'.'<br>';
     else echo '未签收'.'<br>';
     foreach($result['list'] as $val)
     {
         echo $val['time'].' '.$val['status'].'<br>';
     }
     
     }

}
    