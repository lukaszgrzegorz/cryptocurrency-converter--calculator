(function ($) {

    $('body').on('click', '#currencyConvert', function (e) {
        e.preventDefault();
        var amount = parseInt($('#currencyAmount').val(), 10);
        var sourceCurrency = $('#sourceCurrency').val();
        var descCurrency = $('#descCurrency option').filter(':selected').val();

        console.log('Amount change ' + amount + ' ' + sourceCurrency + ' ' + descCurrency);

        if (CustomAppAjaxObject !== undefined) {
            $.ajax({
                url: CustomAppAjaxObject.ajax_url,
                data: {
                    action: 'customapp',
                    security: CustomAppAjaxObject.check_nonce,
                    amount: amount,
                    sourceCurrency: sourceCurrency,
                    descCurrency: descCurrency
                },
                type: 'GET',
                success: function (response) {
                    var returnedData = JSON.parse(response);
                    $('#resultAmount').html(returnedData['amount']);
                    $('#resultCalculation').html(returnedData['result']);
                    $('#sourceCurrencyResult').html(returnedData['sourceCurrency']);
                    $('#descCurrencyResult').html(returnedData['descCurrency']);
                    $('#resultExchangeRate').html(returnedData['exchange_rate']);

                    var row = '<tr>';
                    row = row + '<td>' + returnedData['sourceCurrency']+ '</td>';
                    row = row + '<td>' + returnedData['descCurrency']+ '</td>';
                    row = row + '<td>' + returnedData['amount']+ '</td>';
                    row = row + '<td>' + returnedData['exchange_rate']+ '</td>';
                    row = row + '<td>' + returnedData['result']+ '</td>';
                    row = row + '</tr>';

                    $('#last-calculations > tbody:last').append(row);
                },
            });
        }
    });

})(jQuery);