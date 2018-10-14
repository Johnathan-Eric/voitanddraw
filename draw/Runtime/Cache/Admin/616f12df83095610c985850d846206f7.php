<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo C('site_title');?>-<?php echo ($page_title); ?></title>
<link rel="stylesheet" type="text/css" href="/Public/new/css/public.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/new/css/jquery.step.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/layui/css/layui.css?v=<?php echo C('jsversion');?>" />
<link rel="stylesheet" type="text/css" href="/Public/layer/layui/css/modules/layer/default/layer.css?v=<?php echo C('jsversion');?>" />
<script type="text/javascript" src="/Public/js/jquery-2.1.0.min.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/layui/layui.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/layui/lay/modules/layer.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/jquery.step.min.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/public.js?v=<?php echo C('jsversion');?>"></script>
<script type="text/javascript" src="/Public/new/js/base64.js?v=<?php echo C('jsversion');?>"></script>
</head>
<body width="100%">
<div class="main-body">
    <div class="nav_title" style="left: 0; width: 100%;">
        <div class="panel_title">
            <div class="p_div">
                <div class="p_text" style="visibility: inherit; top: 0px; transform-origin: 84px 9px 0px;">
                    <p><span>系统首页</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="lay-body">
        <div class="nav_header">
            <div class="p_title">
                <div class="p_text" style="visibility: inherit; top: 0px; transform-origin: 84px 9px 0px;">
                    <p><span>账户设置</span></p>
                </div>
            </div>
        </div>
        <div class="pro-content">
            <form class="layui-form" action="" method="post">
                <div class="layui-form-item">
                    <label class="layui-form-label">旧密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="oldpassword" required lay-verify="required" placeholder="请输入旧密码" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="password" required lay-verify="required" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="repassword" required lay-verify="required" placeholder="请输入确认密码" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formSub">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>    
</div>
<script type="text/javascript">
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(formSub)', function(data){
            $("form").submit();
        });
    });
    layui.use('upload', function(){
    var $ = layui.jquery
    ,upload = layui.upload;
  
    //普通图片上传
      var uploadInst = upload.render({
          elem: '#proImg'
          ,url: '/merchant/Upload/AJAX_upload'
          ,before: function(obj){
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result){
                $('#imgUrl').attr('src', result); //图片链接（base64）
            });
          }
          ,done: function(res){
            if(res.status == 1){
                $('#fid').val(res.info.fid);
                return layer.msg('上传成功');
            }else{
                return layer.msg(res.msg);
            }
            //如果上传失败
            if(res.code > 0){
              
            }
            //上传成功
          }
          ,error: function(){
            //演示失败状态，并实现重传
            var demoText = $('#demoText');
            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
            demoText.find('.demo-reload').on('click', function(){
              uploadInst.upload();
            });
          }
      });
    });
</script>
</body>
</html>