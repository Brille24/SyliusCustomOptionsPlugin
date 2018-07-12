var customerOptions = {
	"changeCustomerAmountCurrencyOnChannelChange":
			function (element) {
					// Get the currency from the selected channel
					var currency = $(element).children().filter(":selected")[0].getAttribute("data-attribute");

					//Select the amount input
					var amountInputId = '#' + $(element).attr('id').replace('_channel', '_amount');

					//Set the label (previous element) to the currency
					var amountLabel = $(amountInputId).prev();
					$(amountLabel).text(currency);
			}
};

$(document).ready(function () {
	var i = 0;
	var result;
	do {
			result = $('#sylius_product_customerOptionValuePrices_' + i + '_channel');
			result.change();
			i++;
	} while (result.length !== 0 && i < 100);
});