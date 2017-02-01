window.TRANSACTION = (function($) {

	var renderCol1 = function(d,t,f,m){
        var btn = '<a class="btn btn-default edit-modal-transaksi" data-toggle="modal" data-target="#defaultModal" href="'+window.APP.siteUrl+'admin/transaction/edit/'+f['id']+'">Edit</a>' +
        	'<a class="btn btn-danger delete-transaksi" href="javascript:;" data-id="'+f['id']+'">Hapus</a>';
        return btn;
    }

    var renderDate = function(d,t,f,m){
        var btn = '<label class="transaction-date" data-id="'+f['id']+'">'+d+'</label>';
        return btn;
    }

    var renderShift = function(d,t,f,m){
        var btn = '<label class="transaction-shift" data-id="'+f['id']+'" data-value="'+d+'">'+d+'</label>';
        return btn;
    }

    var renderSection = function(d,t,f,m){
        var btn = '<label class="transaction-sectionid" id="sectionid'+f['id']+'" data-header="'+f['header_id']+'" data-id="'+f['id']+'" data-value="'+d+'" data-machine="'+f['machine_id']+'">'+d+'</label>';
        return btn;
    }

	return {
		dataTable: null,
		detailId: null,
		init: function() {
			var _this = this;

			_this.handleSave();
			_this.handleValidate();
			_this.handleSelect();
			_this.handleDatepicker();
			_this.handleNumberInput();
		},

		handleEditable: function() {

			$.fn.editable.defaults.mode = 'inline';
			
			$('.transaction-date').click(function() {
				$(this).editable({
					inputclass: 'some_class'
				});

				if($(this).hasClass('hasclass') == false){
					$(this).editable('toggle');
				}

				$(this).addClass('hasclass');

				$('.some_class').bootstrapMaterialDatePicker({ 
					weekStart : 0,
					time: false 
				});
			});

			$('.transaction-shift').editable({
				type: 'select',
				sourceCache: false,
				emptytext: 'Silahkan pilih',
				mode: 'popup',
				source: window.APP.siteUrl + 'admin/master/get_data_shift',
				success: function(response, newValue) {

					$.ajax({
						url: window.APP.siteUrl + 'admin/transaction/update_inline',
						type: 'post',
						data: {
							id: $(this).attr('data-id'),
							type: 'shift',
							value: newValue
						},
						success: function() {
							console.log($(this));
						}
					});
				}
			});

			$('.transaction-sectionid').click(function() {

				$(this).editable({
					type: 'select',
					sourceCache: false,
					emptytext: 'Silahkan pilih',
					mode: 'popup',
					source: window.APP.siteUrl + 'admin/master/get_data_section/'+$(this).attr('data-header'),
					success: function(response, newValue) {

						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/update_inline',
							type: 'post',
							data: {
								id: $(this).attr('data-id'),
								type: 'section_id',
								value: newValue
							},
							success: function() {
								console.log($(this));
							}
						});
					}
				});

				if($(this).hasClass('hasclass') == false){
					$(this).editable('toggle');
				}

				$(this).addClass('hasclass');

			});


			//$('.transaction-sectionid').click(function() {

				function getSource() {
			        var url = window.APP.siteUrl + 'admin/master/get_data_section/';
			        return $.ajax({
			            type:  'POST',
			            async: true,
			            url:   url,
			            data: {
							id: $(this).attr('data-id')
						},
			            dataType: "json"
			        });
			    }

			    //getSource().done(function(result) {
			    	/*$(this).editable({
						type: 'select',
						sourceCache: false,
						emptytext: 'Silahkan pilih',
						mode: 'popup',
						source: result,
						success: function(response) {
							$.ajax({
								url: window.APP.siteUrl + 'admin/transaction/update_inline',
								type: 'post',
								data: {
									id: $(this).attr('data-id'),
									type: 'section'
								},
								success: function() {
									console.log($(this));
								}
							});
						}
					});*/

			        /*$(this).editable({
			            type: 'select',
			            title: 'Select status',
			            placement: 'right',
			            value: 2,
			            source: result,
			            success: function(response) {
							$.ajax({
								url: window.APP.siteUrl + 'admin/transaction/update_inline',
								type: 'post',
								data: {
									id: $(this).attr('data-id'),
									type: 'section'
								},
								success: function() {
									console.log($(this));
								}
							});
						}
			        });

			    }).fail(function() {
			        alert("Error with editable section")
			    });*/

			//});

		},

		handleValidate: function() {
            /*$('.transaction-form').validate({
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
            });*/   
        },

		handleSave: function() {

			var _this = this;

			// handle save transaction
			$('.transaction-form').ajaxForm({
				dataType: 'json',
				beforeSend: function() {
					$('.preloader').css('visibility', 'visible');
				},
				success: function(response) {
					$('.preloader').css('visibility', 'hidden');
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
						_this.dataTable.ajax.reload();
						//window.location.reload();
						$('.btn-close-modal').click();
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

				$.ajax({
					url: window.APP.siteUrl + 'admin/transaction/get_new_master/',
					type: 'post',
					dataType:'json',
					data: {
						section_id : this.value
					},
					success: function(response) {
						$('.section-machine').html(response.machine_type_id);
						$('.section-billet').html(response.billet_id);
						$('.section-master-id').val(response.master_id);
					}
				});
			})
		},

		handleDatepicker: function() {
			$('.transaksi-tanggal').bootstrapMaterialDatePicker({ 
				weekStart : 0,
				time: false 
			}).on('change', function(e, date) {
				$('.transaksi-tanggal').parents('.form-line').removeClass('error');
				$('.transaksi-tanggal').parents('.form-line').next('label').remove();
			});
		},

		handleNumberInput: function() {
			$('.input-number').keypress(function(e) {
		        if(e.charCode > 57) {
		            return false;
		        }

		        if(e.charCode < 48) {
		            if(e.charCode == 0) {
		            }else {
		                return false;
		            }
		        }
		    });
		},

		handleDatatable: function() {

			var _this = this;
			_this.dataTable = $('.table-transaksi').DataTable({
				ajax: {
					url: window.APP.siteUrl + 'admin/transaction/data',
					dataType: 'json',
				},
				columns: [
					{
						data: 'no',
					},
					{
						data: 'tanggal',
						render: renderDate
					},
					{
						data: 'shift',
						render: renderShift
					},
					{
						data: 'section_id',
						render: renderSection
					},
					{
						data: 'section_name',
					},
					{
						data: 'mesin',
					},
					{
						data: 'billet',
					},
					{
						data: 'len',
					},
					{
						data: 'finishing',
					},
					{
						data: 'target_prod',
					},
					{
						data: 'index_dice',
					},
					{
						data: 'ppic_note',
					},
					{
						data: 'target_prod_btg',
					},
					{
						data: 'weight_standard',
					},
					{
						data: 'target_section',
					},
					{
						data: 'total_target',
					},
					{
						data: 'die_type',
					},
					{
						data: 'apt',
					},
					{
						data: 'shift_start',
					},
					{
						data: 'shift_end',
					},
					{
						data: 'null',
					},
					{
						data: 'action',
						width: '200px'
					},

				],
				lengthChange: false,
				initComplete: function() {
					$('.dataTables_filter').parent().parent().remove();

					_this.handleEditable();
					
					/*$('.edit-modal-transaksi').click(function(e) {
						e.preventDefault();
						$('.content-modal-transaksi').load($(this).attr('href'));
					});*/
				}
			});
		},


		handleEditModal: function(id) {
			$(document).find('.content-modal-transaksi').load($('#edit-transaksi-'+id).attr('href'));
		},

		handleModal: function() {


			$('.tambah-transaksi').click(function(e) {
				e.preventDefault();
				$('.content-modal-transaksi').load($(this).attr('url'));
			});
		},

		handleDelete: function(id) {

			var _this = this;

			swal({
		        title: "Are you sure?",
		        text: "Akan menghapus transaksi terpilih ?",
		        type: "warning",
		        showCancelButton: true,
		        confirmButtonColor: "#DD6B55",
		        confirmButtonText: "Ya",
		        closeOnConfirm: false
		    }, function () {
		    	$.ajax({
		    		url: window.APP.siteUrl + 'admin/transaction/delete/'+id,
		    		dataType: 'json',
		    		type: 'post',
		    		success: function(response) {
		    			if(response.status == 'success') {	
			        		swal("Deleted!", response.message, response.status);
			        		setTimeout(function() {
			        			_this.dataTable.ajax.reload();
			        		}, 1000);
		    			} else {
			        		swal("Failed!", response.message, response.status);
		    			}
		    		}
		    	});
		    });
		}
	}
})(jQuery);