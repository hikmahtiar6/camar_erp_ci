window.LOT = (function($) {
	return {

		jqGrid: null, 
		masterDetailId: null, 

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
						label: "Dies Index <br> Used",
						index:"", 
                        name: "dies_used",
                        hidden: false,
                        width: 100,
                        editable: true       
                    },
                    {
						label: "Berat <br> Std",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Berat <br> Akt/50",
						index:"", 
                        name: "berat_ak",
                        hidden: false,
                        width: 70,       
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
						label: "Total <br> Billet",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Rak#",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Jml di <br> Rak",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
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
                        width: 70,       
                    },
                    {
						label: "Mulai <br> Pukul",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Selesai <br> Pukul",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Total <br> Menit",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Downtime",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Dead <br> Cycle",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Ram <br> Speed",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Pressure <br> (bar)",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 70,       
                    },
                    {
						label: "Keterangan <br> Extrusion",
						index:"", 
                        name: "",
                        hidden: false,
                        width: 150,       
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
				caption: "Data SPK Lot",
            });

			grid.jqGrid('navGrid', elJqgridPager,
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
			            	focusField: 1,
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
			            	focusField: 1,
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
		            	focusField: 1,
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
				$.ajax({
					url: APP.siteUrl + 'admin/lot/add_row_data/' + _this.masterDetailId,
					dataType: 'json',
					type: 'post',
					success: function() {
						_this.jqGrid.trigger("reloadGrid");
					}
				});
			});
		}
	}
})(jQuery);