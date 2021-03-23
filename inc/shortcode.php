<?php
add_action( 'init', 'custom_app_init' );

function custom_app_init() {
	add_shortcode( 'custom-app-form', 'custom_app_form_shortcode' );
}

function custom_app_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'amount' => 1,
	), $atts, 'custom-app-form' );

	?>
    <h1>Cryptocurrency Converter Calculator</h1>
    <form id="cryptocurrencyConverter" class="cryptocurrency-converter">
        <div class="calculator-row"><input type="text" name="currencyAmount" id="currencyAmount" placeholder="1"
                                           value="1"></div>

        <div class="calculator-row">
            <div class="leftColumn">
                <label for="sourceCurrency">Source currency:</label>

                <select name="sourceCurrency" id="sourceCurrency">
                    <option value="">select</option>
                    <option value="btc">BTC</option>
                </select>
            </div>
            <div class="rightColumn">
                <label for="descCurrency">Destination currency:</label>

                <select name="descCurrency" id="descCurrency">
                    <option value="">select</option>
                    <option value="eth">ETH</option>
                </select>
            </div>
        </div>
        <div class="calculator-row">
            <input type="submit" id="currencyConvert" placeholder="1" value="Calculate">
        </div>
        <div class="calculator-row calculator-result">
            <span id="resultAmount"></span> <span id="sourceCurrencyResult"></span> to
            <span id="descCurrencyResult"></span>,
            Exchange Rate: <span id="resultExchangeRate"></span><br/>
            <strong>Result: <span id="resultCalculation"></span></strong>
        </div>
    </form>

	<?php
	$transient_cache_name           = 'cryptocurrency_last_operations';
	$cryptocurrency_last_operations = get_transient( $transient_cache_name );
	?>

    <h2>Last Operations</h2>
    <table class="last-calculations" id="last-calculations">
        <thead>
            <tr>
                <th>Source currency</th>
                <th>Destination currency</th>
                <th>Amount</th>
                <th>Exchange Rate</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
		<?php if ( $cryptocurrency_last_operations ): ?>
			<?php foreach ( $cryptocurrency_last_operations as $last_operation ): ?>
                <tr>
                    <td><?php echo esc_html( $last_operation['sourceCurrency'] ); ?></td>
                    <td><?php echo esc_html( $last_operation['descCurrency'] ); ?></td>
                    <td><?php echo esc_html( $last_operation['amount'] ); ?></td>
                    <td><?php echo esc_html( $last_operation['exchange_rate'] ); ?></td>
                    <td><?php echo esc_html( $last_operation['result'] ); ?></td>
                </tr>
			<?php endforeach; ?>
		<?php endif; ?>
        </tbody>
    </table>

	<?php
}