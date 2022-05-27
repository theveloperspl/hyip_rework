$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var data = [],
        totalPoints = 1000

    function xFormatter(v, xaxis) {
        return " ";
    }

    function yFormatter(v, yaxis) {
        return " ";
    }

    function getRandomData() {

        if (data.length > 0)
            data = data.slice(1)

        // Do a random walk
        while (data.length < totalPoints) {

            var prev = data.length > 0 ? data[data.length - 1] : 200,
                y = prev + Math.random() * 10 - 5

            if (y < 164) {
                y = 164
            } else if (y > 350) {
                y = 336
            }
            data.push(y)
        }

        // Zip the generated y values with the x values
        var res = []
        for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]])
        }

        return res
    }

    var interactive_plot = $.plot('#interactive', [getRandomData()], {
        grid: {
            color: "#ffffff",
            hoverable: false,
            borderWidth: 0,
            backgroundColor: 'rgba(255, 255, 255, 0)'
        },
        series: {
            shadowSize: 0,
            color: '#0E8A74'
        },
        lines: {
            fill: true,
            color: '#ff4c52'
        },
        yaxis: {
            min: 150,
            max: 350,
            show: false,
            font: {
                color: '#cccccc'
            },
            tickFormatter: yFormatter
        },
        xaxis: {
            show: false,
            showTickLabels: 'none',
            font: {
                color: '#cccccc'
            },
            tickFormatter: xFormatter
        }
    })

    var updateInterval = 5
    var realtime = 'on'

    function update() {
        interactive_plot.setData([getRandomData()])
        interactive_plot.draw()
        if (realtime === 'on')
            setTimeout(update, updateInterval)
    }

    if (realtime === 'on') {
        update()
    }

    $(document).on('click', '.list-addable-currencies', function () {
        $.magnificPopup.open({
            type: 'ajax',
            items: {
                src: 'addable-currencies'
            },
            tLoading: '<div class="spinner-border" role="status"></div>',
            closeMarkup: '<i class="flaticon-close text-gradient mfp-close"></i>',
            ajax: {
                tError: '<img src="' + baseURL + '/images/lock.png" class="img-fluid"><h3 class="mt-4">Session has expired, please refresh page</h3>'
            },
            callbacks: {
                ajaxContentAdded: function () {
                    $('.add-currency').click(function () {
                        const button = $(this);
                        const currency = button.data('currency');
                        $.ajax({
                            type: "POST",
                            url: "add_currency",
                            dataType: "JSON",
                            data: {
                                currency_id: currency,
                            },
                            error: function () {
                                serverError();
                            },
                            success: function (data) {
                                if (data.type === 'error') {
                                    deployNotification(data);
                                    return false;
                                }

                                button.attr('disabled', 'disabled');
                                animateCSS('.currency-' + currency, 'zoomOut').then(function () {
                                    $('.currency-' + currency).addClass('d-none');
                                    $("#currenciesHolder").load(location.href + " #currenciesHolder>*", resizeElements);
                                    if (data.last)
                                        $.magnificPopup.close();
                                });
                            }
                        });
                    });
                },
                close: function () {

                }
            }
        });
    });

    // let cryptos = $("script[src*='js/dashboard.js']").data("crypto");
    // setInterval(function () {
    //     const entry = cryptos[Math.floor(Math.random() * cryptos.length)];
    //     const amount = (Math.random() * (0.65 - 0.001) + 0.001).toFixed(8);
    //     console.log(entry.long + " " + amount);
    // }, 1000);
});
