<?php
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Class CustomAppSettingsPage
 */
class CustomAppSettingsPage {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Custom App Settings',
			'manage_options',
			'custom-app-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'custom_app_option_name' );
		?>
        <div class="wrap">
            <h1>App Settings</h1>
            <form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'custom_app_option_group' );
				do_settings_sections( 'custom-app-setting-admin' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'custom_app_option_group', // Option group
			'custom_app_option_name', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Custom App Settings', // Title
			array( $this, 'print_section_info' ), // Callback
			'custom-app-setting-admin' // Page
		);

		add_settings_field(
			'id_app_api_key', // ID
			'Custom API KEY', // Title
			array( $this, 'api_key_callback' ), // Callback
			'custom-app-setting-admin', // Page
			'setting_section_id' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['custom_api_key'] ) ) {
			$new_input['custom_api_key'] = sanitize_text_field( $input['custom_api_key'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print 'Enter your settings below:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="custom_api_key" size="54" name="custom_app_option_name[custom_api_key]" value="%s" />',
			isset( $this->options['custom_api_key'] ) ? esc_attr( $this->options['custom_api_key'] ) : ''
		);
	}
}
