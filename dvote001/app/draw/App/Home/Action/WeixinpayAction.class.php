<?php
namespace Home\Action;
defined("THINK_PATH") or die();
use Think\Action;
class WeixinpayAction extends Action {
  /**微信下单接口**/
  private $play_url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
  
  /**微信支付商户号**/
  private $buess_id = "1489047312";
  
  /**微信APPKEY 密钥**/
  private $appkey = "9EAEF234234Aadfaf90123456520mion";

  /**模拟账号**/
  private $appid = "wxe8517f6c045140f4";
  private $merchant_num = '1492880022'; // 商户号
  private $merchant_appkey = 'JKLJKL994321421kklqw3412fafadsfa'; // 商家APPKEY

  /**
  * 接受客户端传过来的参数
  **/
  private $_ClientData = NULL;
  private $_Code = array('success'=>200,'fail'=>402,'empty'=>401);
  private $_MemMod,$_OrdersMod,$orderStatus;

  /**
   * 构造函数
   * 初始化数据
   **/
  public function _initialize() {
    $client_data = file_get_contents("php://input");
    if(!empty($client_data)) {
          try{
              $this->_ClientData = json_decode($client_data,TRUE);
              if(empty($this->_ClientData)) {
                  throw new \Think\Exception("Error Processing Request");
              }
          }catch(\Think\Exception $e){
              $ErrString = $e->getMessage();
              $this->ajaxReturn(array('code' => $this->_Code['fail'], 'msg' => $ErrString),'JSON');
              die();
          }
    }
    $this->_MemMod = D('Member');
    $this->_OrdersMod = D('Orders');
    $this->orderStatus = C('orderStatus');
  }

  /**
   * 微信支付
   **/
  public function doPay() {
    // 接收参数
    $clientData = $this->_ClientData;

    // 验证参数
    if (!$clientData['ordersn'] || !$clientData['openid']) {
        $this->ajaxReturn(array('code' => 201, 'msg' => '参数错误！'));
        return;
    }

    // 查看订单信息
    $orders = $this->_OrdersMod->getInfoByOrdersn($clientData['ordersn'], 'order_id,order_amount,act_type');
    if (empty($orders)) {
        $this->ajaxReturn(array('code' => 203, 'msg' => '订单信息错误！'));
        return;
    }

    // 执行支付
    if ($orders['order_status'] != 0) {
        $this->ajaxReturn(array('code' => 204, 'msg' => $this->orderStatus[$orders['order_status']]));
        return;
    } else {
        if ($orders['order_amount'] == 0) { // 订单为零
            $data['order_status'] = 1;
            $data['pay_price'] = 0;
            $data['pay_time'] = time();
            $is_succ = M('Orders')->where(array('order_id'=>$orders['order_id']))->save($data);
            if ($is_succ) {
                $this->ajaxReturn(array('code' => 200, 'msg' => '支付成功'));
                return;
            } else {
                $this->ajaxReturn(array('code' => 205, 'msg' => '支付失败'));
                return;
            }
        } else {
            // 订单编号
            $ordersn = $clientData['ordersn'].'_'.rand(0,999);

            // 执行微信支付
            $weixinpay = new \Home\Model\WeixinPayModel($this->appid, $clientData['openid'], $this->merchant_num, $this->merchant_appkey, $ordersn, "微信支付", $this->verifyMoney($orders['order_amount']));
            $pay = $weixinpay->pay();
            if(!empty($pay) && is_array($pay)) {
                $this->ajaxReturn(array('code'=>$this->_Code['success'],'msg' => '支付成功','weixinData'=>$pay));
                return;
            }
        }
    }
  }
  
 /**验证用户的金额**/
 private function verifyMoney($money) {
 	  /**如果客户端传过来的参数为空或者不包含金额就返回FALSE**/
    if(empty($money)) {
    	  return FALSE;
    }
    return floatval($money * 100);
 }

  /**
   * @version information(版本信息): v1.0
   * @author(作者): wangxiaoxiao
   * @deprecated(简要说明): 微信支付回调信息(普通电商)
   * @param
   * @write_time(创建时间): 2017-12-6
   **/
  public function weixin_callback(){
    $xml = file_get_contents('php://input');
    $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

    // 订单编号
    $out_trade_no = substr($result['out_trade_no'], 0, 16);

    //订单数据
    if($result['result_code'] == SUCCESS && $result['return_code'] == SUCCESS){
        $nowTime = time();
        $re['finish_time'] = $nowTime;
        $re['pay_time'] = $nowTime;//支付时间
        $re['pay_status'] = 1;
        $re['pay_remark'] = json_encode($result);
        $re['transaction_id'] = $result['transaction_id'];
        $re['pay_price'] = $result['total_fee']/100;
        $re['order_status'] = 1;
        // 保存订单信息
        M('Orders')->where(array('order_sn'=>$out_trade_no))->save($re);

        // 计算提成信息
        $this->doExtract($out_trade_no);
    }
    $return = ['return_code'=>'SUCCESS','return_msg'=>'OK'];
    $xml = '<xml>';
    foreach($return as $k=>$v){
      $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
    }
    $xml.='</xml>';
    echo $xml;
  }

  /**
   * 处理上级和上级提成
   * @param string $ordersn 订单编号
   **/
  private function doExtract($ordersn) {
      // 根据订单编号获取订单信息
      $orders = $this->_OrdersMod->getInfoByOrdersn($ordersn, 'puid,ppuid,order_amount,act_type,pay_status,buid,order_id,is_lev');
      if (empty($orders)) {
          return false;
      }

      // 查询上级和上上级数据
      $puid = $orders['puid'];
      $ppuid = $orders['ppuid'];
      $uids = array();
      if ($orders['puid']) { // 上级uid
          $uids[] = $puid;
      }
      if ($orders['ppuid']) { // 上上级uid
          $uids[] = $ppuid;
      }
      if (!empty($uids)) {
          // 根据buid获取会员提成数据
          $gwhere['buid'] = array('eq', $orders['buid']);
          $gwhere['status'] = array('eq', 0);
          $gradeData = $this->_MemMod->getGradeList($gwhere,'id,aextract,paextract,xextract,pxextract');
          if (empty($gradeData)) {
             return false;
          }
          $gList = array();
          foreach ($gradeData as $gval) {
              $gList[$gval['id']] = $gval;
          }

          // 获取上级和上上级级别
          $memList = $this->_MemMod->getList(array('uid' => array('in', array_values($uids))), 'uid,grade,gnum,total_income,total_fee,uname');
          if (!empty($memList)) {
              $upData = array();
              foreach ($memList as $mval) {
                  // 团队人数
                  $upData['gnum'] = $mval['gnum'] + 1;

                  // 订单类型处理
                  $extNum = 0;
                  switch ($orders['act_type']) {
                    case '1': // 商品订单（推广小程序）
                      if ($puid == $mval['uid']) { // 上级
                          $extNum = round($orders['order_amount'] * $gList[$mval['grade']]['xextract'], 2);
                      } else if ($ppuid == $mval['uid']) { // 上上级
                          $extNum = round($orders['order_amount'] * $gList[$mval['grade']]['pxextract'], 2);
                      }

                      // 收益方式
                      $ptype = 1;
                      break;
                    case '2': // 会员升级（推广代理）
                      if ($puid == $mval['uid']) { // 上级
                          $extNum = round($orders['order_amount'] * $gList[$mval['grade']]['aextract'], 2);
                      } else if ($ppuid == $mval['uid']) { // 上上级
                          $extNum = round($orders['order_amount'] * $gList[$mval['grade']]['paextract'], 2);
                      }

                      // 收益方式:是否会员升级
                      $ptype = 2;
                      if ($orders['is_lev'] == 1) {
                          $ptype = 3;
                      }
                      break;
                  }
                  $upData['total_income'] = $mval['total_income'] + $extNum;
                  $upData['total_fee'] = $mval['total_fee'] + $extNum;

                  // 更新用户数据
                  $this->_MemMod->saveData(array('uid' => array('eq', $mval['uid'])), $upData);

                  // 记录收益信息
                  $assets['uid'] = $mval['uid'];
                  $assets['uname'] = $mval['uname'];
                  $assets['price'] = $orders['order_amount'];
                  $assets['mtype'] = 1;
                  $assets['ptype'] = $ptype;
                  $assets['pamount'] = $extNum;
                  $assets['dateline'] = time();
                  $assets['buid'] = $orders['buid'];
                  $assets['order_id'] = $orders['order_id'];
                  $assets['total_income'] = $upData['total_income'];
                  $assets['balance'] = $upData['total_fee'];
                  $this->_MemMod->addAssetsData($assets);
              }
          }
      }
  }
}