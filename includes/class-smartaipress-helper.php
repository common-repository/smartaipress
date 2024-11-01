<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SmartAIPress Helper Class
 *
 * This helper class provides utility functions and methods to assist with various tasks
 * within the SmartAIPress application. It encapsulates functions that are used across different
 * parts of the application for improved code organization and reusability.
 *
 * @package SmartAIPress
 * @subpackage Helpers
 */
class Smartaipress_Helper {

    /**
     * Singleton instance variable for the Smartaipress_Helper class.
     *
     * This variable holds the single instance of the Smartaipress_Helper class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Helper|null The single instance of Smartaipress_Helper.
     */
    private static $instance;

    /**
     * Get a single instance of the Smartaipress_Helper class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Helper class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Helper The single instance of Smartaipress_Helper.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

     /**
     * Display the title corresponding to the current post type in a WordPress context.
     *
     * This function identifies the post type of the current screen and outputs the appropriate title
     * using internationalization for localization. It supports 'post', 'page', and 'product' post types
     * by default, displaying their respective titles. If the post type is not recognized, it gracefully
     * falls back to an empty string to ensure smooth user experience.
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function get_post_type_title() {
        $post_type = get_current_screen()->post_type;

        if ("post" === $post_type) {
            $title = esc_html__( 'Post Title', 'smartaipress' );
        } elseif ("page" === $post_type) {
            $title = esc_html__( 'Page Title', 'smartaipress' );
        } elseif ("product" === $post_type) {
            $title = esc_html__( 'Product Title', 'smartaipress' );
        }

        return $title ?? esc_html__( 'Post Title', 'smartaipress' );
    }

    /**
     * Retrieves the total count of OpenAI send requests stored in the custom WordPress table.
     *
     * This function queries the WordPress database using the global $wpdb object to determine the
     * number of records in the 'smartaipress_openai_responses' table. It is designed to be used
     * for analytics or monitoring purposes, providing insight into the volume of OpenAI API requests made.
     *
     * @since 1.0.0
     *
     * @return int The total count of OpenAI send requests in the custom table.
     */
    public function get_openai_send_requests_count() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'smartaipress_openai_responses';

        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $table_name)); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

        return $count ?? 0;
    }

    /**
     * Calculate the average time difference between 'created_at' and 'updated_at' columns
     * in the 'wp_smartaipress_openai_responses' table.
     *
     * @since 1.0.0
     *
     * @global wpdb $wpdb WordPress database object.
     *
     * @return string|false The average time difference in a human-readable format, or false on error.
     */
    public function get_average_openai_api_response_time() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'smartaipress_openai_responses';

        $average_time_diff = $wpdb->get_var(
            $wpdb->prepare("SELECT AVG(UNIX_TIMESTAMP(updated_at) - UNIX_TIMESTAMP(created_at)) AS avg_time_diff FROM %i", $table_name)
        ); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

        if ($average_time_diff !== null) {
            $average_time_diff = (int) $average_time_diff;

            // Format the average time difference into a human-readable format
            $average_time_diff_readable = gmdate("i:s", $average_time_diff);
            return $average_time_diff_readable;
        }

        return false;
    }

    /**
     * Get an array of days between two dates.
     * @param string $from Defines starting point
     * @param string $to Defines ending point
     * @param boolean $addToday Defines if today should be added to array, default is true
     * 
     * @return array An array of days.
     */
    public function get_days_between_two_dates($from = '2023-01-01', $to = '', $addToday = true) {
        if(empty($to)) {
            $to = gmdate('Y-m-d');
        }

        $period = new DatePeriod(
            new DateTime($from),
            new DateInterval('P1D'),
            new DateTime($to)
        );
        
        $days = [];
        
        foreach ($period as $key => $value) {
            $days[] = $value->format('Y-m-d');      
        }

        if($addToday) {
            $days[] = gmdate('Y-m-d');
        }

        return $days;
    }
}
