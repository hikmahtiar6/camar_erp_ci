window.DIES = (function($) {
	return {
		init: function() {
			var _this = this;

			_this.handleFilter();
		},

		handleFilter: function() {
			var _this = this;

			$('.form-dies-idx').ajaxForm({
				success: function(response) {
					var html = response;

					if(response.length == 24) {
						html = "<tr>" +
						"<td colspan='4'>Data tidak tersedia.</td>" +
						"</tr>"; 
					}
					$('.result-dies-idx').html(html);

					_this.handleButtonAction();

				}
			});
		},

		submitFilter: function() {
			$('.form-dies-idx').submit();
		},

		handleButtonAction: function() {
			$('.btn-action-die').click(function() {

				var _input = this;

				$.ajax({
					url: window.APP.siteUrl + 'admin/dies/set_log',
					type: 'post',
					data: {
						id: 'new',
						dies_id: $(this).attr('data-dies-id'),
						location: 1,
						status: 3,
					},
					success: function(response) {
						$(_input).parent().html(response);
					}
				});

			});
		}
	}
})(jQuery);