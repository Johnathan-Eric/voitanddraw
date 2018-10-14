<?php
/**
 * 菠萝派接口代码
 * @author wscsky
 * CopyRight @ www.zooyoo.cc
 * 2017-1-11
 */
namespace Common\Api;
class PolyApi{

    protected $error,$appKey,$appSecret,$appToken,$uCode,$uKey;
    protected $write_log = false;
    
    function __construct(){
        $this->appKey       = C("polyapi_appkey");
        $this->appSecret    = C('polyapi_appsecret');
        $this->appToken     = C("polyapi_token");
        $this->uCode        = C('polyapi_ucode');
        $this->uKey         = C('polyapi_ukey');
        $this->write_log    = C('polyapi_write_log',null,0) == 1 ? true:false;
    }
    
    
    /**
     * 处理被动请求
     * @param array $data 请求的数据
     * @author wscsky
     */
    function reqeust($data){
        $this->write_log && \think\Log::log('PolyApi', "reqeust-data", $data);
        if(!self::auth($data)){
            $this->reply(0, $this->error);
            exit();
        }
        if($data['method']){
            !is_array($data['bizcontent']) && $data['bizcontent'] = json_decode($data['bizcontent'],true);
            switch ($data['method']){
                case "Differ.JH.Business.GetOrder":
                    if($data['bizcontent']['PlatOrderNo']){
                        //读取单个订单
                        self::Business_GetOrder($data['bizcontent']);
                    }else{
                        //订单订单列表
                        self::Business_GetOrderList($data['bizcontent']);
                    }
                    break;
                case "Differ.JH.Business.CheckRefundStatus": //检查退款
                   $rdata = array('refundStatus' => 'JH_07','refundStatusdescription'=>"");
                   $this->back($rdata);
                   break;
                case "Differ.JH.Business.Send":
                    self::Business_Send($data['bizcontent']);  //发货操作
                    break;
                case "Differ.JH.Business.DownloadProduct":
                    self::Business_DownloadProduct($data['bizcontent']);  //商品列表
                    break;
                case "Differ.JH.Business.SyncStock":        //同步库存
                    self::Business_SyncStock($data['bizcontent']);  //商品列表
                    break;
                default:
                    \Think\Log::log("PolyApi","ErrorAPI",$data['method']);
                    $this->back(array(),40000,'平台暂不支持此接口','GSE.PLAT_NOT_SUPPORT');
                    break;
            }
            exit();
        }
        //独立商城接入方式
        if(method_exists(__CLASS__, $data['mType'])){
            self::$data['mType']($data);
        }else{
            \think\Log::log("PolyApi", "Reqeust","接口方法{$data['mType']}不存在");
            self::reply(0,"接口方法{$data['mType']}未定义");
        }
    }
    
    /**
     * 读取商品列表信息
     * @param array $data  接口请求数据
     * @author wscsky
     */
    function mGetGoods($data){
        $type  = $data['GoodsType'];
        $sku   = $data['OuterID'];
        $name  = $data['GoodsName'];
        $page  = (int)$data['Page'];
        $limit = $data['PageSize'];
        $filter = array();
        $string = "1=1";
        switch ($type){
            case "OnsSale":
                $filter['online'] = 1;
                break;
            case "InStock":
                $filter['online'] = 0;
                break;
        }
        if($sku) $string .= " and goods_sn = '{$sku}'";
        if($name) $string .= " and name like '%{$name}%'";
        if($filter > 0){
            $limit < 1 ? 20 : $limit;
        }else{
            $page = 1;
            $limit = 100000;
        }
        if($string !="1=1") $filter['_string'] = $string;
        $model = D("Common/Goods","Logic");
        $total = 0;
        $list = $model -> search_goods($filter, $page, $limit, $total, array("goodsspec"));
        $rdata = array(
            'TotalCount' => $total,
        );
        $goods = [];
        foreach($list as $dd){
            $tdata = array(
                'ItemID'    => $dd['goods_id'],
                'ItemName'  => $dd['name'],
                'Num'       => $dd['stock'],
                'Price'     => $dd['price'],
                'OuterID'   => $dd['goods_sn'],
                'IsSku'     => $dd['goodsspec'] ? 1:0,
            );
            if($dd['goodsspec']){
                $Items = [];
                foreach ($dd['goodsspec'] as $item){
                    $Items[] = array(
                        'Unit' => $item['name'],
                        'SkuID' => $item['spec_id'],
                        'Num'   => $item['stock'],
                        'SkuOuterID'    => $item['spec_id'],
                        'SkuPrice'      => $item['price'],
                    );
                }
                $tdata['Items'] = $Items;
            }
            $goods[] = $tdata;
        }
        $rdata = array_merge($rdata, $goods);
        $this->reply(1, "success", $rdata,"Goods",'Ware');
        exit();
    }
    
    
    /**
     * 同步商品库存
     * @param array $data 接口请求数据 
     */
    function mSysGoods($data){
        $goods_id  = $data['ItemID'];
        $spec_id   = $data['SkuID'];
        $stock     = $data['Quantity'];
        if(!$goods_id && !$spec_id){
            $this->reply(2, '商品参数有误');
            exit();
        }
        if($goods_id > 0 && $spec_id > 0){
           $result =  M("goods_spec") -> where("goods_id = %d and spec_id = %d", array($goods_id, $spec_id))->setField('stock',$stock);
        }else{
           $result =  M("goods") -> where("goods_id = %d", array($goods_id))->setField('stock',$stock);
        }
        if($result !== false){
            $this->reply(1, "success");
        }else{
            $this->reply(2,"更新数量失败");
        }
    }
    
    /**
     * 定单列表查询
     * @param array $data
     * @author wscsky
     */
    function mOrderSearch($data){
        
        $status  = $data['OrderStatus']; //要求订单状态
        $limit   = $data['PageSize'];    //每页数量
        $page    = $data['Page'];      //当前页码
        $sdate   = $data['Start_Modified'] ? strtotime($data['Start_Modified']) : 0;//开始时间
        $edate   = $data['End_Modified'] ? strtotime($data['End_Modified']) : 0;    //结束时间
        $where1  = " where gtype !=1 and order_status in(0,1,2) ";
        $where2  = " where status in(0,1) ";

        $model = M();
        if($page < 1) $page = 1;
        if($limit < 1) $limit = 20;
        
        //统计SQL
        $sql1 = "select count(*) as num from zy_orders {$where1}
                 union select count(*) as num from zy_orders_shipping {$where2}";
        $tdata = $model->query($sql1);
        $rdata['OrderList'] = array();
        $rdata['OrderCount'] = my_array_sum($tdata, "num");
        $rdata['Page']       = $page;
        
        //读取SQL
        $sql2 = "select order_sn,ctime from zy_orders {$where1}
                 union select sn,tasktime from zy_orders_shipping {$where2}
                 order by ctime limit ".($page -1)*$limit.",{$limit}";
        
        $tdata = $model -> query($sql2);
        foreach ($tdata as $dd){
            $rdata['OrderList'][] = $dd['order_sn'];
        }
        $this->reply(1, "success", $rdata,"Order", "OrderNo");        
    }
    
    /**
     * 读取信息信息
     * @author wscsky
     */
     function mGetOrder($data){
        $sn = $data['OrderNO'];
        if(!$sn){
            $this->reply(0, '订单号参数不能为空');
            exit();
        }
        $model = D("Common/Orders","Logic");
        //配送单
        if(strpos($sn, "_") > 0){
           $is_ship = true;
           $sdata = $model -> get_order_shipping($sn,0,true);
           $sdata && $order_info = $model -> get_orders_info($sdata['order_id'],'', array("ordersgoods"));
        }else{
            //普通订单
           $is_ship = false;
           $order_info  = $model -> get_orders_info(0, $sn, array("ordersgoods"));
        }
        //未找到订单
        if(!$order_info){
            $this->reply(0, "未找到订单号[{$sn}]信息");
            exit();
        }
       
        //整理返回数据 
        $region = explode(" ",$is_ship ? $sdata['region_name']:$order_info['region_name']);
        $rdata = [
            'OrderNO'       => $is_ship ? $sdata['sn']:$order_info['order_sn'],
            'OrderStatus'   => self::get_order_status($order_info, $sdata, $is_ship),
            'DateTime'      => date("Y-m-d H:i:s", $order_info['ctime']),
            'BuyerID'       => $order_info['uid'],
            'BuyerName'     => $is_ship ? $sdata['name'] : $order_info['name'],
            'Country'       => '中国',
            'Province'      => $region[0],
            'City'          => $region[1],
            'Town'          => $region[2],
            'Adr'           => $is_ship ? $sdata['address'] : $order_info['address'],
            'Phone'         => $is_ship ? $sdata['mobile'] : $order_info['mobile'],
            'Total'         => $is_ship ? 0 : $order_info['order_amount']-$order_info['discount'],
            'Postage'       => $is_ship ? 0 : $order_info['shipping_fee'],
            'PaySource'     => $order_info['payment_name'],
            'Chargetype'    => '金收款',
            'Item'          => array()
        ];
        //处理商品
        foreach ($order_info['ordersgoods'] as $goods){
            if(!$is_ship || ($sdata['no'] == 1 || $goods['gtype'] == 1)){
                $rdata['Item'][] = array(
                        'GoodsID'   => $goods['goods_id'],
                        "Goodsname" => $goods['name'], 
                        'GoodsSpec' => $goods['spec_name'] ? $goods['spec_name'] : $goods['name'], 
                        "Count"     => $is_ship ? ($goods['gtype'] == 1 ? 1 : $goods['number']) : $goods['number'], 
                        'Price'     => $goods['price'],
                        'GoodsStatus'   => '',
                );
            }
        }
        //备注信息
        $remark = "";
        if($order_info['flower_use']) $remark .= "【用途:{$order_info['flower_use']}】";
        if($order_info['flower_bad']) $remark .= "【忌讳:{$order_info['flower_bad']}】";
        if($is_ship && $sdata['no'] == 1 && $order_info['ecard']){
            $remark .= "【电子卡:{$order_info['ecard_content']}  送花人:{$order_info['ecard_name']}】";
        }
        $rdata['Remark'] = $remark;
        $this->reply(1, "success", $rdata, 'Order',"Item");
    }
    
    /**
     * 设置订单发货
     * @param array $data  接口请求的数据
     * @author wscsky
     */
    function mSndGoods($data){
        $sn = $data['OrderNO'];
        if(!$sn){
            $this->reply(0, '订单编号为空');
            exit();
        }
        $result = false;
        $model = D("Common/Orders","Logic");
        $sns = explode(",", $sn);
        $sdata1 = array(
            'shipping_name' => $data['SndStyle'],
            'shipping_no'   => $data['BillID'],
            'shipping_time' => time(),
            'status'        => 2,
        );
        $sdata2 = array(
            'shipping_name' => $data['SndStyle'],
            'shipping_no'   => $data['BillID'],
            'shipping_time' => time(),
            'shiped_num'    => 1,
            'order_status'  => 3,
        );
        $model2 = M("orders");
        foreach ($sns as $sn){
            if(strpos($sn, "_") > 0){
                $order_info = $model -> get_order_shipping($sn,0,true);
                if(in_array($order_info['status'],array(1))){
                    $result = M("orders_shipping")->where("id = %d", $order_info['id'])->save($sdata1);
                    if($result){
                        if($order_info['uid'] > 0){
                            // 给用户发微信通知[礼单]
                            $tmp_data = array(
                                'url'       => U('/orders/shipping' . WAPDM, 'id='.$order_info['id'], true, true),
                                'topcolor'  => '#FF0000',
                                'first'     => "亲，您收到的鲜花礼单已发货。",
                                'order_sn'  => $order_info['sn'],
                                'shipping_name' => $sdata1['shipping_name'],
                                'shipping_no'  => $sdata1['shipping_no'],
                                'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                            );
                            @D("Common/Message", "Logic")->send_wx_message($order_info['uid'], $tmp_data, 'm_order_shipped');
                        }else{
                            $order_info2 = $model->get_orders_info($order_info['order_id']);
                            // 给用户发微信通知[非礼单]
                            $tmp_data = array(
                                'url'       => U('/orders/shipping' . WAPDM, 'id='.$order_info['id'], true, true),
                                'topcolor'  => '#FF0000',
                                'first'     => "亲，您的月配送订单第{$order_info['no']}次配送已发货。",
                                'order_sn'  => $order_info['sn'],
                                'shipping_name' => $sdata1['shipping_name'],
                                'shipping_no'  => $sdata1['shipping_no'],
                                'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                            );
                            @D("Common/Message", "Logic")->send_wx_message($order_info2['uid'], $tmp_data, 'm_order_shipped');
                        }
                        //如果全部配送完，修改主订单
                        $order_info2 = $model->get_orders_info($order_info['order_id']);
                        $map = array(
                            'order_id' => $order_info['order_id'],
                            'status'   => array("lt",2)
                        );
                        if($order_info2['shiped_num'] >= $order_info2['shiped_total'] &&  M("orders_shipping")->where($map)->count() ==0){
                            M("orders")->where("order_id =%d", $order_info2['order_id'])->save( array(
                            'order_status' => ORDERS_STATUS_SHIPPED,'shipping_time' => time()
                            ));
                        }
                    }
                }
            }else{
                $order_info  = $model -> get_orders_info(0, $sn);
                if(in_array($order_info['order_status'],array(1,2))){
                    $result = M("orders")->where("order_id = %d", $order_info['order_id'])->save($sdata2);
                    if($result){
                        $tmp_data = array(
                            'url'       => U('/orders/info' . WAPDM, 'id=' . $order_info['order_id'], true, true),
                            'topcolor'  => '#FF0000',
                            'first'     => "亲，您的订单已发货了。",
                            'order_sn'  => $order_info['order_sn'],
                            'shipping_name' => $sdata2['shipping_name'],
                            'shipping_no'  => $sdata2['shipping_no'],
                            'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                        );
                        @D("Common/Message", "Logic")->send_wx_message($order_info['uid'], $tmp_data, 'm_order_shipped');
                    }
                }
            }
        }
        if($result === false && count($sns) <=1){
            $this->reply(0, "发货操作失败");
            exit();
        }
        $this->reply(1, '发货操作成功');
    }
    
    /**
     * 订单发货
     * @param array $data
     * @author wscsky
     */
    function Business_Send($data){
        
        $sn = $data['PlatOrderNo'];
        if(!$sn){
            $this->back(array(),40000,'订单编号为空');
            exit();
        }
        $result = false;
        $model = D("Common/Orders","Logic");
        $sns = explode(",", $sn);
        $sdata1 = array(
            'shipping_name' => $data['LogisticName'],
            'shipping_no'   => $data['LogisticNo'],
            'shipping_time' => time(),
            'status'        => 2,
        );
        $sdata2 = array(
            'shipping_name' => $data['LogisticName'],
            'shipping_no'   => $data['LogisticNo'],
            'shipping_time' => time(),
            'shiped_num'    => 1,
            'order_status'  => 3,
        );
        $model2 = M("orders");
        foreach ($sns as $sn){
            if(strpos($sn, "_") > 0){
                $order_info = $model -> get_order_shipping($sn,0,true);
                if(in_array($order_info['status'],array(1))){
                    $result = M("orders_shipping")->where("id = %d", $order_info['id'])->save($sdata1);
                    if($result){
                        if($order_info['uid'] > 0){
                            // 给用户发微信通知[礼单]
                            $tmp_data = array(
                                'url'       => U('/orders/shipping' . WAPDM, 'id='.$order_info['id'], true, true),
                                'topcolor'  => '#FF0000',
                                'first'     => "亲，您收到的鲜花礼单已发货。",
                                'order_sn'  => $order_info['sn'],
                                'shipping_name' => $sdata1['shipping_name'],
                                'shipping_no'  => $sdata1['shipping_no'],
                                'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                            );
                            @D("Common/Message", "Logic")->send_wx_message($order_info['uid'], $tmp_data, 'm_order_shipped');
                        }else{
                            $order_info2 = $model->get_orders_info($order_info['order_id']);
                            // 给用户发微信通知[非礼单]
                            $tmp_data = array(
                                'url'       => U('/orders/shipping' . WAPDM, 'id='.$order_info['id'], true, true),
                                'topcolor'  => '#FF0000',
                                'first'     => "亲，您的月配送订单第{$order_info['no']}次配送已发货。",
                                'order_sn'  => $order_info['sn'],
                                'shipping_name' => $sdata1['shipping_name'],
                                'shipping_no'  => $sdata1['shipping_no'],
                                'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                            );
                            @D("Common/Message", "Logic")->send_wx_message($order_info2['uid'], $tmp_data, 'm_order_shipped');
                        }
                        //如果全部配送完，修改主订单
                        $order_info2 = $model->get_orders_info($order_info['order_id']);
                        $map = array(
                            'order_id' => $order_info['order_id'],
                            'status'   => array("lt",2)
                        );
                        if($order_info2['shiped_num'] >= $order_info2['shiped_total'] &&  M("orders_shipping")->where($map)->count() ==0){
                            M("orders")->where("order_id =%d", $order_info2['order_id'])->save( array(
                            'order_status' => ORDERS_STATUS_SHIPPED,'shipping_time' => time()
                            ));
                        }
                    }
                }
            }else{
                $order_info  = $model -> get_orders_info(0, $sn);
                if(in_array($order_info['order_status'],array(1,2))){
                    $result = M("orders")->where("order_id = %d", $order_info['order_id'])->save($sdata2);
                    if($result){
                        $tmp_data = array(
                            'url'       => U('/orders/info' . WAPDM, 'id=' . $order_info['order_id'], true, true),
                            'topcolor'  => '#FF0000',
                            'first'     => "亲，您的订单已发货了。",
                            'order_sn'  => $order_info['order_sn'],
                            'shipping_name' => $sdata2['shipping_name'],
                            'shipping_no'  => $sdata2['shipping_no'],
                            'remark'       => '如有疑问，请拨打客服电话：' . C('service_tel')
                        );
                        @D("Common/Message", "Logic")->send_wx_message($order_info['uid'], $tmp_data, 'm_order_shipped');
                    }
                }
            }
        }
        if($result === false && count($sns) <=1){
            $this->back(array(),40000, "发货操作失败");
            exit();
        }
        $this->back(array());
    }
    
    /**
     * 菠萝派商城对接[读取单个订单]
     * @param array $data 请求的bizcontent数组
     */
    function Business_GetOrder($data){
        
        $sn     = $data['PlatOrderNo']; //订单编号
        $model  = D("Common/Orders","Logic");
        //配送单
        if(strpos($sn, "_") > 0){
            $is_ship = true;
            $sdata = $model -> get_order_shipping($sn,0,true);
            $sdata && $order_info = $model -> get_orders_info($sdata['order_id'],'', array("ordersgoods"));
        }else{
            //普通订单
            $is_ship = false;
            $order_info  = $model -> get_orders_info(0, $sn, array("ordersgoods"));
        }
        //未找到订单
        if(!$order_info){
            $this->back(array(),40000, "未找到订单号[{$sn}]信息",GSE.LOGIC_ERROR);
            exit();
        }
         
        //整理返回数据
        $region = explode(" ",$is_ship ? $sdata['region_name']:$order_info['region_name']);
        $rdata = [
            'PlatOrderNo'   => $is_ship ? $sdata['sn']:$order_info['order_sn'],
            'tradeStatus'   => self::get_order_status2($order_info, $sdata, $is_ship),
            'tradeStatusdescription' => "",
            'tradetime'      => date("Y-m-d H:i:s", $order_info['ctime']),
            'payorderno'    => '',
            'nick'          => $is_ship ? $sdata['mobile'] : $order_info['mobile'], //get_uname($order_info['uid']),
            'receivername'  => $is_ship ? $sdata['name'] : $order_info['name'],
            'country'       => 'CN',
            'province'      => $region[0],
            'city'          => $region[1],
            'area'          => $region[2],
            'town'          => '',
            'address'       => $is_ship ? $sdata['address'] : $order_info['address'],
            'zip'           => '',
            'phone'         => '',
            'email'         => '',
            'mobile'        => $is_ship ? $sdata['mobile'] : $order_info['mobile'],
            'totalmoney'    => $is_ship ? 0 : $order_info['order_amount']-$order_info['discount'],
            'goodsfee'      => $is_ship ? 0 : $order_info['goods_amount'],
            'postfee'       => $is_ship ? 0 : $order_info['shipping_fee'],
            'favourablemoney' => $is_ship ? 0 : $order_info['discount'],
            'commissionvalue' => 0, //佣金
            'pay_time'       => $order_info['pay_time'] ? date("Y-m-d H:i:s", $order_info['pay_time']) : "",
            'goodinfos'      => array()
        ];
        //处理商品
        foreach ($order_info['ordersgoods'] as $goods){
            if(!$is_ship || ($sdata['no'] == 1 || $goods['gtype'] == 1)){
                $rdata['goodinfos'][] = array(
                    'ProductId'      => $goods['goods_id'],
                    "tradegoodsno"   => $goods['name'],
                    'tradegoodsname' => $goods['spec_name'] ? $goods['spec_name'] : $goods['name'],
                    'tradegoodsspec' => $goods['spec_name'],
                    "goodscount"     => $is_ship ? ($goods['gtype'] == 1 ? 1 : $goods['number']) : $goods['number'],
                    'price'          => $goods['price'],
                    'Status'         => '',
                );
            }
        }
        //备注信息
        $remark = $remark2 = "";
        if($order_info['flower_use']) $remark .= "【用途:{$order_info['flower_use']}】";
        if($order_info['flower_bad']) $remark .= "【忌讳:{$order_info['flower_bad']}】";
        if($is_ship && $sdata['no'] == 1 && $order_info['ecard']){
            $remark .= "【电子卡:{$order_info['ecard_content']}  送花人:{$order_info['ecard_name']}】";
        }
//         $rdata['customerremark'] = $remark;
        $rdata['sellerremark'] = $remark;
        $data = array('numtotalorder' => 1, 'orders' => array($rdata));
        $this->back($data);
    }    
    /**
     * 菠萝派商城对接[读取订单列表]
     * @param array $data 请求的bizcontent数组
     */
    function Business_GetOrderList($data){
    
        $otype   = $data['ShopType'];   //订单类型 普通订单:JH_002，月配送单JH_001
        if(!in_array($otype, array('JH_001','JH_002'))) $otype= "JH_001";
        
        //后台设置取回类型
        switch (C('api_order_rtype')){
            case 1:
                $otype = "JH_002";
                break;
            case 2:
                $otype = "JH_001";
                break;
//             case 3: // 团购
        }
        
        //处理普通订单
        if($otype == "JH_002"){
            self::read_orders($data);
        }else{
            //处理配送订单
            self::read_shipping_orders($data);
        }
    }
    
    /**
     * 读取商品列表信息
     * @param array $data  接口请求数据
     * @author wscsky
     */
    function Business_DownloadProduct($data){
        $status      = $data['status'];
        $goods_id    = $data['goods_id'];
        $name   = $data['productname'];
        $page   = (int)$data['pageindex'];
        $limit  = $data['pagesize'];
        $filter = array();
        $string = "1=1";
        switch ($status){
            case "JH_01":
                $filter['online'] = 1;
                break;
            case "JH_02":
                $filter['online'] = 0;
                break;
            case "JH_03":
                $filter['stock'] = array("lt",1);
                break;
        }
        if($name) $string .= " and(name like '%{$name}%' or goods_sn = '{$sku}')";
        if($filter > 0){
            $limit < 1 ? 20 : $limit;
        }else{
            $page = 1;
            $limit = 100000;
        }
        if($string !="1=1") $filter['_string'] = $string;
        $model = D("Common/Goods","Logic");
        $total = 0;
        $list = $model -> search_goods($filter, $page, $limit, $total, array("goodsspec"));
        $rdata = array(
            'totalcount' => $total,
            'goodslist'  => array()
        );
        foreach($list as $dd){
            $tdata = array(
                'platproductid' => $dd['goods_id'],
                'name'          => $dd['name'],
                'num'           => $dd['stock'],
                'price'         => $dd['price'],
                'outerid'       => $dd['goods_sn'],
                'pictureurl'    => check_img_url($dd['thumb']),
                'skus'          => array(),
            );
            if($dd['goodsspec']){
                $Items = array();
                foreach ($dd['goodsspec'] as $item){
                    $Items[] = array(
                        'skuid' => $item['spec_id'],
                        'skuouterid'    => $item['spec_id'],
                        'skuname' => $item['name'],
                        'skuquantity'   => $item['stock'],
                        'skuprice'      => $item['price'],
                    );
                }
                $tdata['skus'] = $Items;
            }
            $rdata['goodslist'][] = $tdata;
        }
        $this->back($rdata);
        exit();
    }
    
    /**
     * 同步库存信息
     * @author wscsky
     */
    function Business_SyncStock($data){
        $data = json_decode(strtolower(json_encode($data)),true);
        $goods_id = $data['platproductid'];
        $goods_sn = $data['outerid'];
        $stock    = $data['quantity'];
        
        $spec_id  = $data['skuid'];
        $spec_sn  = $data['outskuid'];
        
        if(!$goods_id && !$spec_id){
            $this->back(array(),40000,'商品参数有误');
            exit();
        }
        if($goods_id > 0 && $spec_id > 0){
            $result =  M("goods_spec") -> where("goods_id = %d and spec_id = %d", array($goods_id, $spec_id))->setField('stock',$stock);
        }else{
            $result =  M("goods") -> where("goods_id = %d", array($goods_id))->setField('stock',$stock);
        }
        if($result !== false){
            $this->back();
        }else{
            $this->back(array(),40000,'更新库存失败');
        }
    }
    
    
    /**
     * 读取普通订单
     * @param array $data
     * @author wscsky
     */
    function read_orders($data, $flag = 0){
        $status  = $data['OrderStatus']; //要求订单状态
        //订单交易状态(等待买家付款=JH_01，等待卖家发货=JH_02，等待买家确认收货=JH_03，
        //交易成功=JH_04，交易关闭=JH_05，所有订单=JH_99)
        $limit   = $data['PageSize'];    //每页数量
        $page    = $data['PageIndex'];   //当前页码
        $sdate   = $data['StartTime'] ? strtotime($data['StartTime']) : 0;//开始时间
        $edate   = $data['EndTime'] ? strtotime($data['EndTime']) : 0;    //结束时间
        $ttype   = $data['TimeType'];   //时间格式 H_01(支付时间)，JH_02(创建时间)
        if($page < 1) $page = 1;
        if($limit < 0) $limit = 20;
        $map     = array("gtype" => array("neq",1));
        if($flag == 1) $map['gtype'] = 1;
        switch ($status){
            case 'JH_01':
                $map['order_status'] = 0;
                $map['_string'] = " ((order_type in(0,1) and pay_status = 0) or (order_type in(2,3) and group_status in(0,1)))";
                break;
            case 'JH_02':
                $map['order_status'] = array("in",array(1,2));
                $map['_string'] = " (order_type in(0,1) or (order_type in(2,3) and group_status = 3 and pay_status = 1))";
                break;
            case 'JH_03':
                $map['order_status'] = 3;
                break;
            case 'JH_04':
                $map['order_status'] = 4;
                break;
            case 'JH_05':
                $map['order_status'] = 10;
                break;
        }
        $tkey = ($ttype == "H_01") ? "pay_time" : "ctime";
        if($sdate && $edate){
            $map[$tkey] = array(array('egt', $sdate), array("elt", $edate));
        }else{
            if($sdate) $map[$tkey] = array('egt', $sdate);
            if($edate) $map[$tkey] = array('elt', $edate);
        }
        
        $model  = D("Common/orders");
        $rdata = array();
        $rdata['numtotalorder'] = $model -> where($map)->count();
        
        $datas = $model -> where($map) -> relation(array("ordersgoods"))
                        -> page($page, $limit)
                        -> select();
        $this->write_log && \think\Log::log('PolyApi', "SQL", $model->getLastSQL());
        $rdata['orders'] = array();
        $is_ship = false;
        foreach ($datas as $order_info){
            $region = explode(" ", $order_info['region_name']);
            $tdata = array(
                'platorderno'   => $order_info['order_sn'],
                'tradestatus'   => self::get_order_status2($order_info, $sdata),
                'tradestatusdescription' => $order_info["remark"],
                'tradetime'     => date("Y-m-d H:i:s", $order_info['ctime']),
                'payorderno'    => '',
                'nick'          => $order_info['mobile'], //get_uname($order_info['uid']),
                'receivername'  => $order_info['name'],
                'country'       => 'CN',
                'province'      => $region[0],
                'city'          => $region[1],
                'area'          => $region[2],
                'town'          => '',
                'address'       => $order_info['address'],
                'zip'           => '',
                'phone'         => '',
                'email'         => '',
                'mobile'        => $order_info['mobile'],
                'totalmoney'    => $order_info['order_amount']-$order_info['discount'],
                'goodsfee'      => $order_info['goods_amount'],
                'postfee'       => $order_info['shipping_fee'],
                'favourablemoney' => $order_info['discount'],
                'commissionvalue'   => 0, //佣金
                'paytime'       => $order_info['pay_time'] ? date("Y-m-d H:i:s", $order_info['pay_time']) : "",
                'goodinfos'     => array()
            );
            
            //处理商品
            foreach ($order_info['ordersgoods'] as $goods){
                if(!$is_ship || ($sdata['no'] == 1 || $goods['gtype'] == 1)){
                    $tdata['goodinfos'][] = array(
                        'productid'         => $goods['goods_id'],
                        "tradegoodsno"      => $goods['goods_sn'],
                        'tradegoodsname'    => $goods['spec_name'] ? $goods['spec_name'] : $goods['name'],
//                      'tradegoodsspec'    => $goods['spec_name'],
                        "goodscount"        => $is_ship ? ($goods['gtype'] == 1 ? 1 : $goods['number']) : $goods['number'],
                        'price'             => $goods['price'],
                        'Status'            => '',
                    );
                }
            }
            //备注信息
            $remark = "";
            if($order_info['flower_use']) $remark .= "【用途:{$order_info['flower_use']}】";
            if($order_info['flower_bad']) $remark .= "【忌讳:{$order_info['flower_bad']}】";
            if($is_ship && $sdata['no'] == 1 && $order_info['ecard']){
                $remark .= "【电子卡:{$order_info['ecard_content']}  送花人:{$order_info['ecard_name']}】";
            }
            $rdata['customerremark'] = $remark;
            
            $rdata['orders'][] = $tdata;
        }
        $this->write_log && \think\Log::log('PolyApi', "普通订单返回", $rdata );
        $this->back($rdata);
    }
    
    
    /**
     * 读取月配送订单
     * @param array $data
     * @author wscsky
     */
    function read_shipping_orders($data){
        
        $status  = $data['OrderStatus']; //要求订单状态
        //订单交易状态(等待买家付款=JH_01，等待卖家发货=JH_02，等待买家确认收货=JH_03，
        //交易成功=JH_04，交易关闭=JH_05，所有订单=JH_99)
        $limit   = $data['PageSize'];    //每页数量
        $page    = $data['PageIndex'];   //当前页码
        $sdate   = $data['StartTime'] ? strtotime($data['StartTime']) : 0;//开始时间
        $edate   = $data['EndTime'] ? strtotime($data['EndTime']) : 0;    //结束时间
        $ttype   = $data['TimeType'];   //时间格式 H_01(支付时间)，JH_02(创建时间)
        
        if($page < 1) $page = 1;
        if($limit < 0) $limit = 20;
        $map     = array();
        switch ($status){
            case 'JH_01':
                self::read_orders($data,1);
                break;
            case 'JH_02':
                $map['status'] = 1;
                break;
            case 'JH_03':
                $map['status'] = 2;
                break;
            case 'JH_04':
                $map['status'] = 3;
                break;
            case 'JH_05':
                $map['status'] = 10;
                break;
        }
        $tkey = "tasktime";
        if($edate) $edate += C('polyapi_delay_hours',null,0) * 3600;
        if($sdate) $sdate += C('polyapi_delay_hours',null,0) * 3600;
        if($sdate && $edate){
            $map[$tkey] = array(array('egt', $sdate), array("elt", $edate));
        }else{
            if($sdate) $map[$tkey] = array('egt', $sdate);
            if($edate) $map[$tkey] = array('elt', $edate);
        }
    
        $model  = M("orders_shipping");
        $model2 = D("Common/Orders","Logic");
        $rdata = array();
        $rdata['numtotalorder'] = $model -> where($map)->count();
    
        $datas = $model -> where($map)
                        -> page($page, $limit)
                        -> select();
//         echo $model->getlastSQL();
        $this->write_log && \think\Log::log('PolyApi', "read_shipping_orders", $model->getLastSQL());
        $rdata['orders'] = array();
        $is_ship = true;
        foreach ($datas as $sdata){
            $region = explode(" ", $sdata['region_name']);
            $order_info = $model2 -> get_orders_info($sdata['order_id'],'',array('ordersgoods'));
            $tdata = array(
                'platorderno'   => $sdata['sn'],
                'tradestatus'   => self::get_order_status2($order_info, $sdatd, true),
                'tradestatusdescription' => "",
                'tradetime'     => date("Y-m-d H:i:s", $sdata['tasktime']),
                'payorderno'    => '',
                'nick'          => $sdata['mobile'], //get_uname($sdata['uid'] > 0 ? $sdata['uid'] : $order_info['uid']),
                'receivername'  => $sdata['name'],
                'country'       => 'CN',
                'province'      => $region[0],
                'city'          => $region[1],
                'area'          => $region[2],
                'town'          => '',
                'address'       => $sdata['address'],
                'zip'           => '',
                'phone'         => '',
                'email'         => '',
                'mobile'        => $sdata['mobile'],
                'totalmoney'    =>  $order_info['order_amount']-$order_info['discount'],
                'goodsfee'      =>  $order_info['goods_amount'],
                'postfee'       =>  $order_info['shipping_fee'],
                'favourablemoney' =>  $order_info['discount'],
                'commissionvalue'   => 0, //佣金
                'paytime'       => $order_info['pay_time'] ? date("Y-m-d H:i:s", $order_info['pay_time']) : "",
                'goodinfos'     => array()
            );
    
            //处理商品
            foreach ($order_info['ordersgoods'] as $goods){
                if($sdata['no'] == 1 || $goods['gtype'] == 1){
                    $tdata['goodinfos'][] = array(
                        'ProductId'         => $goods['goods_id'],
                        "tradegoodsno"      => $goods['goods_sn'],
                        'tradegoodsname'    => $goods['spec_name'] ? $goods['spec_name'] : $goods['name'],
                        //                         'tradegoodsspec' => $goods['spec_name'],
                        "goodscount"        => $is_ship ? ($goods['gtype'] == 1 ? 1 : $goods['number']) : $goods['number'],
                        'price'             => $goods['price'],
                        'Status'            => '',
                    );
                }
            }
            //备注信息
            $remark = "";
            if($order_info['flower_use']) $remark .= "【用途:{$order_info['flower_use']}】";
            if($order_info['flower_bad']) $remark .= "【忌讳:{$order_info['flower_bad']}】";
            if($sdata['no'] == 1 && $order_info['ecard']){
                $remark .= "【电子卡:{$order_info['ecard_content']}  送花人:{$order_info['ecard_name']}】";
            }
            $rdata['customerremark'] = $remark;
            $rdata['orders'][] = $tdata;
        }
        $this->write_log && \think\Log::log('PolyApi', "月配送订单返回", $rdata );
        $this->back($rdata);
    }
    
    /**
     * 返回订单状供接口使用
     * @param array $order_info 主订单
     * @param array $sdata      配送单
     * @param bool $is_ship     是否为配送单
     *  WAIT_BUYER_PAY（等待买家付款），
        WAIT_SELLER_SEND_GOODS（买家已付款），
        WAIT_BUYER_CONFIRM_GOODS（卖家已发货），
        TRADE_FINISHED（交易成功），
        TRADE_CLOSED（付款以后用户退款成功，交易自动关闭）

     */
    private function get_order_status($order_info, $sdata = array(), $is_ship = false){
        if($is_ship){
            // 0待确认 1待配送 2已配送 3已完成 10已取消
            switch ($sdata['status']){
                case 0:
                case 1:
                    return WAIT_SELLER_SEND_GOODS;
                    break;
                case 2:
                    return WAIT_BUYER_CONFIRM_GOODS;
                    break;
                case 3:
                    return TRADE_FINISHED;
                    break;
                default:
                    return TRADE_CLOSED;
                    break;
            }
        }else{
            //'订单状态状态 0待付款 1.已付款待发货 2.发货中 3.已发货 4.已完成(已付货)  10.已取消',
            switch ($order_info['order_status']){
                case 0:
                    return 'WAIT_BUYER_PAY';
                    break;
                case 1:
                case 2:
                    return 'WAIT_SELLER_SEND_GOODS';
                    break;
                case 3:
                    return 'WAIT_BUYER_CONFIRM_GOODS';
                    break;
                case 4:
                    return 'TRADE_FINISHED';
                    break;
                default:
                    return 'TRADE_CLOSED';
                    break;
            }
        }
    }
    
    /**
     * 转换订单状态
     * @param array $order_info
     * @param array $sdata
     * @param bool $is_ship
     * @return string
     */
    private function get_order_status2($order_info, $sdata = array(), $is_ship = false){
        if($is_ship){
            // 0待确认 1待配送 2已配送 3已完成 10已取消
            switch ($sdata['status']){
                case 0:
                case 1:
                    return 'JH_02';
                    break;
                case 2:
                    return 'JH_02';
                    break;
                case 3:
                    return 'JH_04';
                    break;
                default:
                    return 'JH_05';
                    break;
            }
        }else{
            //'订单状态状态 0待付款 1.已付款待发货 2.发货中 3.已发货 4.已完成(已付货)  10.已取消',
            switch ($order_info['order_status']){
                case 0:
                    return 'JH_01';
                    break;
                case 1:
                case 2:
                    return 'JH_02';
                    break;
                case 3:
                    return 'JH_03';
                    break;
                case 4:
                    return 'JH_04';
                    break;
                default:
                    return 'JH_05';
                    break;
            }
        }
    }
    
    /**
     * 被动请求返回xml
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    function reply($code, $msg, $data = array(), $root = "Order", $child = "Item"){
        $rdata = array("Result" => $code ? $code : 2);
        $rdata['Cause'] = $msg;
        $data && $rdata = array_merge($rdata,$data);
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>'.$root ? '<'.$root.'></'.$root.'>' :'');
        $this->write_log && \think\Log::log("PolyApi", "reply", $rdata);
        $this->data2xml($xml, $rdata, $child);
        ob_end_clean();
        echo($xml->asXML());
        flush();
    }
    
    /**
     * 菠萝派商城接入返回
     * @author wscsky
     */
    function back($rdata = array(), $code = 10000, $message = "SUCCESS",$subcode = ""){
        if(!is_array($rdata)) $rdata = array();
        $rdata['code'] = $code;
        $rdata['message'] = $message;
        if($rdata['code'] == 40000){
            $rdata['subcode'] = $subcode ? $subcode : 'GSE.SYSTEM_ERROR';
            $rdata['submessage'] = $message;
        }
//         \think\Log::log("PolyApi", "back", $rdata);
        echo json_encode($rdata);
        die();
    }
    

    
    /**
     * 请求验证
     * @param array $data 请求的数据
     * @return string $sign
     * @author wscsky
     */
    function auth($data){
        
        //独立商城接口方式
        if($data['uCode']){
            if($this->uCode != $data['uCode']){
                $this->error = "uCode参数错误";
                return false;
            };
            if(time() - $data['TimeStamp'] > 600){
                $this->error = "TimeStamp已失效";
                return false;
            }
            $str = "{$this->uKey}mType{$data['mType']}TimeStamp{$data['TimeStamp']}uCode{$data['uCode']}{$this->uKey}";
            if(strtoupper(md5($str) != $data['Sign'])){
                $this->error = "数据验签失败";
            }
            return true;
        }
        //菠萝派商城接口方式
        $sign = self::create_sign($data);
        if($sign !== $data['sign']){
            $this->back(array(),40000,"签名验证失败",'GSE.VERIFYSIGN_FAILURE');
        }
        return true;
    }
    
    /**
     * 生成主动请求签名
     * @param array $data 要验证名的数据
     * @author wscsky
     */
    function create_sign($data = array()){
        $str = $this->appSecret;
        foreach ($data as $key => $val){
            if($key == 'sign') continue;
            $str .= $key . $val;
        }
        $str .= $this->appSecret;
        $str  = strtolower($str);
        $sign = md5($str);
        return $sign;
    }
    
    /**
     * 数组转xml
     * @param object $xml
     * @param array $data
     * @param string $item
     */
    function data2xml(&$xml, $data, $item = 'item', $loop = 0) {
        $loop++;
        foreach ($data as $key => $value) {
            $item2 = $loop >2 ? "item" : $item;
            is_numeric($key) && $key = $item2;
            if(is_array($value) || is_object($value)){
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item2, $loop);
            } else {
                if(is_numeric($value)){
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }
    
    function getError(){
        return $this->error;
    }
    
}