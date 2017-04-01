window.DATE = (function($) {
	return {
		init: function() {
			$('.date-bootstrap').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false,
				format: 'DD/MM/YYYY' 
			});
		}
	}
})(jQuery);