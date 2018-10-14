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
                </div>
            </div>
            <div class="search-form">
                <form class="layui-form" method="get" action="" id="searchForm">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">输入搜索：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="keyword" placeholder="活动标题" value="<?php echo ($request['keyword']); ?>" autocomplete="off" class="layui-input">
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
                        <p><ic><img src='/Public/new/img/i_list.png'/></ic><span><?php echo ($page_header); ?></span></p>
                    </div>
                </div>
                <div class="pub_menu_left">
                    <div class="m_left">
                        <div class="layui-form-item">
                            <?php if($acc['awAdd']): ?><div class="layui-unselect layui-form-select" onclick="layer_show('<?php echo ($acc['awAdd']['auth_name']); ?>','<?php echo U($acc['awAdd']['auth_path']);?>','700','650')">
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
                        <col width="100">
                        <col width="150">
                        <col width="170">
                        <col width="170">
                        <col width="200">
                        <col width="150">
                        <col width="150">
                        <col width="100">
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="text-align: center;">
                                <div class="layui-form-item">
                                    <input type="checkbox" lay-skin="primary" lay-filter="allChoose"/>
                                </div>
                            </th>
                            <th style="text-align: center;">封面</th>
                            <th style="text-align: center;">标题</th>
                            <th style="text-align: center;">开始时间</th>
                            <th style="text-align: center;">结束时间</th>
                            <th style="text-align: center;">报名时间</th>
                            <th style="text-align: center;">投票时间</th>
                            <th style="text-align: center;">抽奖次数/投票数</th>
                            <th style="text-align: center;">操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php if(is_array($lists)): $k = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr style="text-align: center;">
                                <td>
                                    <div class="layui-form-item">
                                        <input type="checkbox" name="checkId[]" value="<?php echo ($vo["rid"]); ?>" lay-skin="primary" />
                                    </div>
                                </td>
                                <td><img src="<?php echo ($vo["thumb"]); ?>" class="view-img"></td>
                                <td><?php echo ($vo["title"]); ?></td>
                                <td><?php echo (date('Y-m-d H:m:s',$vo["starttime"])); ?></td>
                                <td><?php echo (date('Y-m-d H:m:s',$vo["endtime"])); ?></td>
                                <td><?php echo (date('Y-m-d H:m:s',$vo["apstarttime"])); ?><br/><?php echo (date('Y-m-d H:m:s',$vo["apendtime"])); ?></td>
                                <td><?php echo (date('Y-m-d H:m:s',$vo["votestarttime"])); ?><br/><?php echo (date('Y-m-d H:m:s',$vo["voteendtime"])); ?></td>
                                <td><input type="text" name="pervote" value="<?php echo ($vo["pervote"]); ?>" autocomplete="off" class="layui-input"/></td>
                                <td>
                                    <a href="/Admin/Award/index/actid/<?php echo ($vo["rid"]); ?>">奖品配置</a>
                                </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
                <div class="tab-footer">
                    <!-- <div class="left-oper"> 
                        <div class="layui-form-item">
                            <select name="operType" lay-filter="operType">
                              <option value="">批量操作</option>
                              <option value="1">奖品上架</option>
                              <option value="2">奖品下架</option>
                              <option value="3">移入回收站</option>
                            </select>
                        </div>
                    </div> -->
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
    });

    // 抽奖次数/投票数
    $("input[name='pervote']").on('blur',function(a){
        var id = $(this).parent().parent().find("input[name='checkId[]']").val();
        var nval = $(this).val();
        var ntype = 'pervote';
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

    function setOper(ids,nval,ntype)
    {
        var lod_index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post('/Admin/Activity/ajax_update', { ids: ids,nval: nval,ntype: ntype },function(data){
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
    
    function subForm()
    {
        $("#searchForm").submit();
    }
    
    $(function(){
        var windowHeigth = $(document).height();
        $(".main-body").height(windowHeigth);
    });
</script>
</body>
</html>