/**
 * Javascript for TRANSFER
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
window.TRANSFER = (function($) {
	return {
		elVue: '.card-transfer',
		elRowNoData : '.transfer-no-data',
		elBilletAgingOven : '#transfer-jumlah-aging',
		// initial
		init: function() {
			var parentThis = this;
			
			// menjalankan fungsi fungsi
			parentThis.handleVue();
		},
	
		// handle data binding with vuejs
		handleVue: function() {
			// parent this
			var parentThis = this;
			
			// vue
			new Vue({
				el  : parentThis.elVue,
				data: {
					list : []
				},
				delimiters : ['<%', '%>'],
				methods: {
					getData : function(lotId) {
						// this vue
						var vue = this;
						
						// request data billet hasil extrusion
						$.ajax({
							url      : window.APP.siteUrl + 'admin/transfer/get_data',
							type     : 'POST',
							dataType : 'json',
							data     : {
								lot_id : lotId
							},
							success  : function(response) {
								// jika data tersedia maka generate data list Vue
								vue.$set(vue, 'list', response);
								
								// jika data kosong maka show "data tidak tersedia"
								if(vue.list.length == 0) {
									$(parentThis.elRowNoData).show();
								} else {
									$(parentThis.elRowNoData).hide();
								}
								
							}
						});
						
					},
					
					// ketika memilih lot tiket
					getDataWithChange: function(e) {
						var inputThis = $(e.target);
						var vue = this;
						
						// get data ulang dan mengirim ke vue
						vue.getData(inputThis.val());
					},
					
					// menyimpan data lot ke aging oven
					saveAgingLot: function(row) {
						var vue = this;
						
						// data dari row vue
						var jmlAging = vue.list[row].jumlah_aging;
						var hasilId = vue.list[row].hasil_id;
						
						// request save data
						$.ajax({
							url      : window.APP.siteUrl + 'admin/transfer/save_data',
							type     : 'POST',
							dataType : 'json',
							data     : {
								hasil_id     : hasilId,
								jumlah_aging : jmlAging
							},
							success  : function(response) {
								
							}
						});
					},
					
					// posting sesuai roles
					setPosted: function(row) {
						var vue = this;
						
						// data dari row vue
						var jmlAging = vue.list[row].jumlah_aging;
						var hasilId = vue.list[row].hasil_id;
						
						// cek bila billet aging oven belum diisi
						// maka menampilkan alert isi data
						// jika terisi maka memunculkan alert
						if($(parentThis.elBilletAgingOven + row).val() == "") {
							parentThis.showNotification('Silahkan isi dulu jumlah billetnya', 'warning');
						} else {
						
							// alert sebelum posted data
							swal({  
								title: "Apa Anda Yakin?",
								text: "Anda Akan Posting ini!",   
								type: "warning",   
								showCancelButton: true,   
								confirmButtonColor: "#DD6B55",   
								confirmButtonText: "Ya, Hapus!",   
								closeOnConfirm: false 
							}, function(){
								// jika ya maka 
								// request posted data
								$.ajax({
									url      : window.APP.siteUrl + 'admin/transfer/posted_data',
									type     : 'POST',
									dataType : 'json',
									data     : {
										hasil_id     : hasilId,
										jumlah_aging : jmlAging
									},
									success  : function(response) {
										
									}
								});
							});
						}
						
					}
				},
				// ketika vue dijalankan
				mounted: function() {
					// this vue
					var vue = this;
					
					vue.getData('0');
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
		}
	}
})(jQuery);