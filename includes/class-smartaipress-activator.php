<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This class defines all the necessary functionality to be executed during plugin activation.
 *
 * @since 1.0.0
 * @package Smartaipress
 * @subpackage Smartaipress/includes
 */
class Smartaipress_Activator {

    /**
     * The table name for OpenAI responses.
     *
     * @var string
     */
    private static $openai_responses_table;

    /**
     * The table name for OpenAI API usage data.
     *
     * @var string
     */
    private static $openai_api_usage_table;

    /**
     * Initialize table names.
     *
     * @since 1.0.0
     */
    public static function init() {
        global $wpdb;
        self::$openai_responses_table = $wpdb->prefix . "smartaipress_openai_responses";
        self::$openai_api_usage_table = $wpdb->prefix . "smartaipress_openai_api_usage";
    }

    /**
     * Deactivate the SmartAIPress Pro Plugin
     *
     * This static function checks if the SmartAIPress Pro plugin is currently active and, if so,
     * deactivates it programmatically. This action is intended to provide a seamless and controlled
     * process for managing plugin deactivation when transitioning to SmartAIPress Free.
     *
     * @since 1.0.0
     * @access private
     */
    private static function deactivate_smartaipress_pro() {
        // Check if the 'is_plugin_active' function exists; if not, include the necessary file.
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // Check if the SmartAIPress Free plugin is active and deactivate it.
        if (is_plugin_active('smartaipress-pro/smartaipress-pro.php')) {
            deactivate_plugins('smartaipress-pro/smartaipress-pro.php');
        }
    }

    /**
     * Activate the plugin and perform necessary setup tasks.
     *
     * @since 1.0.0
     */
    public static function activate() {
        self::deactivate_smartaipress_pro();
        self::init();
        self::schedule_api_usage_events();
        self::create_table(self::$openai_responses_table, self::get_openai_responses_schema());
        self::create_table(self::$openai_api_usage_table, self::get_openai_api_usage_schema());
        self::create_log_directory();
    }

    /**
     * Schedule recurring events for fetching API usage data.
     *
     * @since 1.0.0
     */
    private static function schedule_api_usage_events() {
        if (!wp_next_scheduled('smartaipress_get_api_usage_data')) {
            wp_schedule_event(time(), '5minutes', 'smartaipress_get_api_usage_data');
        }
        if (!wp_next_scheduled('smartaipress_get_api_usage_data_for_today')) {
            wp_schedule_event(time(), '10minutes', 'smartaipress_get_api_usage_data_for_today');
        }
    }

    /**
     * Create or check the existence of a custom database table.
     *
     * @param string $table_name  The name of the table to be created or checked.
     * @param string $schema      The schema for the table.
     *
     * @since 1.0.0
     */
    private static function create_table($table_name, $schema) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name ($schema) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Define the schema for OpenAI responses table.
     *
     * @return string
     */
    private static function get_openai_responses_schema() {
        return "
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            prompt LONGTEXT NOT NULL,
			model VARCHAR(50)NOT NULL,
			response LONGTEXT NULL,
			post_type VARCHAR(50) NULL,
            resolution VARCHAR(10) NULL,
            status VARCHAR(10) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ";
    }

    /**
     * Define the schema for OpenAI API usage table.
     *
     * @return string
     */
    private static function get_openai_api_usage_schema() {
        return "
            id INT AUTO_INCREMENT PRIMARY KEY,
            day_fragment VARCHAR(20) NOT NULL,
            usage_data LONGTEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ";
    }

    /**
     * Create the log directory and .htaccess file for SmartAIpress plugin.
     *
     * This function is responsible for ensuring that the log directory exists and
     * creating an .htaccess file to deny web access for security purposes.
     *
     * @since 1.0.0
     *
     * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem object.
     */
    private static function create_log_directory() {
        global $wp_filesystem;

        // Get the filesystem method
        WP_Filesystem();
    
        // Check if the log directory exists, and create it if not
        if (!$wp_filesystem->is_dir(SMARTAIPRESS_LOG_DIR)) {
            $wp_filesystem->mkdir(SMARTAIPRESS_LOG_DIR, 0755);
        }
    
        // Create an .htaccess file to deny web access
        $htaccess_file = SMARTAIPRESS_LOG_DIR . '.htaccess';
        if (!$wp_filesystem->exists($htaccess_file)) {
            $wp_filesystem->put_contents($htaccess_file, "Deny from all\n");
        }
    }
}
