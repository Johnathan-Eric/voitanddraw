var strVarCity = "";
strVarCity += "<div class=\"aui_state_box\"><div class=\"aui_state_box_bg\"></div>";
strVarCity += "  <div class=\"aui_alert_zn aui_outer\">";
strVarCity += "    <table class=\"aui_border\" style=\"border:2px solid #fff;\">";
strVarCity += "      <tbody>";
strVarCity += "        <tr>";
strVarCity += "          <td class=\"aui_w\">";
strVarCity += "          <\/td>";
strVarCity += "          <td class=\"aui_c\">";
strVarCity += "            <div class=\"aui_inner\">";
strVarCity += "              <table class=\"aui_dialog\">";
strVarCity += "                <tbody>";
strVarCity += "                  <tr>";
strVarCity += "                    <td class=\"aui_header\" colspan=\"2\"><div class=\"aui_titleBar\">";
strVarCity += "                    ";
strVarCity += "                        <div class=\"aui_title\" style=\"cursor: move;\">选择城市信息<\/div>";
strVarCity += "                        <a href=\"javascript:;\" class=\"aui_close\" onclick=\"Close()\">×<\/a><\/div><\/td>";
strVarCity += "                  <\/tr>";
strVarCity += "                  <tr>";
strVarCity += "                <td class=\"aui_icon\" style=\"display: none;\">";
strVarCity += "                   <div class=\"aui_iconBg\" style=\"background: transparent none repeat scroll 0% 0%;\"><\/div><\/td>";
strVarCity += "                     <td class=\"aui_main\" style=\"width: auto; height: auto;\">";
strVarCity += "                      <div class=\"aui_content\" style=\"padding: 0px; position:relative\">";
strVarCity += "                        <div id=\"\" style=\"width: 900px; position:relative;\">";
strVarCity += "                          <div class=\"data-result\"><em>最多选择 <strong>3<\/strong> 项<\/em><\/div>";
strVarCity += "                          <div class=\"data-error\" style=\"display: none;\">最多只能选择 3 项<\/div>";
strVarCity += "                          <div class=\"data-tabs\">";
strVarCity += "                            <ul>";
strVarCity += "                              <li onclick=\"removenode_area(this)\" data-selector=\"tab-all\" class=\"active\"><a href=\"javascript:;\"><span>全部<\/span><em><\/em><\/a><\/li>";
strVarCity += "                            <\/ul>";
strVarCity += "                          <\/div>";
strVarCity += "                          <div class=\"data-container data-container-city\">";

strVarCity += "";
strVarCity += "                            <\/div>";
strVarCity += "                          <\/div>";
strVarCity += "                          ";
strVarCity += "                        <\/div>";
strVarCity += "                         ";
strVarCity += "                      <\/div>";
strVarCity += "                      ";
strVarCity += "                    <\/td>";
strVarCity += "                  <\/tr>";
strVarCity += "                  <tr>";
strVarCity += "                    <td class=\"aui_footer\" colspan=\"2\"><div class=\"aui_buttons\">";
strVarCity += "                      <button class=\"aui-btn aui-btn-primary\" onclick=\"svae_City()\" type=\"button\">确定<\/button>";
strVarCity += "                        <button class=\"aui-btn aui-btn-light\" onclick=\"Close()\" type=\"button\">取消<\/button>";
strVarCity += "                      <\/div><\/td>";
strVarCity += "                  <\/tr>";
strVarCity += "                <\/tbody>";
strVarCity += "              <\/table>";
strVarCity += "            <\/div><\/td>";
strVarCity += "          <td class=\"aui_e\"><\/td>";
strVarCity += "        <\/tr>";
strVarCity += "      <\/tbody>";
strVarCity += "    <\/table>";
strVarCity += "  <\/div>";
strVarCity += "<\/div>";

var dataCityinput = null;

var dataarrary = __LocalDataCities.list;
var allArr = [];
function appendCity(thiscon) {
    dataCityinput = thiscon;
    $('body').append(strVarCity);
    //console.log(inputarry);
    allParmDOM = $("input[name='citys_date[]']");
    allArr = [];
    allParmDOM.each(function(){
        var theStr = $(this).val();
        if($(dataCityinput).data("value") != theStr){
            var theArr = theStr.split('-');
            allArr = allArr.concat(theArr);
        }
    });

    if ($(dataCityinput).data("value") != "") {
        var dval = $(dataCityinput).data("value");
        var reg = new RegExp("^.*-.*$");
        if (reg.test(dval)) {
            var inputarry = dval.split('-');
        } else {
            var inputarry = new Array();
            inputarry.push(dval);
        }
        
        if (inputarry.length > 0) {
            for (var i in inputarry) {
                var inputname = dataarrary[inputarry[i]][0];
                $('.data-result').append("<span class=\"svae_box aui-titlespan\" data-code=\"" + inputarry[i] + "\" data-name=\"" + inputname + "\" onclick=\"removespan_area(this)\">" + inputname + "<i>×<\/i><\/span>");
            }
        }
    }
    
    var minwid = document.documentElement.clientWidth;
    $('.aui_outer .aui_header').on("mousedown", function(e) {
        /*$(this)[0].onselectstart = function(e) { return false; }*///防止拖动窗口时，会有文字被选中的现象(事实证明不加上这段效果会更好)
        $(this)[0].oncontextmenu = function(e) { return false; } //防止右击弹出菜单
        var getStartX = e.pageX,
		getStartY = e.pageY;
        var getPositionX = (minwid / 2) - $(this).offset().left,
		getPositionY = 60;
        $(document).on("mousemove", function(e) {
            var getEndX = e.pageX,
			getEndY = e.pageY;
            $('.aui_outer').css({
                left: getEndX - getStartX - getPositionX,
                top: getEndY - getStartY + getPositionY
            });

        });
        $(document).on("mouseup", function() {
            $(document).unbind("mousemove");
        })
    });
    selectProvince('all', null , '');
}

function selectProvince(type, con, isremove) {
    //显示省级
    var strVarCity = "";
    if (type == "all") {
        var dataCityxz = __LocalDataCities.category.provinces;
        //console.log(dataCityxz);
        strVarCity += "                              <p class=\"data-title\">全部省份<\/p>";
        strVarCity += "                              <div class=\"data-list\">";
        strVarCity += "                                <ul class=\"clearfix\">";
        for (var i in dataCityxz) {
            strVarCity += '<li><a href=\"javascript:;\" data-code=\"' + dataCityxz[i] + '\" data-name=\"' + dataarrary[dataCityxz[i]][0] + '\" class=\"d-item\"  onclick="selectProvince(\'sub\',this,\'\')">' + dataarrary[dataCityxz[i]][0] + '<label>0</label></a></li>';
        }
        strVarCity += "                                <\/ul>";
        strVarCity += "                              <\/div>";
        $('.data-container-city').html(strVarCity);
        $('.data-result span').each(function(index) {
            if ($('a[data-code=' + $(this).data("code") + ']').length > 0) {
                $('a[data-code=' + $(this).data("code") + ']').addClass('d-item-active');
                if ($('a[data-code=' + $(this).data("code") + ']').attr("class").indexOf('data-all') > 0) {
                    $('a[data-code=' + $(this).data("code") + ']').parent('li').nextAll('li').find('a').css({ 'color': '#ccc', 'cursor': 'not-allowed' });
                    $('a[data-code=' + $(this).data("code") + ']').parent('li').nextAll("li").find('a').attr("onclick", "");
                }
            } else {
                var numlabel = $('a[data-code=' + $(this).data("code").toString().substring(0, 3) + ']').find('label').text();
                $('a[data-code=' + $(this).data("code").toString().substring(0, 3) + ']').find('label').text(parseInt(numlabel) + 1).show();
            }
        });
    }
    //显示下一级
    else {
        var dataCityxz = __LocalDataCities.category.provinces;
        var relations = __LocalDataCities.relations;
        if (typeof (relations[$(con).data("code")]) != "undefined") {
            //添加标题
            if (isremove != "remove") {
                $('.data-tabs ul').append('<li data-code=' + $(con).data("code") + ' data-name=' + $(con).data("name") + ' class="active" onclick="removenode_area(this)"><a href="javascript:;"><span>' + $(con).data("name") + '</span><em></em></a></li>');
            }
            //添加内容
            strVarCity += "<ul class=\"clearfix\">";
            strVarCity += '<li class="" style="width:100%"><a href="javascript:;" class="d-item data-all"  data-code="' + $(con).data("code") + '"  data-name=\"' + $(con).data("name") + '\"  onclick="selectitem_area(this)">' + $(con).data("name") + '<label>0</label></a></li>';
            for (var i in relations[$(con).data("code")]) {
                strVarCity += '<li><a href="javascript:;" class="d-item" data-code="' + relations[$(con).data("code")][i] + '"  data-name=\"' + dataarrary[relations[$(con).data("code")][i]][0] + '\" onclick="selectProvince(\'sub\',this,\'\')">' + dataarrary[relations[$(con).data("code")][i]][0] + '<label>0</label></a></li>';
            }
            strVarCity += "<\/ul>";
            $('.data-container-city').html(strVarCity);
        } else {
            if ($(con).attr("class").indexOf('d-item-active') > 0) {
                $('.data-result span[data-code="' + $(con).data("code") + '"]').remove();
                $(con).removeClass('d-item-active');
                return false;
            }
            $('.data-result').append("<span class=\"svae_box aui-titlespan\" data-code=\"" + $(con).data("code") + "\" data-name=\"" + $(con).data("name") + "\" onclick=\"removespan_area(this)\">" + $(con).data("name") + "<i>×<\/i><\/span>");
            $(con).addClass('d-item-active');
        }
        $('.data-result span').each(function(index) {
            if ($('a[data-code=' + $(this).data("code") + ']').length > 0) {
                $('a[data-code=' + $(this).data("code") + ']').addClass('d-item-active');
                if ($('a[data-code=' + $(this).data("code") + ']').attr("class").indexOf('data-all') > 0) {
                    $('a[data-code=' + $(this).data("code") + ']').parent('li').nextAll('li').find('a').css({ 'color': '#ccc', 'cursor': 'not-allowed' });
                    $('a[data-code=' + $(this).data("code") + ']').parent('li').nextAll("li").find('a').attr("onclick", "");
                }
            } else {
                //var numlabel=$('a[data-code='+$(this).data("code").toString().substring(0,6)+']').find('label').text();
                $('a[data-code=' + $(this).data("code").toString().substring(0, 6) + ']').find('label').text('*').show();
            }
        });
    }
    //console.log(dataarrary);
    //var keyArr = getKeyByObj(dataarrary);
    $('.data-container-city').find('li').each(function(){
        //console.log($(this).find('a').attr("data-code"));
        var datekey = $(this).find('a').attr("data-code");
        if(contains(allArr, datekey)){
            //console.log($(this).find('a').attr("data-code"));
            $(this).find('a').css({ 'color': '#ccc', 'cursor': 'not-allowed' })
            $(this).find('a').attr("onclick", "");
        }
    });

}

function selectitem_area(con) {
    var relations = __LocalDataCities.relations;
    //console.log(relations);
    //console.log(con);
    var codeVal = $(con).attr("data-code");
    var cityArr = relations[codeVal];
    var newArr = cityArr;
    if(cityArr.length > 0){
        for(i in cityArr){
            if(relations[cityArr[i]]){
                newArr = newArr.concat(relations[cityArr[i]]);
            }
        }
    }
    //console.log(allArr);
    //console.log(newArr);
    if ($(con).attr("class").indexOf("d-item-active") == -1) {
        //选中包含下级区域的地区判断下级区域是否被别的选项选中，如果下级区域被选中不让选择整个区域，并弹出提示。
        var flag = false;
        if(allArr.length > 0){
            for(i in allArr){
                if(contains(newArr, allArr[i])){
                    flag = true;
                }
            }
        }
        if(flag){
            alert("本模板已有可配送区域包含此下级区域的选项所以不能直接选取此区域");
            return;
        }else{
            $(con).parent('li').nextAll("li").find('a').css({ 'color': '#ccc', 'cursor': 'not-allowed' })
            $(con).parent('li').nextAll("li").find('a').attr("onclick", "");
        }
    } else {
        $(con).parent('li').nextAll("li").find('a').css({ 'color': '#0077b3', 'a.d-item-active:hover': '#fff', 'cursor': 'pointer' })
        $(con).parent('li').nextAll("li").find('a').attr("onclick", "selectProvince(\'sub\',this,\'\')");
    }
    $('.data-result span').each(function() {
        if ($(this).data("code").toString().substring(0, $(con).data("code").toString().length) == $(con).data("code").toString()) {
            $(this).remove();
        }
    })
    $(con).parent('li').siblings('li').find("a").removeClass("d-item-active");
    if ($(con).attr("class").indexOf('d-item-active') > 0) {
        $('.data-result span[data-code="' + $(con).data("code") + '"]').remove();
        $(con).removeClass('d-item-active');
        return false;
    }
    $('.data-result').append("<span class=\"svae_box aui-titlespan\" data-code=\"" + $(con).data("code") + "\" data-name=\"" + $(con).data("name") + "\" onclick=\"removespan_area(this)\">" + $(con).data("name") + "<i>×<\/i><\/span>");
    $(con).addClass('d-item-active');
}

function removenode_area(lithis) {
    if ($(lithis).nextAll('li').length == 0) {
        return false;
    }
    $(lithis).nextAll('li').remove();
    if ($(lithis).data("selector") == "tab-all") {
        selectProvince('all', null, '');
    }
    else {
        selectProvince('sub', lithis, 'remove');
    }
}

function removespan_area(spanthis) {
    $('a[data-code=' + $(spanthis).data("code") + ']').removeClass('d-item-active');
    if ($('a[data-code=' + $(spanthis).data("code") + ']').length > 0) {
        if ($('a[data-code=' + $(spanthis).data("code") + ']').attr("class").indexOf('data-all') > 0) {
            $('a[data-code=' + $(spanthis).data("code") + ']').parent('li').nextAll('li').find('a').css({ 'color': '#0077b3', 'a.d-item-active:hover': '#fff', 'cursor': 'pointer' });
            $('a[data-code=' + $(spanthis).data("code") + ']').parent('li').nextAll("li").find('a').attr("onclick", "selectProvince(\'sub\',this,\'\')");
        }
    }
    $(spanthis).remove();
}

//确定选择
function svae_City() {

    var relations = __LocalDataCities.relations;
    var val = '';
    var Cityname = '';
    var CityNewName = '';
    var cityNewCname ='';
    var p_arr = [];
    if ($('.svae_box').length > 0) {
        $('.svae_box').each(function() {
            var codeStr = $(this).data("code").toString();
            var CityChildName = '';
            for (var i in relations[$(this).data("code")]) {
                CityChildName += dataarrary[relations[$(this).data("code")][i]][0] + "-";
            }
            CityChildName = CityChildName.substr(0,CityChildName.length-1);
            if(CityChildName){
                CityNewName += CityChildName + '-';
            }else{
                CityNewName += $(this).data("name") + '-';
            }
            if(codeStr.length >= 6){
                var np_id = codeStr.substr(0,codeStr.length-3);
                p_arr.push(np_id);
            }else{
                p_arr.push(codeStr);
            }
            val += $(this).data("code") + '-';
            Cityname += $(this).data("name") + '-';
        });
        p_arr = unique(p_arr);
        for(var b in p_arr){
            var varr = val.split("-");
            var CityChildName = '';
            for(var c in varr){
                if(varr[c] !==  p_arr[b]){
                    var cStr = varr[c].toString();
                    var cpid = cStr.substr(0,cStr.length-3);
                    if(cpid === p_arr[b]){
                        CityChildName += dataarrary[varr[c]][0] + ",";
                    }
                }
            }
            CityChildName = CityChildName.substr(0,CityChildName.length-1);
            if(CityChildName){
                cityNewCname += dataarrary[p_arr[b]][0] + '(' + CityChildName + ')-';
            }else{
                cityNewCname += dataarrary[p_arr[b]][0] + "-"; 
            }
        }
        cityNewCname = cityNewCname.substr(0,cityNewCname.length-1);
        CityNewName = CityNewName.substr(0,CityNewName.length-1);
        //console.log(CityNewName);
    }
    if (val != '') {
        val = val.substring(0, val.lastIndexOf('-'));
    }
    if (Cityname != '') {
        Cityname = Cityname.substring(0, Cityname.lastIndexOf('-'));
    }
    $(dataCityinput).data("value", val);
    $(dataCityinput).parent().parent().find('input[name="citys[]"]').val(CityNewName);
    $(dataCityinput).parent().parent().find('textarea[name="city_text"]').val(cityNewCname);
    $(dataCityinput).parent().parent().find('input[name="citys_views[]"]').val(cityNewCname);
    $(dataCityinput).parent().parent().find('input[name="citys_date[]"]').val(val);
    Close();
}

function unique(array){ 
    var n = []; //一个新的临时数组 
    //遍历当前数组 
    for(var i = 0; i < array.length; i++){ 
    //如果当前数组的第i已经保存进了临时数组，那么跳过， 
    //否则把当前项push到临时数组里面 
    if (n.indexOf(array[i]) == -1) n.push(array[i]); 
    } 
    return n; 
}

function Close() {
    $('.aui_state_box').remove();
}

function getKeyByObj(obj)
{
    var Mykey = [];
    for(i in obj){
        Mykey.push(i);
    }
    return Mykey;
}

function contains(arr, obj) {  
    var i = arr.length;  
    while (i--) {  
        if (arr[i] === obj) {  
            return true;  
        }  
    }  
    return false;  
}  














