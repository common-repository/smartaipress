<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Smartaipress_Functions {

    private static $instance;

    /**
     * Get the Singleton instance of the class.
     *
     * @return Smartaipress_Functions The Singleton instance.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render a loader element.
     *
     * Outputs the HTML markup for a loader element that displays a loading animation.
     *
     * @since 1.0.0
     */
    public function loader($label) { ?>
        <div class="sap-loader-box">
            <div class="sap-loader"></div>
            <div class="sap-loader-text"><?php echo esc_html($label); ?></div>
        </div>
        <?php
    }

    /**
     * Get a list of supported languages for OpenAI API.
     *
     * This function returns an associative array of supported languages
     * where the keys are language codes and the values are human-readable language names.
     * 
     * @since 1.0.0
     *
     * @return array An associative array of supported languages.
     */
    public function get_openai_languages() {
        $supportedLanguages = [
            "ar-AE" => "Arabic",
            "az-AZ" => "Azerbaijani (Azerbaijan)",
            "cmn-CN" => "Chinese (Mandarin)",
            "hr-HR" => "Croatian (Croatia)",
            "cs-CZ" => "Czech (Czech Republic)",
            "da-DK" => "Danish (Denmark)",
            "nl-NL" => "Dutch (Netherlands)",
            "en-US" => "English (USA)",
            "en-GB" => "English (UK)",
            "et-EE" => "Estonian (Estonia)",
            "fi-FI" => "Finnish (Finland)",
            "fr-FR" => "French (France)",
            "de-DE" => "German (Germany)",
            "el-GR" => "Greek (Greece)",
            "he-IL" => "Hebrew (Israel)",
            "hi-IN" => "Hindi (India)",
            "hu-HU" => "Hungarian (Hungary)",
            "is-IS" => "Icelandic (Iceland)",
            "id-ID" => "Indonesian (Indonesia)",
            "it-IT" => "Italian (Italy)",
            "ja-JP" => "Japanese (Japan)",
            "kk-KZ" => "Kazakh (Kazakhstan)",
            "ko-KR" => "Korean (South Korea)",
            "lt-LT" => "Lithuanian (Lithuania)",
            "ms-MY" => "Malay (Malaysia)",
            "nb-NO" => "Norwegian (Norway)",
            "pl-PL" => "Polish (Poland)",
            "pt-BR" => "Portuguese (Brazil)",
            "pt-PT" => "Portuguese (Portugal)",
            "ro-RO" => "Romanian (Romania)",
            "ru-RU" => "Russian (Russia)",
            "sl-SI" => "Slovenian (Slovenia)",
            "sk-SK" => "Slovak (Slovenský)",
            "rs-RS" => "Serbian (Srpski)",
            "es-ES" => "Spanish (Spain)",
            "sw-KE" => "Swahili (Kenya)",
            "sv-SE" => "Swedish (Sweden)",
            "tr-TR" => "Turkish (Turkey)",
            "vi-VN" => "Vietnamese (Vietnam)"
        ];

        return $supportedLanguages;
    }

    /**
     * Retrieve a list of available OpenAI language models.
     *
     * This function returns a list of OpenAI language models that can be used for various natural language processing tasks.
     *
     * @since 1.0.0
     * 
     * @return array An array of available OpenAI language models, including their names, capabilities, and details.
     */
    public function get_openai_models() {
        $supportedModels = [
            'gpt-3.5-turbo-instruct' => 'GPT 3.5 Turbo Instruct',
            'gpt-3.5-turbo' => 'GPT 3.5 Turbo',
            'gpt-3.5-turbo-16k' => 'GPT 3.5 Turbo 16K',
            'gpt-4' => 'GPT 4',
            'gpt-4-1106-preview' => 'GPT 4 Turbo'
        ];

        return $supportedModels;
    }

    /**
     * Check if the block editor (Gutenberg) is active on the current WordPress page.
     *
     * @since 1.0.0
     * 
     * @return bool True if the block editor is active, false otherwise.
     */
    public function is_block_editor() {
        // Get the current screen object.
        $current_screen = get_current_screen();

        // Check if the screen object has a method is_block_editor() and it returns true.
        return method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor();
    }

    /**
     * Retrieve settings from the SmartAIPress plugin.
     *
     * This function allows you to retrieve specific settings or the entire array
     * of default values from the plugin's settings. If a specific key is provided,
     * it checks if the key exists in the stored settings and returns its value.
     * If a default value is provided and the key is not found, it returns the
     * provided default value. If no key is provided and no default value is set,
     * it returns the entire array of default values.
     *
     * @param string|null $key           The specific setting key to retrieve.
     * @param mixed|null  $default_value The default value to return if the key is not found.
     *
     * @return mixed|array The requested setting value, default value, or an array of default values.
     */
    public function get_settings($key = null, $default_value = null) {
        $serialized_settings = get_option('smartaipress_settings');
        $unserialized_settings = is_string($serialized_settings) ? unserialize($serialized_settings) : [];
    
        $default_values = [
            'openai_api_key' => $unserialized_settings['openai_api_key'] ?? '',
            'openai_default_model' => $unserialized_settings['openai_default_model'] ?? 'gpt-3.5-turbo',
            'openai_default_language' => $unserialized_settings['openai_default_language'] ?? 'English (USA)',
            'openai_default_tone_of_voice' => $unserialized_settings['openai_default_tone_of_voice'] ?? 'professional',
            'openai_default_creativity' => $unserialized_settings['openai_default_creativity'] ?? '0.75',
            'openai_max_input_length' => $unserialized_settings['openai_max_input_length'] ?? '1000',
            'openai_max_output_length' => $unserialized_settings['openai_max_output_length'] ?? '1000',
            'openai_response_log' => $unserialized_settings['openai_response_log'] ?? false,
        ];
    
        if ($key !== null) {
            if (!empty($unserialized_settings[$key])) {
                return $unserialized_settings[$key];
            } else {
                if ($default_value !== null) {
                    return $default_value;
                } elseif (array_key_exists($key, $default_values)) {
                    return $default_values[$key];
                }
            }
        }
    
        // If no key is provided and default_value is not set, return the entire default_values array.
        return $default_values;
    }

    /**
     * Logs error messages with a timestamp and, optionally, a specified filename.
     *
     * This function is designed to log error messages in a structured format, along with a timestamp and the caller's filename.
     * It can be used to track and record error messages for debugging purposes. The caller's filename is identified, the path and
     * extension are removed, and the message is appended to a log file in the specified directory. If a custom filename is provided,
     * that filename will be used for the log entry; otherwise, the caller's filename will be used.
     *
     * @param mixed $message The error message to be logged. It can be a string or an array and will be appropriately formatted.
     * @param string|null $filename The optional filename to be used for the log entry. If not provided, the caller's filename is used.
     *
     * @throws Exception if there is an error while writing to the log file.
     */
    public function log_error($message, $filename = null) {
        try {
            if (is_array($message)) {
                // If $message is an array, convert it to a formatted string
                $message = print_r($message, true);
            } else if (!is_string($message)) {
                // If $message is not a string or an array, convert it to a string
                $message = var_export($message, true);
            }

            $log_entry = "[" . gmdate('Y-m-d H:i:s') . "] " . $message . PHP_EOL;

            if ($filename === null) {
                // Use debug_backtrace to get the filename of the caller if no custom filename is provided
                $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                $filename = $caller[0]['file'];

                // Extract the filename without path and extension
                $file_info = pathinfo($filename);
                $filename = $file_info['filename'];
            }

            $log_file = SMARTAIPRESS_LOG_DIR . $filename . '.txt';

            if (file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX) === false) { // phpcs:ignore
                throw new Exception('Error writing to the log file');
            }

            // Set file permissions after writing
            if (file_exists($log_file)) {
                chmod($log_file, 0644); // phpcs:ignore
            }
        } catch (Exception $e) {
            // Handle the error. You can log the error to another file or take other appropriate actions.
            error_log('Error in log_error function: ' . $e->getMessage());
        }
    }

}