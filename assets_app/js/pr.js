window.PR = (function($) {
	return {
		ajaxEnable: true,
		init: function() {
			var _this = this;

			_this.handleDatepicker();
			_this.handleForm();
			_this.handleTable();
		},
		handleDatepicker: function() {
			$('.datepicker-pr').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false,
				format: 'DD/MM/YYYY' 
			}).on('change', function(e, date) {
				var dates = new Date(date);
				$('.year-pr').val(dates.getFullYear());
			});
		},
		handleForm: function() {
			var _this = this;
			var vendorEl = $('.vendor-data');
			var sectionEl = $('.section-data');
			var machineEl = $('.machine-data');
			var dieTypeEl = $('.die-type-data');
			var billetTypeEl = $('.billet-type-data');

			//$('select').select2();
			$('.form-pr').ajaxForm({
				dataType: 'json',
				success: function(response) {

					if(response.status == 'success') {
						if(_this.ajaxEnable) {
							if(response.id != 'new') {
								$('.header-data').val(response.id);
							}	
							_this.handleFormModal();
						}

						_this.ajaxEnable = false;

						_this.getLastHole(vendorEl.val(), sectionEl.val(), machineEl.val(), dieTypeEl.val(), billetTypeEl.val());

						
					}
				}
			});
		},
		handleFormModal: function() {
			var _this = this;
			var headerEl = $('.header-data');
			var vendorEl = $('.vendor-data');
			var sectionEl = $('.section-data');
			var machineEl = $('.machine-data');
			var dieTypeEl = $('.die-type-data');
			var billetTypeEl = $('.billet-type-data');

			$('select').change(function() {

				_this.getLastHole(vendorEl.val(), sectionEl.val(), machineEl.val(), dieTypeEl.val(), billetTypeEl.val());
			});

			$('.form-detail-pr').ajaxForm({
				data: {
					vendor: vendorEl.val(),
					header: headerEl.val(),
				},
				dataType: 'json',
				beforeSend: function() {
					APP.loader().show();
				},
				success: function(response) {
					APP.loader().hide();

					$.notify(response.message, response.status);

					if(response.status == 'success') {
						setTimeout(function() {
							window.location = window.APP.siteUrl + 'admin/pr/edit/'+response.id
						}, 1000);
					}
				}
			});
		},

		getLastHole: function(vendorId, sectionId, machineType, dieType, billetType) {

			var holeEl = $('.hole-data');

			var ajax = $.ajax({
				url: window.APP.siteUrl + 'admin/pr/get_last_hole_count',
				type: 'post',
				data: {
					vendor_id: vendorId,
					section_id: sectionId,
					machine_type: machineType,
					die_type: dieType,
					billet_type: billetType,
				},
				success: function(response) {
					holeEl.val(response);
				}
			});

			return ajax;
		},

		handleTable: function() {

			var dataPr = [];
			var headerEl = $('.header-data');
			var postingEl = $('.posting-pr');
			var tableEl = '.pr-table';
			var headerVal = headerEl.val();
			if(headerVal == 'new') {
				headerVal = 0;
			}

			$.ajax({
				url      : window.APP.siteUrl + 'admin/pr/get_detail_by_header/' + headerVal,
				type     : 'GET',
				dataType : 'json',
				success  : function(response) {
					dataPr = response;

					if(dataPr.length == 0) {
						postingEl.hide();
					}

					var vueTable = new Vue({
						el: tableEl,
						delimiters: ['<%', '%>'],
						data: {
							prData: dataPr,
							submitted: false
						},
						methods: {
							removePr: function(row) {
								$.ajax({
									url: window.APP.siteUrl + 'admin/pr/delete_detail',
									type: 'post',
									data: {
										'id': this.prData[row].prId
									},
									dataType: 'json',
									success: function(response) {
										$.notify(response.message, response.status);
									}
								});
								this.prData.splice(row, 1);
							},
							showModalEdit: function(row) {
								$('.result-edit-pr').load(window.APP.siteUrl + 'admin/pr/edit_detail/' + this.prData[row].prId, function() {
									$('.form-edit-pr').ajaxForm({
										dataType: 'json',
										beforeSend: function() {
											APP.loader().show();
										},
										success: function(response) {
											APP.loader().hide();

											$.notify(response.message, response.status);

											if(response.status == 'success') {
												setTimeout(function() {
													window.location = window.APP.siteUrl + 'admin/pr/edit/'+response.id
												}, 1000);
											}
										}
									});
								});
							}
						}
					});

					$(tableEl).dataTable();


				}
			});

			postingEl.click(function() {

				if(confirm('Yakin akan diposting ?')) {

					$.ajax({
						url: window.APP.siteUrl + 'admin/pr/set_posted',
						dataType: 'json',
						type: 'post',
						data: {
							id: headerVal
						},
						success: function(response) {
							$.notify(response.message, response.status);

							if(response.status == 'success') {
								setTimeout(function() {
									window.location.reload();
								}, 1000);
							}

						}
					});
				}
			});

			
		},

		handleDeleteHeader: function() {
			$('.delete-header-pr').click(function() {
				if(confirm('Yakin akan menghapus ini ?')) {
					var id = $(this).attr('data-id');

					$.ajax({
						url: window.APP.siteUrl + 'admin/pr/delete_header',
						data: {
							id: id
						},
						type: 'post',
						dataType: 'json',
						success: function(response) {
							$.notify(response.message, response.status);

							setTimeout(function() {
								window.location.reload();
							}, 1000);
						}
					});
				}
			});
		}
	}
})(jQuery);