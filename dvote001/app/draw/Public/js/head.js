$(document).ready(function() {
		
		$('.auseletit').click(function(){
			$(".i-ausele").toggle();
		});
		
		$(".i-ausele a").click(function() {
			var text = $(this).html();
			$(".auseletit span").html(text);
			$(".i-ausele").hide();
		});	
		
		
		jQuery.focusblur = function(focusid) {
		var focusblurid = $(focusid);
		var defval = focusblurid.val();
        focusblurid.focus(function(){
		var thisval = $(this).val();
		if(thisval==defval){
                $(this).val("");
            }
        });
        focusblurid.blur(function(){
		var thisval = $(this).val();
		if(thisval==""){
                $(this).val(defval);
            }
        });
        
    };
    $.focusblur("#searchkey");	
});