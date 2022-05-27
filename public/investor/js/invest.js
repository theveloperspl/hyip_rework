let poller;

function poll(investment) {
    poller = setInterval(function () {
        $.getJSON(baseURL + "/invest/poll/" + investment, function (data) {
            if (data.status === 'active') {
                $.magnificPopup.close();
                animateCSS('#waitingCard', 'fadeOut').then((message) => {
                    $("#waitingCard").addClass("d-none");
                    $("#successCard").removeClass("d-none");
                });
                clearInterval(poller);
            }
        });
    }, 30 * 1000);
}

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
            currency = $.trim($("#currency").val()),
            plan = $.trim($('#plan').val());

        $.magnificPopup.open({
            type: 'ajax',
            items: {
                src: $(form).attr('action')
            },
            ajax: {
                settings: {
                    type: "POST",
                    data: {
                        "amount": amount,
                        "currency_id": currency,
                        "plan": plan
                    }
                }
            },
            tLoading: '<div class="spinner-border" role="status"></div>',
            closeMarkup: '<i class="flaticon-close text-gradient mfp-close"></i>',
            callbacks: {
                parseAjax: function (mfpResponse) {
                    if (mfpResponse.data.type === 'error') {
                        deployNotification(mfpResponse.data);
                        $.magnificPopup.close();
                        return false;
                    }
                },
                ajaxContentAdded: function () {
                    animateCSS('#investCard', 'fadeOut').then((message) => {
                        $("#investCard").addClass("d-none");
                        $("#waitingCard").removeClass("animate__animated animate__fadeOut d-none");
                    });

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
                                            animateCSS('#waitingCard', 'fadeOut').then((message) => {
                                                $("#waitingCard").addClass("d-none");
                                                $("#successCard").removeClass("d-none");
                                            });
                                        } else {
                                            deployNotification(data);
                                        }
                                    }
                                });
                                return false;
                            }
                        });
                    } else {
                        poll(investment);
                    }
                },
                close: function () {
                    clearInterval(poller);
                }
            }
        });
        return false;
    }
});

$('.newDeposit').click(function () {
    animateCSS('#waitingCard', 'fadeOut').then((message) => {
        $("#waitingCard").addClass("d-none");
        $("#investCard").removeClass("animate__animated animate__fadeOut d-none");
    });
});
