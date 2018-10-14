<?php
return array(
    'merchantMenu' => array(
        '1' =>array(
            'name' => '首页',
            'child' => array(
                '0' =>array(
                    'name' => '首页',
                    'con' => 'Index',
                    'fun' => 'main'
                ),
                '1' =>array(
                    'name' => '修改密码',
                    'con' => 'Index',
                    'fun' => 'profile'
                )
            )
        ),
        '2' =>array(
            'name' => '客户管理',
            'child' => array(
                '0' =>array(
                    'name' => '客户列表',
                    'con' => 'Client',
                    'fun' => 'index'
                )
            )
        ),
        '3' =>array(
            'name' => '订单管理',
            'child' => array(
                '0' =>array(
                    'name' => '待提交订单',
                    'con' => 'Torders',
                    'fun' => 'index'
                ),
                '1' =>array(
                    'name' => '待审核订单',
                    'con' => 'Torders',
                    'fun' => 'auditIndex'
                ),
                '2' =>array(
                    'name' => '未通过审核订单',
                    'con' => 'Torders',
                    'fun' => 'throughIndex'
                ),
                '3' =>array(
                    'name' => '欠款订单',
                    'con' => 'Torders',
                    'fun' => 'arrearsIndex'
                ),
                '4' =>array(
                    'name' => '待上线订单',
                    'con' => 'Torders',
                    'fun' => 'onlineIndex'
                ),
                '5' =>array(
                    'name' => '已上线订单',
                    'con' => 'Torders',
                    'fun' => 'beenOnlineIndex'
                ),
                '6' =>array(
                    'name' => '已完成订单',
                    'con' => 'Torders',
                    'fun' => 'completedIndex'
                ),
            )
        ),
        '4' =>array(
            'name' => '小程序管理',
            'child' => array(
                '0' =>array(
                    'name' => '商户账号管理',
                    'con' => 'Miniprogram',
                    'fun' => 'index'
                ),
                
                '1' =>array(
                    'name' => '小程序管理',
                    'con' => 'Miniprogram',
                    'fun' => 'MerchantIndex'
                ),
                /*
                '2' =>array(
                    'name' => '会员等级设置',
                    'con' => 'Member',
                    'fun' => 'group_index'
                )*/
            )
        ),
        '5' =>array(
            'name' => '分销商管理',
            'child' => array(
                '0' =>array(
                    'name' => '分销商列表',
                    'con' => 'Sale',
                    'fun' => 'Index'
                ),
                
                '1' =>array(
                    'name' => '扣款记录',
                    'con' => 'Sale',
                    'fun' => 'deductionsIndex'
                ),
                
                '2' =>array(
                    'name' => '充值记录',
                    'con' => 'Sale',
                    'fun' => 'deductionsAdd'
                ),
                '3' =>array(
                    'name' => '分销商余额',
                    'con' => 'Sale',
                    'fun' => 'balance'
                )
            )
        ),
        '6' =>array(
            'name' => '员工管理',
            'child' => array(
                '0' =>array(
                    'name' => '账号管理',
                    'con' => 'Admin',
                    'fun' => 'index'
                ),
                '1' =>array(
                    'name' => '部门管理',
                    'con' => 'Admin',
                    'fun' => 'departmentIndex'
                )
            )
        )
    ),
    'menu_level' => array(
        '1' => array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
            'name' => '超级管理员',
            'code' => 'message',
            'menu' => array(
                '1' => array(0,1),
                '2' => array(0),
                '3' => array(0,1,2,3,4,5,6),
                '4' => array(0,1),
                '5' => array(0,1,2,3),
                '6' => array(0,1),
            )
        ),
        '2' => array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
            'name' => '系统管理',
            'code' => 'message',
            'menu' => array(
                '1' => array(0,1),
                '2' => array(0),
                '3' => array(0,1,2,3,4,5,6),
                '4' => array(0,1),
                '5' => array(1,2,3),
                '6' => array(0,1),
            )
        ),
        '3' => array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
            'name' => '销售',
            'code' => 'message',
            'menu' => array(
//                 '1' => array(0,1),
                '2' => array(0),
                '3' => array(0,1,2,3,4,5,6),
//                 '4' => array(0),
//                 '5' => array(0,1),
//                 '6' => array(0,1),
            )
        ),
        '4' => array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
            'name' => '客服',
            'code' => 'message',
            'menu' => array(
//                 '1' => array(0,1),
//                 '2' => array(0),
                '3' => array(4,5),
                '4' => array(0,1),
//                 '5' => array(0,1),
//                 '6' => array(0,1),
            )
        ),
        '5' => array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
            'name' => '财务',
            'code' => 'message',
            'menu' => array(
//                 '1' => array(0,1),
//                 '2' => array(0),
                '3' => array(1,3),
//                 '4' => array(0),
//                 '5' => array(0,1),
//                 '6' => array(0,1),
            )
        ),
        '6' => array(
            'name' => '销售主管',
            'code' => 'message',
            'menu' => array(
                '1' => array(0),
                '2' => array(0),
                '3' => array(0,1,3,4,5,6),
//                 '4' => array(0),
//                 '5' => array(0,1),
                '6' => array(0),
            )
        ),
        '7' => array(
            'name' => '客服主管',
            'code' => 'message',
            'menu' => array(
                '1' => array(0),
                //                 '2' => array(0),
                '3' => array(4,5),
                '4' => array(0,1),
            //                 '5' => array(0,1),
                '6' => array(0),
            )
        ),
    ),
);

?>