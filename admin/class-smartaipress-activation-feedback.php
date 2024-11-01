<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SmartAIPress Activation Feedback
 *
 * This class encapsulates functionality related to sending activation feedback data.
 *
 * @package SmartAIPress
 * @subpackage OpenAI
 * @since 1.0.3
 */
class Smartaipress_Activation_Feedback {

    /**
	 * Send activation feedback data and activate SmartAIPress plugin
	 *
	 * @since    1.0.3
	 * @access   public
     * @return string Mail sent message
	 */
    public function send_activation_feedback_data() {
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

        if(!get_option('smartaipress_activate_data_sent')) {
            update_option('smartaipress_activate_data_sent', 'yes');

            $site_url = get_bloginfo('url');

            $to = 'feedback@smartaipress.com';
            $subject = "SmartAIPress plugin activated on $site_url";
            $message = "Activation data";

            $mail_sent = wp_mail($to, $subject, $message);

            if($mail_sent) {
                wp_send_json_success('Mail sent');
            } else {
                wp_send_json_error('Mail not sent');
            }
        }
    }

}