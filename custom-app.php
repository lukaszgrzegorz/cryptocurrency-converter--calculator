<?php
/*
Plugin Name: Custom App
Description: Creates settings page for Custom Application.
Version:     1.0
Author: Aspencore
Author URI: https://aspencore.com/
*/

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'CUSTOM_APP_API_URL' ) ) {
	define( 'CUSTOM_APP_API_URL', 'https://web-api.coinmarketcap.com' );
}

add_action( 'wp_enqueue_scripts', 'ajax_custom_app_enqueue_scripts' );
function ajax_custom_app_enqueue_scripts() {
	wp_register_script( 'ajax_custom_app_js', plugins_url( '/js/script.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'ajax_custom_app_js' );
	wp_localize_script( 'ajax_custom_app_js', 'CustomAppAjaxObject', [
		'ajax_url'    => admin_url( 'admin-ajax.php' ),
		'check_nonce' => wp_create_nonce( 'custom-app-nonce' )
	] );

	wp_enqueue_style(
		'custom_app_css',
		plugins_url( 'css/style.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' )
	);
}

add_action( 'plugins_loaded', 'custom_app_plugins_loaded' );
function custom_app_plugins_loaded() {
	if ( is_admin() ) {
		$custom_settings_page = new CustomAppSettingsPage();
	}
}

require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/cron.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/api.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/ajax.php' );