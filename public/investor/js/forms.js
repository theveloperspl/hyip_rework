$("#invest-form").validate({
    ignore: ":hidden",
    rules: {
        amount: {
            required: true
        },
        currency: {
            required: true
        }
    },
    submitHandler: function (form) {
        // Values
        const amount = $.trim($('#amount').val()),
            currency = $.trim($('#currency').val());


        return false;
    }
});
