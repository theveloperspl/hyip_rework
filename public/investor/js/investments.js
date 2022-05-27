var poller;

function poll(investment) {
    poller = setInterval(function () {
        $.getJSON(baseURL + "/invest/poll/" + investment, function (data) {
            if (data.status === 'active') {
                $.magnificPopup.close();
                $('[data-investment="' + investment + '"] .status').removeClass('waiting').addClass('active');
                clearInterval(poller);
            }
        });
    }, 30 * 1000);
}

$('.details').magnificPopup({
    type: 'ajax',
    tLoading: '<div class="spinner-border" role="status"></div>',
    closeMarkup: '<i class="flaticon-close text-gradient mfp-close"></i>',
    callbacks: {
        parseAjax: function (mfpResponse) {
            if (mfpResponse.data.type === 'error') {
                deployNotification(mfpResponse.data);
                $.magnificPopup.close();
            }
        },
        ajaxContentAdded: function () {
            let investment = $("#investment").val();
            if ($('#manual-form').length > 0) {
                $('#manual-form').validate({
                    ignore: ":hidden",
                    rules: {
                        transaction: {
                            required: true
                        },
                    },
                    submitHandler: function (form) {
                        // Values
                        let transaction = $.trim($('#transaction').val());

                        $.ajax({
                            type: "POST",
                            url: $(form).attr('action'),
                            dataType: "JSON",
                            data: {
                                transaction: transaction,
                                investment: investment
                            },
                            error: function () {
                                serverError();
                            },
                            success: function (data) {
                                if (data.status === 'active') {
                                    $.magnificPopup.close();
                                    $('[data-investment="' + investment + '"] .status').removeClass('waiting').addClass('active');
                                } else {
                                    deployNotification(data);
                                }
                            }
                        });
                        return false;
                    }
                });
            } else {
                if (investment)
                    poll(investment);
            }
        },
        close: function () {
            clearInterval(poller);
        }
    }
});
