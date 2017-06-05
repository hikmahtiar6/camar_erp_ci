window.PO = (function($) {
    return {
    	init: function() {

    		var _this = this;

    		_this.handleSelectPR();
    		_this.handleForm();
    	},

    	handleSelectPR: function() {
    		var prEl = $('.pr-id');
    		var poId = $('.po-id').val();

    		if(poId != 'new') {


    		var url = window.APP.siteUrl + 'admin/po/get_header_pr_by_po_id/' + poId;

			$.getJSON(url  , function(data) {

			    var output = "";
			    var defaultValue = "";

			    $.each(data, function(key, val) {
			    	//if( defaultValue.indexOf(val.value) > -1) {
						selected = 'selected="selected"';
					/*} else {
						selected = '';
					}*/
			        output += '<option value="' + val.value + '" '+selected+'>' + val.text + '</option>';
			    });
			    $(prEl).html(output);

			    //$(el).multipleSelect();

			});

    		}
		    $(prEl).select2({
		        width: "100%",
			});


    	},

    	handleForm: function() {

    		var formEl = $('.form-po');

    		formEl.ajaxForm({
    			dataType: 'json',
    			beforeSend: function() {
    				$("#result").slideUp(500);
    			},
    			success: function(response) {

					var dataPR = [];
					var tableEl = '.po-table';

    				if(response.id != 'new') {
						$('.po-id').val(response.id);
					}
    				$("#result").slideDown(500);

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
							dataPR = response;

							var vueTable = new Vue({
								el: tableEl,
								delimiters: ['<%', '%>'],
								data: {
									prData: dataPR,
									submitted: false
								},
								methods: {
									removePr: function(row) {
										$.ajax({
											url: window.APP.siteUrl + 'admin/po/delete_detail',
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

						}
					});
					

    			}
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
		}
    }
})(jQuery);