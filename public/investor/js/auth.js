$("#login-form").validate({
    ignore: ":hidden",
    rules: {
        login: {
            required: true
        },
        password: {
            required: true,
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        }
    },
    submitHandler: function (form) {
        grecaptcha.execute($("script[src*='js/auth.js']").data("site-key"), {action: 'login'}).then(function (token) {
            $("#g-recaptcha-response").val(token);
            // Values
            var login = $.trim($('#login').val()),
                password = $.trim($('#password').val()),
                captcha = $("#g-recaptcha-response").val();
            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                dataType: "JSON",
                data: {
                    username: login,
                    password: password,
                    captcha: captcha
                },
                error: function () {
                    serverError();
                },
                success: function (data) {
                    if (data.action === "redirect") {
                        animateCSS('.login-box', 'fadeOut').then((message) => {
                            $(".login-box").addClass("d-none");
                            $(".success-box").removeClass("d-none");
                            setTimeout(function () {
                                document.location.href = data.url;
                            }, 850);
                        });
                    } else if (data.action == "2fa") {
                        animateCSS('.login-box', 'fadeOut').then((message) => {
                            $(".login-box").addClass("d-none");
                            $(".factor-box").removeClass("d-none");
                            $('#code').focus();
                        });
                    } else {
                        deployNotification(data);
                        grecaptcha.reset();
                    }
                }
            });
            return false;
        });
    }
});

$("#register-form").validate({
    ignore: ":hidden",
    rules: {
        username: {
            required: true
        },
        email: {
            required: true
        },
        password: {
            required: true,
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
        password_confirmation: {
            required: true,
            equalTo: "#password",
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
    },
    submitHandler: function (form) {
        grecaptcha.execute($("script[src*='js/auth.js']").data("site-key"), {action: 'register'}).then(function (token) {
            $("#g-recaptcha-response").val(token);
            // Values
            var username = $.trim($('#username').val()),
                email = $.trim($('#email').val()),
                password = $.trim($('#password').val()),
                password_confirmation = $.trim($('#password_confirmation').val()),
                captcha = $("#g-recaptcha-response").val();

            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                dataType: "JSON",
                data: {
                    username: username,
                    email: email,
                    password: password,
                    password_confirmation: password_confirmation,
                    captcha: captcha
                },
                error: function () {
                    serverError();
                },
                success: function (data) {
                    if (data.type == "success") {
                        animateCSS('.register-box', 'fadeOut').then((message) => {
                            $(".register-box").addClass("d-none");
                            $(".success-box").removeClass("d-none");
                        });
                    } else {
                        deployNotification(data);
                    }
                    grecaptcha.reset();
                }
            });
            return false;
        });
    }
});

$("#forgot-form").validate({
    ignore: ":hidden",
    rules: {
        email: {
            required: true
        },
    },
    submitHandler: function (form) {
        grecaptcha.execute($("script[src*='js/auth.js']").data("site-key"), {action: 'forgot'}).then(function (token) {
            $("#g-recaptcha-response").val(token);
            // Values
            var email = $.trim($('#email').val()),
                captcha = $("#g-recaptcha-response").val();

            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                dataType: "JSON",
                data: {
                    email: email,
                    captcha: captcha
                },
                error: function () {
                    serverError();
                },
                success: function (data) {
                    deployNotification(data);
                    grecaptcha.reset();
                }
            });
            return false;
        });
    }
});

$("#reset-form").validate({
    ignore: ":hidden",
    rules: {
        password: {
            required: true,
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
        password_confirmation: {
            required: true,
            equalTo: "#password",
            pattern: /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/
        },
    },
    submitHandler: function (form) {
        grecaptcha.execute($("script[src*='js/auth.js']").data("site-key"), {action: 'reset'}).then(function (token) {
            $("#g-recaptcha-response").val(token);
            // Values
            var password = $.trim($('#password').val()),
                password_confirmation = $.trim($('#password_confirmation').val()),
                token = $.trim($('#token').val()),
                email = $.trim($('#email').val()),
                captcha = $("#g-recaptcha-response").val();

            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                dataType: "JSON",
                data: {
                    password: password,
                    password_confirmation: password_confirmation,
                    token: token,
                    email: email,
                    captcha: captcha
                },
                error: function () {
                    serverError();
                },
                success: function (data) {
                    deployNotification(data);
                    grecaptcha.reset();
                }
            });
            return false;
        });
    }
});

$("#sfa-form").validate({
    ignore: ":hidden",
    rules: {
        code: {
            required: true
        }
    },
    submitHandler: function (form) {
        // Values
        var code = $.trim($('#code').val());

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
            }
        });
        return false;
    }
});

$("#resend-form").validate({
    ignore: ":hidden",
    rules: {
        email: {
            required: true
        },
    },
    submitHandler: function (form) {
        grecaptcha.execute($("script[src*='js/auth.js']").data("site-key"), {action: 'resend'}).then(function (token) {
            $("#g-recaptcha-response").val(token);
            // Values
            var email = $.trim($('#email').val()),
                captcha = $("#g-recaptcha-response").val();

            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                dataType: "JSON",
                data: {
                    email: email,
                    captcha: captcha
                },
                error: function () {
                    serverError();
                },
                success: function (data) {
                    deployNotification(data);
                    grecaptcha.reset();
                }
            });
            return false;
        });
    }
});
