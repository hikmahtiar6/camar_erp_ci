window.TRANSACTION = (function($) {

	var renderCheckbox = function(d,t,f,m){
        var btn = '<center><input id="basic_checkbox_'+f['id']+'" class="filled-in sub-checkbox" name="master_detail_id[]" value="'+f['id']+'" type="checkbox">' +
				 '<label for="basic_checkbox_'+f['id']+'">&nbsp;</label></center>';
        return btn;
    }

	var renderCol1 = function(d,t,f,m){
        var btn = '<a class="btn btn-default edit-modal-transaksi" data-toggle="modal" data-target="#defaultModal" href="'+window.APP.siteUrl+'admin/transaction/edit/'+f['id']+'">Edit</a>' +
        	'<a class="btn btn-danger delete-transaksi" href="javascript:;" data-id="'+f['id']+'">Hapus</a>';
        return btn;
    }

    var renderDate = function(d,t,f,m){
        var btn = '<label class="transaction-date" data-id="'+f['id']+'" data-value="'+f['tanggal1']+'" data-header="'+f['header_id']+'"> '+f['tanggal2']+'</label>';
        return btn;
    }

    var renderShift = function(d,t,f,m){
    	var txt = f['shift_name'];
    	if(d == '' || d == null || d == ' ') {
    		var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-shift" data-id="'+f['id']+'" data-value="'+d+'">'+txt+'</label>';
        return btn;
    }

    var renderSection = function(d,t,f,m){
    	var txt = d;
    	if(d == '' || d == null || d == ' ') {
    		//var txt = '-';
    		var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-sectionid" id="sectionid'+f['id']+'" data-header="'+f['header_id']+'" data-id="'+f['id']+'" data-value="'+f['section_id']+'" data-machine="'+f['machine_id']+'">'+txt+'</label>';
        return btn;
    }

    var renderSectionName = function(d,t,f,m){
    	var txt = d;
    	if(d == '' || d == null || d == ' ') {
    		var txt = '-';
    		//var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-sectionname" id="sectionname'+f['id']+'" data-header="'+f['header_id']+'" data-id="'+f['id']+'" data-value="'+f['section_id']+'|'+f['master_id']+'" data-machine="'+f['machine_id']+'">'+txt+'</label>';
        return btn;
    }

    var renderMachine = function(d,t,f,m){
        var btn = '<label class="transaction-machine" id="transaction-machine'+f['id']+'">'+d+'</label>';
        return btn;
    }

    var renderLen = function(d,t,f,m){
    	var txt = f['len_name'];
    	if(d == '' || d == null || d == ' ') {
    		var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-len" data-id="'+f['id']+'" data-value="'+d+'" data-header="'+f['header_id']+'">'+txt+'</label>';
        return btn;
    }

    var renderFinishing = function(d,t,f,m){
    	var txt = f['finishing_name'];
    	if(d == '' || d == null || d == ' ') {
    		var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-finishing" data-id="'+f['id']+'" data-value="'+d+'" >'+txt+'</label>';
        return btn;
    }

    var renderTargetProdBillet = function(d,t,f,m){
        var btn = '<label class="transaction-targetprodbillet" data-id="'+f['id']+'" data-value="'+d+'" >'+d+'</label>';
        return btn;
    }

    var renderIndexDIce = function(d,t,f,m){
    	var txt = d;
    	if(d == '' || d == null || d == ' ') {
    		var txt = '<font class="editable-empty">Silahkan pilih</font>';
    	}
        var btn = '<label class="transaction-indexdice" id="transaction-indexdice'+f['id']+'" data-id="'+f['id']+'" data-machine="'+f['mesin']+'" data-value="'+f['index_dice_value']+'">'+txt+'</label>';

        return btn;
    }

    var renderIndexDIceCount = function(d, t, f, m) {
    	return '<label id="count_dice'+f['id']+'">'+d+'</label>';
    }

    var renderPPICNote = function(d,t,f,m){
        var btn = '<label class="transaction-ppicnote" data-id="'+f['id']+'" data-value="'+d+'" >'+d+'</label>';
        return btn;
    }

    var renderBillet = function(d,t,f,m){
    	var txt = d;
    	if(d == '' || d == null || d == ' ') {
    		var txt = '-';
    	}
        var btn = '<label id="transaction-billet'+f['id']+'" data-id="'+f['id']+'" data-value="'+d+'">'+txt+'</label>';
        return btn;
    }

    var renderWS = function(d,t,f,m){
        var btn = '<label id="transaction-weightstandard'+f['id']+'" data-id="'+f['id']+'" data-value="'+d+'">'+d+'</label>';
        return btn;
    }

    var renderDieType = function(d,t,f,m){
        var btn = '<label id="transaction-dietype'+f['id']+'" data-id="'+f['id']+'" data-value="'+d+'">'+d+'</label>';
        return btn;
    }

	return {
		dataTable: null,
		detailId: null,
		sectionId: null,
		res: null,
		init: function() {
			var _this = this;

			_this.handleSave();
			_this.handleValidate();
			_this.handleSelect();
			_this.handleDatepicker();
			_this.handleNumberInput();
		},

		handleEditable: function() {

			var _this = this;

			$.fn.editable.defaults.mode = 'popup';
			$.fn.editable.defaults.emptytext = 'Silahkan diisi';
			$.fn.editableform.buttons = '';

			$('.transaction-date').click(function() {
				$(this).editable({
					inputclass: 'some_class',
					type: 'select',
					sourceCache: false,
					source: window.APP.siteUrl + 'admin/transaction/get_tanggal_header/'+$(this).attr('data-header'),
					success: function(response, newValue) {

						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/update_inline',
							type: 'post',
							data: {
								id: $(this).attr('data-id'),
								type: 'tanggal',
								value: newValue
							},
							success: function() {
								console.log($(this));
							}
						});
					},
					onblur: 'submit'
				});

				if($(this).hasClass('hasclass') == false){
					$(this).editable('toggle');
				}

				$(this).addClass('hasclass');
			});

			$('.transaction-shift').editable({
				type: 'select',
				sourceCache: false,
				emptyText: 'Silahkan pilih',
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
				},
				onblur: 'submit'
			});

			$('.transaction-sectionid').click(function() {

				var _inputThis = this;

				var edt = $(this).editable({
					type: 'select',
					sourceCache: false,
					emptytext: 'Silahkan pilih',
					mode: 'popup',
					source: window.APP.siteUrl + 'admin/master/get_data_section/'+$(this).attr('data-header')+'/name',
					success: function(response, newValue) {

						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/update_inline',
							type: 'post',
							dataType: 'json',
							data: {
								id: $(this).attr('data-id'),
								type: 'section_id',
								value: newValue,
								machine: $('.machine-id').val()
							},
							success: function(response) {
								if(response.status == 'success') {
									//$('#sectionname'+ $(_inputThis).attr('data-id')).attr('data-value', response.detail_section);
									//$('#sectionid'+ $(_inputThis).attr('data-id')).attr('data-value', response.section_id);

									//$('.transaction-sectionname').setValue(response.detail_section);
									setTimeout(function() {
										$('#sectionid'+ $(_inputThis).attr('data-id')).html(response.section_id);
										$('#sectionname'+ $(_inputThis).attr('data-id')).html(response.section_name);
										$('#transaction-weightstandard'+ $(_inputThis).attr('data-id')).html(response.weight_standard);
										$('#transaction-billet'+ $(_inputThis).attr('data-id')).html(response.billet_id);
										$('#transaction-dietype'+ $(_inputThis).attr('data-id')).html(response.die_type_name);
										$('#transaction-indexdice'+ $(_inputThis).attr('data-id')).removeAttr('data-sectionid');
										$('#transaction-indexdice'+ $(_inputThis).attr('data-id')).attr('data-sectionid', response.section_id);
									}, 500);
								}
							}
						});
					},
					onblur: 'submit'
				});

				if($(this).hasClass('hasclass') == false){
					$(this).editable('toggle');
				}

				$(this).addClass('hasclass');

			});

			/*$('.transaction-sectionname').click(function() {

				var _inputThis = this;

				$(this).editable({
					type: 'select',
					sourceCache: false,
					emptytext: 'Silahkan pilih',
					mode: 'popup',
					source: window.APP.siteUrl + 'admin/master/get_data_section/'+$(this).attr('data-header')+'/name',
					success: function(response, newValue) {

						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/update_inline',
							type: 'post',
							dataType: 'json',
							data: {
								id: $(this).attr('data-id'),
								type: 'section_id',
								value: newValue
							},
							success: function(response) {
								if(response.status == 'success') {
									$('#sectionid'+ $(_inputThis).attr('data-id')).attr('data-value', response.detail_section);									
									$('#sectionid'+ $(_inputThis).attr('data-id')).html(response.section_id);
									$('#sectionname'+ $(_inputThis).attr('data-id')).html(response.section_name);
									$('#transaction-weightstandard'+ $(_inputThis).attr('data-id')).html(response.weight_standard);
									$('#transaction-billet'+ $(_inputThis).attr('data-id')).html(response.billet_id);
									$('#transaction-dietype'+ $(_inputThis).attr('data-id')).html(response.die_type_name);
									$('#transaction-indexdice'+ $(_inputThis).attr('data-id')).removeAttr('data-sectionid');
									$('#transaction-indexdice'+ $(_inputThis).attr('data-id')).attr('data-sectionid', response.section_id);
								}
							}
						});
					},
					onblur: 'submit'
				});

				if($(this).hasClass('hasclass') == false){
					$(this).editable('toggle');
				}

				$(this).addClass('hasclass');

			});*/

			$('.transaction-machine').click(function() {
				$(document).find('.content-modal-transaksi').load(window.APP.siteUrl + 'admin/transaction/edit/'+ $(this).attr('data-id'));
			});

			$('.transaction-len').click(function() {
				var _thisInput = this;
				$(_thisInput).editable({
					type: 'select',
					sourceCache: false,
					mode: 'popup',
					source: window.APP.siteUrl + 'admin/master/get_data_len/'+$(_thisInput).attr('data-id'),
					success: function(response, newValue) {

						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/update_inline',
							type: 'post',
							data: {
								id: $(_thisInput).attr('data-id'),
								type: 'len',
								value: newValue
							},
							success: function() {
								console.log($(_thisInput));
							}
						});
					},
					onblur: 'submit'
				});

				if($(_thisInput).hasClass('hasclass') == false){
					$(_thisInput).editable('toggle');
				}

				$(_thisInput).addClass('hasclass');
			});

			$('.transaction-finishing').editable({
				type: 'select',
				sourceCache: false,
				mode: 'popup',
				source: window.APP.siteUrl + 'admin/master/get_data_finishing',
				success: function(response, newValue) {

					$.ajax({
						url: window.APP.siteUrl + 'admin/transaction/update_inline',
						type: 'post',
						data: {
							id: $(this).attr('data-id'),
							type: 'finishing',
							value: newValue
						},
						success: function() {
							console.log($(this));
						}
					});
				},
				onblur: 'submit'
			});

			$('.transaction-targetprodbillet').editable({
				inputclass: 'input-number',
				type: 'text',
				success: function(response, newValue) {

					$.ajax({
						url: window.APP.siteUrl + 'admin/transaction/update_inline',
						type: 'post',
						data: {
							id: $(this).attr('data-id'),
							type: 'target_prod',
							value: newValue
						},
						success: function() {
							console.log($(this));
						}
					});
				},
				onblur: 'submit'
			});

			_this.handleNumberInput();


			
			$('.transaction-indexdice').click(function() {

				var _inputThis = this;
				var sectionId = $(this).attr('data-sectionid');

				_this.sectionId = sectionId;

				var count = 4, sources = [];

				for(var i = 1; i <= count; i++){
				    //sources.push({ id : i, text : 's-'+String(i) })
				}

				//alert($(_inputThis).attr('data-sectionid'));
				//
				var url = window.APP.siteUrl + 'admin/master/get_data_index_dice/'+$(_inputThis).attr('data-id')+'/'+$(_inputThis).attr('data-machine')

				$.ajax({
					url: url,
					type: 'get',
					dataType: 'json',
					success: function(response) {
						for(var i in response){
							sources.push({ id : response[i]['text'], text : response[i]['text'] });
						}

						var getDisplay = function(){
						    return function(value, sourceData) {
						       //display checklist as comma-separated values
						       var html = [],
						           checked = $.fn.editableutils.itemsByValue(value, getSource(), 'id');  // it was needed to send 'id' as idKey otherwise it was fetching with value
						       if(checked.length) {
						           $.each(checked, function(i, v) { html.push($.fn.editableutils.escape(v.text) + ' '); });
						           $(this).html(html.join(', '));
						       } else {
						           $(this).html('Silahkan pilih'); 
						       }
						    };
						};

						var getSource = function() {
							return JSON.parse(JSON.stringify(sources));

						    //i want this function must be called whenever available options is rendred. to ensure i used JSON.parse
						};

						var getQuery = function(options){
						    options.callback({ results : getSource() });
						};

						var getInitSel = function(multiple) {
						    return function(el, cb){
						        var t, toSet = [], sc = getSource();
						        el[0].value.split(',').forEach(function(a){
						            t = _.findWhere(sc, { id : Number(a.trim()) });
						            if(t) toSet.push(t);
						        });
						        cb(multiple ? toSet : (toSet.length ? toSet[0] : null));
						    };
						};


						$(_inputThis).editable({
							source : getSource(),
						    display: getDisplay(),
						    select2: {
						        multiple : true,
						        initSelection : getInitSel(true),
						        query :getQuery,
						        width: 200,
						    },
							type: 'select2',
							mode: 'inline',
							emptyText: 'Silahkan pilih',
							showButtons: false,
							success: function(response, newValue) {

								var _ne = newValue;

								$.ajax({
									url: window.APP.siteUrl + 'admin/transaction/update_inline',
									type: 'post',
									dataType: 'json',
									data: {
										id: $(_inputThis).attr('data-id'),
										type: 'index_dice',
										value: newValue
									},
									success: function(response) {
										$(_inputThis).html(response.dice);
										$('#count_dice'+$(_inputThis).attr('data-id')).html(response.dice_count);
										if(response.dice != 'Silahkan pilih') {
											$(_inputThis).removeClass('editable-empty');
										} else {
											$(_inputThis).addClass('editable-empty');
										}

										$(_inputThis).editable('remove');
										$(_inputThis).editable('destroy');
									}
								});
							},
							onblur: 'submit'
						});

						if($(_inputThis).hasClass('hasclass') == false){
							
								$(_inputThis).editable('toggle');
						}

						$(_inputThis).addClass('hasclass');


					}
				});
				//	alert(sources);

				//alert(url);
				//
				

			});

			$('.transaction-ppicnote').editable({
				type: 'text',
				success: function(response, newValue) {

					$.ajax({
						url: window.APP.siteUrl + 'admin/transaction/update_inline',
						type: 'post',
						data: {
							id: $(this).attr('data-id'),
							type: 'ppic_note',
							value: newValue
						},
						success: function() {
							console.log($(this));
						}
					});
				},
				onblur: 'submit'
			});


			

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
					//console.log(response);

					/*$.notify({
						message: response.message
					},{
						element: 'body',
						type: response.status,
            			newest_on_top: true,
            			z_index: 1050,
            			placement: {
            				align: 'center'
            			}
					});*/

					if(response.status == 'success') {
						//_this.dataTable.ajax.reload();
						//window.location.reload();
						//$('.btn-close-modal').click();
						
						if(response.url != undefined) {
							window.location = response.url;
						}

					}
					swal(response.status+'!', response.message, response.status);
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
					url: window.APP.siteUrl + 'admin/transaction/data/'+$('.header-id').val(),
					dataType: 'json',
				},
				paging: false,
				columns: [
					{
						data: 'no',
						render: renderCheckbox,
						orderable: false
					},
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
						render: renderSectionName
					},
					{
						data: 'mesin',
						render: renderMachine
					},
					{
						data: 'billet',
						render: renderBillet
					},
					{
						data: 'len',
						render: renderLen
					},
					{
						data: 'finishing',
						render: renderFinishing
					},
					{
						data: 'target_prod',
						render: renderTargetProdBillet
					},
					{
						data: 'index_dice',
						render: renderIndexDIce
					},
					{
						data: 'index_dice_count',
						render: renderIndexDIceCount
					},
					{
						data: 'ppic_note',
						render: renderPPICNote
					},
					{
						data: 'target_prod_btg',
					},
					{
						data: 'weight_standard',
						render: renderWS
					},
					{
						data: 'target_section',
					},
					/*{
						data: 'total_target',
					},*/
					{
						data: 'die_type',
						render: renderDieType
					},
					/*{
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
					},*/

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

			/**/

			$('.parent-checkbox').click(function(){
				checkboxes = $('.sub-checkbox');
				for(var i=0, n=checkboxes.length;i<n;i++) {
					checkboxes[i].checked = $('.parent-checkbox').is(':checked');
				}
			});

			$('.hapus-transaksi').click(function(){

				swal({
			        title: "Are you sure?",
			        text: "Akan menghapus transaksi terpilih ?",
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: "Ya",
			        closeOnConfirm: false
			    }, function () {
			    	var checkboxes = $('.sub-checkbox');
					var id = checkboxes.filter(':checked');

					var ids = [];

					// counting checked checkbox
					for(var x=0; x<id.length; x++){

						// value checkbox in checked
						var getValue = $(id[x]).val();

						// set merge array value checkbox
						ids.push(getValue);
					}

					if(ids.length > 0) {
						$.ajax({
							url: window.APP.siteUrl + 'admin/transaction/delete_selected',
							data: {
								id: ids
							},
							type: 'post',
							dataType: 'json',
							success: function(response) {
				        		if(response.status == 'success') {	
					        		setTimeout(function() {
										window.location.reload();
					        			
					        			//_this.dataTable.ajax.reload();
					        		}, 1000);
				    			}
					        	swal(response.status+'!', response.message, response.status);
							}
						});
					} else {
					    swal('warning!', 'belum ada yg dipilih untuk dihapus', 'warning');
						
					}

			    });

				

			});

		},

		handleAddRow: function() {
			$('.tambah-transaksi').click(function() {

				var grid = $('.list-spk');

				var rowKey = grid.jqGrid('getGridParam',"selrow");
				var idxDice = $('#indexdice'+rowKey).val();
				grid.jqGrid('saveRow', rowKey, {
					extraparam: {
						idxdice: idxDice
					},
				});

				$.ajax({
					url: window.APP.siteUrl + 'admin/transaction/add_row_by_header',
					type: 'post',
					dataType: 'json',
					data: {
						header_id: $(this).attr('data-header')
					},
					success: function(response) {
						
						//swal(response.status + '!', response.message, response.status);

						if(response.status == 'success') {
							setTimeout(function() {
								var grid = $('.list-spk');
								grid.trigger("reloadGrid");
								window.TRANSACTION.handleGridUpDinamic();
								//window.location.reload();							
							}, 300);
						}


					}
				});
			});
		},

		handleFullscreen: function() {
			// check native support
			if($.fullscreen.isNativelySupported()) {
			} else {
				alert('tidak support plugin fullscreen');
			}

			// open in fullscreen
			$('.fullscreen-transaksi').click(function() {
				$('.card-transaksi').fullscreen();
				$(this).hide();
				$('.fullscreen-transaksi-exit').show();

				return false;
			});

			//exit fullscreen
			$('.fullscreen-transaksi-exit').click(function() {
				$.fullscreen.exit();
				$(this).hide();
				$('.fullscreen-transaksi').show();

				return false;
			});

			$(document).bind('fscreenchange', function(e, state, elem) {
				// if we currently in fullscreen mode
				if ($.fullscreen.isFullScreen()) {
					$('.fullscreen-transaksi').hide();
					$('.fullscreen-transaksi-exit').show();

				} else {
					$('.fullscreen-transaksi').show();
					$('.fullscreen-transaksi-exit').hide();
				}

				$('#state').text($.fullscreen.isFullScreen() ? '' : 'not');
			});
		},
		handleWeek: function() {
			$('.week-options').change(function() {
				var splt = this.value.split('-');
				$('.report-date-start').val(splt[0]);
				$('.report-date-finish').val(splt[1]);

				var selectedText = $('.week-options option:selected').text();
				var splt2 = selectedText.split('-');
				$('.week-number').val(splt2[0]);
			});
		},

		handlejqGrid: function() {
			//$.jgrid.defaults.responsive = true;     
			//$.jgrid.defaults.width = $('.navbar').width() - 150;     
		    $.jgrid.defaults.styleUI = 'Bootstrap';

			var grid = $('.list-spk');
			grid.jqGrid({
				url: APP.siteUrl + 'admin/transaction/json/' + $('.header-id').val(), //URL Tujuan Yg Mengenerate data Json nya
				datatype: "json", //Datatype yg di gunakan
				height: "auto", //Mengset Tinggi table jadi Auto menyesuaikan dengan isi table
				mtype: "POST",
				cmTemplate: {sortable:false},
				pgbuttons : false,
				viewrecords : false,
				pgtext : "",
				pginput : false,
				colModel: [
					
					{
						label: "Edit Actions <br> <br>",
						index:'master_detail_id', 
                        name: "id",
                        hidden: true,
                        width: 100,
                        formatter: "actions",
                        formatoptions: {
                            keys: false,
                            editOptions: {},
                            addOptions: {},
                            delOptions: {}
                        }       
                    },
                    {
						label: "<br>",
                        name: "to_lot",
                        hidden: false,
                        width: 80,  
                    },
					{
						label: 'Tanggal <br><br>',
						name:'tanggal', 
						index:'tanggal', 
						hidden: false,
						editable:true,
						width: 120,
						edittype: "select",
						editoptions: {
							dataInit: function(el) {
								var el = el;
								$.ajax({
									url: window.APP.siteUrl + 'admin/transaction/get_tanggal_header/'+$('.header-id').val(),
									dataType: 'json',
									success: function(res) {
										var opt =  '';
										var i = 0;
										for(i in res) {
											if(res[i]['value'] == $(el).parent().attr('title')) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
											opt += '<option '+selected+'>'+res[i]['value']+'</option>';
										}
										$(el).html(opt);
									}
								});
							}
						}
					},
					{
						label: 'Shift <br><br>',
						name:'shift', 
						index:'shift', 
						hidden: false,
						editable:true,
						width: 70,
						edittype: "select",
						editoptions: {
							dataInit: function(el) {
								var el = el;
								$.ajax({
									url: window.APP.siteUrl + 'admin/master/get_data_shift',
									dataType: 'json',
									success: function(res) {
										var opt =  '';
										var i = 0;
										for(i in res) {
											if(res[i]['value'] == $(el).parent().attr('title')) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
											opt += '<option value="'+res[i]['value']+'" '+selected+'>'+res[i]['text']+'</option>';
										}
										$(el).html(opt);
									}
								});
							}
						}
					},
					{
						label: 'Section Name <br><br>',
						name:'section_id', 
						index:'section_id', 
						hidden: false,
						editable:true,
						width: 150,
						edittype: "select",
						editoptions: {
							dataInit: function(el) {
								var el = el;
								var grid = $('.list-spk');
								var rowId = $(el).parent().parent().attr('id');
								var celMachine = grid.jqGrid ('getCell', rowId, 'machine_id');
								var celSecId = grid.jqGrid ('getCell', rowId, 'section_name');

								$(el).attr('id', 'section_id'+rowId);

								$.ajax({
									url: window.APP.siteUrl + 'admin/master/get_data_section/' + $('.header-id').val()+ '/name',
									dataType: 'json',
									success: function(res) {
										var opt =  '';
										var i = 0;
										for(i in res) {
											if(res[i]['value'] == celSecId) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
											opt += '<option value="'+res[i]['value']+'" '+selected+'>'+res[i]['text']+'</option>';
										}
										$(el).html(opt);
									}
								});
							},
							dataEvents: [
								{ 
									type: 'change', 
									fn: function(e) {

										var grid = $('.list-spk');

										var rowId = $(this).parent().parent().attr('id');
										var _this = this;	

										//grid.jqGrid();
										var celValue = grid.jqGrid ('getCell', rowId, 'machine_id');

										grid.setRowData(rowId, { 
								        	section_name: $('#section_id'+rowId+' option:selected').val()
								        });

										$.ajax({
											url: window.APP.siteUrl + 'admin/transaction/update_inline',
											type: 'post',
											dataType: 'json',
											data: {
												id: rowId,
												type: 'section_id',
												value: this.value,
												machine: celValue
											},
											success: function(response) {


												var idxDice = $('#indexdice'+rowId);
												var len = $('#len'+rowId);


												$.ajax({
													url: window.APP.siteUrl + 'admin/transaction/get_rumus/'+rowId+'/'+$('#target_prod'+rowId).val(),
													type: 'POST',
													dataType: 'json',
													data: {
														len: $('#len'+rowId+' option:selected').text()
													},
													success: function(data) {
														grid.setRowData(rowId, { 
												        	weight_standard: data.weight_standard,
												        	target_prod_btg: data.target_prod_btg,
												        	target_section: data.target_section,
												        	die_type_name: data.die_type_name,
												        });

												        window.TRANSACTION.handleGridUpDinamic();
													}
												});

												$.getJSON(window.APP.siteUrl + 'admin/master/get_data_index_dice/'+rowId+'/'+celValue, function(data) {

											        var output = "";

											        $.each(data, function(key, val) {
											        	if(val.value == idxDice.parent().attr('title')) {
															selected = 'selected="selected"';
														} else {
															selected = '';
														}
											            output += '<option value="' + val.value + '" '+selected+'>' + val.text + '</option>';
											        });
											        idxDice.html(output);
											    });

											    $.ajax({
													url: window.APP.siteUrl + 'admin/master/get_data_len/'+rowId,
													dataType: 'json',
													success: function(res) {
														var opt =  '';
														var i = 0;
														for(i in res) {
															if(res[i]['value'] == len.parent().attr('title')) {
																selected = 'selected="selected"';
															} else {
																selected = '';
															}
															opt += '<option value="'+res[i]['value']+'" '+selected+'>'+res[i]['text']+'</option>';
														}
														len.html(opt);
													}
												});

											}
										});

									} 
								},
							]
						}
					},
					{
						label: 'Section Id <br><br>',
						name:'section_name', 
						index:'section_name', 
						hidden: false,
						editable:false,
						width: 200
					},
					{
						label: 'Mesin Id <br><br>',
						name:'machine_id', 
						index:'machine_id', 
						hidden: false,
						editable:false,
						width: 120
					},
					{
						label: 'Billet Id <br><br>', 
						name:'billet_id', 
						index:'billet_id', 
						hidden: false,
						editable:false,
						width: 120
					},
					{
						label: 'Len (m) <br><br>',
						name:'len', 
						index:'master_detail_id', 
						hidden: false,
						editable:true,
						width: 90,
						edittype: "select",
						editoptions: {
							dataInit: function(el) {
								var el = el;
								var rowId = $(el).parent().parent().attr('id');

								$(el).attr('id', 'len'+rowId);

								$.ajax({
									url: window.APP.siteUrl + 'admin/master/get_data_len/'+rowId,
									dataType: 'json',
									success: function(res) {
										var opt =  '';
										var i = 0;
										for(i in res) {
											if(res[i]['value'] == $(el).parent().attr('title')) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
											opt += '<option value="'+res[i]['value']+'" '+selected+'>'+res[i]['text']+'</option>';
										}
										$(el).html(opt);
									}
								});

								$(el).change(function() {
									$.ajax({
										url: window.APP.siteUrl + 'admin/transaction/get_rumus/'+rowId,
										type: 'POST',
										dataType: 'json',
										data: {
											len: $('#len'+rowId+' option:selected').text()
										},
										success: function(data) {
											grid.setRowData(rowId, { 
									        	weight_standard: data.weight_standard,
									        	target_prod_btg: data.target_prod_btg,
									        	target_section: data.target_section,
									        	die_type_name: data.die_type_name,
									        });
										}
									});
								});
							}
						}
					},
					{
						label: 'Finishing <br><br>',
						name:'finishing', 
						index:'finishing', 
						hidden: false,
						editable:true,
						width: 90,
						edittype: "select",
						editoptions: {
							dataInit: function(el) {
								var el = el;
								$.ajax({
									url: window.APP.siteUrl + 'admin/master/get_data_finishing',
									dataType: 'json',
									success: function(res) {
										var opt =  '';
										var i = 0;
										for(i in res) {
											if(res[i]['value'] == $(el).parent().attr('title')) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
											opt += '<option value="'+res[i]['value']+'" '+selected+'>'+res[i]['text']+'</option>';
										}
										$(el).html(opt);
									}
								});
							}
						}
					},
					
					{
						label: 'Index Die <br><br>',
						name:'index_dice', 
						index:'index_dice', 
						hidden: false,
						editable: true,
						width: 300,
						multipleselect: true,
						edittype: "select", 
						editoptions: {
						    //dataUrl: window.APP.siteUrl + 'admin/master/get_data_index_dice/037/SN0690.02',
						    //defaultValue: "DHL",
						    selectFilled: function (options) {
						        
						    },
						    dataInit: function(el) {
						    	var el = el;
						    	var grid = $('.list-spk');
						    	var rowId = $(el).parent().parent().attr('id');
								var celMachine = grid.jqGrid ('getCell', rowId, 'machine_id');

								$(el).attr('id', 'indexdice'+rowId);

						    		$(el).attr('multiple', 'multiple');

						    		$.getJSON(window.APP.siteUrl + 'admin/master/get_data_index_dice/'+rowId+'/'+celMachine, function(data) {

								        var output = "";
								        var defaultValue = $(el).parent().attr('title');

								        $.each(data, function(key, val) {
								        	if( defaultValue.indexOf(val.value) > -1) {
												selected = 'selected="selected"';
											} else {
												selected = '';
											}
								            output += '<option value="' + val.value + '" '+selected+'>' + val.text + '</option>';
								        });
								        $(el).html(output);

								        //$(el).multipleSelect();

								        $(el).select2({
								            width: "100%",
								    	});
								    	
								    });
						    	
						    	
						    }
						}
					},
					{
						label: 'Index Die Qty',
						name:'index_dice_qty', 
						index:'index_dice_qty', 
						hidden: false,
						editable:false,
						width: 90
					},
					{
						label: 'Target Prod <br> (Bilet)',
						name:'target_prod', 
						index:'target_prod', 
						hidden: false,
						editable:true,
						width: 90,
						editoptions: {
							dataInit: function(el) {

								var str = $(el).val();
								var rowId = $(el).parent().parent().attr('id');

								$(el).attr('id', 'target_prod'+rowId);

								$(el).val(str.replace(',', ''));



								$(el).keyup(function() {
									var grid = $('.list-spk');

									$.ajax({
										url: window.APP.siteUrl + 'admin/transaction/update_inline',
										type: 'post',
										dataType: 'json',
										data: {
											id: rowId,
											type: 'target_prod',
											value: $(this).val(),
										},
										success: function(response) {
											window.TRANSACTION.handleGridUpDinamic();
										}
									});

									$.ajax({
										url: window.APP.siteUrl + 'admin/transaction/get_rumus/'+rowId+'/'+$(this).val(),
										type: 'POST',
										dataType: 'json',
										data: {
											len: $('#len'+rowId+' option:selected').text()
										},
										success: function(data) {
											grid.setRowData(rowId, { 
									        	weight_standard: data.weight_standard,
									        	target_prod_btg: data.target_prod_btg,
									        	target_section: data.target_section,
									        	die_type_name: data.die_type_name,
									        });
										}
									});
								});
							}
						}
					},
					{
						label: 'PPIC Note <br><br>',
						name:'ppic_note', 
						index:'ppic_note', 
						hidden: false,
						editable:true,
						width: 100,
						editoptions: {
							dataInit: function(el) {
								var el = el;
								var grid = $('.list-spk');
								var rowId = $(el).parent().parent().attr('id');


								$(el).on('keypress', function(e) {
									var idxDice = $('#indexdice'+rowId).val();
									// if entering keyboard
									if(e.keyCode == 13) {
										grid.jqGrid('saveRow', rowId, {
											extraparam: {
												idxdice: idxDice
											},
											successfunc: function(response) {

												swal({
											        title: "Anda Yakin ?",
											        text: "Akan menambah data ?",
											        type: "warning",
											        showCancelButton: true,
											        confirmButtonColor: "#DD6B55",
											        confirmButtonText: "Ya",
											        closeOnConfirm: true
											    }, function () {
											    	$('.tambah-transaksi').click();
											    });
												grid.trigger('reloadGrid');

					        					window.TRANSACTION.handleGridUpDinamic();

												$('.body').animate({scrollLeft: -200},150);

												return true;
											}
										});
									}
								});
							}
						}
					},
					{
						label: 'Target Prod <br> (Btg)',
						name:'target_prod_btg', 
						index:'target_prod_btg', 
						hidden: false,
						editable:false,
					},
					{
						label: 'Berat Std <br><br>',
						name:'weight_standard', 
						index:'weight_standard', 
						hidden: false,
						editable:false,
					},
					{
						label: 'Target Section Kg<br><br>',
						name:'target_section', 
						index:'target_section', 
						hidden: false,
						editable:false,
					},
					{
						label: 'Die Type <br><br>',
						name:'die_type_name', 
						index:'die_type_name', 
						hidden: false,
						editable:false,
						width: 150
					},
					/*{
						name:'lastName',
						index:'lastName',
						editable:true,
						editrules:
						{
							required:true
						}
					},
					{
						name:'firstName',
						index:'firstName',
						editable:true,
						edittype:"text",
						editrules:
						{
							required:true
						}
					},
					{
						name:'extension',
						index:'extension',
						editable:true,
						editrules:
						{
							required:true
						}
					},
					{
						name:'email',
						index:'email',
						editable:true,
						editrules:
						{
							required:true
						}
					},*/
				],
				onSelectRow: editRow,
				rownumbers:true,
				rowNum: 10000000,
				//rowList: [10,20,30],
				pager: '#pager2',
				sortname: 'master_detail_id',
				viewrecords: true,
				sortorder: "desc",
				editurl: APP.siteUrl + 'admin/transaction/crud', //URL Proses CRUD Nya
				multiselect: false,
				caption: "Data SPK", //Caption List
			});

			grid.jqGrid('navGrid','#pager2',
			{
				view:false,
				edit:false,
				add:false,
				del:true,
				search: false,
			},{},{},{},
			{
				closeOnEscape:true,
				closeAfterSearch:false,
				multipleSearch:false, 
				multipleGroup:true, 
				showQuery:false,
				drag:true,
				showOnLoad:false,
				sopt:['cn'],
				resize:false,
				caption:'Cari Record', 
				Find:'Cari', 
				Reset:'Batalkan Pencarian'
			});

			var lastSelection;

			function getCellValue(rowId, cellId) {
				//var grid = $(".list-spk");
			    var cell = $('#' + rowId + '_' + cellId);        
			    var val = cell.val();
			    return val;
			}

			function editRow(id) {
		        var grid = $(".list-spk");

		       	var count = grid.jqGrid('getGridParam', 'records');
		       	if(count > 1) {
		       		if (id != lastSelection) {

						var idxDice = $('#indexdice'+lastSelection).val();
						
						grid.jqGrid('saveRow', lastSelection, {
							extraparam: {
								idxdice: idxDice
							},
						});
						

			            grid.jqGrid('restoreRow',lastSelection);
			            grid.jqGrid('editRow',id, {
			            	keys:false, 
			            	focusField: 3,
			            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
						        grid.trigger("reloadGrid");
					        	window.TRANSACTION.handleGridUpDinamic();

						        /*grid.setRowData(rowid, { 
						        	section_name:  
						        });*/

						    }
			            });
			            lastSelection = id;

			        } else {
			        	grid.jqGrid('editRow',id, {
			            	keys:false, 
			            	focusField: 3,
			            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
						        grid.trigger("reloadGrid");
					        	window.TRANSACTION.handleGridUpDinamic();

						        /*grid.setRowData(rowid, { 
						        	section_name:  
						        });*/

						    }
			            });
			            lastSelection = id;
			        }
		       	} else {
		       		grid.jqGrid('editRow',id, {
		            	keys:false, 
		            	focusField: 3,
		            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
					        grid.trigger("reloadGrid");
					        window.TRANSACTION.handleGridUpDinamic();
					        /*grid.setRowData(rowid, { 
					        	section_name:  
					        });*/

					    }
		            });
		            lastSelection = id;
		       	}

		        

		    }

		    
		},

		handleGridUpDinamic: function() {
			var table = $('.grid-dinamic');
			var headerId = $('.header-id').val();
			var machineId = $('.machine-id').val();

			$.ajax({
				url: APP.siteUrl + 'admin/transaction/grid_dinamic/'+headerId,
				type: 'GET',
				dataType: 'html',
				success: function(response) {
					table.html(response);
				}
			});
		},

		handleModalLot: function(masterDetailId) {
            $(document).find('.content-modal-transaksi').load(APP.siteUrl + 'admin/lot/get_detail/' + masterDetailId);

		}
	}

})(jQuery);