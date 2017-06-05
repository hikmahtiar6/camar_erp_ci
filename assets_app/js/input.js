window.INPUT = (function($) {
	return {
		numberingOnly: function(inputEl) {
			$(inputEl).keydown(function (event) {


	            if (event.shiftKey == true) {
	                event.preventDefault();
	            }

	            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

	            } else {
	                event.preventDefault();
	            }
	            
	            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
	                event.preventDefault();

	        });
		},

		numberingKeyDown: function(event, val) {
            if (event.shiftKey == true) {
                //event.preventDefault();
            }

            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }
            
            if($(val).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();

		},

		moveInputInTable: function(event, val) {
			var $this = $(val);
		    var $tr = $this.closest("tr");
		    var id = 'test';
		    
		    if(event.keyCode == 38){
		        $tr.prev().find('input[id^='+id+']').focus();
		    }
		    else if(event.keyCode == 40)
		    {
		       $tr.next().find("input[id^='"+id+"']").focus();
		    }
		}
	}
})(jQuery);