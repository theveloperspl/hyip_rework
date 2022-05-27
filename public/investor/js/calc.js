function calcthis() {
    var plan = $("#calcPercent");
    var amount = $("#calcDeposit");
    var type = plan.val();
    var depo = parseFloat(amount.val());
    var bonus = amount.data('bonus');
    var bonusMin = amount.data('bonus-min');
    var bonusMax = amount.data('bonus-max');
    var min = plan.find(':selected').data('min');
    var max = plan.find(':selected').data('max');
    var cycles = plan.find(':selected').data('cycles');
    var daily = plan.find(':selected').data('daily');

    //some clearing
    $("#results").addClass("d-none");
    $("#error").html("");
    $("#calcBonus").text("");

    if (depo < min || depo.length === 0) {
        $("#error").html("<p class=\"text-left invalid-feedback d-block animate__animated animate__headShake\">" + amount.data('msg-min').replace("{USD}", min) + "</p>");
        depo = min;
        amount.val(depo);
    } else if (depo > max) {
        $("#error").html("<p class=\"text-left invalid-feedback d-block animate__animated animate__headShake\">" + amount.data('msg-max').replace("{USD}", max) + "</p>");
        depo = max;
        amount.val(max);
    } else {
        if (bonus > 0 && depo >= parseFloat(bonusMin) && depo <= parseFloat(bonusMax)) {
            //bonus calculations
            bonusAmount = (depo * bonus / 100);
            $("#calcBonus").text(amount.data('bonus-msg').replace("{AMOUNT}", parseFloat(bonusAmount).toFixed(2)));
        }

        //make calculations
        dailyReturn = (depo * parseFloat(daily) / 100).toFixed(2);
        if (type == "daily") {
            finalProfit = ((dailyReturn * cycles) + depo).toFixed(2);
        } else if (type == "after") {
            finalProfit = dailyReturn;
        }

        $("#results").removeClass("d-none");
        console.log(depo);
        $("#calcAmount").text(depo);
        if (type == "daily") {
            $("#calcDaily").text(dailyReturn);
        } else if (type == "after") {
            $("#calcDaily").text("-");
        }
        $("#calcProfit").text(finalProfit);
        $("#calcDays").text(cycles);
    }
}

$("#calc").submit(function (event) {
    calcthis();
    event.preventDefault();
});
