<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin
 * @author     Majestic Code <kdonet@gmail.com>
 */
class Smartaipress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin Pages Property
	 *
	 * The `$plugin_pages` property is an array that stores the page ids of important pages
	 * related to the SmartAIPress plugin within the WordPress admin panel. These ids are used
	 * to identify and manage the plugin's presence and settings pages.
	 *
	 * @var array An array of page slugs.
	 * @access private
	 * @since 1.0.0
	 */
	private $plugin_pages = [
		'toplevel_page_smartaipress',
		'smartaipress_page_smartaipress-settings',
	];

	/**
	 * API Usage - Chart Pages Property
	 *
	 * The `$api_usage_chart_pages` property is an array that stores the page ids of important pages
	 * related to the Openai API usage within the WordPress admin panel. These ids are used
	 * to identify and manage api usage pages.
	 *
	 * @var array An array of page slugs.
	 * @access private
	 * @since 1.0.0
	 */
	private $api_usage_chart_pages = [
		'toplevel_page_smartaipress'
	];

	/**
	 * Class Constructor
	 *
	 * Initializes an instance of the plugin or class, setting its essential properties.
	 *
	 * @param string $plugin_name The unique identifier or name of the plugin or class.
	 * @param string $version     The version number of the plugin or class.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		global $smartaipress_settings;

		$smartaipress_settings = smartaipress()->get_settings();

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Enqueue Styles
	 *
	 * This function is responsible for enqueuing and registering the necessary CSS stylesheets
	 * required for styling a WordPress plugin. It is typically called within the
	 * WordPress 'wp_enqueue_scripts' action hook to ensure styles are properly loaded.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		// SweetAlert v11.7.32
		wp_enqueue_style( $this->plugin_name . '-sweetalert2', SMARTAIPRESS_ADMIN_ASSETS_URL . 'vendor/sweetalert2/sweetalert2.min.css', array(), '11.7.32', 'all' );
		
		// SmartAIPress
		wp_enqueue_style( $this->plugin_name . '-grid', SMARTAIPRESS_ADMIN_ASSETS_URL . 'css/smartaipress-grid.min.css', array(), uniqid(), 'all' );
		wp_enqueue_style( $this->plugin_name, SMARTAIPRESS_ADMIN_ASSETS_URL . 'css/smartaipress-admin.css', array(), uniqid(), 'all' );

		// SmartAIPress OpenAI
		wp_enqueue_style( $this->plugin_name . '-openai', SMARTAIPRESS_ADMIN_ASSETS_URL . 'css/smartaipress-openai.css', array(), uniqid(), 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 	1.0.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen()->id;

		wp_enqueue_script( $this->plugin_name . '-sweetalert2', SMARTAIPRESS_ADMIN_ASSETS_URL . 'vendor/sweetalert2/sweetalert2.all.min.js', array( 'jquery' ), '11.7.32', true );

		wp_enqueue_script( $this->plugin_name, SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-admin-settings.js', array( 'jquery' ), uniqid(), false );

		$ajax_data = array(
			'ajax_url' 							=> esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'                             => wp_create_nonce( 'smartaipress_nonce' ),
			'is_block_editor' 					=> smartaipress()->is_block_editor(),
			'logo_without_text' 				=> esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/smartaipress-no-text-logo.png' ),
			'openai_icon_url' 					=> esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/smartaipress-logo.png' ),
			'insert_to_txteditor_label' 		=> esc_html__( 'Insert to Editor', 'smartaipress' ),
			'cancel_btn_label' 					=> esc_html__( 'Cancel', 'smartaipress' ),
			'click_on_textarea_to_copy_label' 	=> esc_html__( 'Click on textarea to copy!', 'smartaipress' ),
			'text_copied_label' 				=> esc_html__( 'Text copied!', 'smartaipress' ),
			'promptImageRequiredMsg'            => esc_html__( 'The image prompt is required!', 'smartaipress' ),
			'dall_e_2_max_length'               => esc_html__( 'Maximum prompt lenght for Dalle 2 model is 1000 characters.', 'smartaipress' ),
			'chart_day_filter'                  => esc_html__( 'Day filter can not be less than 1.', 'smartaipress' ),
			'chart_days_maximum'                => esc_html__( '365 days is maximum.', 'smartaipress' ),
			'chart_dates_defined'               => esc_html__( 'Dates must be defined.', 'smartaipress' ),
			'chart_date_in_future'              => esc_html__( 'Selected date is in future.', 'smartaipress' ),
			'chart_date_in_past'                => esc_html__( 'Date can not be before ', 'smartaipress' ),
			'from_bigger_than_to'               => esc_html__( 'From date can not be bigger than To date.', 'smartaipress' ),
			'chart_dates_already_displayed'     => esc_html__( 'Api usage data for specified dates already displayed.', 'smartaipress' ),
			'chart_days_data_already_displayed' => esc_html__( 'Data already displayed for specified number of days.', 'smartaipress' )
		);

		wp_localize_script( $this->plugin_name, 'smartaipress', $ajax_data );

		wp_enqueue_script( $this->plugin_name . '-openai', SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-admin-openai.js', array( 'jquery' ), uniqid(), false );

		$smartaipress_feedback_fragments = [
			'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce' => wp_create_nonce( 'smartaipress_feedback_nonce' ),
			'activation_data_sent' => esc_html(sanitize_text_field(get_option('smartaipress_activate_data_sent'))),
			'deactivation_data_sent' => esc_html(sanitize_text_field(get_option('smartaipress_deactivate_data_sent')))
		];

		wp_enqueue_script( $this->plugin_name . '-activation-feedback', SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-activation-feedback.js', array( 'jquery' ), uniqid(), false );

		wp_localize_script( 
			$this->plugin_name . '-activation-feedback', 
			'smartaipress_activation_obj', 
			$smartaipress_feedback_fragments
		);

		if($screen == "plugins") {
			wp_enqueue_script( $this->plugin_name . '-deactivation-feedback', SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-deactivation-feedback.js', array( 'jquery' ), uniqid(), false );

			wp_localize_script( 
				$this->plugin_name . '-deactivation-feedback', 
				'smartaipress_deactivation_obj', 
				$smartaipress_feedback_fragments
			);
		}
		
		if ( in_array( $screen, $this->api_usage_chart_pages ) ) {
			wp_enqueue_script( $this->plugin_name . '-api-usage', SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-admin-api-usage.js', array( 'jquery' ), uniqid(), true );
			$api_usage_nonce = wp_create_nonce( 'api_usage_data' );
			wp_localize_script( 
				$this->plugin_name . '-api-usage', 
				'openai_api', 
				[
					'url' => esc_url( admin_url('admin-ajax.php') ), 
					'nonce' => $api_usage_nonce, 'ajax_data' => $ajax_data
				] 
			);
			wp_enqueue_script( $this->plugin_name . '-chart', SMARTAIPRESS_ADMIN_ASSETS_URL . 'vendor/chartjs/chart.umd.js', array( 'jquery' ), uniqid(), true );
		}
	}

	/**
	 * Display a defined notifications in admin dashboard.
	 * 
	 * @since 1.0.0
	 */
	public function notifications() {
		$openai_api_key = smartaipress()->get_settings('openai_api_key');

		if (!$openai_api_key) {
			printf( 
				'<div class="%s"><p>%s <a href="%s">%s</a></p></div>', 
				'notice notice-warning', 
				esc_html__( 'The OpenAI API key is not set for the SmartAIPress plugin.', 'smartaipress' ),
				esc_url( admin_url('admin.php?page=smartaipress-settings') ),
				esc_html__( 'Set API Key', 'smartaipress' ),
			);
		}
	}

	/**
	 * Remove Admin Notifications from Plugin Pages
	 *
	 * This function is responsible for removing admin notifications displayed on specific
	 * SmartAIPress pages within the WordPress admin panel. It identifies the current
	 * screen, and if it matches any of the plugin pages specified in the $plugin_pages
	 * property, it removes admin notices from those pages.
	 *
	 * @since 1.0.0
	 */
	public function remove_admin_notifications_from_smartaipress_pages() {
		$plugin_pages = [
			'smartaipress',
			'smartaipress-settings',
		];
		if( isset($_GET) && !empty( $_GET['page']) && in_array( $_GET['page'], $plugin_pages) ) { // phpcs:ignore
			remove_all_actions( 'admin_notices' );
		}
	}

	/**
	 * Enqueue assets for the Gutenberg block editor.
	 *
	 * This function enqueues JavaScript assets required for the Gutenberg block editor.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script( 
			$this->plugin_name . '-gutenberg', 
			SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-admin-gutenberg.js', 
			array('wp-blocks', 'wp-components', 'wp-element', 'wp-editor'), 
			uniqid(), 
			false 
		);
		wp_enqueue_style( 
			$this->plugin_name . '-gutenberg-styles', 
			SMARTAIPRESS_ADMIN_ASSETS_URL . 'css/smartaipress-admin-gutenberg.css', 
			array(), 
			uniqid(), 
			'all' 
		);
	}

	/**
	 * Adds the SmartAIPress trigger button to the admin interface.
	 */
	public function add_openai_trigger_button() { ?>
		<div id="smartaipress-trigger-button-wrapper">
			<a href="#" class="smartaipress-content-generator-button">
				<img src="<?php echo esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/smartaipress-no-text-logo.png' ); ?>" height="16" width="16"> 
				SmartAIPress
			</a>
			<?php require_once SMARTAIPRESS_DIR . 'admin/partials/smartaipress-admin-tooltip.php'; ?>
		</div>
		<?php
	}

	/**
     * Save plugin settings via AJAX request.
     *
     * This function handles the AJAX request for saving plugin settings. It verifies the nonce,
     * sanitizes and validates the settings data, and updates the corresponding WordPress options.
     * The function sends a JSON response indicating success or failure.
     *
     * @since 1.0.0
     */
    public function save_settings() {
		// Check the AJAX nonce
		$nonce = isset($_POST['nonce']) ? esc_html(sanitize_text_field($_POST['nonce'])) : ''; // phpcs:ignore
	
		if (!wp_verify_nonce($nonce, 'smartaipress_data_nonce')) {
			$error = [
				'error' => 
				[
					'code' 		=> 'invalid_nonce',
					'label'   	=> esc_html__( 'Invalid nonce!', 'smartaipress' ),
					'message' 	=> esc_html__( 'The nonce verification failed.', 'smartaipress' ),
				]
			];
			wp_send_json_error($error);
		}
	
		// Retrieve and sanitize the settings data
		$settings = [];

		$settings['openai_api_key'] = esc_html(sanitize_text_field($_POST['settings']['openai_api_key']));
		$settings['openai_default_model'] = esc_html(sanitize_text_field($_POST['settings']['openai_default_model']));
		$settings['openai_default_language'] = esc_html(sanitize_text_field($_POST['settings']['openai_default_language']));
		$settings['openai_default_tone_of_voice'] = esc_html(sanitize_text_field($_POST['settings']['openai_default_tone_of_voice']));
		$settings['openai_default_creativity'] = esc_html(sanitize_text_field($_POST['settings']['openai_default_creativity']));
		$settings['openai_max_input_length'] = esc_html(sanitize_text_field(absint($_POST['settings']['openai_max_input_length'])));
		$settings['openai_max_output_length'] = esc_html(sanitize_text_field(absint($_POST['settings']['openai_max_output_length'])));
		$settings['openai_response_log'] = esc_html(sanitize_text_field(absint($_POST['settings']['openai_response_log'])));
	
		// Serialize the settings data before saving
		$serialized_settings = serialize($settings);
	
		// Update the option with the serialized settings data
		update_option('smartaipress_settings', $serialized_settings);
	
		wp_send_json_success([
			'success' => [
				'label' 	=> esc_html__( 'Saved!', 'smartaipress' ),
				'message' 	=> esc_html__( 'Settings saved successfully.', 'smartaipress' )
			]
		]);
	}

	/**
	 * Renders the SmartAI Press admin footer content.
	 *
	 * This function includes the necessary footer content for the SmartAI Press
	 * administration panel. It's responsible for rendering additional scripts,
	 * styles, and elements needed for the admin interface.
	 *
	 * @since 1.0.0
	 */
	public function admin_footer() {
		require_once SMARTAIPRESS_DIR . 'admin/partials/smartaipress-admin-footer-include.php';
	}

}
