<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $smartaipress_settings;

$default_post_title     = $post->post_title ?? '';
$title                  = smartaipress('helper')->get_post_type_title();
$openai_api_key         = $smartaipress_settings['openai_api_key'];
$default_model          = $smartaipress_settings['openai_default_model'];
$default_language       = $smartaipress_settings['openai_default_language']; 
$default_tone_of_voice  = $smartaipress_settings['openai_default_tone_of_voice']; 
$default_creativity     = $smartaipress_settings['openai_default_creativity'];
$default_max_length     = $smartaipress_settings['openai_max_output_length']; ?>

<div class="smartaipress-openai-small-popup sap-hidden">
    <div class="smartaipress-openai-popup-content">
        <?php if (!empty($openai_api_key)) : ?>
        <div class="smartaipress-tabs">
            <div class="smartaipress-tab smartaipress-tab-active" data-tab="smartaipress-prompt"><?php esc_html_e( 'Prompt', 'smartaipress' ); ?></div>
            <div class="smartaipress-tab" data-tab="smartaipress-custom-prompt"><?php esc_html_e( 'Custom Prompt', 'smartaipress' ); ?></div>
        </div>
        <div class="smartaipress-tab-content smartaipress-tab-active" id="smartaipress-prompt">
            <form>
                <div class="sap-row">
                    <label for="sap-input-post-title"><?php echo esc_html( $title ); ?> <span class="sap-required">*</span></label>
                    <input name="smartaipress[post_title]" type="text" placeholder="<?php esc_attr_e( 'Article title', 'smartaipress' ); ?>" class="sap-form-control smartaipress-input" id="sap-input-post-title" value="<?php echo esc_attr( sanitize_text_field( $default_post_title ) ); ?>">
                </div>
                <div class="sap-row">
                    <label for="sap-input-focus-keyword"><?php esc_html_e( 'Focus Keyword', 'smartaipress' ); ?></label>
                    <input name="smartaipress[focus_keyword]" type="text" placeholder="<?php esc_attr_e( 'Focus keywords (separate with a comma)', 'smartaipress' ); ?>" class="sap-form-control smartaipress-input" id="sap-input-focus-keyword">
                </div>
                <div class="sap-row">
                    <label for="sap-select-openai-model"><?php esc_html_e( 'OpenAI Model', 'smartaipress' ); ?></label>
                    <select name="smartaipress[openai_model]" class="sap-form-control smartaipress-select" id="sap-select-openai-model">
                        <?php foreach ( smartaipress()->get_openai_models() as $name => $label ) : ?>
                            <option value="<?php echo esc_attr( sanitize_text_field( $name ) ); ?>" <?php selected( $name, $default_model, true ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sap-row">
                    <label for="sap-select-language"><?php esc_html_e( 'Language', 'smartaipress' ); ?></label>
                    <select name="smartaipress[openai_default_language]" class="sap-form-control smartaipress-select" id="sap-select-language">
                        <?php foreach ( smartaipress()->get_openai_languages() as $iso => $language ): ?>
                            <option value="<?php echo esc_attr( sanitize_text_field( $language ) ); ?>" <?php selected( $language, $default_language, true ); ?>><?php echo esc_html( $language ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sap-row">
                    <label for="sap-input-maximum-length"><?php esc_html_e( 'Maximum Length in Words', 'smartaipress' ); ?></label>
                    <input name="smartaipress[maximum_length]" type="number" placeholder="<?php esc_attr_e( 'Maximum length', 'smartaipress' ); ?>" class="sap-form-control smartaipress-input" id="sap-input-maximum-length" value="<?php echo esc_attr( sanitize_key( $default_max_length ) ); ?>">
                </div>
                <div class="sap-row">
                    <label for="sap-select-creativity"><?php esc_html_e( 'Creativity', 'smartaipress' ); ?></label>
                    <select name="smartaipress[creativity]" class="sap-form-control smartaipress-select" id="sap-select-creativity">
                        <option value="0.25" <?php selected( '0.25', $default_creativity, true ); ?>><?php esc_html_e( 'Economic', 'smartaipress' ); ?></option>
                        <option value="0.5" <?php selected( '0.5', $default_creativity, true ); ?>><?php esc_html_e( 'Average', 'smartaipress' ); ?></option>
                        <option value="0.75" <?php selected( '0.75', $default_creativity, true ); ?>><?php esc_html_e( 'Good', 'smartaipress' ); ?></option>
                        <option value="1" <?php selected( '1', $default_creativity, true ); ?>><?php esc_html_e( 'Premium', 'smartaipress' ); ?></option>
                    </select>
                </div>
                <div class="sap-row">
                    <label for="sap-select-tone-of-voice"><?php esc_html_e( 'Tone of Voice', 'smartaipress' ); ?></label>
                    <select name="smartaipress[tone_of_voice]" class="sap-form-control smartaipress-select" id="sap-select-tone-of-voice">
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
                <div class="sap-row">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <button type="button" name="smartaipress_generate" class="smartaipress-btn-generate">
                            <i class="dashicons dashicons-update smartaipress-animated-360 smartaipress-display-none"></i>
                            <?php esc_html_e( 'Generate Content', 'smartaipress' ); ?>
                        </button>
                        <div class="smartaipress-timer">00:00</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="smartaipress-tab-content" id="smartaipress-custom-prompt" style="display: none;">
            <textarea class="sap-form-control" disabled="disabled" rows="5" style="margin-bottom: 15px; filter:blur(0.5px);">Generate article about #post_title# in language #language# with maximum length ow words #max_words#.</textarea>
            <a href="https://smartaipress.com/pricing/?utm_source=smartaipress-wordpress&utm_medium=custom-prompt&utm_campaign=upgrade-to-pro" target="_blank" class="smartaipress-btn smartaipress-btn-gradient-orange smartaipress-uppercase smartaipress-weight-700">
                <i class="dashicons dashicons-money-alt"></i> 
                <?php esc_html_e( 'Upgrade to PRO', 'smartaipress' ); ?>
            </a>
        </div>
        <?php else : ?>
            <p style="color:red;">
                <strong><?php esc_html_e( 'The OpenAI API key is not set.', 'smartaipress' ); ?></strong> 
                <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress-settings') ); ?>"><?php esc_html_e( 'Set Now', 'smartaipress' ); ?></a>
            </p>
        <?php endif; ?>
    </div>
</div>
