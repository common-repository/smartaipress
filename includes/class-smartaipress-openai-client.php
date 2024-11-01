<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Smartaipress OpenAI Client Class.
 *
 * This class represents the Smartaipress OpenAI Client and is responsible for managing interactions
 * with the OpenAI API, including handling API key, making API requests, and processing responses.
 *
 * @package Smartaipress
 */
class Smartaipress_Openai_Client {

    /**
     * Singleton instance variable for the Smartaipress_Openai_Client class.
     *
     * This variable holds the single instance of the Smartaipress_Openai_Client class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Openai_Client|null The single instance of Smartaipress_Openai_Client.
     */
    private static $instance;

    /**
     * The API key for authenticating with the OpenAI service.
     *
     * This variable holds the API key used for authentication when making requests to the OpenAI service.
     * It should be kept secure and not exposed in public code repositories.
     *
     * @var string|null The API key for OpenAI authentication, or null if not set.
     */
    private $api_key;

    /**
     * URLs for different API endpoints used by the OpenAI service.
     *
     * This variable holds an associative array of URLs for various API endpoints used for interactions
     * with the OpenAI service. Each key corresponds to a specific endpoint, such as 'complete' for
     * text completions, 'chat' for chat-based completions, and 'edit' for editing content.
     *
     * @var array The array of API endpoint URLs.
     */
    private $api_url = [
        'complete' => 'https://api.openai.com/v1/completions',
        'chat' => 'https://api.openai.com/v1/chat/completions',
        'edit' => 'https://api.openai.com/v1/edits',
        'image' => 'https://api.openai.com/v1/images/generations',
        'usage' => 'https://api.openai.com/v1/usage'
    ];

    /**
     * Configuration for maximum token limits for different OpenAI models.
     *
     * This associative array maps each supported OpenAI model to its respective
     * maximum token limit, which is crucial for controlling text generation length.
     *
     * @var array
     * @since 1.0.0
     */
    private $max_tokens = [
        'gpt-3.5-turbo-instruct' => 4000,
        'gpt-3.5-turbo' => 4000,
        'gpt-3.5-turbo-16k' => 16000,
        'gpt-4' => 8000,
        'gpt-4-1106-preview' => 4000
    ];

    /**
     * Get a single instance of the Smartaipress_Openai_Client class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Openai_Client class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Openai_Client The single instance of Smartaipress_Openai_Client.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retrieve the API key from application settings.
     *
     * This private method retrieves the API key from the application settings, specifically from
     * the 'smartaipress_settings' option. It assumes that the API key is stored within the settings
     * and sets it as the 'api_key' property for use in the class.
     *
     * @return string|null The retrieved API key or null if not found in settings.
     */
    private function api_key() {
        $this->api_key = smartaipress()->get_settings('openai_api_key');
        if( !empty($this->api_key) ) {
            return $this->api_key;
        }
    }

    /**
     * Set the appropriate API URL based on the selected model.
     *
     * This private method determines the appropriate API URL to use based on the specified model.
     * It is designed to support different models and map them to their respective API endpoints.
     *
     * @param string $model The selected model for content generation.
     *
     * @return string|null The API URL associated with the selected model, or null if no matching model is found.
     */
    private function set_prompt_url($model) {
        switch ($model) {
            case "gpt-3.5-turbo-instruct":
                return $this->api_url['complete'];
                break;
            case "gpt-3.5-turbo":
            case "gpt-3.5-turbo-16k":
            case "gpt-4":
            case "gpt-4-1106-preview":
                return $this->api_url['chat'];
                break;
            case "dalle":
                return $this->api_url['image'];
                break;
        }
    }

    /**
     * Set the POST fields for an API request based on the selected model and prompt.
     *
     * This private method constructs the POST fields for an API request, tailoring them to the specific
     * model and input prompt. It ensures that the appropriate data structure is used for the request payload.
     *
     * @param string $model The selected model for content generation.
     * @param string $prompt The text prompt used for content generation.
     *
     * @return string JSON-encoded POST fields for the API request.
     */
    private function set_post_fields($model, $prompt) {
        switch ($model) {
            case "gpt-3.5-turbo-instruct":
                $fields = [
                    'model' => $model,
                    'prompt' => $prompt,
                    'max_tokens' => $this->max_tokens[$model],
                ];
                break;
            case "gpt-3.5-turbo":
            case "gpt-3.5-turbo-16k":
            case "gpt-4":
            case "gpt-4-1106-preview":
                $fields = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => $this->max_tokens[$model],
                ];
                break;
            case "dalle":
                $fields = [
                    'prompt' => $prompt['image'],
                    'n' => $prompt['total'],
                    'size' => $prompt['resolution'],
                    'response_format' => 'url'
                ];
                break;
        }

        return wp_json_encode($fields);
    }

    /**
     * Generate content based on the provided prompt and model.
     *
     * This method generates content using the specified prompt and model, leveraging the OpenAI service.
     * The resulting content will be based on the combination of the input prompt and model, which can be
     * useful for various natural language generation tasks.
     *
     * @param string $prompt    The text prompt used to generate content.
     * @param string $model     The selected model for content generation.
     * @param string $post_type The post type associated with the prompt, if applicable.
     *
     * @return string The generated content based on the provided prompt and model.
     */
    public function prompt($prompt, $model, $post_type) {
        $user_id = get_current_user_id();
        $api_url = $this->set_prompt_url($model);
        $resolution = "";
        $status = "error";

        if(!empty($prompt['resolution'])) {
            $resolution = $prompt['resolution'];
        }
    
        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key(),
            'Content-Type'  => 'application/json',
        );
    
        $body = $this->set_post_fields($model, $prompt);

        if ("dalle" === $model) {
            $prompt = $prompt['image'] ?? '';
        }

        $row_id = $this->insert_prompt_in_db($user_id, $prompt, $model, $post_type, $resolution, $status);
    
        // Set up the request arguments
        $args = array(
            'headers' => $headers,
            'body'    => $body,
            'timeout' => 300, // 5 minutes
        );
    
        // Make the request using wp_remote_post
        $response = wp_remote_post($api_url, $args);
    
        // Check for errors
        if (is_wp_error($response)) {
            $error = array(
                'error' => array(
                    'message' => 'HTTP error: ' . $response->get_error_message(),
                ),
            );
            wp_send_json_error($error);
        }
    
        $http_status = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
    
        // Log the whole response before errors catch or return.
        if (smartaipress()->get_settings('openai_response_log', false)) {
            smartaipress()->log_error($response_body, 'openai_response');
        }

        $prompt_error = json_decode($response_body, true);
        if (!empty($prompt_error['error'])) {
            $error = array(
                'error' => array(
                    'label' => $prompt_error['error']['type'] ?? '',
                    'message' => $prompt_error['error']['message'] ?? '',
                ),
            );
            $this->update_prompt_in_db($row_id, $response_body, $status);
            wp_send_json_error($error);
        }

        if ($http_status !== 200) {
            $error = array(
                'error' => array(
                    'message' => 'HTTP error: ' . $http_status,
                ),
            );
            $this->update_prompt_in_db($row_id, $response_body, $status);
            wp_send_json_error($error);
        }
    
        $status = "success";
        $this->update_prompt_in_db($row_id, $response_body, $status);
    
        $decoded_response = json_decode($response_body);
    
        if ($decoded_response === null) {
            $error = array(
                'error' => array(
                    'message' => 'JSON decoding error.',
                ),
            );
            wp_send_json_error($error);
        }
    
        return $decoded_response;
    }

    /**
     * Collects usage data for a specific day from the OpenAI API.
     *
     * This function initializes a cURL session, makes an API request to retrieve usage data
     * for the specified day, and returns the response.
     *
     * @param string $day The date for which usage data is requested (in 'YYYY-MM-DD' format).
     * @return string|object The API response as a JSON string or an error object.
     * 
     * @new function name: fetch_api_usage_for_day
     */
    public function fetch_api_usage_for_day($day) {
        // Set the URL you want to retrieve data from
        $url = $this->api_url["usage"] . "?date=$day";

        // Set custom headers
        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key(),
            'Content-Type' => 'application/json',
        );

        $args = array(
            'headers' => $headers
        );

        // Make the request using wp_remote_get
        $response = wp_remote_get($url, $args);

        // Check for errors
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $response_data = array(
                'error'   => true,
                'day'     => $day,
                'message' => $error_message,
            );
            echo wp_json_encode($response_data);
            die;
        }

        // Get the body of the response
        $body = wp_remote_retrieve_body($response);

        return $body;
    }

    /**
     * Get OpenAI API Usage Data for the Last Five Days
     *
     * This function retrieves OpenAI API usage data for the last five days and returns it in JSON format.
     * It calculates the date range based on the current month and retrieves the corresponding data
     * from the database table. If data is found, it's transformed into a structured JSON response.
     *
     * @since 1.0.0
     */
    public function fetch_api_usage_data() {
        // Check AJAX Referer and exit if it's invalid
        check_ajax_referer('api_usage_data');

        $limit = esc_html(sanitize_text_field($_POST["limit"]));
        $from = esc_html(sanitize_text_field($_POST["from"]));
        $to = esc_html(sanitize_text_field($_POST["to"]));
    
        // Fetch API Usage Data
        $openai_api_usage_data = smartaipress('openai-usage')->get_api_usage_details($limit, $from, $to);
        
        if ($openai_api_usage_data) {
            // Process data and send the response
            $usage_data = array_map(function ($record) {
                return ['dayFragment' => $record->day_fragment, 'dataFragments' => $record->usage_data];
            }, $openai_api_usage_data);
    
            $response = ['usage_content' => wp_json_encode($usage_data)];
        } else {
            // Handle the case where no data is found
            $response = ['error' => true, 'message' => esc_html__( 'No API usage data found.', 'smartaipress' )];
        }
    
        // Send the JSON response and terminate execution
        echo wp_json_encode($response);
        die;
    }

    /**
     * Inserts a new prompt into the smartaipress_openai_responses table.
     *
     * This function inserts a new prompt and its associated user ID into the smartaipress_openai_responses table, which stores OpenAI responses data.
     *
     * @param int    $user_id The user ID associated with the prompt.
     * @param string $prompt  The prompt text to be inserted.
     * @param string $model Openai API Model
     * @param string $post_type WordPress post type
     * @param string $resolution Dalle model image resolution
     * @param string $status DB Record status, two possible values: error or success
     *
     * @return bool|int The inserted row ID on success, false on failure.
     *
     * @since 1.0.0
     */
    private function insert_prompt_in_db($user_id, $prompt, $model, $post_type, $resolution, $status) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'smartaipress_openai_responses';

        $data_to_insert = array(
            'user_id' => $user_id,
            'prompt' => $prompt,
            'model' => $model,
            'post_type' => $post_type,
            'resolution' => $resolution,
            'status' => $status
        );

        $insert_result = $wpdb->insert($table_name, $data_to_insert);

        if ($wpdb->last_error) {
            return false; // An error occurred during insertion.
        }

        return $insert_result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Updates the response for a given prompt in the smartaipress_openai_responses table.
     *
     * This function updates the response for a specific prompt in the smartaipress_openai_responses table.
     *
     * @param int    $row_id   The response ID of the prompt to update.
     * @param string $response The new response text to be stored.
     * @param string $status DB Record status, two possible values: error or success
     *
     * @return bool True on successful update, false on failure.
     *
     * @since 1.0.0
     */
    private function update_prompt_in_db($row_id, $response, $status) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'smartaipress_openai_responses';

        $db_time = $wpdb->get_var("SELECT NOW()");

        // Define the data to update
        $data_to_update = array(
            'response' => $response,
            'status' => $status,
            'updated_at' => $db_time // Use current_time to get the current timestamp.
        );

        $where = array(
            'id' => $row_id,
        );

        return $wpdb->update($table_name, $data_to_update, $where) !== false;
    }

}
