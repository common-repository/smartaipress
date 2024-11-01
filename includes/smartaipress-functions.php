<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get an instance of the appropriate SmartAIPress service based on the specified object.
 *
 * This function returns an instance of either SmartAIPress_OpenAI_Client or SmartAIPress_Functions
 * depending on the provided $object parameter. The $object parameter specifies which service to retrieve.
 *
 * @param string|null $object The name of the service object to retrieve (e.g., 'openai-client'). Defaults to null.
 *
 * @return SmartAIPress_OpenAI_Client|SmartAIPress_Functions The requested service instance.
 * @since 1.0.0
 */
if (!function_exists('smartaipress')) {
    function smartaipress($object = null) {
        if ($object === 'openai-client') {
            return Smartaipress_OpenAI_Client::get_instance();
        } elseif ($object === 'openai-usage') {
            return Smartaipress_Openai_Usage::get_instance();
        } elseif ($object === 'helper') {
            return Smartaipress_Helper::get_instance();
        } else {
            return Smartaipress_Functions::get_instance();
        }
    }
}