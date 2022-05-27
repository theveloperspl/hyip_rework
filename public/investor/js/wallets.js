$(document).ready(function () {
    $(".wallets-form").each(function () {
        $(this).validate({
            ignore: ":hidden",
            submitHandler: function (form) {
                var address = $.trim($(form).find("input[name='address']").val()),
                    destination_tag = $.trim($(form).find("input[name='destination_tag']").val()),
                    currency_id = $.trim($(form).find("input[name='currency_id']").val());

                $.ajax({
                    type: "POST",
                    url: $(form).attr('action'),
                    dataType: "JSON",
                    data: {
                        address: address,
                        destination_tag: destination_tag,
                        currency_id: currency_id
                    },
                    error: function () {
                        serverError();
                    },
                    success: function (data) {
                        deployNotification(data);
                    }
                });

                return false;
            }
        });
    });
});
