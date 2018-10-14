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
                    <p><span><?php echo ($page_title); ?></span></p>
                </div>
            </div>
        </div>
        <div class="refresh_div">
            <button class="layui-btn layui-btn-small layui-btn-primary" onclick="javascript:location.reload();">
                <i class="layui-icon">&#x1002;</i>
            </button>
        </div>
    </div>
    <div class="lay-body">
        <div class="table-body" id="tyep">
            <form class="layui-form">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><ic><img src='/Public/new/img/i_list.png'/></ic><span><?php echo ($page_header); ?></span></p>
                    </div>
                </div>
                <div class="pub_menu_left">
                    <div class="m_left">
                        <div class="layui-form-item">
                            <?php if($acc['user_add']): ?><div class="layui-unselect layui-form-select" onclick="layer_show('<?php echo ($acc['user_add']['auth_name']); ?>','<?php echo U($acc['user_add']['auth_path']);?>','500','450')">
                                    <div class="layui-select-title">
                                        <input type="button" class="layui-input layui-unselect" value="添加"><i class="layui-edge"></i>
                                    </div>
                                </div><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-table">
                <table class="layui-table">
                    <thead>
                        <tr>
                            <!-- <th style="text-align: center;">
                                <div class="layui-form-item">
                                    <input type="checkbox" lay-skin="primary" lay-filter="allChoose"/>
                                </div>
                            </th> -->
                            <th style="text-align: center;">编号</th>
                            <th style="text-align: center;">登录名</th>
                            <?php if($is_store != 1): ?><th style="text-align: center; ">角色名称</th>
                            <th style="text-align: center; width: 300px;">权限</th><?php endif; ?>
                            <th style="text-align: center;">创建时间</th>
                            <th style="text-align: center;">状态</th>
                            <th style="text-align: center;">操作</th>
                            
                        </tr> 
                    </thead>
                    <tbody>
                        <?php if(is_array($lists)): $k = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr style="text-align: center;">
                                <!-- <td>
                                    <div class="layui-form-item">
                                        <input type="checkbox" name="checkId[]" value="<?php echo ($vo["id"]); ?>" lay-skin="primary" />
                                    </div>
                                </td> -->
                                <td><?php echo ($vo["id"]); ?></td>
                                <td><?php echo ($vo["username"]); ?></td>
                                <?php if($is_store != 1): ?><td>
                                    <?php if($acc['user_role']): if($vo['is_system'] != 1): if($vo['rid'] != 2): ?><a href="javascript:void(0);" onclick="layer_show('<?php echo ($acc['user_role']['auth_name']); ?>','<?php echo U($acc['user_role']['auth_path'],array('id'=>$vo['aid']));?>','500','450')"><?php echo ((isset($vo["role_name"]) && ($vo["role_name"] !== ""))?($vo["role_name"]):'未分配角色'); ?></a>
                                            <?php else: ?>
                                            <?php echo ((isset($vo["role_name"]) && ($vo["role_name"] !== ""))?($vo["role_name"]):'未分配角色'); endif; ?>
                                            <?php else: ?>
                                            <?php echo ((isset($vo["role_name"]) && ($vo["role_name"] !== ""))?($vo["role_name"]):'未分配角色'); endif; ?>
                                    <?php else: ?>
                                        <?php echo ((isset($vo["role_name"]) && ($vo["role_name"] !== ""))?($vo["role_name"]):'未分配角色'); endif; ?>
                                </td>
                                <td><?php echo ((isset($vo["auth_name"]) && ($vo["auth_name"] !== ""))?($vo["auth_name"]):'未分配权限'); ?></td><?php endif; ?>
                                <td><?php echo (date('Y-m-d',$vo["add_time"])); ?></td>
                                <td><?php echo C('userStatus')[$vo['status']];?></td>
                                <td>
                                    <?php if($vo['id'] != 1): if($acc['user_edit']): ?><a href="javascript:void(0);" onclick="layer_show('<?php echo ($acc['user_edit']['auth_name']); ?>','<?php echo U($acc['user_edit']['auth_path'],array('id'=>$vo['id']));?>','500','450')">编辑</a><?php endif; ?>
                                        <?php if($acc['user_del']): ?><a href="javascript:void(0);" onclick="areaDel('<?php echo U($acc['user_del']['auth_path'],array('id'=>$vo['id']));?>')">删除</a><?php endif; ?>
                                        <?php if($acc['store_save']): ?><a href="javascript:void(0);" onclick="layer_show('编辑','<?php echo U($acc['store_save']['auth_path'],array('id'=>$vo['id']));?>','500','450')">编辑</a><?php endif; ?>
                                        <?php if($acc['store_del']): ?><a href="javascript:void(0);" onclick="areaDel('<?php echo U($acc['store_del']['auth_path'],array('id'=>$vo['id']));?>')">删除</a><?php endif; endif; ?>
                                    
                                </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
                <div class="tab-footer">
                    <div class="right-oper">
                        <div id="layPage"></div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>    
</div>
<script type="text/javascript">
    var allTotal = <?php echo ((isset($_total) && ($_total !== ""))?($_total):0); ?>;
    var curr = <?php echo ((isset($_curr) && ($_curr !== ""))?($_curr):1); ?>;
    var limit = <?php echo ((isset($_limit) && ($_limit !== ""))?($_limit):10); ?>;
    layui.use(['laypage', 'layer'], function(){
        var laypage = layui.laypage
        ,layer = layui.layer;
        laypage.render({
            elem: 'layPage'
            ,count: allTotal
            ,limit: limit
            ,curr : curr
            ,layout: ['count', 'prev', 'page', 'next', 'skip']
            ,jump: function(obj,first){
                if(!first){
                    var activity_name = $("input[name='activity_name']").val();
                    var date = $("input[name='date']").val();
                    jsPost('', {curr: obj.curr,activity_name:activity_name,date:date}); 
                }
            }
        });
    });
    layui.use('form', function(){
        var form = layui.form
        ,layer = layui.layer;
        
        form.on('switch(switchOpen)', function(data){
            var id = $(this).val();
            var nval = this.checked ? 1 : 2;
            var ntype = 'is_open';
            setOper([id],nval,ntype);
        });
        
        form.on('checkbox(allChoose)', function(data){  
            var child = $(data.elem).parents('table').find('tbody input[name="checkId[]"]');  
            child.each(function(index, item){  
                item.checked = data.elem.checked;  
            });  
            form.render('checkbox');  
        });
        
        form.on('select(showNum)', function(data){
            var order_num = $("input[name='order_num']").val();
            var take = $("input[name='take']").val();
            var reg_time = $("input[name='reg_time']").val();
            var order_status = $('[name="order_status"]').val();
            var showNum = data.value;
            var showOrder = $("[name='showOrder']").val();
            jsPost('', {order_num:order_num,take:take,reg_time:reg_time,order_status:order_status,showNum:showNum,showOrder:showOrder}); 
        });
        
    });
    
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        laydate.render({
            elem: '#subTime' //指定元素
            ,theme: 'molv'
        });
    });
    
    function subForm()
    {
        $("#searchForm").submit();
    }
    
    function setOper(ids,nval,ntype)
    {
        var lod_index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post('/Merchant/Config/ajax_area', { ids: ids,nval: nval,ntype: ntype },function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.status == 1){
                layer.msg(parsedJson.msg);
                window.location.reload();
            }else{
                layer.msg(parsedJson.msg);
            }
        });
    }
    
    function areaDel(url)
    {
        layer.confirm('确认删除？', {
          btn: ['确认', '取消'],
        }, function(index, layero){
            var lod_index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
            $.get(url, {} ,function(a){
                layer.close(lod_index);
                var jsonDate = jQuery.parseJSON(a);
                if(jsonDate.status == 1){
                    layer.msg(jsonDate.msg);
                    window.location.reload();
                    var index = parent.layer.getFrameIndex(window.name);  
                    parent.layer.close(index);
                }else{
                    layer.msg(jsonDate.msg);
                }
            });
        }, function(index){
          layer.close(index);
        });
    }
</script>
</body>
</html>