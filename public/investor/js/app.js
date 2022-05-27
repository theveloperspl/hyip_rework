$(document).ajaxStart(function () {
    Pace.restart();
});

if ($.validator) {
    $.validator.setDefaults({
        onfocusout: function (e) {
            this.element(e);
        },
        onkeyup: true,
        highlight: function (element) {
            $(element).closest('.form-control').addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).closest('.form-control').removeClass('is-invalid');
            $(element).closest('.form-control').addClass('is-valid');
        },

        errorElement: 'div',
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group-prepend').length) {
                $(element).siblings(".invalid-feedback").append(error);
            } else {
                error.insertAfter(element);
            }
        },
    });
}

const baseURL = window.location.origin;

const copyToClipboard = function (text) {
    text.select();
    document.execCommand("copy");
};

function resizeElements() {
    if ($('.responsive-text').length > 0) {
        setTimeout(function () {
            $('.responsive-text').each(function () {
                const container = $(this);
                container.find('.media-body').width(container.width() - 120);
                fitty('.counter-number', {
                    minSize: 12,
                    maxSize: 30
                });
            })
        }, 500);
    }
}

$(window).on('load resize', function () {
    resizeElements();

    if ($('[data-bs-toggle="tooltip"]').length > 0) {
        $('body').tooltip({
            selector: '[data-bs-toggle="tooltip"]'
        });
    }

    if ($('[data-bs-toggle="popover"]').length > 0) {
        $('body').popover({
            selector: '[data-bs-toggle="popover"]',
            trigger: 'focus'
        });
    }

    if ($('.match-height').length > 0) {
        $('.match-height').matchHeight();
    }
});

$(document).on('click', '.copy', function () {
    const button = $(this),
        buttonText = button.text();
    copyToClipboard($(this).closest('.input-group').find('input'));
    button.html('<i class="flaticon-check"></i>');
    setTimeout(function () {
        button.html(buttonText);
    }, 750);
});

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($.magnificPopup) {
        $('.language-switcher').magnificPopup({
            type: 'inline',
            midClick: true,
            closeMarkup: '<i class="flaticon-close text-gradient mfp-close"></i>',
        });
    }
})
