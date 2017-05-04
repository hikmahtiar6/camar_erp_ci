window.DO = (function($) {
    return {
        handleNavigate: function() {
            var wrap = $('.wrap-grid');
            
            $('.kanan').click(function() {
                
                var pos = wrap.scrollLeft() + 700;
                wrap.animate({
                    scrollLeft:pos
                }, 200);
            });
            
            $('.kiri').click(function() {
                
                var pos = wrap.scrollLeft() - 700;
                wrap.animate({
                    scrollLeft:pos
                }, 200);
            });
        },
        
        handleTable: function() {
            
            var dataDo = [];
            
            $.ajax({
                url: window.APP.siteUrl + 'admin/deliveryorder/get_data',
                dataType: 'json',
                success: function(response) {
                    dataDo = response;
                    
                    if(dataDo.length > 0) {
                        $('.do-save').show();
                    }
                    
                    var DOTable = new Vue({
                        delimiters: ['<%', '%>'],
                        el: '.vue-do',
                        data: {
                            doData: dataDo,
                            submitted: false
                        },
                        methods: {
                            addNewRow: function() {
                                this.doData.push({
                                    masterDoId   : '',
                                    customerName : '',
                                    supplier     : '',
                                    doContractNo : '',
                                    doDate       : '',
                                    status       : '',
                                    rcvDate      : '',
                                    rcvNo        : '',
                                    lineNo       : '',
                                    catatan      : '',
                                    dieType      : '',
                                    subComp      : '',
                                    productCode  : '',
                                    size  : '',
                                    unit  : '',
                                    qty  : '',
                                    finalIdx  : '',
                                });
                                
                                setTimeout(function() {
                                    window.DATE.init();
                                }, 100);
                                
                                $('.do-save').show();
                                
                            },
                            saveAllRow: function() {
                                $.ajax({
                                    url: window.APP.siteUrl + 'admin/deliveryorder/save_all',
                                    type: 'post',
                                    data: {
                                        post: this.doData
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        $.notify(response.message, response.status);
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 1000);
                                    }
                                });
                            },
                            removeRow: function(row) {
                                
                                $.ajax({
									url: window.APP.siteUrl + 'admin/deliveryorder/delete',
									type: 'post',
									data: {
										'id': this.doData[row].masterDoId
									},
									dataType: 'json',
									success: function(response) {
										$.notify(response.message, response.status);
									}
								});
                                
                                this.doData.splice(row, 1);
                                var jml = this.doData.length;
                                
                                if(jml == 0) {
                                    $('.do-save').hide();
                                }
                            }
                        }
                    });
                    
                }
            });
            
            
            
            /*$('.form-do').ajaxForm({
                type: 'post'
            });*/
        }
    }
})(jQuery);