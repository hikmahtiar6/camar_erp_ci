window.DO = (function($) {
    return {
        handleNavigate: function() {
            var wrap = $('.wrap-grid');
            
            $('.kanan').click(function() {
                
                var pos = wrap.scrollLeft() + 150;
                wrap.animate({
                    scrollLeft:pos
                }, 200);
            });
            
            $('.kiri').click(function() {
                
                var pos = wrap.scrollLeft() - 150;
                wrap.animate({
                    scrollLeft:pos
                }, 200);
            });
        },
        
        handleTable: function() {
            
            var dataDo = [];
            
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
                            customerName: '',
                        });
                        setTimeout(function() {
                            window.DATE.init();
                            
                        }, 100);
                        
                    },
                    removeRow: function(row) {

                        this.doData.splice(row, 1);
                    }
                }
            });
        }
    }
})(jQuery);