$(document).ready(function () {
    const chart = $("#referralRatesChart"),
        rates = chart.data('rates');

    let ratesArray = [[0, 0]], ticksArray = [['', '']], splitRates = rates.split(",");
    splitRates.forEach(function (value, index) {
        ratesArray.push([parseFloat(index + 1), parseFloat(value)]);
        ticksArray.push([parseFloat(index + 1), 'Level ' + parseFloat(index + 1)]);
    });

    if (window.outerWidth < 576) {
        chart.width(window.innerWidth - 80);
        chart.height(window.innerWidth / 2);
    } else {
        chart.width(window.innerWidth / 3);
        chart.height(window.innerWidth / 10);
    }

    $.plot(chart, [{
        data: ratesArray
    }], {
        series: {
            bars: {
                show: true,
                lineWidth: 0,
                fillColor: '#0E8A74',
                barWidth: 0.9
            }
        },
        grid: {
            borderWidth: 1,
            borderColor: 'transparent',
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            cssClass: "flotTip",
            content: "<b>Level %x</b><br>%y"
        },
        yaxis: {
            min: 0,
            max: splitRates[0],
            tickColor: 'transparent',
            font: {
                color: '#fff',
                size: 12
            },
            tickFormatter: function (v, axis) {
                return v + "%";
            }
        },
        xaxis: {
            ticks: ticksArray,
            tickColor: 'transparent',
            font: {
                color: '#fff',
                size: 14
            },
        }
    });
});
