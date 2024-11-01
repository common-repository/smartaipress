<?php

/**
 * SmartAIPress
 *
 * @link              https://smartaipress.com
 * @since             1.0.0
 * @package           Smartaipress
 *
 * @wordpress-plugin
 * Plugin Name:       SmartAIPress
 * Plugin URI:        https://smartaipress.com/smartaipress-for-wordpress/
 * Description:       SmartAIPress: Unleash the Power of AI to Revolutionize Your Content Creation. Effortlessly generate high-quality articles, blog posts, and web content with our intelligent AI-driven plugin. Say goodbye to writer's block and hello to a world of creativity and efficiency.
 * Version:           1.0.41
 * Requires at least: 6.2
 * Requires PHP:      7.0
 * Author:            SmartAIPress <contact@smartaipress.com>
 * Author URI:        https://smartaipress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smartaipress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Current plugin version.
if( ! defined( 'SMARTAIPRESS_VERSION' ) ) {
	define( 'SMARTAIPRESS_VERSION', '1.0.41' );
}

// Define the debug to start logging. 
if( ! defined( 'SMARTAIPRESS_DEBUG' ) ) {
	define( 'SMARTAIPRESS_DEBUG', false );
}

// Define the file path of the Smartaipress plugin's main file.
if ( ! defined( 'SMARTAIPRESS_FILE' ) ) {
	define( 'SMARTAIPRESS_FILE', __FILE__ );
}

// Define the directory path of the Smartaipress plugin.
if( ! defined( 'SMARTAIPRESS_DIR' ) ) {
	define( 'SMARTAIPRESS_DIR', plugin_dir_path( __FILE__ ) );
}

// Define the URL path of the Smartaipress plugin.
if( ! defined( 'SMARTAIPRESS_URL' ) ) {
	define( 'SMARTAIPRESS_URL', plugin_dir_url( __FILE__ ) );
}

// Define the URL path of the plugin admin assets.
if( ! defined( 'SMARTAIPRESS_ADMIN_ASSETS_URL' ) ) {
	define( 'SMARTAIPRESS_ADMIN_ASSETS_URL', SMARTAIPRESS_URL . 'admin/assets/' );
}

// Define the logs directory path.
if( ! defined( 'SMARTAIPRESS_LOG_DIR' ) ) {
	define('SMARTAIPRESS_LOG_DIR', SMARTAIPRESS_DIR . 'logs/');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smartaipress-activator.php
 */
function smartaipress_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smartaipress-activator.php';
	Smartaipress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smartaipress-deactivator.php
 */
function smartaipress_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smartaipress-deactivator.php';
	Smartaipress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'smartaipress_activate' );
register_deactivation_hook( __FILE__, 'smartaipress_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smartaipress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function smartaipress_run() {

	$plugin = new Smartaipress();
	$plugin->run();

}
smartaipress_run();
