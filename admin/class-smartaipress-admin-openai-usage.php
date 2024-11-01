<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SmartAIPress OpenAI Usage Class
 *
 * This class encapsulates functionality related to retrieving and managing OpenAI API usage data.
 * It provides methods to collect usage data, filter data for specific time periods, and facilitate
 * data retrieval for various parts of the SmartAIPress application.
 *
 * @package SmartAIPress
 * @subpackage OpenAI
 * @since 1.0.0
 */
class Smartaipress_Openai_Usage {

    /**
     * Singleton instance variable for the Smartaipress_Openai_Usage class.
     *
     * This variable holds the single instance of the Smartaipress_Openai_Usage class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Openai_Usage|null The single instance of Smartaipress_Openai_Usage.
     */
    private static $instance;

    /**
     * Get a single instance of the Smartaipress_Openai_Usage class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Openai_Usage class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Openai_Usage The single instance of Smartaipress_Openai_Usage.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retrieve OpenAI API Usage Data
     *
     * This function queries the database to retrieve OpenAI API usage data based on the specified conditions.
     * It allows filtering the data with a WHERE clause, and returns an array of results containing day fragments
     * and corresponding usage data. The data is retrieved from the designated database table.
     *
     * @return array An array of database results with day fragments and usage data.
     */
    public function get_openai_api_usage_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . "smartaipress_openai_api_usage";
        $results = $wpdb->get_results($wpdb->prepare("SELECT day_fragment, usage_data FROM %i", $table_name)); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
        return $results;
    }

    /**
     * Retrieve OpenAI API Usage Data with filter
     *
     * This function queries the database to retrieve OpenAI API usage data based on the specified conditions.
     * It allows filtering the data with a WHERE clause, and returns an array of results containing day fragments
     * and corresponding usage data. The data is retrieved from the designated database table.
     *
     * @param string $day Current day
     * @return array An array of database results with day fragments and usage data.
     */
    public function get_openai_api_usage_data_with_filter($day) {
        global $wpdb;
        $table_name = $wpdb->prefix . "smartaipress_openai_api_usage";
        $results = $wpdb->get_results($wpdb->prepare("SELECT day_fragment, usage_data FROM %i WHERE day_fragment = %s", $table_name, $day)); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedPlaceholder, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
        return $results;
    }

	/**
     * Collect and store OpenAI API Usage Data
     *
     * This function is responsible for collecting and storing OpenAI API usage data in the database.
     * It checks for the availability of the API key and ensures that data is collected only for
     * days not previously recorded. The function collects usage data for up to five days, updating
     * the database with the collected information for each day.
     *
     * @return void This function returns early if conditions for data collection are not met.
     */
	public function fetch_openai_api_usage_data() {
        if (!smartaipress()->get_settings('openai_api_key')) {
            return;
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . "smartaipress_openai_api_usage";

        $all_days = smartaipress('helper')->get_days_between_two_dates();

        $openai_api_usage_data = $this->get_openai_api_usage_data();

        $stored_days = [];

        foreach($openai_api_usage_data as $key => $record) {
            $stored_days[] = $record->day_fragment;    
        }

        $days = [];

        foreach($all_days as $key => $day) {
            if(!in_array($day, $stored_days)) {
                $days[] = $day;
            }
            if(count($days) === 5) {
                break;
            }
        }

        if(empty($days)) {
            return;
        }
    
        foreach ($days as $day) {
            $data = smartaipress('openai-client')->fetch_api_usage_for_day($day);
    
            if (isset(json_decode($data)->error)) {
                return;
            }
    
            $stored = $wpdb->insert(
                $table_name, 
                array(
                    'day_fragment' => $day,
                    'usage_data' => $data
                )
            );
        }
    }   

    /**
     * Collect and Update OpenAI API Usage Data for Today
     *
     * This function is responsible for collecting and updating OpenAI API usage data for the current day.
     * It ensures that the API key is set and retrieves data for the specified day. If the data is successfully
     * collected, it checks whether usage data for the day already exists and updates it if applicable.
     *
     * @return void This function returns early if conditions for data collection are not met.
     */
	public function fetch_openai_api_usage_data_for_today() {
        if (!smartaipress()->get_settings('openai_api_key')) {
            return;
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . "smartaipress_openai_api_usage";
        $day = gmdate("Y-m-d");
        $data = smartaipress('openai-client')->fetch_api_usage_for_day($day);
    
        if (!isset(json_decode($data)->error)) {
            $openai_api_usage_data = $this->get_openai_api_usage_data_with_filter($day);
    
            if ($openai_api_usage_data) {
                $updated = $wpdb->update(
                    $table_name, 
                    array('usage_data' => $data, 'updated_at' => wp_date('Y-m-d H:i:sa')), 
                    array('day_fragment' => $day)
                );
            }
        }
    }

    /**
     * Retrieve OpenAI API Usage Data
     *
     * This function queries the database to retrieve OpenAI API usage data
     * Fetch api usage data for last five days 
     * @param string $limit Total records to retrieve from db
     * @param string $from Starting day in YYYY-MM-DD format
     * @param string $to Ending day in YYYY-MM-DD format
     *
     * @return array An array of database results with day fragments and usage data.
     */
    public function get_api_usage_details($limit, $from, $to) {
        global $wpdb;
    
        $table_name = $wpdb->prefix . "smartaipress_openai_api_usage";
    
        $where_clause = '';
    
        if ($from && $to) {
            $where_clause = $wpdb->prepare("WHERE day_fragment BETWEEN %s AND %s", $from, $to);
        }
    
        $limit_clause = $limit ? $wpdb->prepare("LIMIT %d", $limit) : '';
    
        $results = $wpdb->get_results(
            $wpdb->prepare("
                SELECT day_fragment, usage_data
                FROM (
                    SELECT day_fragment, usage_data
                    FROM %i
                    {$where_clause}
                    ORDER BY day_fragment DESC
                    {$limit_clause}
                ) AS records
                ORDER BY day_fragment ASC;
            ", $table_name)
        ); // phpcs:ignore
    
        return $results;
    }
    
}
