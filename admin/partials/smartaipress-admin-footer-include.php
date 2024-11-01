<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $smartaipress_settings;

$default_post_title     = get_the_title( $post ) ?? '';
$title                  = smartaipress('helper')->get_post_type_title();
$openai_api_key         = $smartaipress_settings['openai_api_key'];
$default_model          = $smartaipress_settings['openai_default_model'];
$default_language       = $smartaipress_settings['openai_default_language']; 
$default_tone_of_voice  = $smartaipress_settings['openai_default_tone_of_voice']; 
$default_creativity     = $smartaipress_settings['openai_default_creativity'];
$default_max_length     = $smartaipress_settings['openai_max_output_length']; ?>

<script type="text/template" id="smartaipress-gutenberg-tooltip">
    <div class="smartaipress-openai-swal">
        <div class="smartaipress-openai-swal-content">
            <?php if (!empty($openai_api_key)) : ?>
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
                    <select name="smartaipress[openai_model]" class="sap-form-control smartaipress-select" id="smartaipress-meta-select-openai-model">
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
                    <button type="button" name="smartaipress_generate" class="smartaipress-btn-generate">
                        <i class="dashicons dashicons-update smartaipress-animated-360 smartaipress-display-none"></i>
                        <?php esc_html_e( 'Generate Content', 'smartaipress' ); ?>
                    </button>
                </div>
            </form>
            <?php else : ?>
                <p style="color:red;">
                    <strong><?php esc_html_e( 'The OpenAI API key is not set.', 'smartaipress' ); ?></strong> 
                    <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress-settings') ); ?>"><?php esc_html_e( 'Set Now', 'smartaipress' ); ?></a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</script>

<script type="text/html" id="smartaipress-swal-dalle-popup-template">
    <div class="smartaipress-openai-swal-content">
        <?php if (!empty($openai_api_key)) : ?>
            <div class="smartaipress-image-wrapper">
                <div class="smartaipress-image-placeholder smartaipress-display-none">
                    <div class="smartaipress-image-animated-background">
                        <img src="<?php echo esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/thumb.png' ); ?>" width="64" height="64">
                    </div>
                </div>
            </div>
            <form id="smartaipress-openai-image-form">
                <div class="sap-row">
                    <label for="smartaipress-image-model"><?php esc_html_e( 'Image model', 'smartaipress' ); ?></label>
                </div>
                <div class="sap-row">
                    <select name="smartaipress-dalle-image-model" class="sap-form-control">
                        <option value="dall-e-2" selected><?php esc_html_e( 'Dalle 2', 'smartaipress' ); ?></option>
                        <option value="dall-e-3"><?php esc_html_e('Dalle 3', 'smartaipress' ); ?></option>
                        <option value="dall-e-3-hd"><?php esc_html_e( 'Dalle 3 HD', 'smartaipress' ); ?></option>
                    </select>
                </div>
                <div class="sap-maybe-hide">
                    <div class="sap-row">
                        <label for="smartaipress-image-style"><?php esc_html_e( 'Image style', 'smartaipress' ); ?></label>
                    </div>
                    <select name="smartaipress-dalle-image-style" class="sap-form-control dalle-two-style">
                        <option value="classic"><?php esc_html_e( 'Classic', 'smartaipress' ); ?></option>
                    </select>
                    <div class="sap-row">
                        <label for="smartaipress-image-256"><?php esc_html_e( 'Image size', 'smartaipress' ); ?></label>
                    </div>
                    <div class="sap-row">
                        <select name="smartaipress-dalle-image-resolution" class="sap-form-control dalle-two">
                            <option value="256x256">256x256 <?php esc_html_e( 'pixels', 'smartaipress' ); ?></option>
                            <option value="512x512">512x512 <?php esc_html_e( 'pixels', 'smartaipress' ); ?></option>
                            <option value="1024x1024">1024x1024 <?php esc_html_e( 'pixels', 'smartaipress' ); ?></option>
                        </select>
                    </div>
                    <div class="sap-row">
                        <label for="smartaipress-image-prompt"><?php esc_html_e( 'Image prompt', 'smartaipress' ); ?></label>
                        <textarea id="smartaipress-image-prompt" class="sap-form-control" placeholder="<?php esc_attr_e( 'ex: Ferarri in cartoon style', 'smartaipress' ); ?>" rows="5"></textarea>
                        <span class="smartaipress-error-message"></span>
                    </div>
                    <div class="sap-row sap-buttons" style="display:flex;align-items:center;margin-top:20px;justify-content:space-between;">
                        <button id="smartaipress-send-image-request-btn" type="button" name="smartaipress_generate_image" class="smartaipress-btn-generate">
                            <i class="dashicons dashicons-update smartaipress-animated-360 smartaipress-display-none"></i>
                            <?php esc_html_e( 'Generate Image', 'smartaipress' ); ?>
                        </button>
                        <a href="javascript:;" class="smartaipress-back smartaipress-display-none"><?php esc_html_e( 'back', 'smartaipress' ); ?></a>
                        <a href="javascript:;" id="smartaipress-set-featured-image" type="button" class="smartaipress-content-generator-button smartaipress-display-none">
                            <?php esc_html_e( 'Set featured image', 'smartaipress' ); ?>
                        </a>
                    </div>
                </div>
                <div class="sap-maybe-show sap-text-center" style="border:1px solid #ccc;border-radius:5px;display:none;padding:5px;">
                    <p><?php esc_html_e( 'Upgrade to SmartAIPress PRO to start generating a', 'smartaipress' ); ?> <br> <?php esc_html_e('FULL-HD images using a DALL-E 3 AI model.', 'smartaipress'); ?></p>
                    <p><?php esc_html_e( 'DALL-E 3 was trained to generate 1024x1024, 1024x1792 or 1792x1024 images.', 'smartaipress' ); ?></p>
                    <p><?php esc_html_e( 'Check our example gallery of DALL-E 3 generated images', 'smartaipress' ); ?>:</p>
                    <div class="sap-display-flex sap-justify-content-between">
                        <a href="<?php echo esc_url("https://smartaipress.com/ai-generated-images/?utm_source=smartaipress-wordpress-plugin&utm_medium=image-generation-popup&utm_campaign=smartaipress-gallery-link"); ?>" class="smartaipress-btn smartaipress-btn-gradient-orange" target="_blank">SmartAIPress <?php esc_html_e('Gallery', 'smartaipress'); ?></a>
                        <a href="https://smartaipress.com/smartaipress-pro-for-wordpress/?utm_source=smartaipress-wordpress-plugin&utm_medium=image-generation-popup&utm_campaign=pro-button"  class="smartaipress-btn smartaipress-btn-gradient-orange" target="_blank">
                            <?php esc_html_e( 'SmartAIPress PRO', 'smartaipress' ); ?>
                        </a>
                    </div>
                </div>
            </form>
        <?php else : ?>
            <p style="color:red;">
                <strong><?php esc_html_e( 'The OpenAI API key is not set.', 'smartaipress' ); ?></strong> 
                <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress-settings') ); ?>"><?php esc_html_e( 'Set Now', 'smartaipress' ); ?></a>
            </p>
        <?php endif; ?>
    </div>
</script>

<script type="text/html" id="smartaipress-swal-deactivation-feedback-template">
    <div class="smartaipress-openai-swal-content">
        <form id="smartaipress-deactivation-feedback-form">
            <div class="sap-row">
                <label for="smartaipress-deactivation-feedback-reason"><?php esc_html_e( 'Reason for deactivation', 'smartaipress' ); ?></label>
            </div>
            <div class="sap-row">
                <select name="smartaipress-deactivation-feedback-options" class="sap-form-control" id="smartaipress-deactivation-feedback-reason">
                    <option value="no-option-selected" selected><?php esc_html_e( 'Select option', 'smartaipress' ); ?></option>
                    <option value="no-money-for-api-key"><?php esc_html_e( 'I dont have money for OpenAI API key', 'smartaipress' ); ?></option>
                    <option value="found-better-plugin"><?php esc_html_e('I have found a better plugin for my needs', 'smartaipress' ); ?></option>
                    <option value="i-dont-like-it"><?php esc_html_e( 'I dont like this plugin', 'smartaipress' ); ?></option>
                    <option value="plugin-needs-improvement"><?php esc_html_e( 'This plugin needs improvement', 'smartaipress' ); ?></option>
                    <option value="other"><?php esc_html_e( 'Other', 'smartaipress' ); ?></option>
                </select>
            </div>
            <div class="sap-maybe-hide">
                <div class="sap-row">
                    <label for="smartaipress-deactivation-feedback-message"><?php esc_html_e( 'Message', 'smartaipress' ); ?></label>
                    <textarea id="smartaipress-deactivation-feedback-message" class="sap-form-control" placeholder="<?php esc_attr_e( 'Your message...', 'smartaipress' ); ?>" rows="5"></textarea>
                    <span class="smartaipress-deactivation-feedback-error-message"></span>
                </div>
                <div class="sap-row sap-buttons" style="display:flex;align-items:center;margin-top:20px;justify-content:space-between;">
                    <button id="send-deactivation-data-btn" type="button" name="smartaipress_deactivation_feedback" class="smartaipress-btn-generate">
                        <i class="dashicons dashicons-update smartaipress-animated-360 smartaipress-display-none"></i>
                        <?php esc_html_e( 'Deactivate', 'smartaipress' ); ?>
                    </button>
                </div>
            </div>
            <div class="sap-maybe-show sap-text-center" style="border:1px solid #ccc;border-radius:5px;padding:5px;margin-top:5px;display:none;">
                <p style="font-size:11px;"><?php esc_html_e( 'Upgrade to SmartAIPress PRO to start generating a', 'smartaipress' ); ?> <br> <?php esc_html_e('FULL-HD images using a DALL-E 3 AI model.', 'smartaipress'); ?></p>
                <p style="font-size:11px;"><?php esc_html_e( 'DALL-E 3 was trained to generate 1024x1024, 1024x1792 or 1792x1024 images.', 'smartaipress' ); ?></p>
                <p style="font-size:11px;"><?php esc_html_e( 'Check our example gallery of DALL-E 3 generated images', 'smartaipress' ); ?>:</p>
                <div class="sap-display-flex sap-justify-content-between">
                    <a href="<?php echo esc_url("https://smartaipress.com/ai-generated-images/?utm_source=smartaipress-wordpress-plugin&utm_medium=image-generation-popup&utm_campaign=smartaipress-gallery-link"); ?>" class="smartaipress-btn smartaipress-btn-gradient-orange" target="_blank">SmartAIPress <?php esc_html_e('Gallery', 'smartaipress'); ?></a>
                    <a href="https://smartaipress.com/smartaipress-pro-for-wordpress/?utm_source=smartaipress-wordpress-plugin&utm_medium=image-generation-popup&utm_campaign=pro-button"  class="smartaipress-btn smartaipress-btn-gradient-orange" target="_blank">
                        <?php esc_html_e( 'SmartAIPress PRO', 'smartaipress' ); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
</script>

<script type="text/html" id="smartaipress-openai-generate-image-btn-template">
    <a href="javascript:void(0)" id="smartaipress-openai-show-generate-image-popup" class="smartaipress-content-generator-button">
        <img src="<?php echo esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/smartaipress-no-text-logo.png' ); ?>"> 
        <?php esc_html_e( 'Generate AI image', 'smartaipress' ); ?>
    </a>
</script>

<script type="text/html" id="smartaipress-featured-image-tmpl">
    <p class="hide-if-no-js">
        <a href="http://smartaipress-plugin-environment.project/wp-admin/media-upload.php?post_id={postId}&amp;type=image&amp;TB_iframe=1" id="set-post-thumbnail" aria-describedby="set-post-thumbnail-desc" class="thickbox">
            <img 
                width="256" 
                height="256" 
                src="{originalUrl}" 
                class="attachment-post-thumbnail size-post-thumbnail" 
                alt="" 
                decoding="async" 
                loading="lazy" 
                srcset="{originalUrl} 256w, {resizedUrl} 150w" 
                sizes="(max-width: 256px) 100vw, 256px">
        </a>
    </p>
    <p class="hide-if-no-js howto" id="set-post-thumbnail-desc"><?php esc_html_e( 'Click the image to edit or update', 'smartaipress' ); ?></p>
    <p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail"><?php esc_html_e( 'Remove featured image', 'smartaipress' ); ?></a></p>
    <input type="hidden" id="_thumbnail_id" name="_thumbnail_id" value="{imageId}">
</script>