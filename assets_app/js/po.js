window.PO = (function($) {
    return {
    	init: function() {
    		var _this = this;

    		_this.handleVue();
    		_this.handleSelectPR();
    		_this.handleDelete();
    	},

    	handleVue: function() {

    		var parentThis = this;

			var elPO = '#card-po';

    		var vueTable = new Vue({
				el: elPO,
				delimiters: ['<%', '%>'],
				data: {
					poData: [],
					submitted: false
				},
				methods: {
					getData: function() {

						var __this = this;
						var po_number = $('.document_no').val();

						// mengambil data dengan ajax
						$.ajax({
							url      : window.APP.siteUrl + 'admin/po/get_data',
							type     : 'POST',
							dataType : 'json',
							data     : {
								po_no: po_number
							},
							success  : function(response) {

								// set data utk vue ketika request data dari server berhasil
								__this.$set(__this, 'poData', response);
								//__this.setDatatable();
							}
						});
					},

					handleForm: function() {
						var __this = this;

						var formEl = $('.form-po');

			    		formEl.ajaxForm({
			    			dataType: 'json',
			    			success: function(response) {

								var dataPR = [];

			    				if(response.id != 'new') {
									$('.po-id').val(response.id);
								}

			    				var headerVal = $('.pr-id').val();
			    				var poNo = $('.document_no').val();

			    				$.ajax({
									url      : window.APP.siteUrl + 'admin/po/get_detail_po_from_pr/',
									type     : 'POST',
									dataType : 'json',
									data     : {
										purchase_request_no: headerVal,
										purchase_order_no : poNo 
									},
									success  : function(response) {
										__this.getData();
									}
								});
								

			    			}
			    		});
					},
					
					removePo: function(row) {

						var __this = this;

						// alert konfirmasi
						swal({   
							title: "Apa Anda Yakin?",
							text: "Anda Akan Menghapus ini!",   
							type: "warning",   
							showCancelButton: true,   
							confirmButtonColor: "#DD6B55",   
							confirmButtonText: "Ya, Hapus!",   
							closeOnConfirm: false 
						}, function(){

							$.ajax({
								url: window.APP.siteUrl + 'admin/po/delete_detail',
								type: 'post',
								data: {
									'id': __this.poData[row].pod_id
								},
								dataType: 'json',
								success: function(response) {
									parentThis.showNotification(response.message, response.status);
									__this.poData.splice(row, 1);
								}
							});


							
						});

						
					},

					savePrice: function(row) {
						var __this = this;

						var typingTimer;
						var doneTypingInterval = 1000;

						clearTimeout(typingTimer);
					    if (__this.poData[row].price_idr != "") {
					        typingTimer = setTimeout(doneTyping, doneTypingInterval);
					    }

					    function doneTyping() {
							$.ajax({
								url: window.APP.siteUrl + 'admin/po/save_price',
								type: 'post',
								data: {
									dies_id : __this.poData[row].dies_po_id,
									price_idr : __this.poData[row].price_idr,
									price_chn : __this.poData[row].price_chn,
								},
								success: function(response) {

								}
							});
					    }

					}
				},
				mounted: function() {
					var __this = this;

					__this.getData();
					__this.handleForm();
				}
			});

    	},

    	handleSelectPR: function() {
    		var prEl = $('.pr-id');
    		
    		var poId = $('.po-id').val();

    		if(poId != 'new') {
	    		// request PR No
	    		$.ajax({
	    			url      : window.APP.siteUrl + 'admin/po/get_pr_no',
	    			type     : 'post',
	    			data     : {
	    				po_no: poId
	    			},
	    			dataType : 'json',
	    			success  : function(response) {
				    	var output = "";

				    	for(var i = 0; i < response['data'].length; i++) {
				    		var selected = '';
				    		var defaultValue = response['used'];

				    		if( defaultValue.indexOf(response['data'][i].value) > -1) {
								// check koma
				        		if(defaultValue.indexOf(',') > -1) {
									selected = 'selected="selected"';
				        		} else {
					        		if(defaultValue ==  response['data'][i].value) {
										selected = 'selected="selected"';
					        		}
				        		}
							} else {
								selected = '';
							}

				        	output += '<option value="' + response['data'][i].value + '" '+selected+'>' + response['data'][i].text + '</option>';
				    	}
					    
					    $(prEl).html(output);


	    			}
	    		});


			}
    		$(prEl).select2({
		        width: "100%",
			});

    	},

    	handleDeleteHeader: function() {
			$('.delete-header-po').click(function() {
				if(confirm('Yakin akan menghapus ini ?')) {
					var id = $(this).attr('data-id');

					$.ajax({
						url: window.APP.siteUrl + 'admin/po/delete_header',
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
		},

		handleCheckbox: function() {
			var checkboxes = $('.po-parent-checkbox');

	        for(var i=0, n=checkboxes.length;i<n;i++) {
	            checkboxes[i].checked = $('.po-sub-checkbox').attr('checked', 'checked');
	        }
		},

		handleDelete: function() {

			var elBtnDel = '.po-btn-delete';

			$(elBtnDel).click(function() {
				var items = $('.po-table').find('input[class="po-sub-checkbox"]:checked');

	            var users = [];
	            for (var i=0; i<items.length;i++) {
	                users.push($(items[i]).val());
	            }

	            if(!users.length) {
	                // menampilkan sweet alert
					swal({
						title: 'Anda belum memilih yg akan dihapus',
						text: "",
						timer: 2000,
						type: 'error',
						showConfirmButton: false
					});
	                
	                return false;
	            } else {

	            	// alert
					swal({  
						title: "Apa Anda Yakin?",
						text: "Anda Akan Menghapus ini!",   
						type: "warning",   
						showCancelButton: true,   
						confirmButtonColor: "#DD6B55",   
						confirmButtonText: "Ya, Hapus!",   
						closeOnConfirm: false 
					}, function(){   

						// jika yakin menghapus maka menjalankan ajax request hapus data
						$.ajax({
							type: "POST",
							url: window.APP.siteUrl + 'admin/po/delete',
							data: {
							    id:users,
							},
							success: function(response) {
								window.location.reload();
							}
		           		});
					});
	            }
			});
                                    
            
           

		},

		// menampilkan notifikasi/alert dengan plugin sweetalert
		showNotification: function(message, status) {
			// menampilkan sweet alert
			swal({
				title: message,
				text: "",
				timer: 2000,
				type: status,
				showConfirmButton: false
			});
		},
    }
})(jQuery);