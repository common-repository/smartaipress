<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

/**
 * Delete plugin options when uninstalling.
 */
$smartaipress_settings = 'smartaipress_settings';
delete_option($smartaipress_settings);
delete_option('smartaipress_activate_data_sent');
delete_option('smartaipress_deactivate_data_sent');

/**
 * Function to drop a database table.
 *
 * @param string $table_name The name of the table to drop.
 */
function smartaipress_drop_table($table_name) {
    global $wpdb;
    $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %i", $table_name)); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
}

// Drop the OpenAI responses table.
$openai_responses_table = $wpdb->prefix . "smartaipress_openai_responses";
smartaipress_drop_table($openai_responses_table);

// Drop the OpenAI API usage table.
$openai_api_usage_table = $wpdb->prefix . "smartaipress_openai_api_usage";
smartaipress_drop_table($openai_api_usage_table);
