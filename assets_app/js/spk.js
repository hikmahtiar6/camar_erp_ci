/**
 * Javascript SPK
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
window.SPK = (function() {
	return {
		loader: '.spk-loader',
		elModal: '.spk-modal',
		elForm: '.spk-form',

		init: function() {
			var parentThis = this;

			parentThis.handleVue();
		},

		removeArraySame: function(originalArray, objKey) {

			var trimmedArray = [];
			  var values = [];
			  var value;

			  for(var i = 0; i < originalArray.length; i++) {
			    value = originalArray[i][objKey];

			    if(values.indexOf(value) === -1) {
			      trimmedArray.push(originalArray[i]);
			      values.push(value);
			    }
			  }

			  return trimmedArray;
		},

		sumArray: function(array, shiftNo) {
			var sum = 0;

			for(var i = 0; i < array.length; i++) {
				if(array[i].shift_no == shiftNo) {
					sum += array[i].target_section;
				}
			}

			return window.APP.decimal3(sum);
		},

		handleVue: function() {
			var parentThis = this;
			var el = '#spk-card';

			var headerId = $('.header-id').val();
			var machineId = $('.machine-id').val();

			new Vue({
				el: el,
				delimiters: ['<%', '%>'],
				data: {
					list       : [],
					loading    : true,
					target_prod: '',
					shift      : 'SH-15/10-0001',
					section_id : '035',
					detail_id  : '0',
					len        : '',
					ppic       : '',
					dies       : '',
					posted     : '',
					type       : 'add'
				},
				computed: {
					getShift: function() {
						var __this = this;
						var shift = [];
						var data = __this.list;

						for(var i = 0; i < data.length; i++) {
							shift.push({
								shift          : data[i].shift_no,
								target_section : parentThis.sumArray(data, data[i].shift_no),
								machine_id     : data[i].machine_id
							});
						}

						return parentThis.removeArraySame(shift, 'shift');
						//return shift;
					} 
				},
				methods: {
					getData: function() {

						var __this = this;

						// mengambil data dengan ajax
						$.ajax({
							url      : window.APP.siteUrl + 'admin/spk/get_data/' + headerId,
							type     : 'GET',
							dataType : 'json',
							success  : function(response) {

								// set data utk vue ketika request data dari server berhasil
								__this.$set(__this, 'list', response);
								__this.$set(__this, 'loading', false);
							}
						});
					},
					
					// menambahkan data per baris dan set data kosong ke modal
					addRow: function() {
						var __this = this;

						// set data kosong untuk inputan di modal baru
						//__this.$set(__this, 'id', 'new');
						//
						__this.$set(__this, 'ppic', '');
						__this.$set(__this, 'len', '');
						__this.$set(__this, 'section_id', '035');
						__this.$set(__this, 'target_prod', '');
						__this.$set(__this, 'detail_id', '0');
						__this.$set(__this, 'shift', 'SH-15/10-0001');
						__this.$set(__this, 'posted', '');
						__this.$set(__this, 'type', 'add');
						
						
						// menampilkan modal
						$(parentThis.elModal).modal({backdrop: 'static', keyboard: false}, 'show');
						__this.requestDataLen('0', '035', headerId);
						__this.requestDataDies('0', '035', '');
					},

					// edit data per baris dan kirim data ke modal
					editRow: function(index) {
						var __this = this;

						// set data dari row terpilih
						__this.$set(__this, 'target_prod', __this.list[index].target_prod);
						__this.$set(__this, 'shift', __this.list[index].shift_id);
						__this.$set(__this, 'section_id', __this.list[index].section_id);
						__this.$set(__this, 'detail_id', __this.list[index].master_detail_id);
						__this.$set(__this, 'len', __this.list[index].len_id);
						__this.$set(__this, 'ppic', __this.list[index].ppic);
						__this.$set(__this, 'posted', __this.list[index].posted);
						__this.$set(__this, 'type', 'edit');

						// menampilkan modal
						$(parentThis.elModal).modal({backdrop: 'static', keyboard: false}, 'show');

						__this.requestDataLen(__this.list[index].master_detail_id, __this.list[index].section_id, headerId);
						__this.requestDataDies(__this.list[index].master_detail_id, __this.list[index].section_id, __this.list[index].dies);

						// cek validasi menggunakan validate.js
						$(parentThis.elForm).valid();
					},
					// menghapus data per baris
					removeRow: function(index) {
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

							// jika yakin menghapus maka menjalankan ajax request hapus data
							$.ajax({
								url: window.APP.siteUrl + 'admin/spk/delete',
								type: 'post',
								dataType: 'json',
								data: {
									id: __this.list[index].master_detail_id
								},
								success: function(response) {
									// menampilkan alert
									parentThis.showNotification(response.message, response.status);

									// refresh data vue
									__this.getData();
								}
							})
						});
					},

					// handle form menggunakan ajaxform
					handleForm: function() {

						var __this = this;

						// merubah select menjadi select2

						// nunggu 500 milidetik utk inisialisasi fungsi ajaxformnya
						setTimeout(function() {

							var spkLen = $('.spk-len option:selected');

							$('.spk-select').select2({
								width: '100%'
							});

							__this.handleRequestDataSpk();

							// menambahkan validasi menggunakan validate.js
							$(parentThis.elForm).validate();

							// coding ajax form
							$(parentThis.elForm).ajaxForm({
								dataType: 'json',
								data: {
									header_id: headerId,
									len      : spkLen.val()
								},
								success: function(response) {

									// ketika response success
									if(response.status == 'success')
									{
										// close modal
										$(parentThis.elModal).modal('hide');

										// refresh data vue
										__this.getData();
									}

									// alert
									//parentThis.showNotification(response.message, response.status);
								}
							});

							// saat close modal maka menyimpan data
							var closeModalEl = $('.spk-close-modal');
							closeModalEl.click(function() {
								$(parentThis.elForm).submit();

								if( $(parentThis.elForm).valid()) {
									$(parentThis.elModal).modal('hide');
								}
							});
						}, 500);
					},

					handleRequestDataSpk: function() {

						var __this = this;
						// handle select section
						var sectionNameEl = $('.spk-section-name');
						var sectionIdEl = $('.spk-section-id');
						
						var detailIdEl = $('.spk-detail-id');


						sectionNameEl.change(function() {
							sectionIdEl.html(this.value);

							__this.requestDataLen(detailIdEl.val(), this.value, headerId);
							__this.requestDataDies(detailIdEl.val(), this.value, '');
							
						});
					},

					requestDataLen: function(detailId, sectionId, headerId) {
						var __this = this;
						var lenEl = $('.spk-len');

						// request data len
						$.ajax({
							url     : window.APP.siteUrl + 'admin/master/get_data_len/' + detailId,
							type    : 'post',
							dataType: 'json',
							data    : {
								section_id: sectionId,
								header_id : headerId
 							},
							success : function(response) {
								var html = '';
								var selected = '';
								for (var i = 0; i < response.length ; i++) {
									if(__this.len == response[i]['value']) {
										selected = 'selected="selected"';
									}
									html += '<option value="'+response[i]['value']+'" '+selected+'>'+response[i]['text']+'</option>';
								}

								lenEl.html(html);
							}
						});
					},

					requestDataDies: function(detailId, sectionId, defaultValue) {
						var dieEl = $('.spk-dies');

						// request data len
						$.ajax({
							url     : window.APP.siteUrl + 'admin/master/get_data_index_dice/' + detailId + '/' + machineId,
							dataType: 'json',
							type    : 'post',

							data    : {
								section_id: sectionId,
 							},
							success : function(response) {


								var html = '';
								$.each(response, function(key, val) {
						        	if( defaultValue.indexOf(val.value) > -1) {
										selected = 'selected="selected"';
									} else {
										selected = '';
									}
						            html += '<option value="' + val.value + '" '+selected+'>' + val.text + '</option>';
						        });
								/*for (var i = 0; i < response.length ; i++) {
									html += '<option value="'+response[i]['value']+'">'+response[i]['text']+'</option>';
								}*/

								dieEl.html(html);
							}
						});
					}
				},
				created: function() {
					this.getData();
					this.handleForm();
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