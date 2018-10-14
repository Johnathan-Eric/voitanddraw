<?php
//文件资源及上传模块
namespace Common\Logic;

class FileLogic{

	protected $_error = null;
	protected $use_type = null;
	protected $filetype	= null;

	protected $_config = array(
		'image' => array(
		"accept" 	=> 'image/jpeg;image/png;image/gif',
                'extensions'        => array('gif','jpg','jpeg','bmp','png'),//允许的格式
                'max_size'          => '10M',			//最大上传
                'directory_type'    => 'date',			//文件目录生成方式
                'directory_format'  => 'Ymd'			//目录格式
              ),

         'video' => array(	//视频
                "accept" 	=> 'video/x-mpeg2',
                'extensions'        => array('swf','mpg','mp4'),
                'max_size'          => '1G',
                'directory_type'    => 'date',
                'directory_format'  => 'Ymd'
              ),

         'audio' => array( //音频
                "accept" 	        => 'audio/x-mpeg',
                'extensions'        => array('mp3'),
                'max_size'          => '10M',
                'directory_type'    => 'date',
                'directory_format'  => 'Ymd'
              ),
        'file' => array( //附件
                                'extensions'        => array('rar','zip','doc','xls','exe','apk','ipa'),
                                'max_size'          => '1G',
                                'directory_type'    => 'date',
                                'directory_format'  => 'Ymd'
                ),
            'pem' => array( //附件
                        'extensions'        => array('pem'),
                        'max_size'          => '5M',
                        'directory_type'    => 'date',
                        'directory_format'  => 'Ymd'
                    ),    
	);
	
	/**
	 * 读取文件列表
	 */
	function get_list($filter= array(), $page = 1, $pagelimit = 20 , &$total){
		
		$map = array();
		// 组合条件
		$filter['keyword'] && $map['uri|filename'] = array("like","%{$filter['keyword']}%");
		$filter['use_type'] && $map['use_type'] = array("eq",$filter['use_type']);
		$filter['driver'] && $map['driver'] = array("eq",$filter['driver']);
		if($filter['user_type'] && $filter['user_type']=='aid'){
			$map['aid'] = array("GT",0);
		}elseif($filter['user_type'] && $filter['user_type']=='uid'){
			$map['uid'] = array("GT",0);
		}
		$filter['status'] > -1 && $map['status'] = array('eq',$filter['status']);
		
		$model = M("file");		
		$total = $model -> where($map) -> count();
		$list  = $model -> where($map) -> order("timestamp desc") -> page($page,$pagelimit) -> select();
		return $list;
	}

	/**
	 * 读取上传配置信息
	 * @param string $filetype :上传文件类型　image图片　video:视频　audio:音频
	 * @return array()	:配置数组
	 */
	function get_config($filetype = null){
	    $filetype = $filetype ? $filetype : $this->filetype;
		if(in_array($filetype,array_keys($this->_config)))
			return $this->_config[$filetype];
		else
			return $this->_config['image'];
	}

	/**
	 * 设置上传类型
	 * @param string $filetype :上传文件类型　image图片　video:视频　audio:音频
	 * @param string 	$use_type	:文件用途归类
	 */
	function set_config($filetype = "image", $use_type = "weixin"){
		if(in_array($filetype , array_keys($this->_config)))
			$this -> filetype = $filetype;
		$this->use_type = $use_type;
	}

	/**
	 * 上传保存文件
	 * @param file 		$files 		:上传的文件对象
	 * @return array 成功返回文件信息,否则返回false
	 */
	function uploadFile($file, $driver = null){
		$member = session('member');
		$driver = $driver ? $driver :C('store_type');
		if(!$driver) $driver = 'local';
		
		if(is_null($this->filetype)){
			$this->_error = "文件类型有误!";
			return false;
		}
		//读取参数设置
		$config = $this->_config[$this->filetype];
		//保存到本地服务器
		if ($driver == 'local') {

			$upload 			= new \Think\Upload();				// 实例化上传类
			$upload->maxSize 	= parse_byte($config['max_size']) ;	// 设置附件上传大小
			$upload->exts 		= $config['extensions'];			// 设置附件上传类型
			$upload->rootPath 	= C('UPLOAD_FILE_PATH') ? C('UPLOAD_FILE_PATH') : './Uploads/';; // 设置附件上传根目录

			//依不同用途产生子目录
			$use_type = strtr($this->use_type, array('-' => ' ', '_' => ' ', '  ' => ' '));
			$use_type = strtr(ucwords($use_type), ' ', '/');

			//依设置产生目录
			switch ($config['directory_type']){
				case "date":
					$use_type .= "/".date($config['directory_format'], NOW_TIME);
					break;
				default:
					$use_type .= "/".date($config['directory_format']);
					break;
			}
			$upload->subName 	= $use_type; 						// 设置附件上传（子）目录
			
			//取得成功上传的文件信息
			$info = $upload->uploadOne($file);
			
			//保存当前数据对象
			$data['filename'] 	= $info['name'];
			$data['uri'] 		= $upload->rootPath . $info['savepath'].$info['savename'];
			$data['uid']		= isset($member->uid)? intval($member->uid):0;
			$data['aid']		= isset($member->aid)? intval($member->aid):0;
                        $data['shanghu_uid']	= isset($member->id)? intval($member->id):0;
			$data['use_type']	= $this->use_type;
			$data['filesize']	= $info['size'];
			$data['minetype']	= $info['type'];
			if (!$info) {
			    $this->_error = $upload->getError();
			    return false;
			}
		}elseif($driver == 'qiniu'){
		    $upload = new \Think\Upload(C('QINIU_SDK_CONFIG'));
		    $info = $upload->uploadOne($file);
		    //保存当前数据对象
		    $data['filename'] 	= $info['name'];
		    $data['uri'] 		= $info['url'];
		    $data['filesize']	= $info['size'];
		    $data['minetype']	= $info['type'];
		    $data['sha1']       = $info['sha1'];
		    if (!$info) {
		        $this->_error = $upload->getError();
		        return false;
		    }
		}
		else {
			//TODO 使用其它介质存储文件(ftp/socket/hdfs/gridfs等)
			$info = array();
			if (!$info) {
			    $this->_error = "上传文件失败!";
			    return false;
			}
		}


		$model = D('File');
		$data['driver']     = $driver;
		$data['uid']		= isset($member->uid)? intval($member->uid):0;
		$data['aid']		= isset($member->aid)? intval($member->aid):0;
                $data['shanghu_uid']	= isset($member->id)? intval($member->id):0;
		$data['use_type']	= $this->use_type;
		$data['timestamp'] 	= time();

		if ($model->create($data)) {
			$data['fid'] = $model->add();
			$info['uri'] = $data['uri'];
			$info['fid'] = $data['fid'];
		}
		$return = $info['fid'] ? $info : array();
		return $return;
	}
	
	/**
	 * 保存远程图文件
	 * @param string $url
	 * @return string imguri
	 * @param string $filetype
	 * @author wscsky
	 */
	function save_remote_file($url, $filetype){
		$member = session('member');
		if(is_null($this->filetype)){
			$this->_error = "文件类型有误!";
			return false;
		}
		$config = $this->_config[$this->filetype];
		//$driver = C('store_type');
		if(!$driver) $driver = 'local';
		//查文件 类型
		$ext = $filetype ? $filetype : substr($url, strrpos($url, ".")+1);
		if(!in_array($ext, $config['extensions'])) {
			return false;
		}
		
		//保存到本地服务器
		if ($driver == 'local') {
			$rootPath 	= C('UPLOAD_FILE_PATH') ? C('UPLOAD_FILE_PATH') : './Uploads/';; // 设置附件上传根目录
			//依不同用途产生子目录
			$use_type = strtr($this->use_type, array('-' => ' ', '_' => ' ', '  ' => ' '));
			$use_type = strtr(ucwords($use_type), ' ', '/');
		
			//依设置产生目录
			switch ($config['directory_type']){
				case "date":
					$use_type .= "/".date($config['directory_format'], NOW_TIME);
					break;
				default:
					$use_type .= "/".date($config['directory_format']);
					break;
			}
			$subName 	= $use_type; 	// 设置附件上传（子）目录
			//查目录是否可以用
			if($rootPath{0} == "/") $rootPath = ".".$rootPath;
			$this->check_save_path($rootPath, $subName);						
			$get_file = @file_get_contents($url);
			$filename = $subName . "/" . substr(sha1($url),10,16).".".$ext;
			
			if($get_file)
			{
				$fp = @fopen($rootPath . $filename,"w");
				@fwrite($fp,$get_file);
				@fclose($fp);
			}
			$file_uri = $rootPath . $filename;		
		}
		else {
			//TODO 使用其它介质存储文件(ftp/socket/hdfs/gridfs等)
			return false;
		}
		
		
		$model = D('File');
		
		//保存当前数据对象
		$data['filename'] 	= end(explode("/", $file_uri));
		$data['uri'] 		= trim($file_uri, '.');
		$data['uid']		= isset($member->uid)? intval($member->uid):0;
		$data['aid']		= isset($member->aid)? intval($member->aid):0;
                $data['shanghu_uid']	= isset($member->id)? intval($member->id):0;
		$data['use_type']	= $this->use_type;
		$data['filesize']	= filesize($file_uri);
		$data['minetype']	= 'image/jpeg';
		$data['timestamp'] 	= time();
		
		if ($model->create($data)) {
			$data['fid'] = $model->add();
		}
		$return = $data['fid'] ? $data['fid']  : 0;
		return $return;
	}

	/**
	 * 检测上传目录
	 * @param  string $savepath 上传目录
	 * @return boolean          检测结果，true-通过，false-失败
	 */
	public function check_save_path($rootpath, $savepath){
		/* 检测并创建目录 */
		if (!$this->mkdir($rootpath, $savepath)) {
			return false;
		} else {
			/* 检测目录是否可写 */
			if (!is_writable($rootpath . $savepath)) {
				$this->_error = '上传目录 ' . $savepath . ' 不可写！';
				return false;
			} else {
				return true;
			}
		}
	}
	
	/**
	 * 创建目录
	 * @param  string $savepath 要创建的穆里
	 * @return boolean          创建状态，true-成功，false-失败
	 */
	public function mkdir($rootpath, $savepath){
		$dir = $rootpath . $savepath;
		if(is_dir($dir)){
			return true;
		}
	
		if(mkdir($dir, 0777, true)){
			return true;
		} else {
			$this->_error = "目录 {$savepath} 创建失败！";
			return false;
		}
	}

	/**
	 * 给图片加水印
	 * @param string $fileurl
	 *
	 */
	function addwater($fileurl){

		if($fileurl{0} == "/") $fileurl = ".".$fileurl;

		if(!file_exists($fileurl)) return false;

		$type = C("image_water_type");
		if(!$type) return false;
		$water_config = C("{$type}_water_config");
		if(!$water_config) return false;

		if($water_config){
			$image 	= new \Think\Image();
			$img 	= $image->open($fileurl);
			//尺寸的限制
			if($img->width() < $water_config['min_width'] || $img->height() < $water_config['min_height'])
				return false;
			//图片水印
			if($type == "image"){
				if(C("water_imgurl") && file_exists(C("water_imgurl"))){
					$water_url = C("water_imgurl");
				}
				else{
					$water_url = $water_config['path'];
				}
				if(!file_exists($water_url)) return false;
				$img -> water($water_url, $water_config['position'], $water_config['alpha'])->save($fileurl);
				return true;
			}
			//文件水印
			$water_text = C("water_text")? C("water_text"):$water_config['text'];
			$image->text($water_text,$water_config['font'],$water_config['size'],$water_config['color'],$water_config['position'], $water_config['offset'])->save($fileurl);
			return true;

		}


		// 在图片左上角添加水印（水印文件位于./logo.png） 并保存为water.jpg
		//$img = $image->open(".".$data['filepath']);

		//->water('./water.png',\Think\Image::IMAGE_WATER_NORTHWEST)->save(".".$data['filepath']);

	}

	/*
	 * 改变上传图片状态
	 * @param $fids 	上传的文件ID[数组]
	 * @param $status   文件状态 0:未使用 1:已使用 2:已删除
	 * @param $type     文件应用类型 如:goods_img 图片 goods_gallery 商品相册等
	 * @param $id       文件应用类型ID
	 */

	function changeStatus($fids='',$status=0,$type=FILE_TYPE_GOODS,$id=0)
	{
		if(!is_array($fids)||!is_numeric($status))	return false;
		$fidStr 	= implode(',',$fids);
		$Model 	= M('file');
		$data 	= array('status'=>$status);
		$result = $Model->where("fid in({$fidStr})")->save($data);
		if($result)
		{
			return $this->addFileUse($fids,$type,$id);
		}
		return false;
	}

	/*
	 *  使用文件的插入
	 *	@param mix $fids 	上传的文件ID[文件ID/文件ID数组]
	 *  @param string $type    文件应用类型 如:news 新闻文件上传等
	 *  @param int $id      文件应用类型ID:如商品ID
	 */
	function addFileUse($fids='',$type=FILE_TYPE_NEWS,$id=0)
	{
		if($type == "" || empty($fids) || $fids == 0 || !is_numeric($id) || $id == 0) return false;
		$fidStr 	= is_array($fids) ? implode(',',$fids): $fids;
		$fileModel  = M('file');
		$file 		= $fileModel->where("fid in({$fidStr})")->field('fid')->select();
		if($file)
		{
			$fids	= array();
			$fuse	= array();
			$Model 	= M('file_use');
			foreach ($file as $key=>$value)
			{
				$fids[]			= $value['fid'];
				$tfile			= array('fid' => $value['fid'],'type' => $type,'id' => $id);
				$fuse[]			= $tfile;
				//删除之前的文件使用绑定
				$Model -> where($tfile) ->delete();
			}

			//添加文件使用表
			$result = $Model->addAll($fuse);
			//把文件设置为已使用状态
			if(!empty($fids)) $fileModel->where("fid in(%s)", implode(',',$fids))->save(array("status"=>1));
			if($result)
				return true;
			else
				return false;
		}
		return false;
	}

	/**
	 * 读取文件资料使用列表数据
	 *  @param int $id      文件应用类型ID:如商品ID
	 *  @param string $type 文件应用类型 如:goods_img 图片 goods_gallery 商品相册等
	 *  @param bool $is_all 是否返回所有数据，如果是fale只返回一条
	 *  @return array()
	 */
	function get_file_use($id='', $type = "goods_img", $is_all = true){

		if(!is_numeric($id) || empty($type)) return false;
		$map = array(
				"id" 	=> $id,
				"type"	=> array("like", $type)
		);

		$Model 	= M('file_use');
		$Model->alias('fu')
			  ->join('left join '. C('DB_PREFIX').'file as f on fu.fid=f.fid')
			  ->where($map)
			  ->field('f.fid,f.filename,f.uri,f.filesize,f.minetype,fu.type')
			  ->order('fuid');

		if($is_all)
			return $Model->select();
		else
			return $Model->find();
	}

	/**
	 * 通过fid读取文件资源
	 * @param int $fid :文件资源ID
	 * @param int $uid :会员ID
	 * @param int $aid:管理员ID
	 */
	function get_file_buyfid($fids, $uid = 0, $aid = 0, $type=''){
		if(!$fids || !is_numeric($uid) || !is_numeric($aid)) return false;
		$map['fid'] 	= array("in",(array)$fids);
		$uid > 0 && $map['uid']	= $uid;
		$aid > 0 && $map['aid'] = $aid;
		!empty($type) && $map['use_type']=$type;
	    $rs = M('file')->where($map)->select();
		return $rs;
	}

	/**
	 * 删除上传资源文件
	 * @param int $fid :文件资源ID
	 * @param int $id :应用类型ID
	 * @param int $type:应用类型
	 * @return bool
	 */
	function delete_file_buyfid($fid = 0, $id = 0 , $type = ''){
		#TODO删除指定资料文件 ，如果$id $type不为0则需要验证
		#还需要删除物理文件
		#删除file_use里的相关数据
		if(is_numeric($fid) && $fid!=0)
		{
			$map['fid'] 	= $fid;
			$id != 0 && $map['id'] = $id;
			$type != '' && $map['type'] = $type;
			$fModel 		= M('file');
			$file 			= $fModel->where($map)->find();
			$uri 			= $file['uri'];
			if($uri)
			{
				if($fModel->where($map)->delete()!==false)
				{
					$uri{0} == "/" && $uri = ".".$uri;
					unlink($uri);
					return M('file_use')->where("fid={$fid}")->delete();
				}
			}
		}
		return false;
	}
	
	/**
	 * 通过uri读取文件资源
	 * @param int $uri :文件资源ID
	 * @param int $uid :会员ID
	 * @param int $aid:管理员ID
	 */
	function get_file_buyuri($uri, $uid = 0, $aid = 0, $type=''){
	    if(!$uri)	return false;
	    $map['uri'] 	= $uri;
	    $uid > 0 && $map['uid']	= $uid;
		$aid > 0 && $map['aid'] = $aid;
		$type && $map['use_type']=$type;
		return M('file')->where($map)->find();
	}

	/**
	 *  删除文件资源
	 *  @param int $fuid	文件使用ID
	 *  @param int $id      文件应用类型ID:如商品ID
	 *  @param mix $type 	文件应用类型 如:goods_img 图片 goods_gallery 商品相册等,
	 *  @param bool $is_all 是否返回所有数据，如果是false只返回一条
	 *  @return array()
	 */
	function delete_file_use($fuid = 0, $id = 0, $type = ""){

		//当指定使用文件fuid时
		if(is_numeric($fuid) && $fuid !=0){
			$where 		= "fuid={$fuid}";
			$fuModel 	= M('file_use')->where($where);
			$file_use 	= $fuModel->find();
			$fid 		= $file_use['fid'];
			if($fuModel->delete()!==false)
			{
				//如果没有其它地方使用该文件更新该文件为未使用状态
				if($fuModel->where("fid = %d", $fid)->count() == 0){
					M('file')->where("fid={$fid}")->save(array('status' => 2));
				}
				return true;
			}
			return false;
		}
		
		//如果使用ID和类型未指定直接返回
		if($id == 0 || $type == "" || empty($id)) return false;

		$type 	= is_array($type)? implode(',',$type):$type;
		if(!empty($id)){
			if(is_numeric($id))
				$condition['id'] 	= array('eq',$id);
			else
				$condition['id'] 	= array('in',$id);
		}
		$condition['type']  = array('in',$type);
		$model 	= M('file_use');
		$fids 			= $model->where($condition)->field('fid')->select();
		if($model->where($condition)->delete()!==false)
		{
			//如果其它地方未使用文件设置为未使用状态
			foreach ($fids as $fid){
				if($model->where("fid = %d", $fid['fid'])->count() == 0){
					M('file')->where("fid={$fid['fid']}")->save(array('status' => 2));
				}
			}
		}
		return false;

	}
	
	
	/**
	 * 保存远程图文件
	 * @param string $url
	 * @return string imguri
	 * @param string $filetype
	 * @author wscsky
	 */
	function save_remote_img($url, $openid, $type = "logo"){
			$rootPath 	= RUNTIME_PATH . 'Temp/'; // 设置附件上传根目录
			//查目录是否可以用
			if($rootPath{0} == "/") $rootPath = ".".$rootPath;
			$get_file = @file_get_contents($url);
			$filename = $openid ."_". $type . ".jpg";
				
			if($get_file)
			{
				$fp = @fopen($rootPath . $filename,"w");
				@fwrite($fp,$get_file);
				@fclose($fp);
			}
			$file_uri = $rootPath . $filename;
			return $file_uri;
	}
	
	/**
	 * 保存远程图文件
	 * @param string $url
	 * @return string imguri
	 * @param string $filetype
	 * @author wscsky
	 */
	function get_member_img($url, $openid, $type = "ulogo"){
	    $dir = UPLOAD_PATH . $type ;
	    if($dir{0} == "/") $dir = ".".$dir;
	    !is_dir($dir) && mkdir($dir, 0777, true);
	    $file_uri = $dir. "/{$openid}.jpg";
	    if(file_exists($file_uri)) return $file_uri;
	    $get_file = @file_get_contents($url);
	    if($get_file)
	    {
	        $fp = @fopen($file_uri,"w");
	        @fwrite($fp,$get_file);
	        @fclose($fp);
	    }
	    return $file_uri;
	}
	
	/**
	 * 保存微信临时二维码数据
	 * @param string $url 微信文件url
	 * @return string $uri 文件路径不包含 .jpg
	 * @param string $filetype
	 * @author wscsky
	 */
	function get_weixin_qrcode($url, $uri){
		$filename = $uri.".jpg";
		if($filename{0} == "/") $filename = ".".$filename;
		$get_file = file_get_contents($url);
		if($get_file)
		{
			$fp = @fopen($filename,"w");
			@fwrite($fp,$get_file);
			@fclose($fp);
			return trim($uri.".jpg",".");
		}else{
			return false;
		}
		
	}
	
    /**
     * 删除文件
     * @param mix $fids
     */
	function delete($fids){
	    if(!$fids){
	        $this->_error = "文件参数为空";
	        return false;
	    }
	    $map = array("fid" => array("in", (array)$fids));
	    $model = M("file");
        $data = $model -> where($map) -> select();
        foreach ($data as $dd){
            switch ($dd['driver']){
                case "local":  //删除本地文件
                    $uri =  ".".ltrim($dd['uri'],".");
                    if(file_exists($uri)) unlink($uri);
                    break;
                case "qiniu": //七牛文件删除
                    D("Common/Qiniu", "Api") -> delete($dd['filename']);
                    break;
            }
        }  
        $model -> where($map) -> delete();
        return true;
	}
	
	
	/**
	 * 获取最后的错误信息
	 */
	function getError(){
		return $this->_error;
	}
        
        /**
	 * 上传保存文件
	 * @param file 		$files 		:上传的文件对象
	 * @return array 成功返回文件信息,否则返回false
	 */
	function uploadNewFile($file, $driver = null){
		if(is_null($this->filetype)){
                    $this->_error = "文件类型有误!";
                    return false;
		}
                $filePath = "./Public/pem";
                
                if($driver){
                    $pathName = $driver;
                }
                
                $path = $filePath."/".$pathName;
                if (!file_exists($path)){
                    mkdir($path); 
                }
                if($file['error'] > 0){
                    $this->_error = $file['error'];
                    return false;
                }else{
                    if (file_exists($path."/".$file["name"])){
                        rmdir($path);
                    }
                    move_uploaded_file($file["tmp_name"],$path."/".$file["name"]);
                    return array(
                        'url' => $path."/".$file["name"]
                    );
                }
	}
        
}