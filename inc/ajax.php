<?php
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Ajax handler for currency translation
 */
function ajax_customapp_handler() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$amount         = filter_input( INPUT_GET, 'amount', FILTER_VALIDATE_INT );
		$sourceCurrency = filter_input( INPUT_GET, 'sourceCurrency', FILTER_SANITIZE_STRING );
		$descCurrency   = filter_input( INPUT_GET, 'descCurrency', FILTER_SANITIZE_STRING );

		if ( ! check_ajax_referer( 'custom-app-nonce', 'security' ) ) {
			echo 'Security Error';
			wp_die();
		}

		$result = [
			'amount'         => $amount,
			'sourceCurrency' => $sourceCurrency,
			'descCurrency'   => $descCurrency,
			'result'         => 0,
			'exchange_rate'  => 0
		];

		if ( strtoupper( $sourceCurrency ) != 'BTC' || strtoupper( $descCurrency ) != 'ETH' ) {
			$result = json_encode( $result );
			echo $result;
			wp_die();
		}

		$transient_cache_name  = 'exchange_rate_BTC_ETH';
		$exchange_rate_BTC_ETH = get_transient( $transient_cache_name );

		if ( false === $exchange_rate_BTC_ETH ) {
			$custom_api_key = get_option( 'custom_api_key' );
			$appApi         = new Custom_App_API( CUSTOM_APP_API_URL, $custom_api_key );
			$exchange_rate  = $appApi->get_exchange_rate( 'BTC', 'ETH' );

		} else {
			$exchange_rate = $exchange_rate_BTC_ETH;
		}

		$result['result']        = $amount * $exchange_rate;
		$result['exchange_rate'] = $exchange_rate;

		if ( is_numeric( $result['result'] ) && $result['result'] > 0 ) {
			custom_app_log_calculation( $result );
		}

		$result = json_encode( $result );
		echo $result;

		wp_die();
	}
}

add_action( 'wp_ajax_customapp', 'ajax_customapp_handler' );
add_action( 'wp_ajax_nopriv_customapp', 'ajax_customapp_handler' );

function custom_app_log_calculation( $calculation ) {

	$transient_cache_name = 'cryptocurrency_last_operations';
	$recent_calculations  = get_transient( $transient_cache_name );

	if ( $recent_calculations && is_array( $recent_calculations ) ) {
		$recent_calculations[] = $calculation;
		if ( $recent_calculations > 10 ) {
			$recent_calculations = array_slice( $recent_calculations, - 10, 10, true );
		}

		$set_in_cache = set_transient(
			$transient_cache_name, // Transient name
			$recent_calculations
		);
	} else {
		$last_calculations[0] = $calculation;

		$set_in_cache = set_transient(
			$transient_cache_name, // Transient name
			$last_calculations
		);
	}
}