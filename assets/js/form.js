jQuery(".section-inner .submit-btn").on("click", function (event) {
    event.preventDefault();
    var amount = jQuery("input[name='amount']").val(),
        symbol = jQuery("select[name='symbol']").val(),
        convert = jQuery("select[name='convert']").val(),
        action = jQuery("form.js_form_cmc").attr('action');
    jQuery.ajax({
        url: action,
        method: 'post',
        data: {
            action: 'covert_form',
            amount: amount,
            symbol: symbol,
            convert: convert,
        },
        success: function (response) {
            jQuery("input[name='covert_sum']").val(response);
        }
    });
});