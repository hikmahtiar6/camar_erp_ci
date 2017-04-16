/**
 * Javascript Lot
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
window.LOT = (function($) {
	return {

		jqGrid: null, 
		masterDetailId: null,
		formHeaderLot: null, 

		handleJqgrid: function(elJqgrid, elJqgridPager) {
		    
		    var _this = this;

		    $.jgrid.defaults.styleUI = 'Bootstrap';

			var grid = $(elJqgrid);
			var detailId = $('.master-detail-id').val();
			
			_this.jqGrid = grid;
			_this.masterDetailId = detailId;

			grid.jqGrid({
				url: APP.siteUrl + 'admin/lot/json/' + detailId, //URL Tujuan Yg Mengenerate data Json nya
				datatype: "json", //Datatype yg di gunakan
				height: "auto", //Mengset Tinggi table jadi Auto menyesuaikan dengan isi table
				mtype: "POST",
				cmTemplate: {sortable:false},
				colModel: [
                    {
						label: "Berat <br> Std",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 100,       
                    },
                    {
						label: "Berat <br> Akt/50",
						index:"", 
                        name: "berat_ak",
                        hidden: false,
                        width: 75,       
                        editable: true       
                    },
                    {
						label: "Rata2 Berat <br> Akt/m",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 100,       
                    },
                    {
						label: "Billet <br> #",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "P Billet <br> Aktual",
						index:"", 
                        name: "p_billet_aktual",
                        hidden: false,
                        width: 70,       
                        editable: true       
                    },
                    {
						label: "Jml <br> Billet",
						index:"", 
                        name: "jumlah_billet",
                        hidden: false,
                        width: 70,       
                        editable: true       
                    },
                    {
						label: "Billet <br> VendorId",
						index:"", 
                        name: "billet_vendor_id",
                        hidden: false,
                        width: 120,       
                        editable: true       
                    },
                    {
						label: "Berat <br> Billet",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Total <br> Billet kg",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Rak#",
						index:"", 
                        name: "rak_btg",
                        hidden: false,
                        width: 70,
                        editable: true       
                    },
                    {
						label: "Jml btg di <br> Rak",
						index:"", 
                        name: "jumlah_di_rak_btg",
                        hidden: false,
                        width: 70,       
                        editable: true       
                    },
                    {
						label: "Hasil <br> Prod",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Berat <br> Hasil",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Recovery",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 100,       
                    },
                ],
                onSelectRow: editRow,
				rownumbers:true,
				rowNum: 10,
				//rowList: [10,20,30],
				pager: elJqgridPager,
				sortname: 'lot_id',
				viewrecords: true,
				sortorder: "desc",
				editurl: APP.siteUrl + 'admin/lot/crud', //URL Proses CRUD Nya
				multiselect: false,
				caption: '<button class="btn btn-primary add-lot" type="button">' + 'Tambah' + '</button> &nbsp;&nbsp; Data SPK Lot',
				loadComplete: function() {
					
					var total = 0;
					$('.berat-billet').each(function() {
						//alert($(this).html());
						//
						total = total + parseFloat($(this).html());

					});
					
					$('.total-billet').html(Math.round(total * 100) / 100);
				}
            });

			grid.jqGrid('navGrid', elJqgridPager,
			{
				view:false,
				edit:false,
				add:false,
				del:true,
				search: false,
				beforeRefresh: function () {
			        var rowKey = grid.jqGrid('getGridParam',"selrow");
					//var idxDice = $('#indexdice'+rowKey).val();
					grid.jqGrid('saveRow', rowKey, {
						/*extraparam: {
							idxdice: idxDice
						},*/
					});
			    }
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
		        var grid = $(elJqgrid);

		       	var count = grid.jqGrid('getGridParam', 'records');
		       	if(count > 1) {
		       		if (id != lastSelection) {

						//var idxDice = $('#indexdice'+lastSelection).val();
						
						grid.jqGrid('saveRow', lastSelection, {
						});

			            grid.jqGrid('restoreRow',lastSelection);
			            grid.jqGrid('editRow',id, {
			            	keys:false, 
			            	focusField: 2,
			            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
						        grid.trigger("reloadGrid");
					        	//window.TRANSACTION.handleGridUpDinamic();

						        /*grid.setRowData(rowid, { 
						        	section_name:  
						        });*/

						    }
			            });
			            lastSelection = id;

			        } else {
			        	grid.jqGrid('editRow',id, {
			            	keys:false, 
			            	focusField: 2,
			            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
						        grid.trigger("reloadGrid");
					        	//window.TRANSACTION.handleGridUpDinamic();

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
		            	focusField: 2,
		            	aftersavefunc: function (rowid, result) { // can add jqXHR, sentData, options 
					        grid.trigger("reloadGrid");
					        //window.TRANSACTION.handleGridUpDinamic();
					        /*grid.setRowData(rowid, { 
					        	section_name:  
					        });*/

					    }
		            });
		            lastSelection = id;
		       	}

		        

		    }



		},

		handleAddRow: function(elBtn) {
			var _this = this;

			$(elBtn).click(function() {

				var grid = $('.list-lot');

				var rowKey = grid.jqGrid('getGridParam',"selrow");
				//var idxDice = $('#indexdice'+rowKey).val();
				grid.jqGrid('saveRow', rowKey, {
					/*extraparam: {
						idxdice: idxDice
					},*/
				});

				$.ajax({
					url: APP.siteUrl + 'admin/lot/add_row_data/' + _this.masterDetailId,
					dataType: 'json',
					type: 'post',
					success: function() {
						_this.jqGrid.trigger("reloadGrid");
					}
				});
			});
		},

		handleTagEditor: function() {
			$('.tag-editor').tagEditor();

			$('.tag-editor').css({
        		"border": "1px solid #CCC",
				"width": "150px",
        		"border-radius": "3px"
        	});
		},

		handleSaveHeaderLot: function() {

			var _this = this;

			var formHeaderLot = $('.form-header-lot');
			_this.formHeaderLot = formHeaderLot;
			formHeaderLot.ajaxForm({
				success: function() {
					var grid = $('.list-spk');
					grid.trigger("reloadGrid");
				}
			});

			$('#potendbutt').keypress(function(e) {
				if(e.keyCode == 13) {
					formHeaderLot.submit();
				}
			});

			$('.close-modal').click(function() {
				formHeaderLot.submit();

				var grid = $('.list-lot');

				var rowKey = grid.jqGrid('getGridParam',"selrow");
				//var idxDice = $('#indexdice'+rowKey).val();
				grid.jqGrid('saveRow', rowKey, {
					/*extraparam: {
						idxdice: idxDice
					},*/
				});
			});
		},

		handleNumberInput: function() {
			var _this = this;


			$('.input-number').keypress(function(e) {

		        if(e.charCode > 57) {
		            return false;
		        }

		        if(e.charCode < 48) {
		            if(e.charCode == 0) {

		            	if(e.keyCode == 13) {
							_this.formHeaderLot.submit();
						}

		            }else {
		                return false;
		            }
		        }
		    });
		},

		handleSaveCardLog(status, location, dies)
		{
			var request = $.ajax({
				url: window.APP.siteUrl + 'admin/dies/set_log',
				type: 'post',
				data: {
					status: status,
					location: location,
					dies_id: dies
				},
				success: function() {

				}
			});

			return request;
		},

		handleMulaiPukul: function(elBtn, elInput, elIdxDies) {

			var _this = this;

			$(elBtn).click(function() {
				var date = new Date();
				var time = date.getHours() + ':' + date.getMinutes();

				if($(elInput).val() == "" || $(elInput).val() == " ")
				{
					$(elInput).val(time);
				}

				_this.handleSaveCardLog("0", 1, $(elIdxDies).val());


				var formHeaderLot = $('.form-header-lot');
				formHeaderLot.submit();
				$(elIdxDies).prop("disabled", true);
			});
		},

		handleSelesaiPukul: function(elBtn, elInputMulai, elInputSelesai, elSelisih) {
			$(elBtn).click(function() {
				var date = new Date();
				var time = date.getHours() + ':' + date.getMinutes();

				if($(elInputSelesai).val() == " " || $(elInputSelesai).val() == "")
				{
					$(elInputSelesai).val(time);
				}

				$.ajax({
					url: window.APP.siteUrl + 'admin/lot/check_selisih_time',
					type: 'POST',
					data: {
						time1: $(elInputMulai).val(),
						time2: $(elInputSelesai).val()
					},
					success: function(response) {
						$(elSelisih).html(response);
					}
				});


				var formHeaderLot = $('.form-header-lot');
				formHeaderLot.submit();
			});
		},

		handlePosting: function(el) {
			var _this = this;

			$(el).click(function() {
				swal({
					title: "Perlu Caustic ?",
					text: '<a href="#" class="btn btn-primary yes-swal">Ya</a>&nbsp;&nbsp;' +
						'<a href="#" class="btn btn-warning no-swal">Tidak</a>&nbsp;&nbsp;' +
						'<a href="#" class="btn btn-danger problem-swal">Problem</a>&nbsp;&nbsp;' + 
						'<div class="select-problem" style="display: none;">' + 
							'<select class="list-problem" style="padding: 5px;"></select>&nbsp;&nbsp;'+
							'<a href="#" class="btn btn-primary save-swal">Simpan</a>' + 
						'</div>',
					showConfirmButton: false,
					html: true
				});

				$.ajax({
					url: window.APP.siteUrl + 'admin/dies/list_problem',
					success: function(response) {
						$('.list-problem').html(response);
					}
				});	

				$('.yes-swal').click(function() {
					//alert('yes');
					$.ajax({
						url: window.APP.siteUrl + 'admin/dies/set_log',
						type: 'post',
						data: {
							status: 2,
							location: 1,
							dies_id: $('.index-dice').val()
						},
						success: function() {
							swal({
							  title: "Dies telah di set Ya",
							  text: "",
							  timer: 2000,
							  type: "success",
							  showConfirmButton: false
							});
						}
					});
				});

				$('.no-swal').click(function() {
					//alert('no');
					$.ajax({
						url: window.APP.siteUrl + 'admin/dies/set_log',
						type: 'post',
						data: {
							status: 2,
							location: 1,
							dies_id: $('.index-dice').val()
						},
						success: function() {
							swal({
							  title: "Dies telah di set Tidak",
							  text: "",
							  type: "success",
							  timer: 2000,
							  showConfirmButton: false
							});

						}
					});
				});

				$('.problem-swal').click(function() {
					
					$('.no-swal').hide();
					$('.yes-swal').hide();
					$('.select-problem').show();
					$(this).hide();
					
					$('.save-swal').click(function() {
						$.ajax({
							url: window.APP.siteUrl + 'admin/dies/set_log',
							type: 'post',
							data: {
								status: 29,
								location: 1,
								dies_id: $('.index-dice').val(),
								dies_problem: $('.list-problem').val()
							},
							success: function() {
								swal({
								  title: "Dies telah di set Problem",
								  text: "",
								  type: "warning",
								  timer: 2000,
								  showConfirmButton: false
								});

							}
						});
					});
					//alert('prob');
					//
					
					
				});
			});				
				
			
			

			
		}
	}
})(jQuery);