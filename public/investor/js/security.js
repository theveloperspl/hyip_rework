$("#saved-codes").click(function () {
    const submitButton = $("#setup-2fa-form").find(':submit');
    submitButton.prop('disabled', false);
    $(this).prop('disabled', true);
});

$("#setup-2fa-form").validate({
    ignore: ":hidden",
    rules: {
        code: {
            required: true
        }
    },
    submitHandler: function (form) {
        const code = $.trim($('#code').val());

        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            dataType: "JSON",
            data: {
                code: code
            },
            error: function () {
                serverError();
            },
            success: function (data) {
                deployNotification(data);
                if (data.type === 'success') {
                    setTimeout(function () {
                        location.reload();
                    }, 850);
                }
            }
        });
        return false;
    }
});

$("#enable-2fa-form").validate({
    ignore: ":hidden",
    rules: {
        code: {
            required: true
        }
    },
    submitHandler: function (form) {
        const code = $.trim($("#enable-2fa-form input[name=code]").val());

        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            dataType: "JSON",
            data: {
                code: code
            },
            error: function () {
                serverError();
            },
            success: function (data) {
                if (data.action === "enabled") {
                    animateCSS('#enable2fa', 'fadeOut').then((message) => {
                        $("#enable2fa").addClass("d-none");
                        $("#disable2fa").removeClass("d-none");
                    });
                }
                deployNotification(data);
            }
        });
        return false;
    }
});

$("#disable-2fa-form").validate({
    ignore: ":hidden",
    rules: {
        code: {
            required: true
        }
    },
    submitHandler: function (form) {
        const code = $.trim($("#disable-2fa-form input[name=code]").val());

        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            dataType: "JSON",
            data: {
                code: code
            },
            error: function () {
                serverError();
            },
            success: function (data) {
                if (data.action === "disabled") {
                    animateCSS('#disable2fa', 'fadeOut').then((message) => {
                        $("#disable2fa").addClass("d-none");
                        $("#enable2fa").removeClass("d-none");
                    });
                }
                deployNotification(data);
            }
        });
        return false;
    }
});

$("#security-settings-form").validate({
    ignore: ":hidden",
    submitHandler: function (form) {
        const signin = $("#signin").prop("checked") | 0,
            withdrawal = $("#withdrawal").prop("checked") | 0,
            password_change = $("#password_change").prop("checked") | 0,
            wallets = $("#wallets").prop("checked") | 0,
            code = $.trim($("#security-settings-form input[name=code]").val());

        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            dataType: "JSON",
            data: {
                signin: signin,
                withdrawal: withdrawal,
                password_change: password_change,
                wallets: wallets,
                code: code
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


const table = $('#security-logs');
if (table.length > 0) {
    table.DataTable({
        'order': [[0, 'desc']],
        'searching': true,
        'responsive': true,
        'language': {
            'url': 'datatables-translations'
        }
    });
    new $.fn.dataTable.FixedHeader(table);

    // table.on('responsive-display', function (e, datatable, row, showHide, update) {
    //     if(showHide)
    //         $('[data-toggle="tooltip"]').tooltip();
    // });
}
