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
    .anniu{
        padding-bottom: 22px;
    }
    .ziti{
        color:red;
    }
</style>
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
        <form class="layui-form" method="get" action="" id="searchForms">
        <div class="layui-btn-container anniu">
          <button name="search" id="search_1" value="1" onclick="subForms()" class="layui-btn layui-btn-primary">全部奖品(<span class="ziti"><?php echo ((isset($count0) && ($count0 !== ""))?($count0):0); ?></span>)</button> 
          <button name="search" id="search_2" value="2" onclick="subForms()" class="layui-btn layui-btn-primary">已上架(<span class="ziti"><?php echo ((isset($count1) && ($count1 !== ""))?($count1):0); ?></span>)</button> 
          <button name="search" id="search_3" value="3" onclick="subForms()" class="layui-btn layui-btn-primary">未上架(<span class="ziti"><?php echo ((isset($count2) && ($count2 !== ""))?($count2):0); ?></span>)</button>
          <span style="margin-left: 160px;width: 300px;text-align: right;">总中奖基数：<?php echo ($tpnum); ?></span>
          <span style="float: right;margin-right: 20px;"><a onclick="checkNum()" class="layui-btn">校验出奖率</a></span>
        </div>
        </form>
        <div id="numInfo" style="text-align: left;margin-left: 150px;margin-bottom: 20px;display: none;">
        </div>
        <div class="lay-search">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p><i class="layui-icon">&#xe615;</i><span>筛选查询</span></p>
                    </div>
                </div>
                <div class="p_menu_left">
                    <div class="menu_left">
                        <button class="layui-btn" onclick="subForm()">查询结果</button>
                    </div>
                    <input type="hidden" name="actid" value="<?php echo ($request['actid']); ?>" />
                </div>
            </div>
            <div class="search-form">
                <form class="layui-form" method="get" action="" id="searchForm">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">输入搜索：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="keyword" placeholder="奖品名称" value="<?php echo ($request['keyword']); ?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline" style="text-align: left;">
                            <label class="layui-form-label">活动标题：</label>
                            <div class="layui-input-inline">
                                <select name="actid">
                                    <option value="">全部</option>
                                    <?php if(is_array($actList)): $i = 0; $__LIST__ = $actList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["rid"]); ?>" <?php if($vo["rid"] == $request['actid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline" style="text-align: left;">
                            <label class="layui-form-label">公众号：</label>
                            <div class="layui-input-inline">
                                <select name="uniacid">
                                    <option value="">全部</option>
                                    <?php if(is_array($uniList)): $i = 0; $__LIST__ = $uniList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["uniacid"]); ?>" <?php if($vo["uniacid"] == $request['uniacid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-body">
            <div class="nav_header">
                <div class="p-search">
                    <div class="p_text">
                        <p style="width: 500px;"><ic><img src='/Public/new/img/i_list.png'/></ic><span><?php echo ($page_header); ?>（<font color="red"><b>注：</b>中奖次数设置为0时，不限制中奖次数。</font>)</span></p>
                    </div>
                </div>
                <div class="pub_menu_left">
                    <div class="m_left">
                        <div class="layui-form-item">
                            <?php if($acc['awAdd']): ?><div class="layui-unselect layui-form-select" onclick="layer_show('<?php echo ($acc['awAdd']['auth_name']); ?>','<?php echo U($acc['awAdd']['auth_path'],'actid='.$request['actid']);?>','700','650')">
                                    <div class="layui-select-title">
                                        <input type="button" class="layui-input layui-unselect" value="添加"><i class="layui-edge"></i>
                                    </div>
                                </div><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <form class="layui-form">
            <div class="layui-table">
                <table class="layui-table">
                    <colgroup>
                        <col width="50">
                        <col width="200">
                        <col width="100">
                        <col width="150">
                        <col width="220">
                        <col width="100">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="150">
                        <col width="200">
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="text-align: center;">
                                <div class="layui-form-item">
                                    <input type="checkbox" lay-skin="primary" lay-filter="allChoose"/>
                                </div>
                            </th>
                            <th style="text-align: center;">活动标题</th>
                            <th style="text-align: center;">奖品图片</th>
                            <th style="text-align: center;">奖品类型</th>
                            <th style="text-align: center;">奖品名称</th>
                            <th style="text-align: center;">库存量</th>
                            <th style="text-align: center;">中奖次数</th>
                            <th style="text-align: center;">出奖率</th>
                            <th style="text-align: center;">排序</th>
                            <th style="text-align: center;">时间区间</th>
                            <th style="text-align: center;">是否上架</th>
                            <th style="text-align: center;">操作</th>
                            
                        </tr> 
                    </thead>
                    <tbody>
                        <?php if(is_array($lists)): $k = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr style="text-align: center;">
                                <td>
                                    <div class="layui-form-item">
                                        <input type="checkbox" name="checkId[]" value="<?php echo ($vo["id"]); ?>" lay-skin="primary" />
                                    </div>
                                </td>
                                <td><?php echo ($vo["title"]); ?></td>
                                <td><img src="<?php echo ($vo["thumb"]); ?>" class="view-img"></td>
                                <td><?php echo ($vo["atype"]); ?></td>
                                <td><?php echo ($vo["name"]); ?></td>
                                <td><?php echo ($vo["stock"]); ?></td>
                                <td>
                                    <div class="layui-form-item">
                                        <input type="text" name="winnum" value="<?php echo ($vo["winnum"]); ?>" autocomplete="off" class="layui-input"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="layui-form-item">
                                        <input type="text" name="pronum" value="<?php echo ($vo["pronum"]); ?>" autocomplete="off" class="layui-input"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="layui-form-item">
                                        <input type="text" name="listorder" value="<?php echo ($vo["listorder"]); ?>" autocomplete="off" class="layui-input"/>
                                    </div>
                                </td>
                                <td><?php echo (date('Y-m-d H:m:s',$vo["stime"])); ?><br/><?php echo (date('Y-m-d H:m:s',$vo["etime"])); ?></td>
                                <td>
                                    <input type="checkbox" value='<?php echo ($vo["id"]); ?>' <?php if($vo['online'] == 1): ?>checked<?php endif; ?> lay-filter="switchOnline" lay-skin="switch" lay-text="ON|OFF">
                                </td>
                                <td>
                                    <?php if($acc['awEdit']): ?><a href="javascript:void(0);" onclick="lay_full('编辑奖品','<?php echo U($acc['awEdit']['auth_path'],'id='.$vo['id'].'&actid='.$vo['actid']);?>','1200','600')"><?php echo ($acc['awEdit']['auth_name']); ?></a><?php endif; ?>
                                    <?php if($acc['awDel']): ?><a href="javascript:void(0);" onclick="delCate(<?php echo ($vo["id"]); ?>)"><?php echo ($acc['awDel']['auth_name']); ?></a><?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
                <div class="tab-footer">
                    <div class="left-oper"> 
                        <div class="layui-form-item">
                            <select name="operType" lay-filter="operType">
                              <option value="">批量操作</option>
                              <option value="1">奖品上架</option>
                              <option value="2">奖品下架</option>
                              <option value="3">移入回收站</option>
                            </select>
                        </div>
                    </div>
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
    layui.use('form', function(){
        var form = layui.form
        ,layer = layui.layer;
        
        form.on('switch(switchOnline)', function(data){
            var id = $(this).val();
            var nval = this.checked ? 1 : 2;
            var ntype = 'online';
            setOper([id],nval,ntype);
        });
        
        form.on('checkbox(allChoose)', function(data){  
            var child = $(data.elem).parents('table').find('tbody input[name="checkId[]"]');  
            child.each(function(index, item){  
                item.checked = data.elem.checked;  
            });  
            form.render('checkbox');  
        });
        form.on('select(operType)', function(data){
            var dateType = data.value;
            var child = $('table').find('tbody input[name="checkId[]"]');
            var ids = [];
            child.each(function(index, item){  
                if(item.checked){
                    ids.push(item.value);
                }
            });
            if(dateType != ""){
                if(ids.length > 0){
                    data.elem.options[0].selected = true;
                    form.render('select');
                    switch(dateType){
                        case '1': //奖品上架
                            setAllOper(ids,1,'online');
                            break;
                        case '2': //奖品下架
                            setAllOper(ids,2,'online');
                            break;
                        case '3': //移入回收站
                            delAll(ids);
                            break;
                    }
                }else{
                    layer.msg("未选中无法操作");
                    data.elem.options[0].selected = true;
                    form.render('select');
                }
            }
        }); 
    });
    
    function setAllOper(ids,nval,ntype)
    {
        var msg = "";
        switch(ntype){
            case 'online': //上架/下架
                if(nval == 1){
                    msg = "您确定要将选中数据批量上架吗？";
                }else{
                    msg = "您确定要将选中数据批量下架吗？";
                }
                break;
        }
        layer.confirm(msg, {
            btn: ['确定','取消'] //可以无限个按钮
        }, function(index){
            setOper(ids,nval,ntype);
        });
    }
    
    function setOper(ids,nval,ntype)
    {
        var lod_index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post('/Admin/Award/ajax_update', { ids: ids,nval: nval,ntype: ntype },function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.error == 0){
                layer.msg(parsedJson.message);
                window.location.reload();
            }else{
                layer.msg(parsedJson.message);
            }
        });
    }
    
    function delAll(ids)
    {
        var msg = "您确定要批量删除选中数据吗？";
        layer.confirm(msg, {
            btn: ['确定','取消'] //可以无限个按钮
        }, function(index){
            var lod_index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });;
            $.post('/Admin/Award/del', { ids: ids },function(data){
                layer.close(lod_index);
                var parsedJson = jQuery.parseJSON(data);
                if(parsedJson.state == 0){
                    layer.msg(parsedJson.message);
                    window.location.reload();
                }else{
                    layer.msg(parsedJson.message);
                }
            });
        });
    }

    // 中奖次数设置
    $("input[name='winnum']").on('blur',function(a){
        var id = $(this).parent().parent().parent().find("input[name='checkId[]']").val();
        var nval = $(this).val();
        var ntype = 'winnum';
        setOper([id],nval,ntype);
    });

    // 中奖率设置
    $("input[name='pronum']").on('blur',function(a){
        var id = $(this).parent().parent().parent().find("input[name='checkId[]']").val();
        var nval = $(this).val();
        var ntype = 'pronum';
        setOper([id],nval,ntype);
    });

    // 排序设置
    $("input[name='listorder']").on('blur',function(a){
        var id = $(this).parent().parent().parent().find("input[name='checkId[]']").val();
        var nval = $(this).val();
        var ntype = 'listorder';
        setOper([id],nval,ntype);
    });

    // 分页设置
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
                    let keyword = $('input[name="keyword"]').val();
                    jsGet('', {curr: obj.curr,keyword:keyword});
                }
            }
        });
    });
    
    // 单个奖品删除
    function delCate(gid)
    {
        var lod_index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
        $.post('/Admin/Award/del', { ids: gid },function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.state == 0){
                window.location.reload();
                layer.msg(parsedJson.message);
            }else{
                layer.msg(parsedJson.message);
            }
        });
    }
    
    function subForm()
    {
        $("#searchForm").submit();
    }

    function subForms(id)
    {
        $("#searchForms").submit();
    }
    
    $(function(){
        var windowHeigth = $(document).height();
        $(".main-body").height(windowHeigth);
    });
    
    $(function(){
        var goods_search = <?php echo ($request['search']); ?>;
        if(goods_search == 1){
            $("#search_1").removeClass('layui-btn-primary');
            $("#search_1 span").removeClass('ziti');
        }else if(goods_search == 2){
            $("#search_1").addClass('layui-btn-primary');
            $("#search_2").removeClass('layui-btn-primary');
            $("#search_2 span").removeClass('ziti');
        }else if(goods_search == 3){
            $("#search_1").addClass('layui-btn-primary');
            $("#search_3").removeClass('layui-btn-primary');
            $("#search_3 span").removeClass('ziti');
        }
    });

    // 保存默认数据
    function saveData() {
        var lod_index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
        var defNum = $('input[name="defNum"]').val();
        var actid = $('input[name="actid"]').val();
        $.post('/Admin/Award/saveData', { defNum:defNum, actid:actid },function(data){
            layer.close(lod_index);
            var parsedJson = jQuery.parseJSON(data);
            if(parsedJson.error == 0){
                layer.msg(parsedJson.message);
                window.location.href='/Admin/Award/index/actid/'+actid;
            }else{
                layer.msg(parsedJson.message);
            }
        });
    }

    // 出奖率计算
    function checkNum() {
        var actid = $('input[name="actid"]').val();
        $.post('/Admin/Award/proRand', { actid:actid },function(data){
            var parsedJson = jQuery.parseJSON(data);
            $("#numInfo").show();
            $("#numInfo").empty();
            $("#numInfo").append(parsedJson.msg);
        });
    }
</script>
</body>
</html>