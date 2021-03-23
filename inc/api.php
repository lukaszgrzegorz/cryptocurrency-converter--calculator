<?php
/**
 * Class Custom_API
 */

class Custom_App_API {

	function __construct( $api_url, $api_key = null ) {
		$this->api_url = $api_url;
		$this->api_key = $api_key;
	}

	/**
	 * Updating exchange rate for selected currencies
	 *
	 * @param $symbol_currency
	 * @param $convert_currency
	 */
	function update_exchange_rate( $symbol_currency, $convert_currency ) {

		if ( $symbol_currency === 'BTC' && $convert_currency === 'ETH' ) {
			//$url = 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?amount=1&convert_id=1027&id=1';
			$request_url = $this->api_url . '/v1/tools/price-conversion?amount=1&symbol=BTC&convert=ETH';

			$args = [
				'timeout' => 120,
				'headers' => [
					'Accepts: application/json',
					'X-CMC_PRO_API_KEY: ' . $this->api_key
				]
			];

			$response = wp_remote_get( $request_url, $args );

			if ( is_wp_error( $response ) ) {
				return;
			} else {
				$results = wp_remote_retrieve_body( $response );
			}

			try {
				$json = json_decode( $results );

				if ( isset( $json->data->quote->ETH->price ) ) {
					$transient_cache_name = 'exchange_rate_BTC_ETH';

					$set_in_cache = set_transient(
						$transient_cache_name, // Transient name
						$json->data->quote->ETH->price, // What should be saved
						7 * DAY_IN_SECONDS // Lifespan of transient is 7 days
					);
				}
			} catch ( Exception $ex ) {
				$json = null;
			}
		}
	}

	/**
	 * Updating exchange rate for selected currencies
	 *
	 * @param $symbol_currency
	 * @param $convert_currency
	 */
	function get_exchange_rate( $symbol_currency, $convert_currency ) {
		$exchange_rate = false;

		if ( $symbol_currency === 'BTC' && $convert_currency === 'ETH' ) {
			//$url = 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?amount=1&convert_id=1027&id=1';
			$request_url = $this->api_url . '/v1/tools/price-conversion?amount=1&symbol=BTC&convert=ETH';

			$args = [
				'timeout' => 120,
				'headers' => [
					'Accepts: application/json',
					'X-CMC_PRO_API_KEY: ' . $this->api_key
				]
			];

			$response = wp_remote_get( $request_url, $args );

			if ( is_wp_error( $response ) ) {
				return false;
			} else {
				$results = wp_remote_retrieve_body( $response );
			}

			try {
				$json = json_decode( $results );

				if ( isset( $json->data->quote->ETH->price ) ) {
					$transient_cache_name = 'exchange_rate_BTC_ETH';

					$set_in_cache = set_transient(
						$transient_cache_name, // Transient name
						$json->data->quote->ETH->price, // What should be saved
						7 * DAY_IN_SECONDS // Lifespan of transient is 7 days
					);

					$exchange_rate = $json->data->quote->ETH->price;
				}
			} catch ( Exception $ex ) {
				$json = null;
			}
		}

		return $exchange_rate;
	}
}
