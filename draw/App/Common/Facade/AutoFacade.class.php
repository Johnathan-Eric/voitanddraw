<?php
/*
 * 更新 门面模式
 * @autor xuan
 */
namespace Common\Facade;
class AutoFacade
{
    protected static $_error=null;
    
    public static function mainAuto(){
        self::updatePackStatus(time());
        self::updateGbStatus(time());
        self::updateGbsStatus(time());
    }

    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 套餐开关 
     * @param
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function updatePackStatus($time)
    {
        M("Package")->where("audit_status = 2 and status = 1 and start_time<= $time and end_time >= $time")->save(array("status" => 2));
        M("Package")->where("status = 2  and end_time <= ".$time)->save(array("status" => 3));
    }
 
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 团购开关 
     * @param
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function updateGbStatus($time)
    {
        M("Group_buying")->where("audit_status = 2 and status = 1 and start_time<= $time and end_time >= $time")->save(array("status" => 2));
        M("Group_buying")->where("status = 2  and end_time <= ".$time)->save(array("status" => 3));
         //根据时间开启团购
        $lists = M("Group_buying")->field('id')->where("status in(1,2) and end_time <= ".$time)->select(); //获取过期团购列表
        if($lists){
            $ids = array_column($lists, 'id');
            if(self::getOrdersIds($ids)){
               M("Group_buying")->where(array('id'=>array('in', implode(',', $ids))))->save(array("status" => 3)); 
            }
        }
        return TRUE;
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 发起拼团开关 
     * @param
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function updateGbsStatus($time)
    {
        $lists = M("Group_buying_spell")->field('id')->where("status = 1 and end_time <= ".$time)->select(); //获取过期发起拼团列表
        if($lists){
            $ids = array_column($lists, 'id');
            if(self::setSpellByGbsId($ids)){
               M("Group_buying")->where(array('id'=>array('in', implode(',', $ids))))->save(array("status" => 3)); 
            }
        }
        return TRUE;
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 根据团购数组ids关闭已发起的拼团，并更新订单支付状态为退款中，关闭订单 
     * @param gbIdArr (团购id数组)
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function getOrdersIds($idArr)
    {
        //获取未完成拼团的拼团id与订单id
        $lists = M("Group_buying_spell")
                    -> alias("gbs")
                    -> field("gbo.order_id,gbs.id")
                    ->join("left join ".C("DB_PREFIX")."group_buying_orders gbo on gbo.gbs_id = gbs.id ")
                    ->where(array('gbs.gbid'=>array('in', implode(',', $idArr)),'gbs.status'=>array('eq',1)))
                    ->select();
        //拼团id
        $gsIdArr = array_unique(array_column($lists,'id'));
        if($gsIdArr){
            //关闭发起拼团
            M("Group_buying_spell")
            ->where(array('id'=>array('in', implode(',', $gsIdArr)),'status'=>array('eq',1)))
            ->save(array("status" => 3));
        }
        //订单id
        $orderArr = array_unique(array_column($lists,'order_id'));
        if($orderArr){
            M("Orders")->where(array('order_id'=>array('in', implode(',', $orderArr)),'pay_status'=>array('eq',1)))
            ->save(array("order_status" => 4,'pay_status'=>2));
        }
        return TRUE;
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 根据团购数组ids关闭已发起的拼团，并更新订单支付状态为退款中，关闭订单 
     * @param gbIdArr (团购id数组)
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function setSpellByGbsId($idArr)
    {
        //获取未完成拼团的拼团id与订单id
        $lists = M("Group_buying_spell")
                    -> alias("gbs")
                    -> field("gbo.order_id,gbs.id")
                    ->join("left join ".C("DB_PREFIX")."group_buying_orders gbo on gbo.gbs_id = gbs.id ")
                    ->where(array('gbs.id'=>array('in', implode(',', $idArr)),'gbs.status'=>array('eq',1)))
                    ->select();
        //拼团id
        $gsIdArr = array_unique(array_column($lists,'id'));
        if($gsIdArr){
            //关闭发起拼团
            M("Group_buying_spell")
            ->where(array('id'=>array('in', implode(',', $gsIdArr)),'status'=>array('eq',1)))
            ->save(array("status" => 3));
        }
        //订单id
        $orderArr = array_unique(array_column($lists,'order_id'));
        if($orderArr){
            M("Orders")->where(array('order_id'=>array('in', implode(',', $orderArr)),'pay_status'=>array('eq',1)))
            ->save(array("order_status" => 4,'pay_status'=>2));
        }
        return TRUE;
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 优惠券 
     * @param
     * @write_time(创建时间): 2018-7-10
     *   */
    public static function updateConStatus($time)
    {
        M("Coupon")->where("status in(1,2) and end_time <= ".$time)->save(array("status" => 3));
        M("Coupon")->where("status = 1 and start_time >= ".$time)->save(array("status" => 2));
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 获取优惠券 
     * @param 
     *  type 1、注册赠券 2、购物赠券 3、立减金 4、会员赠券
     *  id 优惠券id （zy_coupon）
     *  date 用户信息
     *  oid 订单id
     * @write_time(创建时间): 2018-7-10
     **/
    public static function getCoupon($type,$uid,$oid=0,$id=0)
    {
        $where = array();
        $where['audit_status'] = array('eq',2);
        $where['status'] = array('eq',1);
        $where['ac_id'] = array('eq',$type);
        $id > 0 && $where['id'] = array('eq',$id);
        $field = "*";
        switch ($type){
            case '1':   //注册赠券
                $info = M("Coupon")->field($field)->where($where)->find();
                if(!$info){
                    return FALSE;
                }
                if(self::checkCoupon($info['id'], $uid, $info['max_num'])){
                    //优惠券领取
                    self::setCouponLog($info, $uid);
                    //修改优惠券领取记录
                    M("Coupon")->where(array('id'=>$info['id']))->save(array('send_num'=>array("exp","send_num+1"),'leave_num'=>array("exp","leave_num-1")));
                    return TRUE;
                }
                return FALSE;
                break;
            case '2': //购物赠券
                $where['leave_num'] = array('gt',0);
                $lists = M("Coupon")->field($field)->where($where)->select();
                if(!$lists || intval($oid) < 0 ){
                    return FALSE;
                }
                foreach ($lists as $v){
                    if(self::setCouponType2($v, $uid,$oid)){
                        return TRUE;
                        die();
                    }
                }
                break;
            case '3': //立减金，获取可用立减金列表
                $where['leave_num'] = array('gt',0);
                $lists = M("Coupon")->field($field)->where($where)->select();
                if(!$lists || intval($oid) < 0 ){
                    return FALSE;
                }
                $reLists = array();
                foreach ($lists as $v){
                    $info = self::setCouponType3($v, $uid,$oid);
                    if($info){
                        $reLists[] = $info;
                    }
                }
                return $reLists;
                break;
            case '4':
                return FALSE;
                break;
            default :
                $info = M("Coupon")->field($field)->where($where)->find();
                if(!$info){
                    return FALSE;
                }
                if(self::checkCoupon($id, $uid, $info['max_num'])){
                    //优惠券领取
                    self::setCouponLog($info, $uid);
                    //修改优惠券领取记录
                    M("Coupon")->where(array('id'=>$info['id']))->save(array('send_num'=>array("exp","send_num+1"),'leave_num'=>array("exp","leave_num-1")));
                    return TRUE;
                }
                return FALSE;
                break;
        }
    }
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 用户领取优惠券（购物赠券）
     * @param 
     *  $date 可用优惠券信息 （zy_coupon）
     *  uid 用户id
     *  oid 订单id
     * @write_time(创建时间): 2018-7-10
     **/
    public static function setCouponType2($date,$uid,$oid){
        if(self::checkCoupon($date['id'], $uid, $date['max_num'])){
            $finder = "ob.order_id = ".$oid;
            switch ($date['cou_type2']){
                case "2":
                    !empty($date['assign_scope2']) && $finder .= " AND ob.book_id IN (".$date['assign_scope2'].")";
                    break;
                case "3":
                    !empty($date['assign_scope2']) && $finder .= " AND b.cid IN (".$date['assign_scope2'].")";
                    break;
            }
            $order_books = M("Orders_book")->alias("ob")
                    ->join("left join ".C("DB_PREFIX")."books b on b.book_id = ob.book_id ")
                    ->field("ob.price,ob.number")
                    ->where($finder)->select();
            if(count($order_books) < 0){
                return FALSE;
            }
            if($date['price'] <= 0){
                //优惠券领取
                self::setCouponLog($date, $uid);
                //修改优惠券领取记录
                M("Coupon")->where(array('id'=>$date['id']))->save(array('send_num'=>array("exp","send_num+1")));
                return TRUE;
            } else{
                 $amount_price = 0;
                foreach ($order_books as $v){
                    $amount_price += $v['price']*$v['number'];
                }
                if($amount_price > $date['price']){
                    //优惠券领取
                    self::setCouponLog($date, $uid);
                    //修改优惠券领取记录
                    M("Coupon")->where(array('id'=>$date['id']))->save(array('send_num'=>array("exp","send_num+1"),'leave_num'=>array("exp","leave_num-1")));
                    return TRUE;
                }
            }
            return FALSE; 
        } else {
            return FALSE;
        }
    }

    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 用户领取优惠券（购物赠券）
     * @param 
     *  $date 可用优惠券信息 （zy_coupon）
     *  uid 用户id
     *  oid 订单id
     * @write_time(创建时间): 2018-7-10
     **/
    public static function setCouponType3($date,$uid,$oid){
        if(self::checkCoupon($date['id'], $uid, $date['max_num'])){
            $finder = "ob.order_id = ".$oid;
            switch ($date['cou_type']){
                case "2":
                    !empty($date['assign_scope']) && $finder .= " AND ob.book_id IN (".$date['assign_scope'].")";
                    break;
                case "3":
                    !empty($date['assign_scope']) && $finder .= " AND b.cid IN (".$date['assign_scope'].")";
                    break;
            }
            $order_books = M("Orders_book")->alias("ob")
                    ->join("left join ".C("DB_PREFIX")."books b on b.book_id = ob.book_id ")
                    ->field("ob.price,ob.number")
                    ->where($finder)->select();
            if(count($order_books) < 0){
                return FALSE;
            }
            if($date['amount'] <= 0){
                return $date;
            } else{
                $amount_price = 0;
                foreach ($order_books as $v){
                    $amount_price += $v['price']*$v['number'];
                }
                if($amount_price > $date['amount']){
                    return $date;
                }
            }
            return FALSE; 
        } else {
            return FALSE;
        }
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 检测用户优惠券领取条件
     * @param 
     *  id 优惠券id （zy_coupon）
     *  uid 用户id
     *  max_num 单个用户最大领取数量
     * @write_time(创建时间): 2018-7-10
     **/
    public static function checkCoupon($id,$uid,$max_num){
        $count = M("Coupon_log")->where(array('uid'=>$uid,'cid'=>$id))->count();
        if($max_num > $count){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 优惠券领取记录
     * @param 
     *  date 优惠券信息数组
     *  uid 用户id
     * @write_time(创建时间): 2018-7-10
     **/
    
    public static function setCouponLog($date,$uid)
    {
        $da = array(
            'uid' => $uid,
            'cid' => $date['id'],
            'cname' => $date['name'],
            'remark' => $date['desc'],
            'get_type' => $date['ac_id'],
            'gtime' => time(),
            'ds_id' => $date['ds_id']
        );
        $id = M('coupon_log')->add($da);
        if($id){
            $data = array(
                'uid' => $uid,
                'logid' => $id,
                'cid' => $date['id'],
                'cname' => $date['name'],
                'amount' => $date['amount'],
                'status' => 0,
                'is_del' => 0,
                'get_time' => time(),
                'ds_id' => $date['ds_id']
            );
            if($date['c_type'] == 1){
                $data['money'] = $date['c_min'];
            } else {
                $data['money'] = rand($date['c_min'],$date['c_max']);
            }
            if($date['period'] == 1){
                $data['sdate'] = $date['period_start'];
                $data['edate'] = $date['period_end'];
            } else {
                $data['sdate'] = time();
                $data['edate'] = strtotime(" +{$date['period_day']} day ");
            }
            M('Member_coupon')->add($data); //添加用户优惠券
        }
        return TRUE;
    }
    
     /**
     * @version information(版本信息): v1.0
     * @author(作者): xuan
     * @deprecated(简要说明): 优惠券使用
     * @param 
     *  date 优惠券信息数组
     *  uid 用户id
     *  mc_id 用户领取的优惠券id（zy_member_coupon的ID）
     * @write_time(创建时间): 2018-7-16
     **/
    
    public static function useCoupon($uid,$mc_id)
    {
        $where = array(
            'uid'=>$uid,
            'mc_id'=>$mc_id,
            'is_del'=>0
        );
        $info = M('Member_coupon')->where($where)->find(); 
        if($info){
            $data = array(
                'use_time' => time(),
                'status' => 2
            );
            M('Member_coupon')->where($where)->save($data); //添加用户优惠券
            M('Coupon')->where(array('id'=>$info['cid']))->save(array('used_num'=>array('exp','used_num+1')));
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}
?>