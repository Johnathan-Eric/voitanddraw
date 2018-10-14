<?php
//Ajax读取数据
namespace Admin\Action;
use Think\BaseAction;
defined("THINK_PATH") or die();
class AjaxAction extends BaseAction{
	
    /**
     * 读取城市信息
     */
    function get_region(){
    
        $type 		= I("type",0,"intval");  //0读取同级，1读取子级
        $region_id	= I("id",0,"intval");	 //地区ID
        $model = D('Common/City','Logic');
        if($type == 0){
            $data = $model -> get_brother_region($region_id);
        }else{
            $data = $model -> get_son_region($region_id);
        }
        if($data){
            $data =  array_reverse($data);
            switch ($data[0]['region_type']){
                case 0:
                    $data[] = array('region_id'=>0, 'region_name'=> '国家');
                    break;
                case 1:
                    $data[] = array('region_id'=>0, 'region_name'=> '省');
                    break;
                case 2:
                    $data[] = array('region_id'=>0, 'region_name'=> '市');
                    break;
                case 3:
                    $data[] = array('region_id'=>0, 'region_name'=> '县/区');
                    break;
            }
            $data =  array_reverse($data);
        }
        $this->ajaxReturn($data,"json");
    
    }
    
	/**
	 * 中文转英文、拼音
	 * @author wscsky
	 */
	function cntoeng(){
	    $data = $_POST;
	    if(!$data['keyword']){
	        $this->ajaxReturn(array("result"=>false,"data"=>"","msg"=>"数据为空"),"json", true);
	    }
	    if($data['type'] == "py"){
	        $str = \Common\Logic\PinyinLogic::pinyin($data['keyword']);
	        if($data['lower']){
	            $str = strtolower($str);
	        }
	        if($data['killblank']){
	            $str = str_replace(" ", "", $str);
	            $str = str_replace("_", "", $str);
	        }
	    }elseif($data['type'] == 'newscode'){
	        $str = substr(strtolower(sha1($data['keyword'] . time())),0,20);
	    }else{
	        $str = \Common\Logic\PinyinLogic::cn2en($data['keyword'],$data['killblank'], $data['lower']);
	    }
	    $this->ajaxReturn(array("result"=>true,"data"=>$str),"json", true);
	}
	
	/**
	 * 读取商家信息
	 * @author wscsky
	 */
	function get_merchant(){
	    $type    = I("type",0,'intval');
	    $keyword = I("keyword","","trim");
	    $pro_id  = I("pro_id",0,'intval');
	    $city_id = I("city_id",0,'intval');
	    $limit   = I("num",10,'intval');
	    $lat     = I("lat",$this->member->lat,'trim');
	    $lng     = I("lng",$this->member->lng,'trim');
	    $data    = [];
	    if($type == 0) $this->success([]);
	    $map = array('type' => $type, 'status' => 1,);
	    if($pro_id > 0) $map['pro_id'] = $pro_id;
	    if($city_id > 0) $map['city_id'] = $city_id;
	    if($keyword) $map['name'] = array('like',"%{$keyword}%");
	
	    $field = "mid,name, logo, intro, region_name, address";
	    if($lat && $lng){
	        $field .= ",get_location({$lat},{$lng},lat,lng) as dis";
	    }else{
	        $field .= ",0 as dis";
	    }
	    $data =  M("merchant") -> where($map)
                        	    -> field($field)
                        	    -> order("dis,listorder")
                        	    -> select();
	    $this->success($data,'',true);
	}
	    
        public function goods_lists()
        {
            $request = I("request.");
            $admin = session('admin');
            if(IS_POST){
                $gbMod = D("Groupbuying");
                $page  = empty($request['page']) ? 1 : intval($request['page']);
                $limit  = empty($request['limit']) ? 10 : intval($request['limit']);
                $where = array();
                $where['b.is_online'] = array('eq',1);
                $where['b.book_stock'] = array('gt',0);
                $where['b.ds_id'] = array('eq',$admin->ds_id);
                $request['key']['keywords'] != "" && $where['b.book_name'] = array('like',"%{$request['key']['keywords']}%");
                $request['cid'] != "" && $where['b.cid'] = array('eq',$request['cid']);
                $request['bidstr'] != "" && $bidArr = explode(',', $request['bidstr']);
                //这块就是不出现已选中未开始或则正在进行中的商品不出现的过滤代码
                //start
                $gidArr = $gbMod->getGbGoodsId($admin->ds_id);
                if($bidArr && $gidArr){
                    $idsArr = array_merge($gidArr, $bidArr);
                }elseif($bidArr){
                    $idsArr = $bidArr;
                }
                if($idsArr){
                    $where['b.book_id'] = array('not in',$idsArr);
                }
                //end
                $total = $this->getCountBooksNum($where);
                if ($total <= 0) {
                    $return = array(
                        'code' => 1,
                        'msg'  => '未查询到数据',
                        'count' => 0,
                        'data' => array()
                    );
                }  else {
                    $lists = $this->searchBooks($where,$page,$limit);
                    foreach ($lists as $key=>$val){
                        $thumbArr = explode(',', $val['book_picture']);
                        $lists[$key]['thumb'] = '<img src="'.$thumbArr[0].'" style="cursor: move;width:80px;height:80px;">';
                        $lists[$key]['oper'] = "<a href=\"javascript:void(0);\" onclick=\"choose('".$val['price']."',".$val['book_id'].",'".$thumbArr[0]."','".$val['book_name']."','".$val['book_stock']."');\">选取</a>";
                    }
                    $return = array(
                        'code' => 0,
                        'msg'  => '',
                        'count' => $total,
                        'data' => $lists
                    );
                }
                echo json_encode($return);die();
            }  else {
                $this->assign('newHtml', 'yes');
                $this->display('Groupbuying/searchGoods');
            }
        }

        public function goods_pack_lists()
        {
            $request = I("request.");
            $admin = session('admin');
            if(IS_POST){
                $gbMod = D("Groupbuying");
                $page  = empty($request['page']) ? 1 : intval($request['page']);
                $limit  = empty($request['limit']) ? 10 : intval($request['limit']);
                $where = array();
                $where['b.is_online'] = array('eq',1);
                $where['b.book_stock'] = array('gt',0);
                $where['b.ds_id'] = array('eq',$admin->ds_id);
                $request['key']['keywords'] != "" && $where['b.book_name'] = array('like',"%{$request['key']['keywords']}%");
                $request['cid'] != "" && $where['b.cid'] = array('eq',$request['cid']);
                $request['bidstr'] != "" && $idsArr = explode(',', $request['bidstr']);
                //这块就是不出现已选中未开始或则正在进行中的商品不出现的过滤代码
                //start
                if($idsArr){
                    $where['b.book_id'] = array('not in',$idsArr);
                }
                //end
                $total = $this->getCountBooksNum($where);
                if ($total <= 0) {
                    $return = array(
                        'code' => 1,
                        'msg'  => '未查询到数据',
                        'count' => 0,
                        'data' => array()
                    );
                }  else {
                    $lists = $this->searchBooks($where,$page,$limit);
                    foreach ($lists as $key=>$val){
                        $thumbArr = explode(',', $val['book_picture']);
                        $lists[$key]['thumb'] = '<img src="'.$thumbArr[0].'" style="cursor: move;width:80px;height:80px;">';
                        $lists[$key]['oper'] = "<a href=\"javascript:void(0);\" onclick=\"choose('".$val['book_id']."');\">选取</a>";
                    }
                    $return = array(
                        'code' => 0,
                        'msg'  => '',
                        'count' => $total,
                        'data' => $lists
                    );
                }
                echo json_encode($return);die();
            }  else {
                $this->assign('newHtml', 'yes');
                $this->display('Groupbuying/searchGoods');
            }
        }
        
        
        public function get_goods_spec()
        {
            if(IS_POST){
                $request = I("request.");
                $specMod = D("books_spec");
                $where = array();
                $where['is_del'] = array('eq',0);
                $where['book_id'] = array('eq',$request['book_id']);
                $lists = $specMod->where($where)->select();
                $return = array(
                    'status' => 1,
                    'lists'  => $lists,
                    'msg'    => '成功'
                );
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
        }
   
        public function get_books_info()
        {
            if(IS_POST){
                $request = I("request.");
                $bookMod = D("books");
                $where = array();
                $where['b.is_del'] = array('eq',0);
                $where['b.book_id'] = array('eq',$request['book_id']);
                $info = $bookMod-> alias("b")->where($where)->join("left join ".C("DB_PREFIX")."category c on c.cat_id = b.cid ")->find();
                $return = array(
                    'status' => 1,
                    'info'  => $info,
                    'msg'    => '成功'
                );
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
        }
        
        private function getCountBooksNum($where = array())
        {
            $model = M("Books");
            $re = $model-> alias("b")
                ->field("count(*) as total")
                ->where($where)->find(); //商品总数
            return $re['total'];
        }

        private function searchBooks($where = array(), $page = 1, $limit = 10, $order=' b.book_ctime desc ')
        {
            $model = M("Books");
            $lists = $model-> alias("b")
                    ->field("b.*,c.cat_name")
                    -> join("left join ".C("DB_PREFIX")."category c on c.cat_id = b.cid ")
                    -> where($where)->page($page,$limit)->order($order)->select();
            return $lists;
        }
        
        public function ajax_goods_lists()
        {
            $request = I("request.");
            $admin = session('admin');
            $where = array();
            $where['b.is_online'] = array('eq',1);
            $where['b.book_stock'] = array('gt',0);
            $where['b.ds_id'] = array('eq',$admin->ds_id);
            $request['keywords'] != "" && $where['b.book_name'] = array('like',"%{$request['keywords']}%");
            $request['ids'] != "" && $idsArr = explode(',', $request['ids']);
            //这块就是不出现已选中未开始或则正在进行中的商品不出现的过滤代码
            //start
            if($idsArr){
                $where['b.book_id'] = array('not in',$idsArr);
            }
            //end
            $lists = $this->searchBooks($where,1,20);
            if($lists){
                $return = array(
                    'status' => 1,
                    'lists'  => $lists,
                    'msg'    => '成功'
                );
            }else {
                $return = array(
                    'status' => 2,
                    'msg'    => '未查询到可用商品'
                );
            }
            echo json_encode($return);die();
        }
        
        public function ajax_set_books()
        {
            $request = I("request.");
            $admin = session('admin');
            if(IS_POST){
                $page  = empty($request['page']) ? 1 : intval($request['page']);
                $limit  = empty($request['limit']) ? 10 : intval($request['limit']);
                $where = array();
                $where['b.is_online'] = array('eq',1);
                $where['b.book_stock'] = array('gt',0);
                $where['b.ds_id'] = array('eq',$admin->ds_id);
                $request['key']['keywords'] != "" && $where['g.name'] = array('like',"%{$request['key']['keywords']}%");
                $request['ids'] != "" && $idsArr = explode(',', $request['ids']);
                //这块就是不出现已选中未开始或则正在进行中的商品不出现的过滤代码
                //start
                if($idsArr){
                        $where['b.book_id'] = array('not in',$idsArr);
                }
                //end
                $total = $this->getCountBooksNum($where);
                if ($total <= 0) {
                    $return = array(
                        'code' => 1,
                        'msg'  => '未查询到数据',
                        'count' => 0,
                        'data' => array()
                    );
                }  else {
                    $lists = $this->searchBooks($where,$page,$limit);
                    foreach ($lists as $key=>$val){
                        $thumbArr = explode(',', $val['book_picture']);
                        $lists[$key]['thumb'] = '<img src="'.$thumbArr[0].'" style="cursor: move;width:80px;height:80px;">';
                        $lists[$key]['oper'] = "<a href=\"javascript:void(0);\" onclick=\"choose('".$val['price']."',".$val['book_id'].",'".$thumbArr[0]."','".$val['book_name']."','".$val['book_stock']."');\">选取</a>";
                    }
                    $return = array(
                        'code' => 0,
                        'msg'  => '',
                        'count' => $total,
                        'data' => $lists
                    );
                }
                echo json_encode($return);die();
            }  else {
                $this->assign('newHtml', 'yes');
                $this->display('Groupbuying/searchGoods');
            }
        }
        
        
        public function get_books_lists()
        {
            if(IS_POST){
                $request = I("request.");
                $bookMod = D("books");
                $where = array();
                $where['b.is_del'] = array('eq',0);
                $where['b.book_id'] = array('in',$request['ids']);
                $lists = $bookMod-> alias("b")->where($where)->join("left join ".C("DB_PREFIX")."category c on c.cat_id = b.cid ")->select();
                $return = array(
                    'status' => 1,
                    'lists'  => $lists,
                    'msg'    => '成功'
                );
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
        }
        
        public function search_books_lists()
        {
            $request = I("request.");
            $admin = session('admin');
            if(IS_POST){
                $page  = empty($request['page']) ? 1 : intval($request['page']);
                $limit  = empty($request['limit']) ? 10 : intval($request['limit']);
                $where = array();
                $where['b.is_online'] = array('eq',1);
                $where['b.book_stock'] = array('gt',0);
                $where['b.is_del'] = array('eq',0);
                $where['b.ds_id'] = array('eq',$admin->ds_id);
                $request['key']['keywords'] != "" && $where['b.book_name'] = array('like',"%{$request['key']['keywords']}%");
                $request['cid'] != "" && $where['b.cid'] = array('eq',$request['cid']);
                $request['bidstr'] != "" && $bidArr = explode(',', $request['bidstr']);
                if($bidArr){
                    $where['b.book_id'] = array('not in',$bidArr);
                }
                //end
                $total = $this->getCountBooksNum($where);
                if ($total <= 0) {
                    $return = array(
                        'code' => 1,
                        'msg'  => '未查询到数据',
                        'count' => 0,
                        'data' => array()
                    );
                }  else {
                    $lists = $this->searchBooks($where,$page,$limit);
                    foreach ($lists as $key=>$val){
                        $thumbArr = explode(',', $val['book_picture']);
                        $lists[$key]['thumb'] = '<img src="'.$thumbArr[0].'" style="cursor: move;width:80px;height:80px;">';
                        $val['ssid'] = $request['ssid'];
                        $lists[$key]['oper'] = "<a href=\"javascript:void(0);\" onclick=\"choose('". base64_encode(json_encode($val))."');\">选取</a>";
                    }
                    $return = array(
                        'code' => 0,
                        'msg'  => '',
                        'count' => $total,
                        'data' => $lists
                    );
                }
                echo json_encode($return);die();
            }  else {
                $this->assign('newHtml', 'yes');
                $this->display('Ajax/searchGoods');
            }
        }
        
        public function getSelCateLists()
        {
            if(IS_POST){
                $admin = session('admin');
                $request = I("request.");
                $cateMod = M("Category");
                $where = array();
                $where['ds_id'] = array('eq',$admin->ds_id);
                $where['cat_id'] = array('in',$request['cateIds']);
                $lists = $cateMod->where($where)->select();
                $return = array(
                    'status' => 1,
                    'lists'  => $lists,
                    'msg'    => '成功'
                );
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
        }
        
        public function getCateLists(){
            $admin = session('admin');
            $request = I("request.");
            if(IS_POST){
                $request = I("request.");
                $cateMod = M("Category");
                $where = array();
                //$where['ds_id'] = array('eq',$admin->ds_id);
                $request['ids'] != "" && $where['cat_id'] = array('not in',$request['ids']);
                $lists = $cateMod->where($where)->select();
                if($lists){
                    $return = array(
                        'status' => 1,
                        'lists'  => $lists,
                        'msg'    => '成功'
                    );
                } else {
                    $return = array(
                        'status' => 2,
                        'msg'    => '未查询到分类'
                    );
                }
                
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
        }
        
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function AJAX_upload(){
        $upload = D("Common/File","Logic");
        $upload -> set_config("image", "profile");
        $info = $upload -> uploadFile($_FILES['file']);
        if(!$info){
            $return = array(
                'status' => '2',
                'msg'   => "上传失败:".$upload->getError()
            );
        }else{
            $return = array(
                'status' => '1',
                'info'   => $info,
                'msg'    => "上传成功:"
            );
        }
        echo json_encode($return);
    }
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function AJAX_new_upload(){
        $upload = D("Common/File","Logic");
        $upload -> set_config("image", "profile");
        $info = $upload -> uploadFile($_FILES['file']);
        if(!$info){
            $return = array(
                'code' => '2',
                'msg'   => "上传失败:".$upload->getError()
            );
        }else{
            $return = array(
                'code' => '0',
                'data'   => array('src'=>$info['uri']),
                'msg'    => "上传成功"
            );
        }
        echo json_encode($return);
    }
    
    /**
     * ajax修改信息
     */
    public function ajax_audit_pack(){
        $admin = session('admin');
        if($admin->d_type == 1){
            $ids = I('ids');
            if(count($ids) > 0){
                $nval   = I('nval','1','int');
                $data = array();
                $model = M('Package');
                foreach ($ids as $val){
                    $data['audit_time'] = time();
                    $data['audit_status'] = $nval;
                    $model->where('pa_id = %d', $val)->save($data);
                }
                $date = array("error"=>0,"message"=>'操作成功');
            }else{
                $date = array("error"=>2,"message"=>'操作失败');
            }
        } else {
            $date = array("error"=>2,"message"=>'只有总店用户才能审核');
        }
        echo json_encode($date);die;
    }
    
    /**
     * ajax修改信息
     */
    public function ajax_audit_groupbuying(){
        $admin = session('admin');
        if($admin->d_type == 1){
            $ids = I('ids');
            if(count($ids) > 0){
                $nval   = I('nval','1','int');
                $data = array();
                $model = M('Group_buying');
                foreach ($ids as $val){
                    $data['audit_time'] = time();
                    $data['audit_status'] = $nval;
                    $model->where('id = %d', $val)->save($data);
                }
                $date = array("error"=>0,"message"=>'操作成功');
            }else{
                $date = array("error"=>2,"message"=>'操作失败');
            }
        } else {
            $date = array("error"=>2,"message"=>'只有总店用户才能审核');
        }
        echo json_encode($date);die;
    }
    
        /**
     * ajax修改信息
     */
    public function ajax_audit_coupon(){
        $admin = session('admin');
        if($admin->d_type == 1){
            $ids = I('ids');
            if(count($ids) > 0){
                $nval   = I('nval','1','int');
                $data = array();
                $model = M('Coupon');
                foreach ($ids as $val){
                    $data['audit_time'] = time();
                    $data['audit_status'] = $nval;
                    $model->where('id = %d', $val)->save($data);
                }
                $date = array("error"=>0,"message"=>'操作成功');
            }else{
                $date = array("error"=>2,"message"=>'操作失败');
            }
        } else {
            $date = array("error"=>2,"message"=>'只有总店用户才能审核');
        }
        echo json_encode($date);die;
    }
    /**
     * ajax修改信息
     */
    public function ajax_audit_eval(){
        $admin = session('admin');
        if($admin->d_type == 1){
            $ids = I('ids');
            if(count($ids) > 0){
                $nval   = I('nval','1','int');
                $data = array();
                $model = M('Orders_evaluation');
                foreach ($ids as $val){
                    $data['audit_time'] = time();
                    $data['evaluation_status'] = $nval;
                    $model->where('id = %d', $val)->save($data);
                }
                $date = array("error"=>0,"message"=>'操作成功');
            }else{
                $date = array("error"=>2,"message"=>'操作失败');
            }
        } else {
            $date = array("error"=>2,"message"=>'只有总店用户才能审核');
        }
        echo json_encode($date);die;
    }
    
    public function search_by_wheel_lists()
    {
        $request = I("request.");
        $admin = session('admin');
        if(IS_POST){
            $page  = empty($request['page']) ? 1 : intval($request['page']);
            $limit  = empty($request['limit']) ? 10 : intval($request['limit']);
            $where = array();
            $where['b.is_online'] = array('eq',1);
            $where['b.book_stock'] = array('gt',0);
            $where['b.ds_id'] = array('eq',$admin->ds_id);
            $request['key']['keywords'] != "" && $where['b.book_name'] = array('like',"%{$request['key']['keywords']}%");
            $request['cid'] != "" && $where['b.cid'] = array('eq',$request['cid']);
            $request['bidstr'] != "" && $bidArr = explode(',', $request['bidstr']);
            //这块就是不出现已选中未开始或则正在进行中的商品不出现的过滤代码
            switch ($request['type']){
                case '1':
                    $gidArr = M('Wheel_goods')->field('gid')->where(array('award_type'=>1))->select();
                    $arr = array_unique(array_column($gidArr, 'gid'));
                    break;
                case '2':
                    $gidArr = M('Integral_goods')->field('goods_id')->where(array('award_type'=>1))->select();
                    $arr = array_unique(array_column($gidArr, 'goods_id'));
                    break;
                default :
                    $gidArr = M('Integral_goods')->field('goods_id')->where(array('award_type'=>1))->select();
                    $arr = array_unique(array_column($gidArr, 'goods_id'));
                    break;
            }
            if(is_numeric($bidArr[0]) && $arr){
                $idsArr = array_merge($bidArr, $arr);
            }elseif($bidArr){
                $idsArr = $bidArr;
            }elseif($arr){
                $idsArr = $arr;
            }
            if($idsArr){
                $where['b.book_id'] = array('not in',$idsArr);
            }
            //end
            $total = $this->getCountBooksNum($where);
            if ($total <= 0) {
                $return = array(
                    'code' => 1,
                    'msg'  => '未查询到数据',
                    'count' => 0,
                    'data' => array()
                );
            }  else {
                $lists = $this->searchBooks($where,$page,$limit);
                foreach ($lists as $key=>$val){
                    $thumbArr = explode(',', $val['book_picture']);
                    $lists[$key]['thumb'] = '<img src="'.$thumbArr[0].'" style="cursor: move;width:80px;height:80px;">';
                    $lists[$key]['oper'] = "<a href=\"javascript:void(0);\" onclick=\"choose('". base64_encode(json_encode($val))."');\">选取</a>";
                }
                $return = array(
                    'code' => 0,
                    'msg'  => '',
                    'count' => $total,
                    'data' => $lists
                );
            }
            echo json_encode($return);die();
        }  else {
            $this->assign('newHtml', 'yes');
            $this->display('Wheel/searchGoods');
        }
    }
    
    public function getSelGoodsLists(){
        if(IS_POST){
            $admin = session('admin');
            $request = I("request.");
            $bookMod = M("Books");
            $where = array();
            $where['ds_id'] = array('eq',$admin->ds_id);
            $where['book_id'] = array('in',$request['goodsIds']);
            $lists = $bookMod->where($where)->select();
            $return = array(
                'status' => 1,
                'lists'  => $lists,
                'msg'    => '成功'
            );
        } else {
            $return = array(
                'status' => 2,
                'msg'    => '参数错误'
            );
        }
        echo json_encode($return);
        exit();
    }
    
    public function ajaxOrderLine(){
            $request = I("request.");
            if(IS_POST){
                $ordMod = D('Orders','Logic');
                $where = array();
                $where['order_status'] = array('in','0,1,2,3');
                $dateTime = $request['date'];
                $timeDate = array();
                $valDate = array();
                if(!empty($dateTime)){
                    $dateArr = explode(" ~ ", $dateTime);
                    $rand = strtotime($dateArr[1]) - strtotime($dateArr[0]);
                    if($dateArr[0] == date("Y-m-d", time())){
                        $beginTime = mktime(0,0,0,date("m"),date("d"),date("y"));
                        for($i = 0; $i < 24; $i++){
                            $b = $beginTime + ($i * 3600);
                            $e = $beginTime + (($i+1) * 3600)-1;
                            $timeDate[] = ($i+1).'点';
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $valDate[$i] = $ordMod->num_count($where);
                        }
                    } elseif($rand == 86400) {
                        $beginTime = strtotime($dateArr[0]);
                        for($i = 0; $i < 24; $i++){
                            $b = $beginTime + ($i * 3600);
                            $e = $beginTime + (($i+1) * 3600)-1;
                            $timeDate[] = ($i+1).'点';
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $valDate[$i] = $ordMod->num_count($where);
                        }
                    } else {
                        $beginTime = strtotime($dateArr[0]);
                        $fi = intval($rand/86400); //天数
                        for($i = 0; $i < $fi; $i++){
                            $b = $beginTime + ($i * 3600 * 24);
                            $e = $beginTime + (($i+1) * 3600 *24)-1;
                            $timeDate[] = date('Y-m-d',$b);
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $valDate[$i] = $ordMod->num_count($where);
                        }
                    }
                    //var_dump($timeDate,$valDate);exit;
                    $return = array(
                        'status' => 1,
                        'data'   => array('timeDate'=>$timeDate,'valDate'=>$valDate),
                        'msg'    => '成功'
                    );
                }else {
                    $return = array(
                        'status' => 2,
                        'msg'    => '参数错误'
                    );
                }
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
    }
    
    public function ajaxSellLine(){
            $request = I("request.");
            if(IS_POST){
                $ordMod = D('Orders','Logic');
                $where = array();
                $where['order_status'] = array('in','0,1,2,3');
                $where['pay_status'] = array('eq',1);
                $dateTime = $request['date'];
                $timeDate = array();
                $valDate = array();
                if(!empty($dateTime)){
                    $dateArr = explode(" ~ ", $dateTime);
                    $rand = strtotime($dateArr[1]) - strtotime($dateArr[0]);
                    if($dateArr[0] == date("Y-m-d", time())){
                        $beginTime = mktime(0,0,0,date("m"),date("d"),date("y"));
                        for($i = 0; $i < 24; $i++){
                            $b = $beginTime + ($i * 3600);
                            $e = $beginTime + (($i+1) * 3600)-1;
                            $timeDate[] = ($i+1).'点';
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $money = $ordMod->sum_money($where);
                            $valDate[$i] = empty($money) ? 0 : $money;
                        }
                    } elseif($rand == 86400) {
                        $beginTime = strtotime($dateArr[0]);
                        for($i = 0; $i < 24; $i++){
                            $b = $beginTime + ($i * 3600);
                            $e = $beginTime + (($i+1) * 3600)-1;
                            $timeDate[] = ($i+1).'点';
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $money = $ordMod->sum_money($where);
                            $valDate[$i] = empty($money) ? 0 : $money;
                        }
                    } else {
                        $beginTime = strtotime($dateArr[0]);
                        $fi = intval($rand/86400); //天数
                        for($i = 0; $i < $fi; $i++){
                            $b = $beginTime + ($i * 3600 * 24);
                            $e = $beginTime + (($i+1) * 3600 *24)-1;
                            $timeDate[] = date('Y-m-d',$b);
                            $where['ctime'] = array(array('gt', $b),array('lt', $e));
                            $money = $ordMod->sum_money($where);
                            $valDate[$i] = empty($money) ? 0 : $money;
                        }
                    }
                    //var_dump($timeDate,$valDate);exit;
                    $return = array(
                        'status' => 1,
                        'data'   => array('timeDate'=>$timeDate,'valDate'=>$valDate),
                        'msg'    => '成功'
                    );
                }else {
                    $return = array(
                        'status' => 2,
                        'msg'    => '参数错误'
                    );
                }
            } else {
                $return = array(
                    'status' => 2,
                    'msg'    => '参数错误'
                );
            }
            echo json_encode($return);
            exit();
    }
    
    public function get_role()
    {
        $request = I("request.");
        if($request['rid']){
            $info = M("admin_role")->field('is_agency')->where(array('rid'=>$request['rid']))->find();
            $return = array(
                'status' => 1,
                'is_agency'    => empty($info['is_agency']) ? 2 : intval($info['is_agency'])
            );
        } else {
            $return = array(
                'status' => 1,
                'is_agency'    => 2
            );
        }
        echo json_encode($return);
        die();
    }
    
    public function get_pro_region()
    {
        $Model = M('region');
        $map = array();
        $map['region_type'] = array('eq',1);
        $list  = $Model->field('region_id,region_name,region_type')->where($map)->select();
        echo json_encode($list);
        die();
    }
    
}

