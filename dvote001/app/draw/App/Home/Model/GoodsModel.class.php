<?php
/**
 * 商品模型
 * autho @big dog
 **/
namespace Home\Model;
use Think\Model\RelationModel;
class GoodsModel extends RelationModel {
    /**
     * 获取商品列表
     * @param array $where 查询条件
     * @param string $fileds 查询字段
     * @param int $page 第几页 -1 查询所有商品
     * @param int $listRows 每页条数
     * @param string $order 排序
     * @return array 返回数据
     **/
    public function getList($where, $fields = '*', $page = 1, $listRows = 10, $order = 'gid desc') {
        $where['online'] = array('eq', 1); // 上架
        $where['is_del'] = array('eq', 0); // 未删除
        if ($page == 0) {
            $data = M('Goods')->where($where)->field($fields)->order($order)->select();
        } else {
            $data = M('Goods')->where($where)->field($fields)->page($page, $listRows)->order($order)->select();
        }
        return $data;
    }

    /**
     * 获取商品详情
     * @param int $gid 商品ID
     * @param string $fields 查询字段
     * @return array 返回数据
     **/
    public function getInfo($gid, $fields = '*') {
        return M('Goods')->where(array('gid' => array('eq', $gid)))->field($fields)->find();
    }
}