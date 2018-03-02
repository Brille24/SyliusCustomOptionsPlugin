var customerOptions = {
    "changeCustomerAmountCurrencyOnChannelChange":
        function (element){
            // Get the currency from the selected channel
            var currency = $(element).children().filter(":selected")[0].getAttribute("data-attribute");

            //Select the amount input
            var amountInputId = '#' + $(element).attr('id').replace('_channel', '_amount');

            //Set the label (previous element) to the currency
            var amountLabel = $(amountInputId).prev();
            $(amountLabel).text(currency);
        }
};