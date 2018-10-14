   $.validator.addMethod("chinese", function(value, element) {  
        var chinese = /^[\u4e00-\u9fa5]+$/;  
        return (chinese.test(value)) || this.optional(element);  
    }, "请填写中文");
	$.validator.addMethod("ChiEng", function(value, element) {       
        var address = /^[\u4e00-\u9fa5A-Za-z]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括中文字、英文字");
	$.validator.addMethod("tag", function(value, element) {       
        var tag = /^[\u4e00-\u9fa5A-Za-z0-9 ,，]+$/;       
        return this.optional(element) || (tag.test(value));       
	}, "只能包括中文字、英文字、数字、空格和逗号");
    $.validator.addMethod("isTel", function(value, element) {       
        var tel = /^\d{3,4}-?\d{7,9}$/;      
        return this.optional(element) || (tel.test(value));       
    }, "请正确填写有效电话号码");  	
	$.validator.addMethod("isZipCode", function(value, element) {       
        var tel = /^[0-9]{6}$/;       
        return this.optional(element) || (tel.test(value));       
    }, "请正确填写有效邮政编码");
	$.validator.addMethod("stringCheck", function(value, element) {       
        var address = /^[\u4e00-\u9fa5A-Za-z0-9]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括中文字、英文字母、数字"); 
	$.validator.addMethod("address", function(value, element) {       
        var address = /^[\u4e00-\u9fa5A-Za-z0-9- ]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括中文字、英文字母、数字、空格和-"); 
	$.validator.addMethod("string", function(value, element) {       
        var address = /^[A-Za-z0-9-]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括英文字母、数字和-");
	$.validator.addMethod("chinesecode", function(value, element) {  
	     var chinesecode = /^[\u4e00-\u9fa5\_]+$/;  
	     return (chinesecode.test(value)) || this.optional(element);  
	}, "只能包括中文字和_");
	$.validator.addMethod("englishcode", function(value, element) {       
        var address = /^[A-Za-z0-9]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括英文字母和数字");
	$.validator.addMethod("qqnum", function(value, element) {
	    var qqnum = /^[1-9]\d{4,9}$/; 
	    return this.optional(element) || (qqnum.test(value));
	}, "qq号码格式错误");
	$.validator.addMethod("valueNotEq", function(value, element, arg){
		return arg != value;
		}, "数据不能为:arg");	
 	$.validator.addMethod("PL", function(value, element) {         
    return this.optional(element) || /^\d+(\.{1})?$/.test(value);         
	}, "不能为小数"); 
	$.validator.addMethod("english", function(value, element) {       
        var address = /^[A-Za-z]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括英文字母");
    $.validator.addMethod("englishcodes", function(value, element) {       
        var address = /^[A-Za-z0-9-_]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "只能包括英文字母和数字-和_");
    $.validator.addMethod("moneystyle", function(value, element) {       
        var address = /^[0-9]+[.][0-9]{2}$/;       
        return this.optional(element) || (address.test(value));       
	}, "填写正确的金额 例如：100.00");
	$.validator.addMethod("shipmoney", function(value, element) {       
        var address = /^[0-9]+$|^[0-9]+[.][0-9]+$/;       
        return this.optional(element) || (address.test(value));       
	}, "填写正确的金额");