window.LOGIN = (function($) {
    return {
        init: function() {
            var _this = this;

            _this.handleValidate();
            _this.handleLogin();
        },
        handleValidate: function() {
            $('#sign_in').validate({
                highlight: function (input) {
                    console.log(input);
                    $(input).parents('.form-line').addClass('error');
                },
                unhighlight: function (input) {
                    $(input).parents('.form-line').removeClass('error');
                },
                errorPlacement: function (error, element) {
                    $(element).parents('.input-group').append(error);
                }
            });   
        },

        handleLogin: function() {
            $('#sign_in').ajaxForm({
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    swal(response.message,"", response.status);

                    if(response.status == 'success') {
                        window.location = $('#sign_in').attr('to_url');
                    }
                }
            });
        }

    }
})(jQuery);  