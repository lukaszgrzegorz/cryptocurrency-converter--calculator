<?php
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// Custom App activate plugin hook
register_activation_hook( __FILE__, 'custom_app_plugin_activation' );
register_deactivation_hook( __FILE__, 'custom_app_plugin_deactivation' );

function custom_app_plugin_activation() {
	if ( ! wp_next_scheduled( 'custom_app_update_rate' ) ) {
		wp_schedule_event( time(), '5min', 'custom_app_update_rate' );
	}
}

function custom_app_plugin_deactivation() {
	wp_clear_scheduled_hook( 'custom_app_update_rate' );
}

function custom_app_update_rate() {
	$custom_api_key = get_option( 'custom_api_key' );
	$appApi = new Custom_App_API(CUSTOM_APP_API_URL, $custom_api_key);
	$appApi->update_exchange_rate( 'BTC', 'ETH' );
}

function custom_cron_schedules( $schedules ) {
	if ( ! isset( $schedules['5min'] ) ) {
		$schedules['5min'] = array(
			//'interval' => 5 * 60,
			'interval' => 5,
			'display'  => __( 'Once every 5 minutes' )
		);
	}

	return $schedules;
}

add_filter( 'cron_schedules', 'custom_cron_schedules' );