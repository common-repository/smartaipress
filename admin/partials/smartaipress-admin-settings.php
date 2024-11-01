<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display admin panel settings page.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin/partials
 */

require_once  plugin_dir_path( dirname( __FILE__ ) ) . '/partials/smartaipress-admin-header.php';

$settings = smartaipress()->get_settings();

$api_secret = $settings['openai_api_key'] ?? '';
$default_model = $settings['openai_default_model'] ?? 'gpt-3.5-turbo';
$default_language = $settings['openai_default_language'] ?? 'en-US';
$default_tone_of_voice = $settings['openai_default_tone_of_voice'] ?? 'professional';
$default_creativity = $settings['openai_default_creativity'] ?? '0.75'; 
$max_input_length = $settings['openai_max_input_length'] ?? '1000';
$max_output_length = $settings['openai_max_output_length'] ?? '1000';
$response_log = $settings['openai_response_log'] ?? false; ?>

<div class="sap-container-fluid">
    <div class="sap-card">
        <div class="sap-card-header">
            <h5><?php esc_html_e( 'Settings', 'smartaipress' ); ?></h5>
            <p><?php esc_html_e( 'Here you can configure the plugin per own requirements.', 'smartaipress' ); ?></p>
        </div>
        <div class="sap-card-body">
            <?php smartaipress()->loader(esc_html__('Saving...', 'smartaipress')); ?>
            <?php wp_nonce_field('smartaipress_data_nonce', 'smartaipress_nonce'); ?>
            <table id="smartaipress-settings-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="openai-api-secret">
                                <?php esc_html_e( 'Openai API Key', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Enter your openai API secret key here.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <input type="password" name="settings[openai_api_key]" class="sap-form-control width-50" id="openai-api-secret" value="<?php echo esc_attr( sanitize_text_field( $api_secret ) ); ?>">
                            <?php esc_html_e( 'You can find your OpenAI API key', 'smartaipress' ); ?> <a href="https://platform.openai.com/account/api-keys" target="_blank"><?php esc_html_e( 'here', 'smartaipress' ); ?>.</a>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-default-model">
                                <?php esc_html_e( 'Default Openai Model', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Please select the option.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <select name="settings[openai_default_model]" class="sap-form-control" id="openai-default-model">
                                    <option value="gpt-3.5-turbo-instruct" <?php selected( 'gpt-3.5-turbo-instruct', $default_model, true ); ?>>ChatGPT 3.5 Turbo Instruct</option>
                                    <option value="gpt-3.5-turbo" <?php selected( 'gpt-3.5-turbo', $default_model, true ); ?>>ChatGPT 3.5 Turbo</option>
                                    <option value="gpt-3.5-turbo-16k" <?php selected( 'gpt-3.5-turbo-16k', $default_model, true ); ?>>ChatGTP 3.5 Turbo 16K</option>
                                    <option value="gpt-4" <?php selected( 'gpt-4', $default_model, true ); ?>>ChatGPT 4</option>
                                    <option value="gpt-4-1106-preview" <?php selected( 'gpt-4-1106-preview', $default_model, true ); ?>>ChatGPT 4 Turbo</option>
                                </select>
                                <div class="sap-tooltip-container">
                                    <i class="dashicons-before dashicons-editor-help sap-tooltip-icon"></i>
                                    <div class="sap-tooltip">
                                        <div class="sap-tooltip-content">
                                            <?php esc_html_e( 'Please note GPT-4 is not working with every api_key. You have to have an api key which can work with GPT-4.Also please note that Chat models works with ChatGPT and GPT-4 models. So if you choose below it will automatically use ChatGPT.', 'smartaipress' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-default-language"><?php esc_html_e( 'Default Openai Language', 'smartaipress' ); ?></label>
                            <span><?php esc_html_e( 'Select default openai language.', 'smartaipress' ); ?></span>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <select name="settings[openai_default_language]" id="openai-default-language" class="sap-form-control">
                                    <?php foreach ( smartaipress()->get_openai_languages() as $iso => $language ): ?>
                                        <option value="<?php echo esc_attr( sanitize_text_field( $language ) ); ?>" <?php selected( $language, $default_language, true ); ?>><?php echo esc_html( $language ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-default-tone-of-voice">
                                <?php esc_html_e( 'Default Tone of Voice', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Select the default tone of voice.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <select name="settings[openai_default_tone_of_voice]" class="sap-form-control" id="openai-default-tone-of-voice">
                                    <option value="professional" <?php selected( 'professional', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Professional', 'smartaipress' ); ?></option>
                                    <option value="funny" <?php selected( 'funny', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Funny', 'smartaipress' ); ?></option>
                                    <option value="casual" <?php selected( 'casual', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Casual', 'smartaipress' ); ?></option>
                                    <option value="excited" <?php selected( 'excited', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Excited', 'smartaipress' ); ?></option>
                                    <option value="witty" <?php selected( 'witty', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Witty', 'smartaipress' ); ?></option>
                                    <option value="sarcastic" <?php selected( 'sarcastic', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Sarcastic', 'smartaipress' ); ?></option>
                                    <option value="feminine" <?php selected( 'feminine', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Feminine', 'smartaipress' ); ?></option>
                                    <option value="masculine" <?php selected( 'masculine', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Masculine', 'smartaipress' ); ?></option>
                                    <option value="bold" <?php selected( 'bold', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Bold', 'smartaipress' ); ?></option>
                                    <option value="dramatic" <?php selected( 'dramatic', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Dramatic', 'smartaipress' ); ?></option>
                                    <option value="grumpy" <?php selected( 'grumpy', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Grumpy', 'smartaipress' ); ?></option>
                                    <option value="secretive" <?php selected( 'secretive', $default_tone_of_voice, true ); ?>><?php esc_html_e( 'Secretive', 'smartaipress' ); ?></option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-default-creativity">
                                <?php esc_html_e( 'Default Creativity', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Select the default creativity.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <select name="settings[openai_default_creativity]" class="sap-form-control" id="openai-default-creativity">
                                    <option value="0.25" <?php selected( '0.25', $default_creativity, true ); ?>><?php esc_html_e( 'Economic', 'smartaipress' ); ?></option>
                                    <option value="0.5" <?php selected( '0.5', $default_creativity, true ); ?>><?php esc_html_e( 'Average', 'smartaipress' ); ?></option>
                                    <option value="0.75" <?php selected( '0.75', $default_creativity, true ); ?>><?php esc_html_e( 'Good', 'smartaipress' ); ?></option>
                                    <option value="1" <?php selected( '1', $default_creativity, true ); ?>><?php esc_html_e( 'Premium', 'smartaipress' ); ?></option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-max-input-length">
                                <?php esc_html_e( 'Maximum Input Length', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Enter maximum input length in characters.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <input name="settings[openai_max_input_length]" type="number" class="sap-form-control width-50" id="openai-max-input-length" value="<?php echo esc_attr( sanitize_key( $max_input_length ) ); ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-max-output-length">
                                <?php esc_html_e( 'Maximum Output Length', 'smartaipress' ); ?>
                                <span><?php esc_html_e( 'Enter maximum output length in characters.', 'smartaipress' ); ?></span>
                            </label>
                        </th>
                        <td>
                            <div class="sap-form-group">
                                <input name="settings[openai_max_output_length]" type="number" class="sap-form-control width-50" id="openai-max-output-length" value="<?php echo esc_attr( sanitize_key( $max_output_length ) ); ?>">
                                <div class="sap-tooltip-container">
                                    <i class="dashicons-before dashicons-editor-help sap-tooltip-icon"></i>
                                    <div class="sap-tooltip">
                                        <div class="sap-tooltip-content">
                                            <?php esc_html_e( 'In Words. OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 2000 tokens The maximum output length refers to the point at which the AI-generated response will stop. It can occur when the response reaches 4096 bytes or when the generated content is considered sufficient for the given context.', 'smartaipress' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="openai-response-debug">
                                <?php esc_html_e( 'Response Debug', 'smartaipress' ); ?>
                            </label>
                        </th>
                        <td>
                            <input name="settings[openai_response_log]" class="sap-form-control" type="checkbox" value="1" id="openai-response-debug" <?php checked($response_log, true); ?>>
                            <div class="sap-tooltip-container">
                                <i class="dashicons-before dashicons-editor-help sap-tooltip-icon"></i>
                                <div class="sap-tooltip">
                                    <div class="sap-tooltip-content">
                                    <?php esc_html_e( 'If you enable this option all responses received from Openai will be logged into "logs/openai_response.txt" file.', 'smartaipress' ); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" name="smartaipress_submit" id="smartaipress-btn-submit"><?php esc_html_e( 'Save Changes', 'smartaipress' ); ?></button>
        </div>
    </div>
</div>

<?php require_once  plugin_dir_path( dirname( __FILE__ ) ) . '/partials/smartaipress-admin-footer.php';  ?>
