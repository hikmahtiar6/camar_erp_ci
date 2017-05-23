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
						"<td colspan='4'>No data available in table.</td>" +
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
						date: $('.tanggal-dies').val()
					},
					success: function(response) {
						$(_input).parent().html(response);
					}
				});

			});
		},

		DiesHistoryDatatable: function() {
			var dTable = $('.dies-history-table').DataTable({
				info: false
			});

			$('.dataTables_length, .dataTables_filter').remove();

			$('.dies-history-search').keyup(function() {
				dTable.search(this.value).draw();
			});
		},
		
		handleModalHistory: function(btn) {

			var _this = this;
			//$('.btn-history').click(function() {
				var _input = $(btn);
				
				$('.dies-header-text').html(_input.attr('data-dies'));
				$('.dies-header-input').val(_input.attr('data-dies'));

				if(_input.attr('data-problem') == "")
				{
					$('.title-modal').html('Status Dies');
				}
				else {
					$('.title-modal').html('Problem Dies');
				}
				
				$('.content-modal-history').load(window.APP.siteUrl + 'admin/dies/edit/'+_input.attr('data-id')+'/'+_input.attr('data-problem'));
				
				_this.handleFormHistoryProblem();
			//})
		},
		
		handleFormHistoryProblem: function() {
			$('.form-problem').ajaxForm({
				dataType: 'json',
				success: function(response) {

					if(response.status == "success") {
						$.ajax({
							url: window.APP.siteUrl + 'admin/dies/set_log',
							type: 'post',
							data: {
								id: 'new',
								dies_id: response.dies,
								location: 1,
								status: '0',
							},
							success: function(response) {
								window.location.reload();
							}
						});
					} else {
						$.notify(response.message, response.status);
					}
					
					
				}
			});
		},

		handleFilterCard: function() {
			$('.card-form').validate();
			
			var section = $('.section');
			var dice = $('.dice');
			var dice2 = $('.dice2');
			
			dice.select2();
			dice2.select2();
			
			section.select2().change(function() {
				console.log(this.value);
				
				$.ajax({
					url: window.APP.siteUrl + 'admin/master/get_dice',
					type: 'post',
					data: {
						section_id: this.value
					},
					dataType: 'json',
					success: function(result) {
						var _data = '';
						for (var i = 0; i < result.length; i++) {
							_data += '<option>'+result[i].value+'</option>';
						}
						
						dice.html(_data);
						
					}
				});
				
			});
			

			var resultCardEl = $('.result-card');
			
			$('.card-form').ajaxForm({
				beforeSend: function() {
					APP.loader().show();
					resultCardEl.html('');
					resultCardEl.slideUp();
				},
				success: function(response) {
					APP.loader().hide();
					resultCardEl.html(response);
					resultCardEl.slideDown();
				}
			});
		}
	}
})(jQuery);