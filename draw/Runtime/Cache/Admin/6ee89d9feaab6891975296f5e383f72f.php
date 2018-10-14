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
<style>
    .layui-input-block{
        margin-left: 105px;
        min-height: 36px;
    }
    .xiaoshou{
        border:1px solid #e6e6e6;
        white-space: nowrap;
        text-align: center;
        font-size: 14px;
        display: inline-block;
        height: 38px;
        line-height: 38px;
        padding: 0 18px;
    }
    .xuni{
        display:none;
    }
    .xunimingcheng{
        display:none;
    }
    .addgoods{
        display:none;
    }
    .tables{
        margin-left: 105px;
        width: 1465px;
    }
    .selectStyle,.selectStyles{
        height: 38px;
        width:  100%;
        line-height: 1.3;
        border-width: 1px;
        border-style: solid;
        background-color: #fff;
        border-radius: 2px;
        border-color: #e6e6e6;
    }
</style>
<div class="body-form">
    <div class="lay-body">
        <form class="layui-form" action="" method="post">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>基本信息</legend>
            </fieldset>
            <div class="lay-form-div">
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>奖品类型：</label>
                    <div class="layui-input-inline">
                        <select name="atype" required lay-verify="required">
                            <?php if(is_array($atypes)): $i = 0; $__LIST__ = $atypes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($info['atype'] == $key): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>公众号：</label>
                    <div class="layui-input-inline">
                        <select name="uniacid" required lay-verify="required">
                            <?php if(is_array($uniList)): $i = 0; $__LIST__ = $uniList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["uniacid"]); ?>" <?php if($info['uniacid'] == $vo['uniacid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>活动标题：</label>
                    <div class="layui-input-inline">
                        <select name="actid" required lay-verify="required">
                            <?php if(is_array($actList)): $i = 0; $__LIST__ = $actList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["rid"]); ?>" <?php if($info['actid'] == $vo['rid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="layui-form-mid layui-word-aux">必填项</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>是否需要发货：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-col-xs6">
                        <input type="checkbox" <?php if($info['deliver'] == 0): else: ?>checked<?php endif; ?> lay-filter="switchDeliver" lay-skin="switch" lay-text="ON|OFF">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>奖品名：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-col-xs6">
                        <input type="text" name="name" value="<?php echo ($info["name"]); ?>" required lay-verify="required" placeholder="请输入奖品名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">URL链接：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-col-xs6">
                        <input type="text" name="hurl" value="<?php echo ($info["hurl"]); ?>" placeholder="请输入URL链接" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>库存：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-input-block lay-col-x1">
                        <div style="width: 108px;" class="layui-inline layui-form-pane">
                            <div style="margin-right: -1px;width:60px;" class="layui-input-inline">
                                <input type="text" class="layui-input" autocomplete="off" required lay-verify="required" name="stock" value="<?php echo ($info["stock"]); ?>" placeholder="必须大于0" style="width:160px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>中奖次数：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-input-block lay-col-x1">
                        <div style="width: 108px;" class="layui-inline layui-form-pane">
                            <div style="margin-right: -1px;width:60px;" class="layui-input-inline">
                                <input type="text" class="layui-input" autocomplete="off" required lay-verify="required" name="winnum" value="<?php echo ($info["winnum"]); ?>" placeholder="不支持小数点，如：1" style="width:160px;">
                            </div>
                        </div>
                        <div style="float: right;width: 220px;margin-top: 10px;">(默认0，不限制；大于0，规定每个用户中奖几次；)</div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>出奖概率：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-input-block lay-col-x1">
                        <div style="width: 108px;" class="layui-inline layui-form-pane">
                            <div style="margin-right: -1px;width:60px;" class="layui-input-inline">
                                <input type="text" class="layui-input" autocomplete="off" required lay-verify="required" name="pronum" value="<?php echo ($info["pronum"]); ?>" placeholder="不支持小数点，如：1" style="width:160px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>封面图：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-input-inline" id="thumbImg">
                        <div class="layui-upload">
                            <button type="button" class="layui-btn" id="uploadThumb">上传图片</button>
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="thumb" src="<?php echo ($info["thumb"]); ?>">
                                <p id="thumbText"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>排序：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-col-xs6">
                        <input type="text" name="listorder" style="width: 80px;" value="<?php echo ((isset($info["listorder"]) && ($info["listorder"] !== ""))?($info["listorder"]):255); ?>" required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>时间区间：&nbsp;&nbsp;&nbsp;</label>
                    <div class="layui-col-xs6">
                        <input type="text" name="stime" id="subTime1" style="width: 160px;" value="<?php echo ($info["stime"]); ?>" required lay-verify="required" placeholder="开始时间" autocomplete="off" class="layui-input"> - <input type="text" name="etime" id="subTime2" style="width: 160px;" value="<?php echo ($info["etime"]); ?>" required lay-verify="required" placeholder="结束时间" autocomplete="off" class="layui-input">
                    </div>
                </div>
                
            </div>
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>奖品详情</legend>
            </fieldset>
            <div class="lay-form-div">
                <div class="layui-form-item">
                    <label class="layui-form-label"><span style="color: red;">*</span>奖品详情：</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="content" id="lay-content" style="display: none">  
                            <?php echo ($info["content"]); ?>
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>"/>
                    <input type="hidden" name="deliver" value="<?php echo ($info["deliver"]); ?>" />
                    <input type="hidden" value="<?php echo ($info["thumb"]); ?>" name="thumb" />
                    <button class="layui-btn" lay-submit lay-filter="formSub">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>   
</div>
<script type="text/javascript">
    layui.use(['form','layedit'], function(){
        var form = layui.form
        ,layedit = layui.layedit
        ,$ = layui.jquery;
        
        //构建一个默认的编辑器
        layedit.set({
            uploadImage: {
                url: '/Admin/Upload/layedit_upload' //接口url
                ,type: 'post' //默认post
            }
        });
        var contentIndex = layedit.build('lay-content',{
            tool: [
                'strong' //加粗
                ,'italic' //斜体
                ,'underline' //下划线
                ,'del' //删除线
                ,'|' //分割线
                ,'left' //左对齐
                ,'center' //居中对齐
                ,'right' //右对齐
                ,'link' //超链接
                ,'unlink' //清除链接
                ,'face' //表情
                ,'image' //插入图片
            ],
        });

        form.on('switch(switchDeliver)', function(data){
            if(data.elem.checked){
                $("input[name='deliver']").val(1);
            }else{
                $("input[name='deliver']").val(0);
            }
        });
        
         //监听提交
        form.on('submit(formSub)', function(data){
                var date = data.field;
                var url = window.location.href;
                var stock = $("input[name='stock']").val();
                if (stock <= 0) {
                    layer.msg('库存数值必须大于0');return false;
                }
                date.content = layedit.getContent(contentIndex);
                var lod_index = layer.load(1, {
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.post(url, date ,function(a){
                        layer.close(lod_index);
                        var jsonDate = jQuery.parseJSON(a);
                        if(jsonDate.status == 1){
                            layer.msg(jsonDate.msg,{
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function(){
                                parent.location.reload();
                                var index = parent.layer.getFrameIndex(window.name);  
                                parent.layer.close(index);
                            });
                        }else{
                            layer.msg(jsonDate.msg);
                        }
                });
            return false;
        }); 
    });

    layui.use('laydate', function(){
        var laydate = layui.laydate;
        laydate.render({
            elem: '#subTime1' //指定元素
            ,theme: 'molv'
            ,type: 'datetime'
        });
    });
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        laydate.render({
            elem: '#subTime2' //指定元素
            ,theme: 'molv'
            ,type: 'datetime'
        });
    });
    
    layui.use('upload', function(){
    var $ = layui.jquery
    ,upload = layui.upload;
        //普通图片上传
        var uploadInst = upload.render({
            elem: '#uploadThumb'
            ,url: '/Admin/Upload/AJAX_upload'
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#thumb').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){
                if(res.status == 1){ //上传成功
                    layer.msg("上传成功");
                    $("input[name='thumb']").val(res.info.uri);
                    return;
                }
            }
            ,error: function(){
                //演示失败状态，并实现重传
                var thumbText = $('#thumbText');
                thumbText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                thumbText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            }
        });
        
    });
</script>
</body>
</html>