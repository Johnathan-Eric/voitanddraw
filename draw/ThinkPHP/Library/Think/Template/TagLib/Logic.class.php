<?php
/*
 * 大集自定义标签
 * @author wscsky<wscsky@qq.com>
 *
 */
namespace Think\Template\TagLib;
use Think\Template\TagLib;

defined('THINK_PATH') or exit();
class Logic extends TagLib {


    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
         'perm'      => array('attr' => 'func,access','close' => 1),					//模版权限
         'editor'    => array('attr'=>'id,name,style,width,height,aid,','close'=>1),			//编辑器
        );

    /**
     * perm标签解析 模版权限验证
     * 格式： <logic:perm access='权限值'>htmlcode</logic:perm>
     * @access public
     * @param array $tag 标签属性
     * @return bool
     */
    public function _perm($tag,$content) {
      $func       =   isset($tag['func']) ? $tag['func'] : 'check_access';
      $perm       =   $tag['access'];

      $parseStr   = '<?php if(';
      $perms = explode(',', $perm);
      foreach ($perms as $perm) {
        $parseStr .= $func.'("'.$perm.'") or ';
      }
      $parseStr = trim($parseStr, "or ");
      $parseStr   .=   '): ?>'.$content.'<?php endif; ?>';
      return $parseStr;
    }

    /**
     * editor标签解析 插入可视化编辑器
     * 格式： <logic:editor id="editor" name="remark" style="" >{$vo.remark}</logic:editor>
     * @access public
     * @param array $tag 标签属性
     * @return string|void
     */
    public function _editor($tag,$content) {
    	$id			=	!empty($tag['id'])?$tag['id']: '_editor';
    	$name   	=	$tag['name'];
    	$style   	=	!empty($tag['style'])?$tag['style']:'';
    	$width		=	!empty($tag['width'])?$tag['width']: '100%';
    	$height     =	!empty($tag['height'])?$tag['height'] :'320px';
    	$content    =   $tag['content'];
    	$aids 		=   !empty($tag['aid'])?$tag['aid'] :'editor';   //标识选择图片的id

    	$parseStr   =  '<div class="editor_box">
						<div style="display:none;" id="'.$id.'_aid_box"></div>
    					<textarea id="'.$id.'" style="'.$style.'" name="'.$name.'" >'.$content.'</textarea>
    					<script type="text/javascript" src="__PUBLIC__/js/kindeditor/kindeditor.js"></script>
    					<script type="text/javascript">
       						   KindEditor.ready(function(K) {
						       K.create(\'textarea[name="'.$name.'"]\', {
						            cssPath: "__PUBLIC__/js/kindeditor/plugins/code/prettify.css",
						       		fileManagerJson:"'.U('Upload/index',array('type'=>'editor','aids'=>$aids)).'",
						            allowFileManager: true,
						       		editorid:"'.$id.'",
						       		filterMode: false, //是否开启过滤模式
						       		aids:"'.$aids.'",
						       		upImgUrl:"'.U('Upload/index',array('type'=>'editor','filetype'=>'image','aids'=>$aids)).'",
						       		upFlashUrl:"'.U('Upload/index',array('type'=>'editor','filetype'=>'video','aids'=>$aids)).'",
						       		upMediaUrl:"'.U('Upload/index',array('type'=>'editor','filetype'=>'audio','aids'=>$aids)).'",
						       		afterBlur: function(){this.sync();}
						       });
						       });
    					</script>
						<div  class=\'editor_bottom2\'></div></div>';

    	return $parseStr;
    }


}

