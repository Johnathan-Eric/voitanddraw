<?php
/*
 * 配置管理类
 * @author xuebs
 */
namespace Admin\Action;
use Think\AdminBaseAction;

class ConfigAction extends AdminBaseAction
{
    protected $admin;

    /**
     * 构建函数
     **/
    public function _initialize()
    {
        // 管理员信息
        $this->admin = session('admin');
    }

    /**
     * 基础信息配置
     */
    public function set_baseinfo()
    {
        $member = session('member');
        $model = D("merchant");
        $add_info = $model->where('id = %d', $member->id)->find();
        $add_info['mer_logo'] = getFilePath($member->is_new, $add_info['mer_logo']);

        // 获取门店照片
        $awhere['shanghu_uid'] = array('eq', $member->id);
        $awhere['is_self'] = array('eq', 1);
        $fields = 'aiid,thumb,fid,title';
        $album = M("Album_item")->where($awhere)->field($fields)->select();
        if (!empty($album)) {
            $fids = array();
            foreach ($album as $aval) {
                if (!in_array($aval['fid'], $fids)) {
                    $fids[] = $aval['fid'];
                }
            }

            // 根据fids查询文件信息
            $fwhere['fid'] = array('in', $fids);
            $fields = 'fid,filename,filesize';
            $files = M("file_album")->where($fwhere)->field($fields)->select();
            $file_list = array();
            if (!empty($files)) {
                foreach ($files as $value) {
                    $file_list[$value['fid']] = $value;
                }
            }

            // 整理相片数据
            foreach ($album as &$alval) {
                $alval['thumb'] = getFilePath($member->is_new, $alval['thumb']);
                $alval['filename'] = $alval['filesize'] = '';
                if (isset($file_list[$alval['fid']])) {
                    $alval['filename'] = $file_list[$alval['fid']]['filename'];
                    $alval['filesize'] = round($file_list[$alval['fid']]['filesize']/1024, 1).'kb';
                }
            }
        }

        if($this->isPost())
        {
            $post = I('post.');

            // 对实体店地址进行判断
            if (!$post['province'] || !$post['city'] || !$post['district']) {
                $return = array(
                    'state' => '3',
                    'msg'   => '实体店地址不能为空！'
                );
                echo json_encode($return);die();
            }
            $cityModel = D('Common/City','Logic');
            $region_arr = array();
            $province = $cityModel->get_region($post['province']);
            $city = $cityModel->get_region($post['city']);
            $district = $cityModel->get_region($post['district']);
            $region_arr[] = $province['region_name'];
            $region_arr[] = $city['region_name'];
            $region_arr[] = $district['region_name'];
            $date = array(
                'store_name' => $post['store_name'],
                'service_phone' => $post['service_phone'],
                'mer_logo' => getSaveFilepath($member->is_new, $post['mer_logo']),
                'address'  => implode(' ', $region_arr),
                'detailed' => $post['detailed'],
                'company_name' => $post['company_name'],
                'index_url' => $post['index_url'],
                'address_x' => $post['address_x'],
                'address_y' => $post['address_y'],
                'desc'      => $post['desc']
            );
            if($post['opening_time']){
                $date['opening_time'] = $post['opening_time'];
            }
            if($post['mer_loding_logo']){
                $date['mer_loding_logo'] = getSaveFilepath($member->is_new, $post['mer_loding_logo']);
            }
            if($post['default_logo']){
                $date['default_logo'] = getSaveFilepath($member->is_new, $post['default_logo']);
            }
            if($post['list_loding_logo']){
                $date['list_loding_logo'] = getSaveFilepath($member->is_new, $post['list_loding_logo']);
            }
            $result = $model->where('id = %d', $member->id)->save($date);

            // 上传门店照片
            $imgarrs = I('imgarr');
            $new_fids = I('fids');
            $data = array();
            if (isset($imgarrs) && !empty($imgarrs)) {
                foreach ($imgarrs as $key => $img) {
                    if (isset($new_fids) && !empty($new_fids)) {
                        if (isset($new_fids[$key]) && !in_array($new_fids[$key], $fids)) {
                            $tmp = array();
                            $tmp['is_self'] = 1;
                            $tmp['shanghu_uid'] = $member->id;
                            $tmp['addtime'] = time();
                            $tmp['thumb'] = getSaveFilepath($member->is_new, $img);
                            $tmp['fid'] = isset($new_fids[$key]) ? $new_fids[$key] : 0;
                            $data[] = $tmp;
                        }
                    }
                }
            }
            if (!empty($data)) {
                M('Album_item')->addAll($data);
                $result = true;
            }

            if($result){
                $return = array(
                    'state' => '1',
                    'msg'   => '操作成功'
                );
            }else{
                $return = array(
                    'state' => '2',
                    'msg'   => '操作失败'
                );
            }
            echo json_encode($return);exit;
        }
        $reginfo = $add_info['address'];
        $reg_arr = explode(' ', $reginfo);
        $add_info['province'] = $reg_arr[0];
        $add_info['city'] = $reg_arr[1];
        $add_info['district'] = $reg_arr[2];
        $this->assign('album', $album);
        $this->assign('info', $add_info);
        if ($member->is_new) {
            $this->assign('img_http', C('IMG_HTTP_URL'));
        } else {
            $this->assign('img_http', '');
        }
        $this->assign('newHtml', 'yes');
        $this->display('set_store');
    }
}