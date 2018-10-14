<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  if(IMS_VERSION<1) { ?>
<link href="<?php echo MODULE_URL;?>/template/static/css/wq1.0.css" rel="stylesheet">
<?php  } ?>
<style>
.multi-img-details .multi-item{height: 176px;text-align: CENTER;background: #efefef;margin-bottom: 16px;border-radius: 5px;}

</style>
<div class="main">
<script type="text/javascript">
	function uploadMultiImage(elm) {
		var name = $(elm).next().val();
		util.image( "", function(urls){
			$.each(urls, function(idx, url){var a =(typeof(url.name)=="undefined")? url.filename : url.name;var b=a.split('.');var c=b[0];$(".multi-img-details").append('<div class="multi-item" ><img onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" src="'+url.url+'" class="img-responsive img-thumbnail"><input type="text" class="form-control" name="imgname[]" value="'+c+'"><input type="hidden" name="imgurl[]" value="'+url.attachment+'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>');});$(".submit_box").show();
		}, {"multiple":true,"direct":false,"fileSizeLimit":8388608});
	}
	function deleteMultiImage(elm){
		$(elm).parent().remove();
	}
</script>
	<div class="mui-input-cell">
	<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="<?php  echo $name;?>" />
	</span>
    </div>
	<br>
	<div class="alert alert-success" role="alert">图片名字为用户名字，例如： 王乐乐.jpg； 然后选择图片上传。选择图片后可以修改姓名。


</div>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
    <div class="input-group multi-img-details"></div>
    </div>
	<div class="form-group col-sm-12 submit_box" style="display:none">
		<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
</form>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>