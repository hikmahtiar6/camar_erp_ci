window.TRANSFER = (function($) {
	return {
		init: function() {
			var parentThis = this;
		},

		handleVue: function() {
			new Vue({
				el: '.card-transfer'
			});
		}
	}
})(jQuery);