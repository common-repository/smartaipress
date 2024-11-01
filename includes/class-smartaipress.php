<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Smartaipress
 * @subpackage Smartaipress/includes
 * @author     Majestic Code <kdonet@gmail.com>
 */
class Smartaipress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smartaipress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SMARTAIPRESS_VERSION' ) ) {
			$this->version = SMARTAIPRESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'smartaipress';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smartaipress_Loader. Orchestrates the hooks of the plugin.
	 * - Smartaipress_i18n. Defines internationalization functionality.
	 * - Smartaipress_Admin. Defines all hooks for the admin area.
	 * - Smartaipress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The cron class.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin-cron.php';

		/**
		 * The OpenAI client class.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-openai-client.php';

		/**
		 * The OpenAI Usage class.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin-openai-usage.php';

		/**
		 * The OpenAI class.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin-openai.php';

		/**
		 * The class responsible for handling plugin menus.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin-menu.php';

		/**
		 * The class responsible for handling plugin metaboxes.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin-metabox.php';

		/**
		 * The helper class.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-helper.php';

		/**
		 * Require the main class for Smartaipress functions.
		 *
		 * This statement includes the primary class file responsible for the core functionality
		 * of the Smartaipress plugin. This class defines various methods and properties
		 * used throughout the plugin.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-functions.php';

		/**
		 * Require additional functions for Smartaipress.
		 *
		 * This statement includes a separate PHP file containing additional functions
		 * and features used by the Smartaipress plugin. These functions complement
		 * the core functionality provided by the main class.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/smartaipress-functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-admin.php';

		/**
		 * The class responsible for sending deactivation feedback data
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-deactivation-feedback.php';

		/**
		 * The class responsible for sending activation feedback data
		 */
		require_once SMARTAIPRESS_DIR . 'admin/class-smartaipress-activation-feedback.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SMARTAIPRESS_DIR . 'public/class-smartaipress-public.php';

		$this->loader = new Smartaipress_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smartaipress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smartaipress_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smartaipress_Admin( $this->get_plugin_name(), $this->get_version() );
		$admin_menu = Smartaipress_Admin_Menu::get_instance();
		$admin_metabox = new Smartaipress_Admin_Metabox();
		$cron = Smartaipress_Cron::get_instance();
		$openai = Smartaipress_Openai::get_instance();
		$openai_client = smartaipress('openai-client');
		$openai_usage = Smartaipress_Openai_Usage::get_instance();
		$deactivation_feedback = new Smartaipress_Deactivation_Feedback();
		$activation_feedback = new Smartaipress_Activation_Feedback();

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notifications' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'remove_admin_notifications_from_smartaipress_pages' );
		$this->loader->add_action( 'admin_menu', $admin_menu, 'register_admin_menu' );
		$this->loader->add_action( 'add_meta_boxes', $admin_metabox, 'register_metaboxes' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );
		$this->loader->add_action( 'cron_schedules', $cron, 'custom_schedules' );
		$this->loader->add_action( 'wp_ajax_smartaipress_get_openai_api_usage_data', $openai_client, 'fetch_api_usage_data' );
		$this->loader->add_action( 'smartaipress_get_api_usage_data', $openai_usage, 'fetch_openai_api_usage_data' );
		$this->loader->add_action( 'smartaipress_get_api_usage_data_for_today', $openai_usage, 'fetch_openai_api_usage_data_for_today' );
		$this->loader->add_action( 'wp_ajax_smartaipress_openai_send_prompt', $openai, 'generate_content' );
		$this->loader->add_action( 'wp_ajax_smartaipress_save_settings', $plugin_admin, 'save_settings' );
		$this->loader->add_action( 'wp_ajax_smartaipress_openai_generate_image', $openai, 'generate_image' );
		$this->loader->add_action( 'wp_ajax_smartaipress_openai_upload_and_set_featured_image', $openai, 'store_image' );
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'add_openai_trigger_button' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'admin_footer' );
		$this->loader->add_filter( 'wp_ajax_smartaipress_send_deactivation_data', $deactivation_feedback, 'send_deactivation_feedback_data');
		$this->loader->add_filter( 'wp_ajax_smartaipress_send_activation_data', $activation_feedback, 'send_activation_feedback_data');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Smartaipress_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Smartaipress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
