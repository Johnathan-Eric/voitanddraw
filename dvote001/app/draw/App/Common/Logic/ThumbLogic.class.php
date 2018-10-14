<?php
//会员处理逻辑模块
namespace Common\Logic;

class ThumbLogic{

 /**
     * 转换或获取缩略图片路径
     * @param string    $path  原图片存储路径
     * @param string    $preset  预置缩略图处理格式，默认为null
     * @param bool      $check  检查图片文件，默认为false不检查
     * @return string
     */
    public function thumbPath($path, $preset = null, $check = false) {
      //远程图片路径不转换  ://
      if (false !== strpos($path, '://')) {
        return $path;
      }

      $thumb_path       = C('THUMB_FILE_PATH') ? C('THUMB_FILE_PATH') : '/Thumb/';
      $original_path    = C('UPLOAD_FILE_PATH') ? C('UPLOAD_FILE_PATH') : '/Uploads/';

      //获取图片处理格式数组
      $presets = C('THUMB_PRESETS');
      if (isset($presets[$preset])) {
          //转换缩略图路径
          $thumbpath = str_replace($original_path, $thumb_path . $preset . "/", $path);
      }else{
      	return $path;
      }

      //检查图片文件
      if ($check) {
        if (file_exists($path)) {
          return $thumbpath;
        }
        else {
          return '';
        }
      }
      //返回缩略图路径
      return $thumbpath;
    }

    /**
     * 生成并返回缩略图文件路径
     * @param string $path 图片路径
     * @preset string $preset 预置缩略图处理格式
     * @param bool $save_img 是否要保存图片
     * @param bool $save_self 是否替换自己
     * @return string
     */
    public function thumb($path, $preset = null, $save_img = true, $save_self = false) {

	  if(strpos($path,C('UPLOAD_FILE_PATH'))===0)
	  {
	  		$original_path = '';
	  }
	  else{
       		$original_path = C('UPLOAD_FILE_PATH') ? C('UPLOAD_FILE_PATH') : './Uploads';
	  }
      $original_path = $original_path . $path;

      $original_path{0} == "/" && $original_path = ".".$original_path;

      if (!is_file($original_path)) {
        return false;
      }

      $pathinfo = pathinfo($original_path);

      if(in_array(strtolower($pathinfo['extension']), array('gif','jpg','jpeg','bmp','png'))) {
        $image =  getimagesize($original_path);
        if(false !== $image) {
          //是图像文件生成缩略图
          $presets = C('THUMB_PRESETS');
          if (isset($presets[$preset])) {
	            $thumbWidth  = $presets[$preset]['width'];
	            $thumbHeight = $presets[$preset]['height'];
	            $thumb_type   = $presets[$preset]['thumb_type'];
	            //拼接成新的图片路径包括处理格式    目录名/处理格式/文件名.扩展名
	            $type_path = $pathinfo['dirname'] . '/' . $pathinfo['basename'];
          }elseif($save_self){
          		$preset = "640";
	          	$thumbWidth  = $presets[$preset]['width'];
	          	$thumbHeight = $presets[$preset]['height'];
	          	$thumb_type   = $presets[$preset]['thumb_type'];
	          	$type_path = $pathinfo['dirname'] . '/' . $pathinfo['basename'];
          }else{
	            $quality  = 75;
	            $thumbWidth = 200;
	            $thumbHeight = 0;
	            $type_path = $original_path;
          }
          //转换后的缩略图路径
          if($save_self){
          		$thumbpath = $original_path;
          }else{
          		$thumbpath = self::thumbPath($type_path,$preset);
          }
          if (!empty($thumbpath)) {
            $newpath = dirname($thumbpath);
            //目录不存在，创建目录
            if (!is_dir($newpath)) {
              if (!mkdir($newpath, 0700, true)) {
                return '';
              }
            }
            //生成图像缩略图
      		$Image = new \Think\Image();
      		$Image->open($original_path);
      		
      		//如果替换自己 大小小于指定尺寸直接返回
      		if($save_self && $Image -> width() < $thumbWidth && $thumbHeight == 0) return $thumbpath;
      		
      		//宽度等比
      		if($thumbHeight == 0){
      			$thumbHeight = (int)($Image -> height() * $thumbWidth/ $Image -> width());
      		}
      		$Image->thumb($thumbWidth, $thumbHeight, $thumb_type ? $thumb_type : 1)->save($thumbpath);
      		if(!$save_img){
      			switch (substr($thumbpath, -3)){
      				case "gif":
      					$imgobj = imagecreatefromgif($thumbpath);
      					ob_start();imagegif($imgobj);
      					break;
      				case "png":
      					$imgobj = imagecreatefrompng($thumbpath);
      					ob_start();imagepng($imgobj);
      					break;
      				case "bmp":
      					$imgobj = imagecreatefrombmp($thumbpath);
      					ob_start();imagebmp($imgobj);
      					break;
      				default:
      					$imgobj = imagecreatefromjpeg($thumbpath);
      					ob_start();imagejpeg($imgobj);
      					break;
      			}      			
      			$data =ob_get_contents();
      			ob_end_clean();
      			imagedestroy($thumbpath);
      			return "data:image/png;base64,". base64_encode($data);
      		}
      		$thumbpath{0} == "." &&  $thumbpath = substr($thumbpath,1);
            return $thumbpath;
          }
        }
      }
      return false;
    }

}