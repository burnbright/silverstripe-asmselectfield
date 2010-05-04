jQuery(document).ready(function($) {
    $("select.asmselectfield").livequery(function(){
	    $(this).asmSelect({
	    	listType: 'ul'
	    });
	});
}); 