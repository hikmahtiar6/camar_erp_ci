window.PO = (function($) {
    return {
    	init: function() {
    		$("form").ajaxForm({
    			success: function(response) {
    				$("#result").slideDown(1000);
    			}
    		});
    	}
    }
})(jQuery);