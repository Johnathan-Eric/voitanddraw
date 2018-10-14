<?php
/**
 * 订单处理逻辑模块
 * @author xiaoxiao
 */
namespace Admin\Logic;

use Admin\Action\CouponAction;
use Think;
class OrdersLogic
{

    protected $model,$pagesize;

	protected $tableName = 'Orders'; //数据表名
	private $_Code = array('success'=>200,'empty'=>401,'fail'=>402,'error'=>403);

    /**
     * 读取会员列表
     * 
     * @author eva
     * @param array $where
     *            查询条件
     * @param int $page            
     * @param int $listRows            
     * @param int $count            
     * @return array $list 订单列表
     */
    function get_list($where = array(), $page = 1, $listRows = 10, &$count = 0, $link = array())
    {
        $map = array();
        $member = session('member');
        $member->uid > 0 && $map['uid'] = $member->uid;
        $model = D("Common/orders");
        // 条件组合
        isset($where['pay_status']) && $map['pay_status'] = intval($where['pay_status']);
        isset($where['order_status']) && $map['order_status'] = array(
            "in",
            $where['order_status']
        );
        
        $order = 'createtime desc';
        $where['createtime'] && $order = $where['createtime'];
        // 数据统计
        $count = $model->where($map)->count();
        is_array($link) && ! empty($link) && $model->relation($link);
        $model->where($map)->page("{$page}, {$listRows}");
        $list = $model->order($order)->select();
        return $list;
    }

    /**
     * 读取用户订单信息
     * 
     * @access public
     * @param int $order_id            
     * @param string $order_sn            
     * @param array $link
     *            orders_goods:订单商品;
     * @return array
     *
     */
    static function get_orders_info($order_id = 0, $order_sn = "", $link = array(), $uid = null)
    {
        if ($order_id == 0 && $order_sn == "") {
            self::$_error = "参数有误";
            return false;
        }
        $map = array();
        $order_id > 0 && $map['order_id'] = $order_id;
        ! empty($order_sn) && $map['order_sn'] = $order_sn;
        if (! is_null($uid))
            $map['uid'] = $uid;
        $model = D("Common/Orders");
        $model->where($map);
        if (! empty($link) && is_array($link))
            $model->relation($link);
        $info = $model->find();
        return $info;
    }
    
    function get_orders_goods($order_id = 0)
    {
        if ($order_id == 0 && $order_sn == "") {
            self::$_error = "参数有误";
            return false;
        }
        $map = array();
        $order_id > 0 && $map['o.order_id'] = $order_id;
        $model = M("OrdersBook");
        $model-> alias("o")
            -> field("o.*")
            ->where($map);
        $lists = $model->select();
        return $lists;
    }
    /**
     * 读取订单配送单信息
     * @author wscsky
     */
    function get_order_shipping_by_order_id($order_id = 0)
    {
        $map = array(
            'a.status' => array('neq',10)
        );
        if ($order_id > 0) $map['a.order_id|b.order_id'] = $order_id;
        $data = M("orders_shipping")->alias('a')
            ->join('left join zy_orders b on b.order_id = a.order_id')
            ->field("a.*")
            ->where($map)
            ->find();
        return $data;
    }

    /**
     * 读取单次配送单信息
     * @author wscsky
     */
    function get_order_shipping($id, $uid = null, $is_sn = false)
    {
        $member = session('member');
        if (is_null($uid)) {
            $uid = $member->uid;
        }
        if($is_sn)
            $map = array("a.sn" =>$id);
        else
            $map = array("a.id" => $id);
        if ($uid > 0) $map['a.uid|b.uid'] = $uid;
        $data = M("orders_shipping")->alias('a')
            ->join('left join zy_orders b on b.order_id = a.order_id')
            ->field("a.*")
            ->where($map)
            ->find();
        return $data;
    }

    /**
     * 订单状态改变
     * 
     * @author watchman
     * @param array $data
     *            订单数据
     * @param int $new_status
     *            改变之后状态
     * @param bool $startTrans
     *            是否开启事务
     * @return boolean
     */
    /*
    static function orders_status_change($data, $new_status, $startTrans = true)
    {
        if (self::status_change_check($data['order_status'], $new_status, $data)) {
            $orders_model = D("Orders");
            $startTrans && $orders_model->startTrans();
            $sdata = array('order_status' => $new_status);
            switch ($new_status){
                case ORDERS_STATUS_CANCEL:
                    if($data['order_type'] == 2 || $data['order_type'] == 3){
                        if(in_array($data['pay_status'],array(1,3))){
                            $sdata['group_status'] = 4; //团购失败
                        }else{
                            $sdata['group_status'] = 10; //取消团购
                        }
                    }
                    break;
            }
            $result = D("Common/Orders")->where('order_id = %d', $data['order_id'])->save($sdata);
            $result !== false && $result = true;
            $p_model = D("Common/Finance", "Logic");
            // 如果是退货或者取消退还余额或积分
            if ($result && ($new_status == ORDERS_STATUS_CANCEL || $new_status == ORDERS_STATUS_RETURNFINISH)) {
                $log = array(
                    'entity_type' => ENTITY_TYPE_ORDER,
                    'entity_id' => $data['order_id'],
                    'remark' => (($new_status == ORDERS_STATUS_CANCEL) ? "取消订单" : "订单退货") . ",编号{$data['order_sn']}"
                );
                $return = true;
                if(in_array($data['order_type'],array(2,3)) && $data['group_status'] == 3) $return = false; //团购成功不退
                //退余额
                if ($return && $data['balance'] > 0) {
                    $log['type'] = BALANCE_TYPE_PAYORDER;
                    $result = $p_model->change_balance($data['balance'], MODE_TYPE_INC, $data['uid'], $log);
                }
                //退积分
                if ($return && $result && $data['integral'] > 0) {
                    $log['type'] = INTEGRAL_TYPE_ORDER;
                    $result = $p_model->change_integral($data['integral'], MODE_TYPE_INC, $data['uid'], $log);
                }
                //团购退在线支付[支付和部分支付退 cash]
                if($return && in_array($data['order_type'],array(2,3)) && $data['cash'] > 0 && in_array($data['pay_status'],array(1,3))){
                    self::group_wxpay_refund($data);
                }
                //还原库存
                $goods = M("orders_goods")->where("order_id = %d", $data['order_id'])->select();
                foreach ($goods as $dd){
                    if($dd['spec_id'] > 0){
                        M("goods_spec")->where("spec_id = %s", $dd['spec_id'])->setInc("stock", $dd['number']);
                    }else{
                        M("goods")->where("goods_id = %s", $dd['goods_id'])->setInc("stock", $dd['number']);
                    }
                }
                //还原兑换券
                if($return && $data['order_type'] == 1 && $data['tcode']){
                    $tcode = M("tcode") -> where("code = '%s' and status = 1", $data['tcode'])->find();
                    if($tcode['edate'] > time()){
                        M("tcode") -> where("id = %d", $tcode['id'])->save(array("use_time"=>null,"status"=>0));
                    }
                }
            }
            
            // 操作制作返回flase
            if (! $result) {
                $startTrans && $orders_model->rollback();
                self::$_error = "订单状态改变失败！";
                return false;
            }
            
            // 订单成功支付start
            if ($new_status == ORDERS_STATUS_PAY) {
                // 给用户增加成长值
                $rank_integral = $data['order_amount'] - $data['discount'] - $data['integral_money'];
                if ($rank_integral > 0) {
                    $log = array(
                        'type' => INTEGRAL_TYPE_ORDER,
                        'entity_type' => ENTITY_TYPE_ORDER,
                        'entity_id' => $data['order_id'],
                        'remark' => "订单编号{$data['order_sn']}"
                    );
                    $p_model->change_rank_integral($rank_integral, MODE_TYPE_INC, $data['uid'], $log);
                }
                // 给自己增加积分
                $integral = floor(($data['order_amount'] - $data['discount'] - $data['integral_money']) * C('integral_ratio'));
                $max_int = C("max_order_integral", null, 0);
                if ($max_int > 0 && $integral > $max_int)
                    $integral = $max_int;
                if ($integral > 0) {
                    $log = array(
                        'type' => INTEGRAL_TYPE_ORDER,
                        'entity_type' => ENTITY_TYPE_ORDER,
                        'entity_id' => $data['order_id'],
                        'remark' => "订单编号{$data['order_sn']}返积分"
                    );
                    $p_model->change_integral($integral, MODE_TYPE_INC, $data['uid'], $log);
                }
                // 处理用户VIP
                // D("Common/Member","Logic") -> check_member_vip($data['uid']);
                
                // 处理用户等级
                // D("Common/Member","Logic") -> check_member_group($data['uid']);
                
                // 计算分佣基数
                switch (C("order_profit_type", null, 0)) {
                    case 1:
                        $amount = $data['goods_amount'];
                        break;
                    case 2:
                        $amount = $data['order_amount'] - $data['discount'];
                        break;
                    default:
                        $amount = 0;
                        break;
                }
                $puid = D("Common/Member", "Logic")->get_member_field($data['uid'], "puid");
                if ($puid > 0 && $amount > 0) {
                    $log = array(
                        'entity_id' => $data['order_id'],
                        'entity_type' => ENTITY_TYPE_ORDER,
                        'entity_remark' => "已付款待发货",
                        'remark' => "订单{$data['order_sn']}佣金分成",
                        'order_sn' => $data['order_sn'],
                        'order_amount' => $data['goods_amount']
                    );
                    if(in_array($data['order_type'],C('profit_order_types'))) D('Common/Profit', 'Logic')->settle_order_profit($puid, $amount, $log);
                }
                // 计算推荐者提成
                if(in_array($data['order_type'],C('profit_order_types'))) self::create_recome_profit($data, $amount);
                
                // 虚拟商品进行发货操作
                // self::auto_ship($data['order_id'], false);
                $first = "亲，您的订单支付成功了。";
                $remark = '如有疑问，请拨打客服电话：' . C('service_tel');
                $msgmb = 'm_payorder_notice';
                if($data['order_type'] == 2){
                    if($order_info['order_pid'] > 0){
                        $first = "恭喜，您已参团成功";
                    }else{
                        $first = "恭喜，您已开团成功";
                    }
                    $remark = '付款成功分享给您的朋友，可以早点完成团购噢';
                    $msgmb = "m_group_open";
                }
                if($data['order_type'] == 3){
                    if($data['pay_status'] == 3){
                        if($order_info['order_pid'] > 0){
                            $first = "恭喜，您已参团成功";
                        }else{
                            $first = "恭喜，您已开团成功";
                        }
                        $remark = '付款成功分享给您的朋友，可以早点完成团购噢';
                        $msgmb = "m_group_ok";
                    }else{
                        $first = "团购订单补款成功";
                        $msgmb = "m_group_ok";
                    }
                }
                // 给用户发送支付成功通知
                $tmp_data = array(
                    'url'          => U('/orders/info' . WAPDM, 'id=' . $data['order_id'], true, true),
                    'topcolor'     => '#FF0000',
                    'first'        => $first,
                    'pay_time'     => date("Y-m-d H:i", $data['pay_time']),
                    'order_sn'     => $data['order_sn'],
                    'goods_name'   => $data['goods_name'],
                    'money'        => ($data['order_amount'] - $data['discount']) . '元',
                    'payment_name' => $data['payment_name'],
                    'order_amount'	=> $data['order_amount'] - $data['discount'].'元',
                    'remark'        => $remark,
                );
                @D("Common/Message", "Logic")->send_wx_message($data['uid'], $tmp_data, $msgmb);
                // 送优惠券
                @D("Common/Coupon", "Logic")->order_pay_back($data);
            }
            
            // 订单发货操作后
            if ($new_status == ORDERS_STATUS_SHIPPED) {
                // 虚拟商品进行发货操作
                // self::auto_ship($data['order_id'], false);
                
//                 // 给用户发微信通知
//                 $tmp_data = array(
//                     'url' => U('/orders/info' . WAPDM, 'id=' . $data['order_id'], true, true),
//                     'topcolor' => '#FF0000',
//                     'first' => "亲，您的订单已发货了。",
//                     'order_sn' => $data['order_sn'],
//                     'shipping_name' => $data['shipping_name'],
//                     'shipping_no' => $data['shipping_no'],
//                     'remark' => '如有疑问，请拨打客服电话：' . C('service_tel')
//                 );
//                 @D("Common/Message", "Logic")->send_wx_message($data['uid'], $tmp_data, 'm_order_shipped');
            }
            
            // 订单完成时执行的操作
            if ($new_status == ORDERS_STATUS_FINISH) {
                // 设置未生效为未结算
                M('profit_log')->where("entity_id = %d and entity_type = %d and status = 3", array(
                    $data['order_id'],
                    ENTITY_TYPE_ORDER
                ))->setField('status', PROFIT_STATUS_UNCONFIRMED);
            }
            
            $entity_remark = "";
            switch ($new_status) {
                case ORDERS_STATUS_CANCEL:
                    $entity_remark = "订单已取消";
                    break;
                case ORDERS_STATUS_UNPAY:
                    $entity_remark = "订单待付款";
                    break;
                case ORDERS_STATUS_PAY:
                    $entity_remark = "已付款待发货";
                    break;
                case ORDERS_STATUS_SHIPPED:
                    $entity_remark = "已发货待收货";
                    break;
                case ORDERS_STATUS_GET:
                    $entity_remark = "已收货待财务审核";
                    break;
                case ORDERS_STATUS_FINISH:
                    $entity_remark = "订单已完成";
                    break;
                case ORDERS_STATUS_APPLY:
                    $entity_remark = "用户申请退货";
                    break;
                case ORDERS_STATUS_RETURN:
                    $entity_remark = "订单退货中";
                    break;
                case ORDERS_STATUS_RETURNFINISH:
                    $entity_remark = "订单退货完成";
                    break;
            }
            // 更新收益订单状态
            if ($entity_remark) {
                M("profit_log")->where("entity_type = %d and entity_id = %d", array(
                    ENTITY_TYPE_ORDER,
                    $data['order_id']
                ))->save(array(
                    "entity_remark" => $entity_remark
                ));
            }
            $startTrans && $orders_model->commit();
            return true;
        } else {
            return false;
        }
    }
    * 
    */

    /**
     * 检查订单新旧状态切换的合法性
     * 
     * @param int   $old_status 改变之前状态
     * @param int   $new_status 改变之后状态
     * @param array $data 订单信息
     * @return booleanean
     */
    /*
    static function status_change_check($old_status, $new_status, $data = array())
    {
        //团购订单处理
        if(in_array($data['order_type'],array(2,3))){
            if($data['group_etime'] > time() 
                && in_array($new_status,array(ORDERS_STATUS_CANCEL))
                && in_array($data['group_status'],array(1,2))
                ){
                self::$_error = "团购中的订单不可以取消";
                return false;
            }
            if($data['group_status'] == 3 && $data['pay_status'] == 1 &&  $new_status == ORDERS_STATUS_CANCEL){
                self::$_error = "团购成功的订单不可以取消";
                return false;
            }
            if($data['group_etime'] < time() && in_array($data['group_status'],array(1,2,3))){
                return true;
            }
        }
        $status = array_keys(self::get_orders_status());
        if (!in_array($old_status, $status) || ! in_array($new_status, $status)) {
            self::$_error = "订单状态参数有误！";
            return false;
        }
        // 新旧状态不能相等
        if ($old_status == $new_status) {
            self::$_error = "订单状态参数有误！";
            return false;
        }
        // 订单状态不能变为"未付款"
        if ($new_status == ORDERS_STATUS_UNPAY) {
            self::$_error = "订单状态不能改为未付款！";
            return false;
        }
        // 订单状态不能变为"未付款"
        if ($new_status == ORDERS_STATUS_GET && $old_status != ORDERS_STATUS_SHIPPED) {
            self::$_error = "已发货订单才能改为收货状态！";
            return false;
        }
        // "已完成"、"已退货完成"、"已取消"状态下订单不允许再改变
        if (in_array($old_status, array( ORDERS_STATUS_FINISH, ORDERS_STATUS_RETURNFINISH, ORDERS_STATUS_CANCEL))) {
            self::$_error = "订单已完成或取消！";
            return false;
        }
        
        // "未付款"状态下的订单只能"取消"或"付款"
        if ($old_status == ORDERS_STATUS_UNPAY) {
            if ($new_status != ORDERS_STATUS_PAY && $new_status != ORDERS_STATUS_CANCEL) {
                self::$_error = "未付款订单只能取消或付款！";
                return false;
            }
        }
        // 只有"未付款"状态下的订单才能取消
        if ($new_status == ORDERS_STATUS_CANCEL && $old_status != ORDERS_STATUS_UNPAY) {
            self::$_error = "只有未付款状态下的订单才能取消！";
            return false;
        }
        
        // "已收货"状态下的订单才能"申请退货"
        if ($new_status == ORDERS_STATUS_APPLY && $old_status != ORDERS_STATUS_GET) {
            self::$_error = "已收货订单才能申请退货！";
            return false;
        }
        
        if (in_array($new_status, array( ORDERS_STATUS_RETURN, ORDERS_STATUS_RETURNFINISH ))){
            if ($old_status < ORDERS_STATUS_APPLY) {
                self::$_error = "订单状态参数有误！";
                return false;
            }
        } else {
            if ($old_status > $new_status) {
                self::$_error = "订单状态参数有误！";
                return false;
            }
        }
        return true;
    }

    */
    
    /**
     * 获取订单状态
     * 
     * @param string $get_name            
     */
    static function get_orders_status()
    {
        $status = array(
            ORDERS_STATUS_UNPAY => '待付款',
            ORDERS_STATUS_PAY => '已付款',
            ORDERS_STATUS_SHIPPING => '配送中',
            ORDERS_STATUS_SHIPPED => '配送完成',
            ORDERS_STATUS_FINISH => '服务完成',
            ORDERS_STATUS_CANCEL => '已取消'
        );
        return $status;
    }
    
    /**
     * 获取团购单状态
     *
     * @param string $get_name
     */
    static function get_group_status()
    {
        $status = array(
            '待付款','开团成功','开团成功','团购成功','团购失败','已取消'
        );
        return $status;
    }

    /**
     * 配送单状态
     * 
     * @author wscsky
     */
    function get_shipping_status()
    {
        return array(
            0 => '待确认',
            1 => '待配送',
            2 => '已配送',
            3 => '已完成',
            10 => "已取消"
        );
    }

    /**
     * 读用户今天购买的数量
     * 
     * @param int $goods_id
     *            商品ID
     * @param int $uid
     *            用户ID
     * @return int
     * @author watchman
     */
    function get_today_num($goods_id, $uid)
    {
        $member = session('member');
        $uid = $uid > 0 ? $uid : $member->uid;
        $map['o.createtime'] = array(
            "gt",
            strtotime(date("Y-m-d 0:0:0", time()))
        );
        $map['o.uid'] = $uid;
        $map['og.goods_id'] = $goods_id;
        
        $model = M("orders");
        $num = $model->alias("o")
            ->join("left join " . C('DB_PREFIX') . "orders_goods og on og.order_id = o.order_id")
            ->where($map)
            ->getField("sum(og.number) as num");
        return (int) $num;
    }

    /**
     * 保存订的支付信息
     * 
     * @author wscsky
     */
    function save_pay_data($trade_info, $order_id)
    {
        $model = M("orders");
        $data = array(
            'pay_data' => json_encode($trade_info),
            'pay_created' => time()
        );
        $model->where("order_id = %d", $order_id)->save($data);
    }

    /**
     * 生成配送单
     * 
     * @param int $order_id
     *            订单ID
     * @param
     *            bool 是否强制生成
     * @author wscsky
     */
    function create_shipping($order_id, $force_create = false)
    {
        $model = M("orders");
        $data = self::get_orders_info($order_id, '', array(
            'ordersgoods',
            'ordershipping'
        ));
        if ($data['gtype'] != 1) {
            self::$_error = "该订单不是多次配送单";
            return false;
        }
        //团购订单
        if(in_array($data['order_type'],array(2,3)) && $data['group_status'] != 3){
            self::$_error = "非团购成功订单";
            return false;  //团购不成功不生成
        }
        if($data['order_type'] == 3 && $data['pay_status'] != 1){
            self::$_error = "请先补齐团购款";
            return false;  //团购不成功不生成
        }
        if (!in_array($data['order_status'], array(1,2))) {
            self::$_error = "订单状态不在服务中";
            return false;
        }
        $tday   = floor(($data['getdate'] - time()) / 86400);
        $thours = floor(($data['getdate'] - time()) / 3600);
        if (!$force_create && $tday > C('create_shipping_day', null, 7)) return false;
        $sdata = array();
        $smodel = M("orders_shipping");
        $shipping_num = count($data['ordershipping']);
        if($shipping_num < 1) $shipping_num = 0;
        if ($shipping_num >= $data['shiped_total']) {
            self::$_error = "该订单的配送单已生成完" . $data['shipped_total'];
            return false;
        }
        $shipping_num ++;
        //首次如果时间太近则自动调整到下一个周期
        if($shipping_num == 1){
            if($thours < 24) $data['getdate'] +=  ceil(abs($thours/24/7)) * 604800;
        }
        $ttime = floor((strtotime(date('Y-m-d', $data['getdate'])) - time()) / 3600);
        $sdata = array(
            'status'    => $ttime <= 30 ? 1 : 0, // 30小时内不可修改
            'order_id'  => $order_id,
            'sn'        => $data['order_sn'] . "_" . $shipping_num,
            'no'        => $shipping_num,
            'tasktime'  => $data['getdate'],
            'add_id'    => $data['add_id'],
            'address'   => $data['address'],
            'region_name' => $data['region_name'],
            'name'      => $data['name'],
            'mobile'    => $data['mobile']
        );
        // 首次增加套餐的商品
        if ($shipping_num == 1) {
            foreach ($data['ordersgoods'] as $key => $dd) {
                if ($key == 0)
                    continue;
                $sdata['meals'] .= $dd['spec_name'] . ";"; // "×".$dd['number'].";";
            }
        }
        $result = $smodel->add($sdata);
        if ($result && $shipping_num < $data['shiped_total']) {
            $model->where("order_id = %d", $data['order_id'])->save(array(
                'order_status' => 2,
                'getdate' => strtotime("+{$data['period']} day", $data['getdate'])
            ));
            return $force_create ? true : self::create_shipping($order_id);
        }
        return $result;
    }
    
    /**
     * 检查团购定单状态
     * @param number $order_id
     */
    function check_groups($order_id = 0){
        if(!$order_id || !$odata = self::get_orders_info($order_id)) return ;
        if(!in_array($odata['order_type'],array(2,3))) return false;
        if($odata['order_pid'] > 0){
            $pdata = self::get_orders_info($odata['order_pid']);
        }else{
            $pdata = $odata;
        }
        
        $model = M("orders");
        //普通团处理
        if($pdata['order_type'] == 2){
            $groups = get_groups_data($pdata['order_id']);
            $isok   = (count($groups) >= $pdata['group_num']) ? true:false;
            $goods_info = M("goods")->find($pdata['goods_id']);
            //更新主团人数
            $map = array("order_id|order_pid"=>$pdata['order_id']);
            $model -> where($map) -> setField("group_users",(int)count($groups));
            if($isok){
                //更新已付款的团购单为团购成功
                $map = array("order_id|order_pid" => $pdata['order_id'],'group_status'=> 2);
                $model -> where($map)->save(array("group_status"=>3,'group_otime'=>time()));
                //取消未付款的订单
                $map = array("order_pid" => $pdata['order_id'],'group_status'=> 0);
                $datas = $model->where($map)->select();
                foreach ($datas as $dd){
                    self::orders_status_change($dd, ORDERS_STATUS_CANCEL);  
                    if($dd['notice'] > 0) continue;
                    $model->where("order_id = %d", $dd['order_id'])->save(array('notice'=> 1));
                    $tmp_data = array(
                        'url'			=> U('/orders/info', 'id='.$dd['order_id'], true,true),
                        'topcolor'		=> '#173177',
                        'first' 		=> "您好！您参考的团购商品拼团失败！",
                        'goods_name'    => $dd['goods_name'],
                        'group_time'    => $goods_info['group_time']."小时",
                        'group_cash'    => $goods_info['group_price']."元",
                        'group_rmark'   => '团购时间结束，您没有及时付款或人数未到开团要求系统取消',
                        'remark'		=> '如您有问题请联系客服,客服电话：'.C('service_tel')
                    );
                    D("Common/Message","Logic")->send_wx_message($dd['uid'], $tmp_data, 'm_group_fail');
                }
                return;
            }
            //如果到时未完成取消全部
            if(!$isok && $pdata['group_etime'] < time()){
                $map = array("order_id|order_pid" => $pdata['order_id']);
                $datas = $model->where($map)->select();
                foreach ($datas as $dd){
                    self::orders_status_change($dd, ORDERS_STATUS_CANCEL);
                    if($dd['notice'] > 0) continue;
                    $model->where("order_id = %d", $dd['order_id'])->save(array('notice'=> 1));
                    $tmp_data = array(
                        'url'			=> U('/orders/info', 'id='.$dd['order_id'], true,true),
                        'topcolor'		=> '#173177',
                        'first' 		=> "您好！您参考的团购商品拼团失败！",
                        'goods_name'    => $dd['goods_name'],
                        'group_time'    => $goods_info['group_time']."小时",
                        'group_cash'    => $goods_info['group_price']."元",
                        'group_rmark'   => '团购时间结束，您没有及时付款或人数未到开团要求系统取消',
                        'remark'		=> '如您有问题请联系客服,客服电话：'.C('service_tel')
                    );
                    D("Common/Message","Logic")->send_wx_message($dd['uid'], $tmp_data, 'm_group_fail');
                }
                return;
            }
        }
        //阶梯团处理
        if($pdata['order_type'] == 3){
            $groups      = get_groups_data($pdata['order_id']);
            $group_users = (int)count($groups);
            //处理商品的价格
            $gmap = array(
                'goods_id' => $pdata['goods_id'],
                'times'    => array('elt', $group_users),
                'online'   => 1,
            );
            $goods_info = M("goods")->find($pdata['goods_id']);
            $spec_data  = M("goods_spec")->where($gmap)->order("price")->find();
            $gmap = array(
                'goods_id' => $pdata['goods_id'],
                'online'   => 1,
            );
            $sdata     = array('group_users'=>$group_users);
            if($spec_data){
                $sdata['goods_amount'] = $spec_data['price'];
                $sdata['order_amount'] = array("exp",$spec_data['price'] .'+card_fee+shipping_fee');
            }
            $model -> where(array(
                            "order_id|order_pid" => $pdata['order_id'], 
                            'group_status'=> array("in","0,1,2"))) 
                   -> save($sdata);
            //最大人数
            $max_group_num = M("goods_spec")->where("goods_id = %d and online = 1",$pdata['goods_id'])->max("times");
            $isok = false; $isend =  false;
            if($max_group_num > 0 && $max_group_num <= $group_users) $isok = true;
            if($pdata['group_etime'] < time()) $isend = true;
            if($isend || $isok){
                $sdata['group_otime'] = time();
                $sdata['group_status'] = 3;
                $map   = array("order_id|order_pid" => $pdata['order_id'],'pay_status'=>array("in",array(1,3)));
                $odatas =  $model -> where($map)->select();
                $model  -> where($map) -> save($sdata);
                //发送成功通知
                $goods_info = M("goods")->find($pdata['goods_id']);
                foreach ($odatas as $dd){
                    $dd = self::get_orders_info($dd['order_id']);
                    if($dd['notice'] > 0) continue;
                    $model -> where("order_id = %d", $dd['order_id']) -> save(array('notice'=> 1));
                    if($dd['order_amount'] <= ($dd['cash']+$dd['balance']+$dd['integral_money']+$dd['discount'])){
                        continue; //付完全款不发送
                        $remark = "我们会尽快为您发安排发货,客服电话：".C('service_tel');
                    }else{
                        $remark = "请及时支付团购余款，我们会尽快为您发安排发货,客服电话：".C('service_tel');
                    }
                    $tmp_data = array(
                        'url'			=> U('/orders/info', 'id='.$dd['order_id'], true,true),
                        'topcolor'		=> '#173177',
                        'first' 		=> "您参与的阶梯团成功了,点击查看您的订单。",
                        'goods_name'    => $dd['goods_name'],
                        'group_users'   => $dd['group_users']."人",
                        'group_otime'   => date("Y-m-d H:i:s",$dd['group_otime']),
                        'order_time'	=> date("Y-m-d H:i"),
                        'order_sn'		=> $dd['order_sn'],
                        'order_amount'	=> $dd['goods_amount']+$dd['shipping_fee']+$dd['card_fee']-$dd['cash']-$dd['balance'],
                        'discount'		=> $goods_info['price']-$dd['goods_amount'].'元',
                        'remark'		=> $remark,
                    );
                    D("Common/Message","Logic")->send_wx_message($dd['uid'], $tmp_data, 'm_group_ok');
                }
                //不成团购操作
                if($isend){
                    $map = array(
                        "order_pid" => $pdata['order_id'],
                        "group_status" => array("in",0),
                    );
                    $datas = $model->where($map)->select();
                    foreach ($datas as $dd){
                        self::orders_status_change($dd, ORDERS_STATUS_CANCEL);
                        if($dd['notice'] > 0) continue;
                        $model->where("order_id = %d", $dd['order_id'])->save(array('notice'=> 1));
                        $tmp_data = array(
                            'url'			=> U('/orders/info', 'id='.$dd['order_id'], true,true),
                            'topcolor'		=> '#173177',
                            'first' 		=> "您好！您参考的团购商品拼团失败！",
                            'goods_name'    => $dd['goods_name'],
                            'group_time'    => $goods_info['group_time']."小时",
                            'group_cash'    => $goods_info['group_price']."元",
                            'group_rmark'   => '团购时间结束，您没有及时付款或人数未到开团要求系统取消',
                            'remark'		=> '如您有问题请联系客服,客服电话：'.C('service_tel')
                        );
                        D("Common/Message","Logic")->send_wx_message($dd['uid'], $tmp_data, 'm_group_fail');
                    }
                }
            }
        }
    }

    /**
     * 在线支付回调函数
     * @param array $trade_info 交易信息
     * @return bool;
     */
    static function pay_back($trade_info = array())
    {
        if (empty($trade_info) || $trade_info['status'] != 1 || $trade_info['entity_type'] != ENTITY_TYPE_ORDER)
            return false;
        $data = self::get_orders_info($trade_info['entity_id']);
        // 更新支付状态和支付方式
        $model = M("orders");
        $sdata = array(
            "pay_status"    => ORDERS_PAY_APPLY,
            'payment_name'  => ($data['payment_name'] ? $data['payment_name'] . '+' : '') . $trade_info['payment_name'],
            'payment_id'    => $trade_info['payment_id'],
            'trade_id'      => $trade_info['trade_id'],
            'pay_time'      => time(),
            'pay_remark'    => '交易编号' . $trade_info['out_trade_sn']
        );
        if($data['order_type'] != 3){  //非阶梯团时
            if ($data['pay_status'] != ORDERS_PAY_UNAPPLY) {
                return false;
            }
            if ($data['cash'] != $trade_info['order_money']) {
                \Think\Log::log("order_pay_bak", "订单支付回调，金额出错！", array(
                    'data' => $data,
                    "trade_info" => $trade_info
                ));
                return false;
            }
            if($data['order_type'] == 2){
                $sdata['group_status'] = 2;
            }
        }else{
            if($data['group_status'] != 3 ){
                if ($data['cash'] != $trade_info['order_money']) {
                    \Think\Log::log("order_pay_bak", "订单支付回调，金额出错！", array(
                        'data' => $data,
                        "trade_info" => $trade_info
                    ));
                    return false;
                }
                if($data['cash'] == ($data['order_amount'] - $data['discount'] - $data['balance'] - $data['integral_money'])){
                    $sdata['group_status'] = 3;
                    $sdata['pay_status']   = ORDERS_PAY_APPLY;
                }else{
                    $sdata['group_status'] = 1;
                    $sdata['pay_status']   = ORDERS_PAY_PART;
                }
            }else{
                $sdata['group_status'] = $data['group_status'] < 2 ? 2:$data['group_status'];
                $sdata['cash'] = $data['cash'] + $trade_info['order_money'];
            }
        }
        // 如果是主团订单重新计算团购结束时间
        if(in_array($data['order_type'], array(2,3)) && $data['order_pid'] == 0){
            $goods_info = M('goods')->find($data['goods_id']);
            if($goods_info){
                $sdata['group_etime'] = strtotime("+{$goods_info['group_time']}hour");
            }
        }
        $model->where("order_id = %d", $trade_info['entity_id'])->save($sdata);
        // 读更新后的数据
        $data = self::get_orders_info($trade_info['entity_id']);
        self::orders_status_change($data, ORDERS_STATUS_PAY, false);
        self::check_groups($data['order_id']);
        self::create_shipping($data['order_id']);
    }

    /**
     * 生成用户商品推荐提成
     * 
     * @param array $data     订单信息
     * @param number $amount  利润基数
     * @author wscsky
     */
    private function create_recome_profit($data = array(), $amount = 0)
    {
        $uinfo = D("Common/Member", "Logic")->get_member_field($data['uid'], "*");
        if ($data['puid'] == 0 || $data['puid'] == $data['uid'] || $uinfo['puid'] == $data['puid']) return;
        $profit_ratio = C('level_0_percent', null, 0);
        if ($profit_ratio <= 0 || $amount <= 0) return;
        $profit_money = round($amount * $profit_ratio / 100, 2);
        $uinfo = D("Common/Member", "Logic")->get_member_field($data['puid'], "*");
        
        if ($profit_money > 0) {
            $log_data = array(
                'uid' => $uinfo['uid'],
                'uname' => $uinfo['uname'],
                'status' => PROFIT_STATUS_UNTAKE,
                'type' => PROFIT_TYPE_REF,
                'entity_id' => $data['order_id'],
                'order_sn' => $data['order_sn'],
                'level' => 0,
                'entity_type' => ENTITY_TYPE_ORDER,
                'entity_remark' => "付款待发货",
                'money' => $profit_money,
                'remark' => "订单{$data['order_sn']}推荐购买佣金提成",
                'log_day' => date('Ymd'),
                'log_month' => date('Ym'),
                'log_time' => time()
            );
            
            $resutl = M('profit_log')->add($log_data);
            if ($resutl && C('send_profit_notice')) {
                // 给用户发送收益推荐
                $tmp_data = array(
                    'url' => U('/Profit/log' . WAPDM, 'from=wx&id=' . $resutl, true, true),
                    'topcolor' => '#FF0000',
                    'first' => "亲，朋友通过您的分享下单了。",
                    'order_sn' => $data['order_sn'],
                    'order_amount' => num2money($data['goods_amount']) . "元",
                    'brokerage' => num2money($profit_money) . "元",
                    'createtime' => date("Y-m-d H:i"),
                    'remark' => '【' . C('wx_mpname') . '】感谢有您，客服热线：' . C('service_tel')
                );
                @D("Common/Message", "Logic")->send_wx_message($log_data['uid'], $tmp_data, 'm_profit_notice');
            }
        }
    }

    /**
     * 订单的微信退款
     * @param array $return_data 退款订单的数据
     * @param array $order_info 订单数据
     * @author wscsky
     */
    function group_wxpay_refund($order_info = array()){
        if(!$order_info || !in_array($order_info['pay_status'],array(1,3))) return ;
        $map=array(
            'entity_id' 	=> $order_info['order_id'],
            'entity_type'	=> ENTITY_TYPE_ORDER,
            'status'	    => 1
        );
        $pay_logs =  M("payment_log")->where($map)->select();
        $pay_cls = \Common\Payment::factory("wxpay");
        if(!$pay_cls) return;
        foreach ($pay_logs as $log){
            $result = $pay_cls->refund($log, $order_info);
        }
        return $result;
    }
    
    /**
     * 读取订单的支付订单号
     * @author wscsky
     */
    function get_order_paysn($entity_id, $entity_type = ENTITY_TYPE_ORDER){
        if(!$entity_id) return "";
        $map=array(
            'entity_id' 	=> $entity_id,
            'entity_type'	=> $entity_type,
            'status'	=> 1
        );
        $sn =  M("payment_log")->where($map)->order("notify desc")->getField("out_trade_sn");
        return $sn;
    }
    
    
    /**
     * 虚拟商品支付成功后自动发货
     * 
     * @author watchman
     */
    static function auto_ship($order_id, $startTrans = true)
    {
        $order_info = self::get_orders_info($order_id, "", array( "ordersgoods"));
        if (! $order_info || ! in_array($order_info['order_status'], 
            array(ORDERS_STATUS_PAY, ORDERS_STATUS_SHIPPED)) || empty($order_info['ordersgoods']))
            return false;
            
            // 查看订单中是否只有虚拟商品
        $only_virtual = 1; // 是否只有虚拟商品
        $cards_num = 0;
        foreach ($order_info['ordersgoods'] as $key => $val) {
            if ($val['gtype'] == 0)
                $only_virtual = 0;
            if ($val['gtype'] == 1)
                $cards_num = $cards_num + $val['number'];
        }
        // 手机卡是否足够
        $cards_count = D("Common/Goods", "Logic")->get_cards_count(0);
        if ($cards_num > $cards_count) {
            self::$_error = "未使用的手机卡数量不足，请添加后再进行此操作！";
            return false;
        }
        
        // 更新手机卡的使用状态并将手机卡相关数据存到订单商品表中
        $cmodel = M("cards");
        $og_model = M("orders_goods");
        $startTrans && $cmodel->startTrans();
        $cdata = array(
            "status" => 1,
            "uid" => $order_info['uid'],
            "order_id" => $order_id,
            "utime" => time()
        );
        $sms_content = "";
        $aes = new \Org\Crypt\AES();
        foreach ($order_info['ordersgoods'] as $key => $val) {
            if ($val['gtype'] == 1) {
                $clist = $cmodel->where("status=0")
                    ->order("id asc")
                    ->getField("id,cardno,passwd", (int) $val['number']);
                $cids = array_keys($clist);
                $cdata['o_goods_id'] = $val['id'];
                $result = $cmodel->where(array(
                    "id" => array(
                        "in",
                        $cids
                    )
                ))->save($cdata);
                if ($result && $result == $val['number']) {
                    $gdata = "";
                    $chr = "";
                    foreach ($clist as $k => $v) {
                        $chr = $gdata == "" ? "" : chr(10);
                        $gdata .= $chr . "卡号：" . $v['cardno'] . '，密码：' . $aes->decrypt($v['passwd'], CARD_PASSWD_KEY);
                        
                        $sms_content .= 'NO.' . $v['cardno'] . ' Key:' . $aes->decrypt($v['passwd'], CARD_PASSWD_KEY);
                    }
                    $result = $og_model->where("id = %d", $val['id'])->setField("gdata", $gdata);
                    if ($result === false) {
                        self::$_error = "订单商品中商品数据保存失败！";
                        $startTrans && $cmodel->rollback();
                        return false;
                    }
                } else {
                    self::$_error = "未使用的手机卡数量不足，请添加后再进行此操作！";
                    $startTrans && $cmodel->rollback();
                    return false;
                }
            }
        }
        
        // 订单中只有虚拟商品则将订单状态变为已完成
        if ($only_virtual == 1) {
            $result = self::orders_status_change($order_info, ORDERS_STATUS_FINISH, $startTrans ? false : true);
            if (! $result) {
                self::$_error = "订单状态更新失败！";
                $startTrans && $cmodel->rollback();
                return false;
            }
        }
        
        // 给用户发送手机卡卡号、密码短信
        if ($sms_content && $order_info['mobile']) {
            D("Common/Sms", "Logic")->sendSms($order_info['mobile'], $sms_content);
        }
        $startTrans && $cmodel->commit();
        return true;
    }

    /**
     * 获取错误信息
     * 
     * @author watchman
     */
    function getError()
    {
        return self::$_error;
    }
    
    /**
     * 读取会员列表
     * @param array $where 查询条件
     * @param int   $page            
     * @param int   $listRows            
     * @param int   $count            
     * @return array $list 订单列表
     */
    function get_order_lists($where = array(), $page = 1, $listRows = 10, $order)
    {
        if ($where['o.order_status'] == -1) {
            unset($where['o.order_status']);
        }
        $model = D("Common/orders");
        $join = " JOIN zy_member m ON m.uid = o.uid ";
        $alllists = $model
        ->field('o.order_id')
        ->alias('o')
        ->where($where)
        ->join($join)
        ->group('o.order_id')
        ->select();
        $count = count($alllists);
        
        $counts = $model
        ->field("order_status,count(*) as count")
        ->group('order_status')
        ->select();
        foreach($counts as $val){
            $countAll += $val['count'];
            $countData[$val['order_status']] = $val['count'];
        }
        $list = $model->field('o.*,m.uname')->alias('o')->where($where)->join($join)->group('o.order_id')->page("{$page}, {$listRows}")->order($order)->select();;
        return array(
            'count'     =>  $count,
            'countAll'  =>  $countAll,
            'list'      =>  $list,
            'countData' =>  $countData
        );
    }
    
    /**
     * 读取订单商品信息
     * @author wscsky
     */
    function get_order_goods_by_og_id($id = 0)
    {
        if ($id == 0) {
            self::$_error = "参数有误";
            return false;
        }
        $map = array(
            'id' => array('eq',$id)
        );
        $model = M("orders_goods");
        $model->where($map);
        $info = $model->find();
        return $info;
    }
    
    function count_order($ds_id)
    {
        $return = array();
        $ds_id > 0 && $where = array('ds_id'=>array('eq',$ds_id));
        $daytime = strtotime(date('Y-m-d'),strtotime('+1 day'));
        $dayWhere = $where;
        $dayWhere['ctime'] = array('gt',$daytime); //当天数据条件
        $return['day_num_count'] = $this->num_count($dayWhere); //当天总订单
        $dayWhere['pay_status'] = array('eq',1);
        $return['daySumMoney'] = $this->sum_money($dayWhere);
        $yesterdaytime = strtotime(date("Y-m-d",strtotime("-1 day")));
        $yesterdayWhere = $where;
        $yesterdayWhere['ctime'] = array(array('gt', $yesterdaytime),array('lt', $daytime));
        $yesterdayWhere['pay_status'] = array('eq',1);
        $return['yesterdaySumMoney'] = $this->sum_money($yesterdayWhere);
        $weektime = strtotime(date("Y-m-d",strtotime("-1 week")));
        $weekWhere = $where;
        $weekWhere['ctime'] = array('gt',$weektime); //当天数据条件
        $weekWhere['pay_status'] = array('eq',1);
        $return['weekSumMoney'] = $this->sum_money($weekWhere);
        $status0Where = $where;
        $status0Where['order_status'] = array('eq','0');  //待付款订单
        $return['status0_num_count'] = $this->num_count($status0Where); //总订单
        $status3Where = $where;
        $status3Where['order_status'] = array('eq','3');  //已完成订单
        $return['status3_num_count'] = $this->num_count($status3Where); //总订单
        $status1Where = $where;
        $status1Where['order_status'] = array('eq','1');  //待发货订单
        $return['status1_num_count'] = $this->num_count($status1Where); //总订单
        $status5Where = $where;
        $status5Where['order_status'] = array('eq','2');  //已发货订单
        $return['status5_num_count'] = $this->num_count($status5Where); //总订单
        $status6Where = $where;
        $status6Where['order_status'] = array('eq','6');  //垃圾订单
        $return['status6_num_count'] = $this->num_count($status6Where); //总订单
        $paystatus2Where = $where;
        $paystatus2Where['pay_status'] = array('eq','2');  //待处理退款订单
        $return['paystatus2_num_count'] = $this->num_count($paystatus2Where); //总订单
        return $return;
    }
    
    function sum_money($where = array())
    {
        $model = D("Common/orders");
        $info = $model->field("sum(pay_price) as total")->where($where)->find(); //订单总额
        return $info['total'];
    }
    
    function num_count($where = array())
    {
        $model = D("Common/orders");
        $info = $model->field("count(*) as total")->where($where)->find(); //订单数量
        return $info['total'];
    }
    
    function count_order_lists($ds_id)
    {
        $return = array();
        $ds_id > 0 && $where = array('ds_id'=>array('eq',$ds_id));
        $time0 = strtotime(date("Y-m-d", time())); //今天0点时间戳
        $time7 = strtotime("-7 day"); //七天前时间戳
        $time14 = strtotime("-14 day"); //七天前时间戳
        $time30 = strtotime("-30 day"); //七天前时间戳
        $time60 = strtotime("-60 day"); //七天前时间戳
        $where1 = $where;
        $where1['ctime'] = array(array('gt', $time7),array('lt', $time0));
        $where1['pay_status'] = array('eq',1);
        $money7 = $this->sum_money($where1); //未来7天统计销售金额
        $money7 = empty($money7) ? 0 : $money7;
        $num7 = $this->num_count($where1); // 未来7天统计销售金额
        $where2 = $where;
        $where2['ctime'] = array(array('gt', $time14),array('lt', $time7));
        $where2['pay_status'] = array('eq',1);
        $money14 = $this->sum_money($where2); //未来14天到7天统计销售金额
        $money14 = empty($money14) ? 0 : $money14;
        $num14 = $this->num_count($where2); // 未来14天到7天统计订单数量
        $where3 = $where;
        $where3['ctime'] = array(array('gt', $time30),array('lt', $time0));
        $where3['pay_status'] = array('eq',1);
        $money30 = $this->sum_money($where3); //未来7天统计销售金额
        $money30 = empty($money30) ? 0 : $money30;
        $num30 = $this->num_count($where3); // 未来7天统计销售金额
        $where4 = $where;
        $where4['ctime'] = array(array('gt', $time60),array('lt', $time30));
        $where4['pay_status'] = array('eq',1);
        $money60 = $this->sum_money($where4); //未来14天到7天统计销售金额
        $money60 = empty($money60) ? 0 : $money60;
        $num60 = $this->num_count($where4); // 未来14天到7天统计订单数量
        $return['order_num'] = array(
            'num7' => array(
                'val' => $num7,
                'rate' => round((($num7/$num14)-1)*100,2)."%"
            ),
            'num30' => array(
                'val' => $num30,
                'rate' => round((($num30/$num60)-1)*100,2)."%"
            ),
        );
        $return['order_money'] = array(
            'money7' => array(
                'val' => $money7,
                'rate' => round((($money7/$money14)-1)*100,2)."%"
            ),
            'money30' => array(
                'val' => $money30,
                'rate' => round((($money30/$money60)-1)*100,2)."%"
            ),
        );
        return $return;
    }
    
    /**
     * 订单状态改变
     * 
     * @author watchman
     * @param array $data
     *            订单数据
     * @param int $new_status
     *            改变之后状态
     * @param bool $startTrans
     *            是否开启事务
     * @return boolean
     */
    static function orders_status_change($data, $new_status, $startTrans = true)
    {
        if($data['shanghu_uid']){
            $merMod = M("merchant");
            $minfo = $merMod->where(array('id'=>array('eq',$data['shanghu_uid'])))->find();
        }  else {
            self::$_error = "订单参数错误！！！";
            return FALSE;
        }
        
        $orderStatus = C("orderStatus");
        if (self::status_change_check($data['order_status'], $new_status, $data)) {
            $orders_model = D("Orders");
            $startTrans && $orders_model->startTrans();
            $sdata = array('order_status' => $new_status);
            $result = D("Common/Orders")->where('order_id = %d', $data['order_id'])->save($sdata);
            $result !== false && $result = true;
            $p_model = D("Common/Finance", "Logic");
            // 如果是退货或者取消退还余额
            if ($result && ($new_status == 10 || $new_status == 7)) {
                $log = array(
                    'entity_type' => 1, //1订单
                    'entity_id' => $data['order_id'],
                    'remark' => (($new_status == 10) ? "取消订单" : "订单退货") . ",编号{$data['order_sn']}"
                );
                $return = true;
                //退余额
                if ($return && $data['balance'] > 0) {
                    $log['type'] = 4;
                    $result = $p_model->change_balance($data['balance'], MODE_TYPE_INC, $data['uid'], $log);
                }
                //还原库存
                $goods = M("orders_goods")->where("order_id = %d", $data['order_id'])->select();
                foreach ($goods as $dd){
                    if($dd['spec_id'] > 0){
                        M("goods_spec")->where("spec_id = %s", $dd['spec_id'])->setInc("stock", $dd['number']);
                    }else{
                        M("goods")->where("goods_id = %s", $dd['goods_id'])->setInc("stock", $dd['number']);
                    }
                }
            }
            
            // 操作制作返回flase
            if (! $result) {
                $startTrans && $orders_model->rollback();
                self::$_error = "订单状态改变失败！";
                return false;
            }
            $startTrans && $orders_model->commit();
            
            switch ($new_status) {
                case '1':
                    self::orderStatus1($data);
                    D("Common/Orderslog")->logs($data['order_id'],"已支付，待发货","商户：{$minfo['store_name']}");
                    break;
                case '2':
                    D("Common/Orderslog")->logs($data['order_id'],"商家确认，备货中","商户：{$minfo['store_name']}");
                    break;
                case '3':
                    D("Common/Orderslog")->logs($data['order_id'],"商家已发货","商户：{$minfo['store_name']}");
                    break;
                case '4':
                    M('profit_log')->where("entity_id = %d and entity_type = %d and status = 3", array($data['order_id'],1))->setField('status', 0);
                    D("Common/Orderslog")->logs($data['order_id'],"交易已完成","商户：{$minfo['store_name']}");
                    break;
                case '5':
                    D("Common/Orderslog")->logs($data['order_id'],"用户申请退货，待商家确认","商户：{$minfo['store_name']}");
                    break;
                case '6':
                    D("Common/Orderslog")->logs($data['order_id'],"商家确认，已接收退货请求","商户：{$minfo['store_name']}");
                    break;
                case '7':
                    D("Common/Orderslog")->logs($data['order_id'],"退货已完成","商户：{$minfo['store_name']}");
                    break;
                case '10':
                    D("Common/Orderslog")->logs($data['order_id'],"取消订单","商户：{$minfo['store_name']}");
                    break;
                case '11':
                    D("Common/Orderslog")->logs($data['order_id'],"商家退款中","商户：{$minfo['store_name']}");
                    break;
                case '12':
                    D("Common/Orderslog")->logs($data['order_id'],"退款已完成","商户：{$minfo['store_name']}");
                    break;
                default :
                    self::$_error = "订单状态改变失败！";
                    return false;
            }
            
            $entity_remark = $orderStatus[$new_status];
            // 更新收益订单状态
            if ($entity_remark) {
                M("profit_log")->where("entity_type = %d and entity_id = %d", array(1,$data['order_id']))->save(array(
                    "entity_remark" => $entity_remark
                ));
            }
            return true;
        } else {
            return false;
        }
    }
    
    static function orderStatus1($data)
    {
        $disMod = D("Merchant/Distributionconfig");
        $disInfo = $disMod->getShareRate($data['shanghu_uid']);
        if(!$disInfo){
            self::$_error = $disMod::$_error;
            return FALSE;
        }
        // 计算分佣基数
        switch ($disInfo['dis_type']) {
            case 1:
                $amount = $data['goods_amount'];
                break;
            case 2:
                $amount = $data['pay_price'];
                break;
            default:
                $amount = 0;
                break;
        }
        $puid = D("Common/Member", "Logic")->get_member_field($data['uid'], "puid");
        if ($puid > 0 && $amount > 0) {
            $log = array(
                'entity_id' => $data['order_id'],
                'entity_type' => 1,
                'entity_remark' => "已付款待发货",
                'remark' => "订单{$data['order_sn']}佣金分成",
                'order_sn' => $data['order_sn'],
                'order_amount' => $data['goods_amount']
            );
            D('Common/Profit', 'Logic')->settle_order_profit($puid, $amount, $log);
        }
        // 送优惠券
        @D("Common/Coupon", "Logic")->order_pay_back($data);
    }

    

    /**
     * 检查订单新旧状态切换的合法性
     * 
     * @param int   $old_status 改变之前状态
     * @param int   $new_status 改变之后状态
     * @param array $data 订单信息
     * @return booleanean
     */
    static function status_change_check($old_status, $new_status, $data = array())
    {
        $status = array_keys(C("orderStatus"));
        if (!in_array($old_status, $status) || ! in_array($new_status, $status)) {
            self::$_error = "订单状态参数有误！";
            return false;
        }
        // 新旧状态不能相等
        if ($old_status == $new_status) {
            self::$_error = "订单状态参数有误！";
            return false;
        }
        // 订单状态不能变为"未付款"
        if ($new_status == '0') {
            self::$_error = "订单状态不能改为未付款！";
            return false;
        }
        // 订单状态不能变为"未付款"
        if ($new_status == '4' && $old_status != '3') {
            self::$_error = "已发货订单才能改为收货状态！";
            return false;
        }
        // "已完成"、"已退货完成"、"已取消"状态下订单不允许再改变
        if (in_array($old_status, array(4,7,10,11))) {
            self::$_error = "订单已完成或取消！";
            return false;
        }
        
        // "未付款"状态下的订单只能"取消"或"付款"
        if ($old_status == 0) {
            if ($new_status != 1 && $new_status != 10) {
                self::$_error = "未付款订单只能取消或付款！";
                return false;
            }
        }
        // 只有"未付款"状态下的订单才能取消
        if ($new_status == 10 && $old_status != 0) {
            self::$_error = "只有未付款状态下的订单才能取消！";
            return false;
        }
        
        // "已收货"状态下的订单才能"申请退货"
        if ($new_status == 5 && $old_status != 4) {
            self::$_error = "已收货订单才能申请退货！";
            return false;
        }
        
        if (in_array($new_status, array( 6, 7 ))){
            if ($old_status < 5) {
                self::$_error = "订单状态参数有误！";
                return false;
            }
        } else {
            if ($old_status > $new_status) {
                self::$_error = "订单状态参数有误！";
                return false;
            }
        }
        return true;
    }
    
    /**
     * 订单统计列表
     * @param array $where 查询条件
     * @param int   $page            
     * @param int   $listRows            
     * @param int   $count            
     * @return array $list 订单列表
     */
    function get_stat_order_lists($where = array(), $page = 1, $listRows = 10, $order)
    {
        $model = D("Common/orders");
        $join = " LEFT JOIN zy_orders_book og ON og.order_id = o.order_id "
                . " LEFT JOIN zy_distributor d ON d.d_id = o.ds_id";
        $alllists = $model
        ->field('o.order_id')
        ->alias('o')
        ->where($where)
        ->join($join)
        ->group('o.order_id')
        ->select();
        $count = count($alllists);
        $list = $model->field('o.*,d.d_name')->alias('o')->where($where)->join($join)->group('o.order_id')->page("{$page}, {$listRows}")->order($order)->select();;
        return array(
            'count'     =>  $count,
            'list'      =>  $list
        );
    }
    
    /**
     * 读取分销商统计列表
     * @param array $where 查询条件
     * @param int   $page            
     * @param int   $listRows            
     * @param int   $count            
     * @return array $list 订单列表
     */
    function get_bd_lists($where = array(), $page = 1, $listRows = 10, $order)
    {
        $model = D("Common/orders");
        $join = " LEFT JOIN zy_orders_book og ON og.order_id = o.order_id "
                . " LEFT JOIN zy_distributor d ON d.d_id = o.ds_id";
        $alllists = $model
        ->field('o.order_id')
        ->alias('o')
        ->where($where)
        ->join($join)
        ->group('o.order_id')
        ->select();
        $count = count($alllists);
        $list = $model->field('o.*,d.d_name')->alias('o')->where($where)->join($join)->group('o.order_id')->page("{$page}, {$listRows}")->order($order)->select();;
        return array(
            'count'     =>  $count,
            'list'      =>  $list
        );
    }
    
    /**
     * 读取会员列表
     * @param array $where 查询条件
     * @param int   $page            
     * @param int   $listRows            
     * @param int   $count            
     * @return array $list 订单列表
     */
    function getOrderCount($where = array())
    {
        $model = D("Common/orders");
        $join = " LEFT JOIN zy_orders_book og ON og.order_id = o.order_id "
                . " LEFT JOIN zy_member m ON m.uid = o.uid";
        $alllists = $model
        ->field('o.order_id')
        ->alias('o')
        ->where($where)
        ->join($join)
        ->group('o.order_id')
        ->select();
        $count = count($alllists);
        
        $counts = $model
        ->field("order_status,count(*) as count")
        ->group('order_status')
        ->select();
        foreach($counts as $val){
            $countAll += $val['count'];
            $countData[$val['order_status']] = $val['count'];
        }
        return array(
            'count'     =>  $count,
            'countAll'  =>  $countAll,
            'countData' =>  $countData
        );
    }
    
    
}