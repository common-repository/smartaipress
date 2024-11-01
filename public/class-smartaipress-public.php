<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/public
 * @author     Majestic Code <kdonet@gmail.com>
 */
class Smartaipress_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smartaipress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smartaipress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smartaipress-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smartaipress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smartaipress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smartaipress-public.js', array( 'jquery' ), $this->version, false );

		$smartaipress_feedback_fragments = [
			'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce' => wp_create_nonce( 'smartaipress_feedback_nonce' ),
			'activation_data_sent' => esc_html(sanitize_text_field(get_option('smartaipress_activate_data_sent')))
		];

		wp_enqueue_script( $this->plugin_name . '-activation-feedback', SMARTAIPRESS_ADMIN_ASSETS_URL . 'js/smartaipress-activation-feedback.js', array( 'jquery' ), uniqid(), false );

		wp_localize_script( 
			$this->plugin_name . '-activation-feedback', 
			'smartaipress_activation_obj', 
			$smartaipress_feedback_fragments
		);

	}

}
