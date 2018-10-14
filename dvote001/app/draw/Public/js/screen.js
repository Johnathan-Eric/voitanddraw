// ----------------------------------------------//
//                                               //
//               summer             //
//                                               //
// ----------------------------------------------//
//Regional开始
$(document).ready(function(){
    $(".Regional").click(function(){
        if ($('.grade-eject').hasClass('grade-w-roll')) {
            $('.grade-eject').removeClass('grade-w-roll');
             $("#mbg").hide();
        } else {
            $('.grade-eject').addClass('grade-w-roll');
             $("#mbg").show();
        }
    });
});

$(document).ready(function(){
    $(".grade-w>li").click(function(){
        $(".grade-t")
            .css("left","50%")
    });
});

$(document).ready(function(){
    $(".grade-t>li").click(function(){
        $(".grade-s")
            .css("left","50%")
    });
});
//suffer,sort,Degree开始
$(document).ready(function(){
    $(".Cdown").click(function(){
    		var linum=$(this).index()-1;
    		var bdnum=$(".person-list .hd").find(".down-eject");
    		 if (bdnum.eq(linum).hasClass('grade-w-roll')){
            bdnum.eq(linum).removeClass('grade-w-roll');
             $("#mbg").hide();
        }else{
        		bdnum.eq(linum).addClass("grade-w-roll").siblings(".down-eject").removeClass("grade-w-roll");
        		 $("#mbg").show();
        };
      
    });
});
//判断页面是否有弹出
$(document).ready(function(){
    $(".Regional").click(function(){
        if ($('.down-eject').hasClass('grade-w-roll')){
            $('.down-eject').removeClass('grade-w-roll');
        };
    });
});
$(document).ready(function(){
    $(".Cdown").click(function(){
        if ($('.grade-eject').hasClass('grade-w-roll')){
            $('.grade-eject').removeClass('grade-w-roll');
        };
    });
});
//js点击事件监听开始
function grade1(wbj){
    var arr = document.getElementById("gradew").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.background = "";
    };
    wbj.style.background = "#eee"
}

function gradet(tbj){
    var arr = document.getElementById("gradet").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.background = "";
    };
}

function grades(sbj){
    var arr = document.getElementById("grades").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.borderBottom = "";
    };
    sbj.style.borderBottom = "solid 1px #ff7c08"
}


function Degree(sbj){
    var arr = document.getElementById("Degree").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.background = "";
    };
    sbj.style.background = "#eee"
}

function Sorts(sbj){
    var arr = document.getElementById("Sorts").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.background = "";
    };
    sbj.style.background = "#eee"
}
function Suffer(sbj){
    var arr = document.getElementById("Suffer").getElementsByTagName("li");
    for (var i = 0; i < arr.length; i++){
        var a = arr[i];
        a.style.background = "";
    };
    sbj.style.background = "#eee"
}