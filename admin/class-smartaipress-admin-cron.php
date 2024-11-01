<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SmartAIPress Cron Class
 *
 * The SmartAIPress Cron class is responsible for managing scheduled tasks and cron jobs
 * within the SmartAIPress application. It provides a structured way to schedule and execute
 * periodic tasks, such as data updates, cleanups, and other automated processes.
 *
 * @package SmartAIPress
 * @subpackage Cron
 * @since 1.0.0
 */
class Smartaipress_Cron {

    /**
     * Singleton instance variable for the Smartaipress_Cron class.
     *
     * This variable holds the single instance of the Smartaipress_Cron class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Cron|null The single instance of Smartaipress_Cron.
     */
    private static $instance;

    /**
     * Get a single instance of the Smartaipress_Cron class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Cron class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Cron The single instance of Smartaipress_Cron.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Define Custom Cron Schedules
     *
     * This function defines custom cron schedules for periodic tasks within the SmartAIPress application.
     *
     * @param array $schedules An array of existing cron schedules.
     * @return array An updated array of cron schedules, including custom intervals.
     */
    public function custom_schedules($schedules) {
        $custom_intervals = array(
            '1minute' => esc_html__('Every minute', 'smartaipress'),
            '5minutes' => esc_html__('Once every 5 minutes', 'smartaipress'),
            '10minutes' => esc_html__('Once every 10 minutes', 'smartaipress'),
            'halfhour' => esc_html__('Once every 30 minutes', 'smartaipress'),
        );

        foreach ($custom_intervals as $interval => $display) {
            if (!isset($schedules[$interval])) {
                $schedules[$interval] = array(
                    'interval' => $this->get_custom_interval($interval),
                    'display' => $display,
                );
            }
        }

        return $schedules;
    }

    /**
     * Get the custom interval in seconds based on the provided key.
     *
     * @param string $key The custom interval key.
     * @return int The interval in seconds.
     */
    private function get_custom_interval($key) {
        $intervals = array(
            '1minute' => 60,
            '5minutes' => 5 * 60,
            '10minutes' => 10 * 60,
            'halfhour' => 30 * 60,
        );

        return $intervals[$key] ?? 0; // Default to 0 if the key is not found.
    }

}
