let poller;

function poll() {
    poller = setInterval(function () {
        $.getJSON(baseURL + "/is-telegram-connected", function (data) {
            if (data.connected) {
                $('#telegram-notifications-form :checkbox').each(function () {
                    $(this).prop("checked", true);
                });

                animateCSS('#connect-telegram-card', 'fadeOut').then((message) => {
                    $("#connect-telegram-card").addClass("d-none");
                    $("#disconnect-telegram-card").removeClass("d-none");
                    $("#telegram-notifications-card").removeClass("d-none");
                });

                clearInterval(poller);
            }
        });
    }, 5 * 1000);
}

$("#change-password-form").validate({
    ignore: ":hidden",
    rules: {
        current_password: {
            required: true,
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
        new_password: {
            required: true,
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
        new_password_confirmation: {
            required: true,
            equalTo: "#new_password",
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
    },
    submitHandler: function (form) {
        // Values
        const current_password = $.trim($('#current_password').val()),
            new_password = $.trim($('#new_password').val()),
            new_password_confirmation = $.trim($('#new_password_confirmation').val());

        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            dataType: "JSON",
            data: {
                current_password: current_password,
                new_password: new_password,
                new_password_confirmation: new_password_confirmation
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

$('#update-main-wallet-form #currency').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    if (clickedIndex != null && isSelected != null) {
        const currency = $(this).val();
        $.ajax({
            type: "POST",
            url: $('#update-main-wallet-form').attr('action'),
            dataType: "JSON",
            data: {
                currency_id: currency,
            },
            error: function () {
                //if failed use this
                $('#update-main-wallet-form #currency').selectpicker('val', previousValue);
                serverError();
            },
            success: function (data) {
                if (data.type === 'error') {
                    $('#update-main-wallet-form #currency').selectpicker('val', previousValue);
                    deployNotification(data);
                }
            }
        });
    }
});

$('#mailing-notifications-form :checkbox').change(function () {
    const checkbox = $(this),
        setting = checkbox.val(),
        checked = this.checked;

    $.ajax({
        type: "POST",
        url: $('#mailing-notifications-form').attr('action'),
        dataType: "JSON",
        data: {
            setting: setting,
            status: checked | 0,
        },
        error: function () {
            checkbox.prop("checked", !checked);
            serverError();
        },
        success: function (data) {
            if (data.type === 'error') {
                checkbox.prop("checked", !checked);
                deployNotification(data);
            }
        }
    });

});

$('#telegram-notifications-form :checkbox').change(function () {
    const checkbox = $(this),
        setting = checkbox.val(),
        checked = this.checked;

    $.ajax({
        type: "POST",
        url: $('#telegram-notifications-form').attr('action'),
        dataType: "JSON",
        data: {
            setting: setting,
            status: checked | 0,
        },
        error: function () {
            checkbox.prop("checked", !checked);
            serverError();
        },
        success: function (data) {
            if (data.type === 'error') {
                checkbox.prop("checked", !checked);
                deployNotification(data);
            }
        }
    });
});

$('#connect-telegram').click(function () {
    const getCodeUrl = $(this).data('url');
    $.get(getCodeUrl, function (data) {
        const input = $('#message-input'),
            button = $('#connect-telegram-button'),
            token = data.token;

        let messageToReplace = input.val(),
            urlToReplace = button.attr('href');

        //replace values
        if (messageToReplace.search(':token') !== -1 && urlToReplace.search(':token') !== -1) {
            console.log('token placeholder');
            messageToReplace = messageToReplace.replace(':token', token);
            urlToReplace = urlToReplace.replace(':token', token);
            //place values again
            input.val(messageToReplace);
            button.attr('href', urlToReplace);
        } else {
            messageToReplace = messageToReplace.slice(0, 7);
            urlToReplace = urlToReplace.slice(0, urlToReplace.length - 32);
            //place values again
            input.val(messageToReplace + token);
            button.attr('href', urlToReplace + token);
        }

        animateCSS('#connect-button-area', 'fadeOut').then((message) => {
            $("#connect-button-area").addClass("d-none");
            $("#code-area").removeClass("d-none");
        });
    });
});

$('#connect-telegram-button, .copy').click(function () {
    poll();
    animateCSS('#code-area', 'fadeOut').then((message) => {
        $("#code-area").addClass("d-none");
        $("#waiting-area").removeClass("d-none");
    });
});

$('#back-to-code').click(function () {
    clearInterval(poller);
    animateCSS('#waiting-area', 'fadeOut').then((message) => {
        $("#waiting-area").addClass("d-none");
        $("#code-area").removeClass("d-none");
    });
});

$('#disconnect-telegram').click(function () {
    const disconnectTelegramUrl = $(this).data('url');
    $.post(disconnectTelegramUrl, function (data) {
        if (data.success) {
            animateCSS('#disconnect-telegram-card', 'fadeOut').then((message) => {
                $("#disconnect-telegram-card").addClass("d-none");
                $("#code-area").addClass("d-none");
                $("#waiting-area").addClass("d-none");
                $("#connect-button-area").removeClass("d-none");
                $("#connect-telegram-card").removeClass("d-none");
            });
            animateCSS('#telegram-notifications-card', 'fadeOut').then((message) => {
                $("#telegram-notifications-card").addClass("d-none");
            });
        } else {
            deployNotification(data);
        }
    });
});
