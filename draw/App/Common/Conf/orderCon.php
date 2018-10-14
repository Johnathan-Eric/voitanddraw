<?php
return array(
    'orderStatus' => array(
        '0' => '待付款',
        '1' => '已完成',
        '2' => '已删除',
        '3' => '已关闭',
        '4' => '已取消'
    ),
    'payStatus' => array(
        '0' => '未支付',
        '1' => '已付款',
        '2' => '退款中',
        '3' => '已退款',
        '4' => '已拒绝'
    ),
    'refundStatus' => array(
        '1' => '待处理',
        '2' => '已处理',
        '3' => '已拒绝',
    ),
    'refundTrench' => array(
        '1' => '退回到原支付渠道',
    ),
    'areaStatus' => array(
        '1' => '省/自治区/直辖市',
        '2' => '地级市',
        '3' => '市辖区/县/县级市',
        '4' => '乡/镇/街道'
    ),
    'memType' => array(
        '1' => '员工',
        '2' => '普通用户',
        '3' => '付费用户'
    ),
    'profitType' => array( // 收益方式
        '1' => '小程序收益',
        '2' => '发展代理收益',
        '3' => '升级下级代理收益'
    ),
    'withdrawType' => array( // 提现状态
        '1' => '待审核',
        '2' => '已完成',
        '3' => '已拒绝',
        '4' => '已取消'
    ),
    'modifyType' => array( // 变动方式
        '1' => '增加',
        '2' => '减少'
    ),
);
?>