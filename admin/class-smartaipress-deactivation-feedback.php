<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SmartAIPress Deactivation Feedback
 *
 * This class encapsulates functionality related to sending deactivation feedback data.
 *
 * @package SmartAIPress
 * @subpackage OpenAI
 * @since 1.0.2
 */
class Smartaipress_Deactivation_Feedback {

    /**
	 * Send deactivation feedback data and deactivate SmartAIPress plugin
	 *
	 * @since    1.0.2
	 * @access   public
     * @return string Mail sent message
	 */
    public function send_deactivation_feedback_data() {
        $nonce = isset($_POST['nonce']) ? esc_html(sanitize_text_field($_POST['nonce'])) : ''; // phpcs:ignore
        
        if ( ! wp_verify_nonce( $nonce, 'smartaipress_feedback_nonce' ) ) {
            wp_send_json_error(
                [ 
                    'error' => [
                        'code' => 'invalid_nonce',
                        'label' => esc_html__( 'Invalid nonce!', 'smartaipress' ),
                        'message' => esc_html__( 'The nonce verification failed.', 'smartaipress' )
                    ]
                ]
            );
        }

        if(!get_option('smartaipress_deactivate_data_sent')) {
            update_option('smartaipress_deactivate_data_sent', 'yes');
            
            $reason = esc_html(sanitize_text_field($_POST["reason"]));
            $message = esc_html(sanitize_text_field($_POST["message"]));

            $final_reason = "";
            $final_message = "";
            $site_url = get_bloginfo('url');

            if($reason == "no-option-selected") {
                $final_reason = "No option selected";
            } elseif($reason == "no-money-for-api-key") {
                $final_reason = "I dont have money for OpenAI API key";
            } elseif($reason == "found-better-plugin") {
                $final_reason = "I have found a better plugin for my needs";
            } elseif($reason == "i-dont-like-it") {
                $final_reason = "I dont like this plugin";
            } elseif($reason == "plugin-needs-improvement") {
                $final_reason = "This plugin needs improvement";
            } elseif($reason == "other") {
                $final_reason = "Other";
            }

            $to = 'feedback@smartaipress.com';
            $subject = "SmartAIPress plugin deactivated on $site_url";
            $final_message = "Reason: $final_reason\nMessage: $message";

            $mail_sent = wp_mail($to, $subject, $final_message);

            deactivate_plugins(SMARTAIPRESS_DIR . 'smartaipress.php');

            if($mail_sent) {
                wp_send_json_success('Mail sent');
            } else {
                wp_send_json_error('Mail not sent');
            }
        }
    }

}