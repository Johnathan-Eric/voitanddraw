<?php
//文件上传

namespace Admin\Action;
use Think\Action;
class UploadAction extends Action
{
	protected $is_merchant = FALSE,$listRows;	//是否为管理员帐户
        protected $member;
        
        //初始化操作
	function _initialize(){
		$info = session('member');
                $this->member = $info;
		$this->listRows = 10;
		C('SHOW_PAGE_TRACE',false); //关闭TRACE
		//读取session＿id
		$ses_id	= I("post.SESSID")? I("post.SESSID"):session_id();
		//如果未有验证读取验证信息
		if(!$member || $member->mid == 0){
			self::_get_session($ses_id);
		}
	}

	/**
	 * 显示上传界面
	 */
	function index()
	{
		$info = session('member');
                //未登陆报错信息
                if($info->mid == 0){
			$this->redirect("Admin/User/login");
			exit();
		}
		//文件类型
		$filetype	= I("filetype","image");
		$type		= I("type","editor");
		$addwater 	= I("addwater",C("image_addwater"),"intval");
		
		//读取文件参数设置
		$config 	= D("Common/File","Logic")->get_config($filetype);
		
		//未读取参数报错
		if($config == false){
			$this->error("input_data_error");
		}
		$data['imageFileTypes'] 	= "*.".implode(";*.", $config['extensions']);	//文件类型
		
		$data['imageSizeLimit'] 	= parse_byte($config['max_size'])/1024/1024;			//最大文件
		$data['imageUploadLimit'] 	= I('get.imageUploadLimit','20','intval');	//数量
		
		$data['url'] 				= http_build_query($data);
		$data['filetype'] 			= $filetype;
		$data['aids'] 				= I('aids');
		
		$this->assign('my_file_num',M('file')->count());
		$this->assign('no_use_files',M('file')->where('status=0')->count());
		$this->assign("type", $type);
		$this->assign('data', $data);
		$this->assign("addwater", $addwater);
		$this->display();
	}

	/**
	 * 文件上传操作
	 * @param string $method	:上传类型 goods
	 */
    public function _empty($method) {
    	//未登陆报错信息
    	if($this->member->aid == 0){
    		$this->ajaxReturn(0, "未登陆或者登陆超时,请重新登陆！", 0);
    		exit();
    	}

    	$filetype	= I("filetype","image");
    	$upload 	= D("Common/File","Logic");
    	$addwater	= I("addwater", 0);
    	$thumb		= I("thumb","","trim");
    	$driver     = I('driver',null,'trim');

    	$upload -> set_config($filetype, $method);

    	$info = $upload -> uploadFile($_FILES['filedata'],$driver);

    	if($info === false){
    	    $this->ajaxReturn(0, $upload->getError(), 0);
    	}else{

    	    $data['fid']         = $info['fid'];
    	    $data['filepath']    = $info['uri'];

    	    $data['thumb']    	 = D("Common/Thumb","Logic")->thumb($info['uri'], "thumb");
    	    $data['thumb']		 = ltrim($data['thumb'],".");
    	    $data['fileext']     = $info['ext'];
    	    $data['filename']    = $info['name'];
    	    $data['filesize']    = $info['size'];
    	    $data['isimage']     = stripos($filetype,"image") !== false? 1:0;
    	    $data['aids']		 = I('aids');
    	   // if($method != "editor"){
    	   //	$data['filepath'] = $data['thumb'];
    	   // }
    	    //添加水印
    	    if($addwater && $data['isimage'] ){
    	    	$thumb && D("Common/Thumb","Logic")->thumb($info['uri'],$thumb,true, true);
    	     	$upload ->addwater($data['filepath']);
    	    }

    	    $returndata['data']  		= $data;
    	    $returndata['status']      	= 1;
    	    $returndata['info']        	= L("upload_ok");

    	    $this->ajaxReturn($returndata);
    	}
    }

    /**
     * 读取session身份验证
     */
  private function _get_session($ses_id){
    	$member = session('member');

    	if(empty($ses_id)){
    		$member = default_anonymous_user();
    		$this->member = $member;
    		return;
    	}
    	//过滤安全字符
    	$ses_id = mysql_real_escape_string($ses_id);

    	//读取session
    	$model 	= M("sessions");
    	$model 	-> where("ses_id = '{$ses_id}' OR (ssl_id = '{$ses_id}' AND ses_id = '' AND uid = 0 AND aid = 0 AND mid = 0)")
    			-> order("session_time DESC");
    	$result = $model ->find();
    	if($result){
    		$member = (object)$result;
    		//加载用户
    		$model 	= D("Merchant","Logic");
    		$result = $model -> get_info($member->mid);
    		if($result){
                        $result['mid'] = $result['id'];
    			$member = (object)$result;
    			$this -> is_merchant = true;
    			}
    		else
    			$member = default_anonymous_user();
    		
    	}else{
    		$member = default_anonymous_user();
    	}
    	$this->member = $member;
    }
    /**
     * 显示图片图库
     */
    public function filelist() {
    	
    	$count 		= 0;
    	$status 	= I('status',I("get.status"));
    	$aids 	    = I('aids',I('get.aids','editor'));
    	//$minetype 	= in_array(I('get.minetype'),array('image','audio'))?I('get.minetype'):'application';
    	$use_type   = I('use_type',I('get.use_type','editor'));

		$map['status']   = array('eq',$status);
		//$map['minetype'] = array('like',$minetype.'%');
		//$map['use_type'] = array('eq',$use_type);
		//$map['aid']		 = $this->member->aid;
    	$count 		= M('file')->where($map)->count();
		$page 		=I('get.p');
    	$page < 1 && $page = 3;
    	$Page = new \Think\Page($count, $this->listRows);
    	$Page = page_config($Page);

    	$show = $Page->show();
    	$show = page2ajax($show, "ajax_load(this,'".I("get.inputid")."')");
    	$list = M('file')->where($map)->page("$page,$this->listRows")->order('fid desc')->select();
    	
    	$type = M('file')->where($map)->field('use_type')->group('use_type')->order('fid')->select();
    	$this->assign('page',$show);
    	$this->assign('type',$type);
    	$this->assign('list', $list);
    	$this->assign('aids',$aids);
    	$this->assign('getList',I('get.'));
    	$this->display();
    }
    /*
     * 清除未使用的图片
     */
    public function cleanfile()
    {
    	$minetype 		 = in_array(I('minetype'),array('image','audio'))?I('minetype'):'application';
    	$map['status']   = 0;
    	$map['aid']		 = $this->member->aid;
    	//$map['minetype'] = array('like',$minetype.'%');
    	$data['status']  = 2;
    	$result 		 = M('file')->where($map)->save($data);
    	if($result!==false)
    	{
    		$this->ajaxReturn(array("status"=>true,"msg"=>"清理成功","callback"=>"location.reload()"),"JSON",true);
    	}
    	else
    	{
    		$this->ajaxReturn(array("status"=>false,"msg"=>"清理失败","callback"=>"location.reload()"),"JSON",true);
    	}
    }

    
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function iframe(){
    	//未登陆报错信息
    	if($this->member->aid == 0){
    		echo '请选择登陆';
    		exit();
    	}
    	$upload 	= D("Common/File","Logic");
    	$preset = $this->_preset();
    	
    	if(IS_POST){
    		$thumb		= I("request.thumb","",'trim');
    		$driver     = I('driver',null,'trim');
    		$utype		= I("utype",'images');
    		$filetype	= I("filetype","image","trim");
    		$jsfun      = I('fun','upload_ok','trim');
    		
    		if($preset[$utype]['filetype']){
    			$filetype = $preset[$utype]['filetype'];
    		}
    		
    		$upload -> set_config($filetype, $utype);
    		
    		$info = $upload -> uploadFile($_FILES['filedata'], $driver);
    		if(!$info){
    			$this->assign("errmsg", "上传失败:".$upload->getError());
    		}else{
    			if($filetype == "image"){
    				//处理原图
    				$thumb && D("Common/Thumb","Logic")->thumb($info['uri'], $thumb,true,true);
    				$info['thumb']    	 = D("Common/Thumb","Logic")->thumb($info['uri'], "200x200");
    				$info['thumb']		 = ltrim($info['thumb'],".");
    			}
    			$info['filetype'] = $filetype;
    			$info['name'] = $utype ? L($utype) : $info['filename'] ;
    			$info['time'] = date("Y-m-d H:i",time());
    			$info['did']  = I("did","","trim");
    			$this->assign("json_data", my_json_encode($info));
    			$this->assign("jsfun", $jsfun);
    			$this->assign("data", $info);
    		}
    	}else{
    	    $utype		= I("utype",'images');
    	    $filetype	= I("filetype","image","trim");
    	    $upload -> set_config($filetype, $utype);
    	}
    	$cfg = $upload->get_config();
    	$this->assign("accept", $cfg['accept']);
    	$title = I("request.title","上传文件","trim");
    	$fields = I("request.");
    	unset($fields['title']);
    	if($preset) unset($fields['utype']);
    	$this -> assign("selects", $preset);
    	$this -> assign("fields", $fields);
    	$this -> assign("title", $title);
    	$this -> display();
    }
    
    /**
     * 删除文件
     */
    function delfile(){
        $fid = I("fid",0,'intval');
        $uri = I("uri",'','trim');
        $model = D("Common/File", "Logic");
        if($fid){
            $data  =  $model -> get_file_buyfid($fid);
            $data = $data[0];
        }else{
            $data  =  $model -> get_file_buyuri($uri);
        }
        if($data['status'] != 1){
            $uri =  ".".ltrim($data['uri'],".");
            if(file_exists($uri)) unlink($uri);
            M("file")->delete($data['fid']);
        }
        echo 'success';
    }

    /**
     * 读取一些预定义数据
     * @author wscsky
     */
    private function _preset(){
    	$preset = I("preset");
    	switch ($preset){
    	
    	}
    }
    
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function AJAX_upload(){
    	$info = session('admin');
    	//未登陆报错信息
    	if($info->id == 0){
            $return = array(
                'status' => '2',
                'msg'   => '请重新登陆'
            );
        }else{
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
        }
        echo json_encode($return);
    }

    /**
     * 文件上传
     * @author xuebs
     * @since 2018-05-22
     **/
    function AJAX_upload_new() {
        // 接收参数
        $request = I("request.");
        $table = isset($request['table']) ? $request['table'] : null;
        $driver = isset($request['driver']) ? $request['driver'] : null;
        $utype = isset($request['utype']) ? $request['utype'] : 'profile';

        // 商户信息
        $info = session('member');
        //未登陆报错信息
        if($info->mid == 0){
            $return = array(
                'status' => '2',
                'msg'   => '请重新登陆'
            );
        }else{
            $upload = D("Common/File","Logic");
            $upload -> set_config("image", $utype);
            $info = $upload -> uploadFile($_FILES['file'], $driver, $table);
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
        }
        echo json_encode($return);
    }
    
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function AJAX_pem_upload(){
    	$info = session('member');
    	//未登陆报错信息
    	if($info->mid == 0){
            $return = array(
                'status' => '2',
                'msg'   => '请重新登陆'
            );
        }else{
            $upload = D("Common/File","Logic");
            $upload -> set_config("pem", "profile");
            $info = $upload -> uploadNewFile($_FILES['file'],$info->id);
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
        }
        echo json_encode($return);
    }
    
    /**
     * 通过iframe上传文件
     * @author wscsky
     */
    function layedit_upload(){
        $member = session('member');
    	//未登陆报错信息
    	if($member->mid == 0){
            $return = array(
                'status' => '2',
                'msg'   => '请重新登陆'
            );
        }else{
            $upload = D("Common/File","Logic");
            $upload -> set_config("image", "profile");
            $info = $upload -> uploadFile($_FILES['file']);
            if(!$info){
                $return = array(
                    'code' => '2',
                    'msg'   => "上传失败:".$upload->getError()
                );
            }else{
                $info['uri'] = getFilePath($member->is_new, $info['uri']);
                $return = array(
                    'code' => '0',
                    'data'   => array(
                        "src" => $info['uri']
                    ),
                    'msg'    => "上传成功:"
                );
            }
        }
        echo json_encode($return);
    }
    
}