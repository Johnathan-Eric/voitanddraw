<?php
/*
* 自定义标签
* @author wscsky<wscsky@qq.com>
*/
namespace Think\Template\TagLib;
use Think\Template\TagLib;

defined('THINK_PATH') or exit();

class DIY extends TagLib {

	// 标签定义
	protected $tags = array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
     	'thumbs'    => array('attr'=>'uri,width,height,id,class,alt,type,presets,noimg', 'close' => 0),
      	'upload'    => array('attr'=>'name, container, limit, description, types, file_type, file','close' => 0),
		'upimg'  	=> array('attr'=>'id,class,width,height,src,value,replace,title,maxnum,loadjs,use_type','close'=> 0),
		'colorPanel'=> array('attr'=>'id,src,value','close'=> 0),
		'districts' => array('attr'=>'id,name,uri,pid,region_id,region_name,province,city,county','close' => 0),
		'districts2' => array('attr'=>'id,name,uri,pid,region_id,region_name,province,city,county','close' => 0),
	    'districts3' => array('attr'=>'id,name,uri,pid,region_id,region_name,province,city,county','close' => 0),
		'city'      => array('attr'=>'id,name,uri,pid,region_id,order,is_com,py','level'=> 3),
	    'tags'      => array('attr'=>'id,name,uri,order,type','level'=> 3),
	    'block'     => array('attr'=>'blockid,pos,key,id','close'=>0),
	    'slide'     => array('attr'=>'flashid,key,mod,id','close'=>0),
	    'list'      => array('attr'=>'name,rnd,skip,field,limit,order,cat_id,nocat_id,wx_id,posid,where,sql,key,page,mod,noid,id,ids,status,day,attr,tagid,region_id','level'=>3),
	    'pre'       => array('attr'=>'blank,msg,type,cat_id,id','close'=>0),
	    'next'      => array('attr'=>'blank,msg,type,cat_id,id','close'=>0),
	    'cate'		=> array('attr'=>'type,ids,noids,self,hot,com,level','level'=>3),
	    'pos'       => array('attr'=>'type,cat_id,region_id,space','close'=>0),
	);
	
	/**
	 * 读取上一个[微信、新闻]
	 * @param unknown $attr
	 * @param unknown $content
	 * @return string
	 */
	
	public function _pre($tag,$content) {
	    $cat_id = !empty($tag['cat_id'])   ?  $tag['cat_id'] : 0;
	    $msg    = !empty($tag['msg'])      ?  $tag['msg'] : '没有了';
	    $target = !empty($tag['blank'])    ?  ' target="_blank" ' :'';
	    $type   = !empty($tag['type'])     ?  $tag['type'] : CATE_TYPE_WEIXIN;
	    
	    $map = "array(";
    	if($tag['id']) 		$map .= '"id" => '.parse_tplvar($tag['id']).',';
    	if($tag['cat_id']) 	$map .= '"cat_id" => '.parse_tplvar($tag['cat_id']).',';
    	if($tag['target']) 	$map .= '"target" => '.parse_tplvar($target).',';
    	if($msg) 	$map .= '"msg" => '.parse_tplvar($msg).',';
    	if($tag['type']) 	$map .= '"type" => '.parse_tplvar($type).',';
    	$map  = trim($map,",");
    	$map .=")";
    	$parsestr = "<?php echo D('Home/Link','Logic') -> get_pre({$map}); ?>";
    	return $parsestr;
	}
	
	public function _next($tag,$content) {
	    $cat_id = !empty($tag['cat_id'])   ?  $tag['cat_id'] : 0;
	    $msg    = !empty($tag['msg'])      ?  $tag['msg'] : '没有了';
	    $target = !empty($tag['blank'])    ?  ' target="_blank" ' :'';
	    $type   = !empty($tag['type'])     ?  $tag['type'] : CATE_TYPE_WEIXIN;
	    
	
	     
	    $map = "array(";
	    if($tag['id']) 		$map .= '"id" =>      '.parse_tplvar($tag['id']).',';
	    if($tag['cat_id']) 	$map .= '"cat_id" =>  '.parse_tplvar($tag['cat_id']).',';
	    if($tag['target']) 	$map .= '"target" =>  '.parse_tplvar($target).',';
	    if($msg) 	$map .= '"msg" =>     '.parse_tplvar($msg).',';
	    if($tag['type']) 	$map .= '"type" =>    '.parse_tplvar($type).',';
	    $map  = trim($map,",");
	    $map .=")";
	    $parsestr = "<?php echo D('Home/Link','Logic') -> get_next({$map}); ?>";
	    return $parsestr;
	}
	
	/**
	 * 城市标签
	 * 读取方法<DIY:city id='r' key='' order='' ...>html</DIY:city>
	 * @param arrty $tag
	 * @param html $content
	 * @author wscsky
	 */
	
	function _city($tag,$content){
	    $id     = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
	    $key    = !empty($tag['key'])?$tag['key']:'i';
	    $mod    = isset($tag['mod'])?$tag['mod']:'2';
	    
	    $order  = isset($tag['order']) ? $tag['order']:'listorder desc';
	    $is_com = isset($tag['is_com']) ? intval($tag['is_com']) : ""; 
	    $limit  = isset($tag['limit']) ? $tag['limit']: '10';
	    $field  = isset($tag['field']) ? $tag['field']:'*';
	    $where = ' 1 = 1 ';
	    	    
	    if($is_com == "1"){
	    	$where .= " and is_com = 1 ";
	    }
	    
	   if(substr($tag['type'],0,1)=='$') {
	        $where .= ' and region_type = '.$tag["type"];
	    }elseif(is_numeric($tag['type'])){
	        $where .= " and region_type = ".intval($tag['type']);
	    }else{
	        $type   = $this->tpl->get($tag['pid']);
	        if($type) $where .= " and region_type in({$type})";
	    }
	    
	    if(substr($tag['pid'],0,1)=='$') {
	        $where .= ' and parent_id = ".'.$tag["pid"].'."';
	    }elseif(is_numeric($tag['pid'])){
	        $where .= " and parent_id = ".intval($tag['pid']);
	    }else{
	        $pid   = $this->tpl->get($tag['pid']);
	        if($pid) $where .= " and parent_id = ".intval($pid);
	    }
	    
	    if(!empty($tag['py'])){
	        if(substr($tag['py'],0,1)=='$') {
	            $where .= ' and region_py like \'".'.$tag["py"].'."%\'';
	        }else{
	            $where .= ' and region_py like \''.$tag["py"].'%\'';
	        }
	        
	    }
	    
	    $sql  = "M(\"region\")->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
	    
	    //下面拼接输出语句
	    $parsestr  = '';
	    $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
	    $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
	    $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
	    $parsestr .= $content;//解析在article标签中的内容
	    $parsestr .= '<?php endforeach; endif;?>';
	    return  $parsestr;
	}
	
	/**
	 * 热门标签
	 * 读取方法<DIY:tags id='r' key='' order='' ...>html</DIY:tags>
	 * @param arrty $tag
	 * @param html $content
	 * @author wscsky
	 */
	
	function _tags($tag,$content){
	    $id     = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
	    $key    = !empty($tag['key'])?$tag['key']:'i';
	    $mod    = isset($tag['mod'])?$tag['mod']:'2';
	     
	    $order  = isset($tag['order']) ? $tag['order']:'weight desc,hitnum desc';
	    $limit  = isset($tag['limit']) ? $tag['limit']: '10';
	    $field  = isset($tag['field']) ? $tag['field']:'*';
	    $where = ' 1 = 1 ';
	     
	    if(substr($tag['type'],0,1)=='$') {
	        $where .= ' and type = '.$tag["type"];
	    }elseif(!empty($tag['type'])){
	        $where .= " and type = '".$tag['type']."'";
	    }

	    $sql  = "M(\"tags\")->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
	    
	    //下面拼接输出语句
	    $parsestr  = '';
	    $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
	    $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
	    $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
	    $parsestr .= $content;//解析在article标签中的内容
	    $parsestr .= '<?php endforeach; endif;?>';
	    return  $parsestr;
	}
	
	/**
	 * 读取面包屑
	 * 方式<DIY:block pos='footer'/>
	 * @param array $attr
	 * @return html
	 * @author wscsky
	 */
	
	public function _block($tag) {
	    $pos    = !empty($tag['pos'])  ?   $tag['pos']     : '';
	    return D('Common/Block',"Logic")->read($pos);
	}
	
	/**
	 * 读取分类信息标签
	 * 使用方法 <DIY:cate type='' com=''>
	 * @param array $tag
	 * @param string $content
	 * @return string
	 * @author wscsky
	 */
	public function _cate($tag,$content) {
	
		$id     = !empty($tag['id'])   ? $tag['id']:'r';  //数据输出时的变量
		$type   = !empty($tag['type']) ? $tag['type']: CATE_TYPE_WEIXIN;
		$key    = !empty($tag['key'])  ? $tag['key']:'i';
		$page   = !empty($tag['page']) ? '1' : '0';
		$mod    = isset($tag['mod'])   ? $tag['mod']:'2';
		$cache	= isset($tag['cache']) ? $tag['cache']: 1;
		$limit  = isset($tag['limit']) ? parse_tplvar($tag['limit']): '0';
		
		$map = "array(";
	 	//分类类型
        if($type){
            $map .= '"type" => '.parse_tplvar($tag['type']).',';
        }
        //父级类
        if($tag['pid']){
            $map .= '"pid" => '.parse_tplvar($tag['pid']).',';
        }
		//推荐
		if(is_numeric($tag['com'])){
			$map .= '"com" => \''.$tag['com'].'\',';
		}
		//热门
		if(is_numeric($tag['hot'])){
		    $map .= '"hot" => \''.$tag['hot'].'\',';
		}
		//层级
		if(is_numeric($tag['level'])){
			$map .= '"level" => \''.$tag['level'].'\',';
		}
		$map .= '"cache" => \''.$cache.'\',';
		$map  = trim($map,",");
		$map .=")";
		
		//下面拼接输出语句
		$parsestr  = '';
		$parsestr .= '<?php  $_result=D("Cate","Logic")->getcate('.$limit.','.$map.'); if ($_result): $'.$key.'=0;';
		$parsestr .= 'foreach($_result as $key=>$'.$id.'):';
		$parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
		$parsestr .= $content;//解析在article标签中的内容
		$parsestr .= '<?php endforeach; endif;?>';
		return  $parsestr;
		
	}

	/**
	 * 读取数据列表[微信,文章]
	 * 使用方法<DIY:list  >html</IDY:list>
	 * @param array $tag
	 * @param string $content
	 * @return string
	 * @author wscsky
	 * 
	 */
	
	public function _list($tag, $content) {
	    
	    $id     = !empty($tag['id'])   ? $tag['id']:'r';  //数据输出时的变量
	    $type   = !empty($tag['type']) ? $tag['type']: CATE_TYPE_GOODS;
	    $key    = !empty($tag['key'])  ? $tag['key']:'i';
	    
	    $mod    = isset($tag['mod'])   ? $tag['mod']:'2';
	    $limit  = isset($tag['limit']) ? parse_tplvar($tag['limit']): '10';
	    $tag['status'] = isset($tag['status']) ? intval($tag['status']) : '1';
	    //条件处理
	    $map = "array(";
	    $skip_key = array('limit','key','id','type','mod');
	    foreach ($tag as $tkey => $tval){
	        if(in_array($tkey, $skip_key)) continue;
	        if($tval !== ''){
	            $map .= '"'.$tkey.'" => '.parse_tplvar($tval).',';
	        }
	    }
	    //数据类型处理
	    switch ($type){
	        case CATE_TYPE_GOODS:
	        case CATE_TYPE_NEWS:
	        case CATE_TYPE_STORE:
	        case 'data':
	            break;
	        default:
	            return '';
	            break;
	    }
	    $map  = trim($map,",");
	    $map .= ")";
	    //下面拼接输出语句
	    $parsestr  = '';
	    if($tag['page']){
	        $parsestr .= '<?php global $pages;$_result=D("Home/List","Logic")->'.$type.'('.$limit.','.$map.',$pages); if ($_result): $'.$key.'=0;';
	    }
	    else{
	        $parsestr .= '<?php  $_result=D("Home/List","Logic")->'.$type.'('.$limit.','.$map.'); if ($_result): $'.$key.'=0;';
	    }
	    $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
	    $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
	    $parsestr .= $content;//解析在article标签中的内容
	    $parsestr .= '<?php endforeach; endif;?>';
	    return  $parsestr;
	}
	
  	/**
     * 缩略图显示标签解析
     * 格式 <DIY:thumbs uri="" alt=""  width="" height="" id="" class="" type="" presets ="" noimg="" />
     * 格式说明：uri:从数据库取出的图片地址
     * width:img标签宽度 height:img标签高度 id class  type: 图片类型，商品，店铺，默认为商品 , goods,,shop，  presets :为缩略图类型，在文件中配置 ，noimg:是否需要img标签
     * @param string $attr 标签属性
     * @return string|void
     */
    public function _thumbs($tag) {
      $uri           = !empty($tag['uri']) ? $tag['uri'] : '"uri"';     //TODO,设置一个默认图片
      $width         = !empty($tag['width']) ? $tag['width'] : '';      // 宽度
      $height        = !empty($tag['height']) ? $tag['height'] : '';    //高度
      $id            = !empty($tag['id']) ? $tag['id'] : '';            //ID
      $class         = !empty($tag['class']) ? $tag['class'] : '';      //CLASS
      $alt           = !empty($tag['alt']) ? $tag['alt'] : '"goods"';   //ALT
      $type          = !empty($tag['type']) ? $tag['type'] : 'good';    //图片类型
      $presets       = !empty($tag['presets']) ? $tag['presets'] : 'thumb'; //生成的图片大小类型 在文件中配置
      $fileroot      = "."; //C('UPLOAD_FILE_PATH') ? C('UPLOAD_FILE_PATH') : '/Uploads/';
      $img		 	 = empty($tag['noimg']) ? true : false; //是否需要生成img标签

      $alt{0} !="$" && $alt = "$alt";

      $parseStr    = '<?php $uri = '.$uri.';?>';
      $parseStr   .= '<?php $width = "'.$width.'";?>';
      $parseStr   .= '<?php $height = "'.$height.'";?>';
      $parseStr   .= '<?php $alt = '.$alt.';?>';
      $parseStr   .= '<?php $id = "'.$id.'";?>';
      $parseStr   .= '<?php $class = "'.$class.'";?>';
      $parseStr   .= '<?php $fileroot = "'.$fileroot.'";?>';
      $parseStr   .= '<?php $presets = "'.$presets.'";?>';
      $parseStr   .= '<?php if(empty($uri) or !file_exists($fileroot . $uri)) { ?>';

      $parseStr   .= '<?php $type = "'.$type.'";?>';
      $parseStr   .= '<?php  switch($type)
                      {case "good":$uri = C("site_goods_default_uri");break;
                       case "shop":$uri = C("site_shop_default_uri"); break;
                       default:$uri = C("site_goods_default_uri"); break;}?>';
      $parseStr   .= '<?php } ?>';
      $parseStr   .= '<?php if(empty($uri) or !file_exists($fileroot . $uri)) { ?>';
      $parseStr   .= '<?php $uri = "__IMG__/no.jpg" ;?>';
      $parseStr   .= '<?php $thumb_uri = $uri;?>';
      $parseStr   .= '<?php } else {?>';
      $parseStr   .= '<?php $thumb_uri = D("Common/Thumb","Logic")->thumb("{'.$uri.'}", "'.$presets.'");?>';
      $parseStr   .= '<?php } ?>';
      if($img){
      	$parseStr   .= '<img src="<?php echo $thumb_uri; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"
                      class="<?php echo $class; ?>" id="<?php echo $id; ?>" alt="<?php echo $alt;?>" />';
      }
      else{
      	$parseStr   .= '<?php echo $thumb_uri;?>';
      }
      return $parseStr;
    }

    /**
     * 读取当前位置
     * @param array $tag
     * @return string
     * @author wscsky
     */
    public function _pos($tag, $content) {
        $space		= !empty($tag['space']) ? $tag['space'] : C('pos_split');
        $type       = !empty($tag['type'])  ? $tag['type'] : 'cate';
        //有预定设置
        if(in_array(strtolower($type), array_keys(C('POS_PRESET')))){
            $html       = L('YOURPOS') ." <a href='".C('site_url')."'>".L("HOME")."</a>";
            $data       = C('POS_PRESET');
        	foreach ($data[$type] as $tag => $url){
        		$html .=" $space <a href='{$url}'>{$tag}</a>";
        	}
        	return $html;
        }
        //地址显示
        if($type == "city"){
            $parsestr  = '<?php $parsestr = "'.L('YOURPOS') ." <a href='".C('site_url')."'>".L("HOME")."</a>".'";';
            $parsestr .= '$_data = C("POS_PRESET"); $parsestr .= " '.$space.' <a href=\'".U("/province")."\'>".L("REGION_CAT")."</a>";';
            $parsestr .= '$_result = D("City","Logic")->get_pos('.$tag['region_id'].');';
            $parsestr .= 'foreach($_result as $_region):';
            $parsestr .= '$_region_act = C("REGION_ACT"); $parsestr .=" '.$space.' <a href=\'".U($_region_act[$_region[\'region_type\']].$_region[\'region_pyall\'])."\'>".$_region[\'region_name\']."</a>";?>';
            $parsestr .= '<?php endforeach;echo $parsestr;?>';
           return $parsestr;
        }
        //分类
        if($type == "cate"){
            $parsestr  = '<?php $parsestr = "'.L('YOURPOS') ." <a href='".C('site_url')."'>".L("HOME")."</a>".'";';
            $parsestr .= '$_result = D("Cate","Logic")->get_pos('.$tag['cat_id'].');';
            $parsestr .= 'if($_result[0]["type"] == "weixin"){ $parsestr .= " '.$space.' <a href=\'".U("/weixin")."\'>".L("weixin")."</a>";}';
            $parsestr .= 'foreach($_result as $_cat):';
            $parsestr .= '$parsestr .=" '.$space.' <a href=\'".U(($_cat["type"]=="weixin" ? "/weixin" : "")."/".$_cat["cat_code"])."\'>".$_cat[\'cat_name\']."</a>";?>';
            $parsestr .= '<?php endforeach;echo $parsestr;?>';
            return $parsestr;
        }
        
        //标签
        if($type == "tags"){
            $parsestr = '<?php $_tagtype= $tags["type"]? $tags["type"] : "'.$tag['tagtype'].'";';
            $parsestr .= ' $parsestr = "'.L('YOURPOS') ." <a href='".C('site_url')."'>".L("HOME")."</a>".'";';
            $parsestr .= '$parsestr .=\' '.$space.' <a href="\'.U("/tags/".$_tagtype).\'">\'.L("TAG_".$_tagtype).\'</a>\';';
            $parsestr .= 'if($tags["type"]){ $parsestr .=" '.$space.' <a href=\'".U("/tags/".$tags["type"]."/".$tags["url"])."\'>".$tags[\'name\']."</a>";} ?>';
            $parsestr .= '<?php echo $parsestr;?>';
            return $parsestr;
        }
        
        //搜索 
        if($type == "search"){
        	$parsestr  = '<?php $_schtype = ($search["type"] == 1) ? "相关微信公众号" : "相关微信文章";';
        	$parsestr .= ' $parsestr = "'.L('YOURPOS') ." <a href='".C('site_url')."'>".L("HOME")."</a>".'";';
        	$parsestr .= ' $parsestr .=" '.$space.' <a href=\'/search.html?type=".$search[\'type\']."&keyword=".urlencode($search[\'keyword\'])."\'>".$search[\'keyword\'].$_schtype."</a>";';
        	$parsestr .= '?>';
        	$parsestr .= '<?php echo $parsestr;?>';
        	return $parsestr;
        }
         
    
    }

    /**
     * 上传文件标签解析 js
     * 格式 <DIY:upload name="attachments[]" container="thumbnails" size_limit="2" upload_limit="15" description="*" types="*;" file_type="FILE_USAGE_SUPPLIER_TRAIL" file="filelist"/>
     * 格式说明 name :  post接受值  container：容器值  size_limit : 文件大小 upload_limit：文件上传数量限制  description:上传类型描述 type:上传类型 file_type：上传文件应用类型  file：文件数据
     * 注：若要更改缩略图的显示大小，需要传入post_params : {"SESSID" : "{:session_id()}","type" : "thumb_230"}, type  参数
     * @param string $attr 标签属性
     * @return string|void
     */
    public function _upload($tag, $content) {
      $presets       = !empty($tag['presets']) ? $tag['presets'] : '80'; //缩略图显示大小
      $name          = !empty($tag['name']) ? $tag['name'] : "attachments[]";
      $container     = !empty($tag['container']) ? $tag['container'] : "thumbnails";
      $size_limit    = !empty($tag['size_limit']) ? $tag['size_limit'] : 2; //文件上传大小限制
      $upload_limit  = !empty($tag['upload_limit']) ? $tag['upload_limit'] : 15; //文件上传数量限制
      $description   = !empty($tag['description']) ? $tag['description'] : 'Image File'; //描述
      $types         = !empty($tag['types']) ? $tag['types'] : '*.jpg;*.gif;*.jpeg;*.bmp;*.png'; //允许上传文件类型
      $file          = $tag['file'];
      $role          = $tag['role'];

      $parseStr      = '<script type="text/javascript" src="__JS__/swfupload/swfupload.js"></script>';
      $parseStr     .= '<script type="text/javascript" src="__JS__/swfupload.js"></script>';
      $parseStr     .= '
        <script type="text/javascript">
        $(function() {
            createSWFUpload({
                upload_url : "'. U('/Upload/') .'",
                post_params : {"SESSID" : "{:session_id()}","type" : "'.$presets.'"},
                file_size_limit : "'. $size_limit .' MB", // 2MB
                file_types : "'. $types .'",
                button_width: 166,
                button_height: 44,
                file_types_description : "'. $description .'",
                file_upload_limit : "'. $upload_limit .'",
                button_placeholder_id : "uploadButtonPlaceholder",
                button_text : \'<span class="button"></span>\',
                flash_url : "__JS__/swfupload/swfupload.swf",
                custom_settings : {
                    upload_target : "divFileProgressContainer"
                },
                file_content_container : "#'. $container .'",
                file_field_name : "'. $name .'"
            });
          });
      </script>';
      $parseStr     .= '<div style="clear: both"></div>';
      if($role == 'base') {
        $parseStr     .= '<div class="uploadButtonBaseUpload">';
      } else {
        $parseStr     .= '<div class="uploadButtonPlaceholder">';
      }
      $parseStr     .= '<span id="uploadButtonPlaceholder"></span>';
      $parseStr     .= '</div>';
      $parseStr     .= '<div><span>仅支持上传jpg,gif,png,bmp图片,且图片小于2M</span></div>';
      $parseStr     .= '<div id="divFileProgressContainer"></div>';

      $parseStr     .= '<div id="'.$container .'" class="thumbnails">';
      if (!empty($file) && count($file) > 0) {
        $parseStr   .= '<?php if(is_array($'.$file.') && count($'.$file.') > 0) { ?>';
        $parseStr   .= '<?php  foreach($'.$file.' as $key=>$val) { ?>';
        $parseStr   .= '<div class="upload-content-item upload-image-item">';
        $parseStr   .= '<img width="121" height="121" src="{$val[\'url\']}">';
        if($role == 'base') {
           $parseStr   .= '<input type="hidden" name="'.$name.'" value="fid-{$val[\'fid\']}">';
        } else {
          $parseStr   .= '<input type="hidden" name="'.$name.'" value="fid-{$val[\'attach_id\']}">';
        }

        $parseStr   .= '</div>';
        $parseStr   .= '<?php } ?>';
        $parseStr   .= '<?php } ?>';
      }
      $parseStr     .= '</div>';
      return $parseStr;
    }
    
    /**
     * 读取幻灯片数据
     * 使用方法:<DIY:slide sid='1' />
     * @param array $tag
     * @param html $content
     * @return string|mixed
     * @author wscsky
     */
    
    public function _slide($tag,$content) {
        $id     = !empty($tag['id'])    ? $tag['id'] :'r';
        $key    = !empty($tag['key'])   ? $tag['key']:'i';
        $mod    = isset($tag['mod'])    ? $tag['mod']:'2';
        $sid   = !empty($tag['sid'])    ? $tag['sid'] : '';
        $pos   = !empty($tag['pos'])    ? $tag['pos'] : '';
        $where = ' status=1 ';
       
        if(is_numeric($sid)) $where .= " and id = $sid ";
        if(!empty($pos)) 	 $where .= " and pos = '{$pos}'";
        
        $slide = M('Slide')->where($where)->find();
        
        if(empty($slide)) return  '';
        
        $wherepic = " status=1 and  sid = ".$slide['id'];
        $order=" listorder ASC ,id DESC ";
        $limit= $tag['num'] ? $tag['num'] : 5;
        $sql="M('Slide_data')->where(\"{$wherepic}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.';if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '$'.$key.'++;$mod = ($'.$key.' % '.$mod.' );parse_str($'.$id.'[\'data\'],$'.$id.'[\'param\']);?>';
        $loopend = '<?php endforeach; endif;?>';
        
        $Tpl = APP_PATH.'Home/View/'.ucfirst($slide['tpl'] ? $slide['tpl'] : "Slide/slide_1").'.html';
        $html = file_get_contents($Tpl);
        $html = str_replace(array('{loop}','{/loop}','{$xmlfile}','{$flashfile}','{$flashwidth}','{$flashheight}','{$flashid}'),array($parsestr,$loopend,$slide['xmlfile'],$slide['flashfile'],$slide['width'],$slide['height'],$sid),$html);
        return  $html;
    }

    /*
     * 上传文件标签(传递的图片表示ID为该标签的ID)
     * <DIY:upimg width="200" height="200" id="goods" src="{$imgurl}" />
     * @tag replace   是否在原有基础上覆盖图片（1/0）  默认：0（不覆盖）
     * @author orchid
     */
    public function _upimg($tag)
    {
		$id 	= !empty($tag['id'])?$tag['id']:'';
		$class 	= !empty($tag['class'])?$tag['class']:'';
		$width 	= !empty($tag['width'])?$tag['width']:'50';
		$height = !empty($tag['height'])?$tag['height']:'';
		$src 	= !empty($tag['src'])?$tag['src']:'__IMG__/upload.png';
	
		$value 	= !empty($tag['value'])?$tag['value']:0;
		$replace= !empty($tag['replace'])?1:0;
		$type	= !empty($tag['type'])? trim($tag['type']):"image";
		$title	= !empty($tag['title']) ? $tag['title'] : '图片上传';
		$maxnum	= empty($tag['maxnum']) ? 1: intval($tag['maxnum']);
		$maxnum < 1 && $maxnum = 1;
		$loadjs = !empty($tag['loadjs'])?'yes':'no';
		$use_type= !empty($tag['use_type'])?$tag['use_type']:$id;    //文件归类 goods商品 news:文单 file:符件 brand:品牌 editor:编辑器 auth:认证信息
		$_id = str_replace("]","",str_replace("[", "_", $id));
		$parseStr    	   = '';
		if ($loadjs=='yes') {
			
			$parseStr 	  .= '<script type="text/javascript" src="__PUBLIC__/js/jquery.min.js"></script>';
			$parseStr     .= '<script type="text/javascript" src="__PUBLIC__/js/jquery.artDialog.js?skin=default"></script>';
			$parseStr     .= '<script type="text/javascript" src="__PUBLIC__/js/iframeTools.js"></script>';
			$parseStr     .= '<script type="text/javascript" src="__PUBLIC__/js/jquery.form.js"></script>';
			$parseStr     .= '<script type="text/javascript" src="__PUBLIC__/js/swfupload.js"></script>';
		}
		if($value){
			$hid_input = '<input id="fid_val_'.$value.'" type="hidden" value="'.$value.'" name="'.$id.'[]">';
		}
		$parseStr 	.=  '<div id="'.$_id.'_aid_box">'.$hid_input.'</div><div id="'.$_id.'_pic"></div>';
		$parseStr 	.= '<a href="javascript:void(0)" onclick="swfupload(\''.$_id.'_uploadfile\',\''.$_id.'\',\''.$title.'\',\''.$id.'\',\''.U('Upload/index',array('imageUploadLimit'=>$maxnum,'filetype'=>$type,'type'=>$use_type,'aids'=>$_id)).'\','.$replace.')"><img id="'.$_id.'" class="'.$class.'" width="'.$width.'" height="'.$height.'" src="'.$src.'" /><input type="hidden" name="galley_'.$_id.'" value="'.$src.'"></a>';
		
		return $parseStr;
    }

    /*颜色选择器
     *egg:<DIY:colorPanel id='test' />
    */
    public function _colorPanel($tag)
    {
    	$id 	= !empty($tag['id'])  ? $tag['id'] :'';
    	$value 	= !empty($tag['value'])  ? $tag['value'] :'';
    	$src 	= !empty($tag['src']) ? $tag['src']:'__IMG__/admin_color_arrow.gif';

    	$parseStr 	 = '';
    	$parseStr   .= '<div id="'.$id.'title_colorimg" class="'.$id.'colorimg" style="width:16px;height:16px;float:left;border:1px solid #ccc;margin-top:3px;'.$value.'">';
    	$parseStr   .= '<img src="'.$src.'">';
    	$parseStr   .= '</div>';
    	$parseStr   .= '<input id="'.$id.'title_style_color" type="hidden" value="'.$value.'" name="'.$id.'style_color">';
    	$parseStr   .= '<script type="text/javascript" src="__JS__/jquery.colorpicker.js"></script>';
    	$parseStr   .= '<script>$.showcolor("'.$id.'title_colorimg","'.$id.'title_style_color");</script>';

    	return $parseStr;
    }

    /**
     * 省市联动标签
     * 格式： <Logic:districts name="districts" province="province['id']" city="city['id']" town="district['id']"/>
     * 格式说明：name：post接受数据名称  province：传入的省ID city：传入的市ID town：传入的区ID
     * @param string $attr 标签属性
     * @return string|void
     * @author orchid
     */

    public function _districts($tag)
    {
		$name 		= !empty($tag['name'])		? $tag['name']	:'region_id';
		$id 		= !empty($tag['id'])		? $tag['id']	:'region_id';
		$uri 		= !empty($tag['uri'])		? $tag['uri']	: U('Ajax/get_region');
		$region_id  = !empty($tag['region_id']) ? $tag['region_id'] : 0;
		
		$hidden_id  = !empty($tag['hidden_id']) ? $tag['hidden_id'] : 0;
		$parseStr 	 = '';
		$parseStr	 = '<?php
			if('.$region_id.' > 0){
				$regions = D("Common/City","Logic") -> get_full_region('.$region_id.');
				@list($country,$province, $city, $county) = $regions;	
			}
				?>';		
		$parseStr   .= '<input event="region_id" type="hidden" name="'.$name.'" id="'.$id.'" value="<?php echo '.$region_id.'; ?>" uri="'.$uri.'"/>';
		$parseStr   .= '<input  type="hidden" name="hidden_id" value="<?php echo '.$hidden_id.'; ?>"/>';
		
		//省
		//$parseStr	.= '<p class="choose"><span class="edit-add"><span class="label">省份：</span><span>';
		//$parseStr	.= '<span class="label" style="color:#666;font-size:14px;text-align:right;width:105px;margin-left:60px;">省份：</span>';
		$parseStr   .= '<select event="loadRegion" class="">';
		$parseStr   .= '<?php if(!empty($province)) { ?>';
		$parseStr   .= '<option selected="selected" value="<?php echo $province[region_id];?>"><?php echo $province[region_name];?></option>';
		$parseStr   .= '<?php } else{ ?>';
		$parseStr 	.= '<option value="0">'.L('province').'</option>';
    	$parseStr   .= '<?php } ?>';
		$parseStr 	.= '</select>';
		//$parseStr	.= '</span></span></p>';

		//市
		//$parseStr	.= '<p class="choose"><span class="edit-add"><span class="label">城市：</span><span>';
		//$parseStr	.= '<span class="label"  style="color:#666;font-size:14px;text-align:right;width:105px;margin-left:60px;">城市：</span>';
		$parseStr   .= '<select event="loadRegion"  class="">';
		$parseStr   .= '<?php if(!empty($province) && !empty($city)) { ?>';
		$parseStr   .= '<option selected="selected" value="<?php echo $city[region_id];?>"><?php echo $city[region_name];?></option>';
		$parseStr   .= '<?php }else{ ?>';
		$parseStr 	.= '<option value="0">'.L('city').'</option>';
    	$parseStr   .= '<?php } ?>';
		$parseStr 	.= '</select>';
		//$parseStr	.= '</span></span></p>';

		//县
		//$parseStr	.= '<p class="choose"><span class="edit-add"><span class="label">区县：</span><span>';
		//$parseStr	.= '<span class="label"  style="color:#666;font-size:14px;text-align:right;width:105px;margin-left:60px;">区县：</span>';
		$parseStr   .= '<select event="loadRegion"  class="">';		
		$parseStr   .= '<?php if(!empty($province) && !empty($city) && !empty($county)) { ?>';
		$parseStr   .= '<option selected="selected" value="<?php echo $county[region_id];?>"><?php echo $county[region_name];?></option>';
		$parseStr   .= '<?php } else { ?>';
		$parseStr 	.= '<option value="0">'.L('county').'</option>';
    	$parseStr   .= '<?php }?>';
		$parseStr 	.= '</select>';
		//$parseStr	.= '</span></span></p>';
		return $parseStr;
    }
    
    /**
     * 省市联动标签2
     * 格式： <Logic:districts name="districts" province="province['id']" city="city['id']" town="district['id']"/>
     * 格式说明：name：post接受数据名称  province：传入的省ID city：传入的市ID town：传入的区ID
     * @param string $attr 标签属性
     * @return string|void
     * @author orchid
     */
    
    public function _districts2($tag)
    {
    	$name 		= !empty($tag['name'])		? $tag['name']	:'region_id';
		$id 		= !empty($tag['id'])		? $tag['id']	:'region_id';
		$uri 		= !empty($tag['uri'])		? $tag['uri']	: U('/Ajax/get_region');
		$region_id  = !empty($tag['region_id']) ? $tag['region_id'] : 0;
		$hidden_id  = !empty($tag['hidden_id']) ? $tag['hidden_id'] : 0;
		
		$parseStr 	 = '';
		$parseStr	 = '<?php
			if('.$region_id.' > 0){
				$regions = D("Common/City","Logic") -> get_full_region('.$region_id.');
				@list($country,$province, $city, $county) = $regions;	
			}
				?>';		
		$parseStr   .= '<input event="region_id" type="hidden" name="'.$name.'" id="'.$id.'" value="<?php echo '.$region_id.'; ?>" uri="'.$uri.'"/>';
    	 //省
        $parseStr   .= '<div class="ui-form-item ui-border-b"><label>省份</label><div class="ui-select">';
        $parseStr   .= '<select event="loadRegion2" id="province" name="province" class="city-select">';
        $parseStr   .= '<?php if(!empty($province)) { ?>';
        $parseStr   .= '<option selected="selected" value="<?php echo $province[region_id];?>"><?php echo $province[region_name];?></option>';
        $parseStr   .= '<?php } else{ ?>';
        $parseStr 	.= '<option value="0">'.L('province').'</option>';
        $parseStr   .= '<?php } ?>';
        $parseStr 	.= '</select></div></div>';
    
    	//市
    	$parseStr   .= '<div class="ui-form-item ui-border-b"><label>地级市</label><div class="ui-select">';
    	$parseStr   .= '<select event="loadRegion2" id="city" name="city" class="city-select">';
    	$parseStr   .= '<?php if(!empty($province) && !empty($city)) { ?>';
    	$parseStr   .= '<option selected="selected" value="<?php echo $city[region_id];?>"><?php echo $city[region_name];?></option>';
    	$parseStr   .= '<?php }else{ ?>';
    	$parseStr 	.= '<option value="0">'.L('city').'</option>';
    	$parseStr   .= '<?php } ?>';
    	$parseStr 	.= '</select></div></div>';
    
    	//县
    	$parseStr   .= '<div class="ui-form-item ui-border-b"><label>区/县</label><div class="ui-select">';
    	$parseStr   .= '<select event="loadRegion2" id="county" name="county" class="city-select">';
    	$parseStr   .= '<?php if(!empty($province) && !empty($city) && !empty($county)) { ?>';
    	$parseStr   .= '<option selected="selected" value="<?php echo $county[region_id];?>"><?php echo $county[region_name];?></option>';
    	$parseStr   .= '<?php } else { ?>';
    	$parseStr 	.= '<option value="0">'.L('county').'</option>';
    	$parseStr   .= '<?php }?>';
    	$parseStr 	.= '</select></div></div>';
    
    	return $parseStr;
    }

}