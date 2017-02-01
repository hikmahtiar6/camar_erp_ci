window.REPORT = (function($) {
	return {
		init: function() {
			var _this = this;

			//_this.handleSave();
			_this.handleValidate();
			_this.handleSelect();
			_this.handleDatepicker();
		},

		handleValidate: function() {
            $('.report-form').validate({
                highlight: function (input) {
                    console.log(input);
                    $(input).parents('.form-line').addClass('error');
                },
                unhighlight: function (input) {
                    $(input).parents('.form-line').removeClass('error');
                },
                errorPlacement: function (error, element) {
                    $(element).parents('.form-group').append(error);
                }
            });   
        },

		handleSave: function() {

			// handle save transaction
			$('.report-form').ajaxForm({
				dataType: 'json',
				beforeSend: function() {
					$('.preloader').show();
				},
				success: function(response) {
					$('.preloader').hide();
					console.log(response);

					$.notify({
						message: response.message
					},{
						element: 'body',
						type: response.status,
            			newest_on_top: true,
            			z_index: 1050,
            			placement: {
            				align: 'center'
            			}
					});

					if(response.status == 'success') {
						window.location.reload();
					}
				}
			});
		},

		handleSelect: function() {

			// handle select change section name
			$('.section-name').change(function() {
				$('.section-id-input').val(this.value);
				
				if(this.value == "") {
					$('.section-id').html("&nbsp;");
				} else {
					$('.section-id').html(this.value);
				}
			})
		},

		handleDatepicker: function() {
			$('.report-date-finish').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false 
			});

			$('.report-date-start').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false  
			});

			$('.report-date-start').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false   
			}).on('change', function(e, date) {
				$('.report-date-finish').bootstrapMaterialDatePicker('setMinDate', date);
			});

			$('.report-date-finish').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false   
			}).on('change', function(e, date) {
				$('.report-date-start').bootstrapMaterialDatePicker('setMaxDate', date);
			});
		}
	}
})(jQuery);