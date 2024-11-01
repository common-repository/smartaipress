<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Smartaipress_Openai {

   /**
     * Singleton instance variable for the Smartaipress_Openai class.
     *
     * This variable holds the single instance of the Smartaipress_Openai class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Openai|null The single instance of Smartaipress_Openai.
     */
    private static $instance;

    /**
     * OpenAI Model
     */
    private $openai_model;

    /**
     * Constructor to initialize the OpenAI class.
     */
    private function __construct() {
        $this->openai_model = smartaipress()->get_settings( 'openai_default_model', 'gpt-3.5-turbo' );
    }

    /**
     * Get a single instance of the Smartaipress_Openai class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Openai class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Openai The single instance of Smartaipress_Openai.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate a prompt based on user input parameters.
     *
     * @since 1.0.0
     * @param array $params User input parameters, including post title, focus keyword, language, maximum length, and tone of voice.
     * @return string The generated prompt for the OpenAI request.
     */
    private function generate_prompt($params) {
        $prompt = "Generate article about {$params['post_title']}. ";
        $prompt .= "Use H2, H3 headings and other SEO related tags. ";

        if ( isset($params['focus_keyword']) && ! empty( $params['focus_keyword'] ) ) {
            $prompt .= "Focus on {$params['focus_keyword']}. ";
        }

        $prompt .= "Maximum {$params['maximum_length']}. ";
        $prompt .= "Creativity is {$params['creativity']} between 0 and 1. ";
        $prompt .= "Tone of voice must be {$params['tone_of_voice']}. ";
        $prompt .= "The text format is markdawn. ";
        $prompt .= "Write whole text in {$params['language']}.";

        return $prompt;
    }

    /**
     * Generate post content using OpenAI.
     * This function is called via AJAX.
     * 
     * @since 1.0.0
     */
    public function generate_content() {
        // Verify nonce for security
        $nonce = isset($_POST['nonce']) ? esc_html(sanitize_text_field($_POST['nonce'])) : ''; // phpcs:ignore
        
        if ( ! wp_verify_nonce( $nonce, 'smartaipress_nonce' ) ) {
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
    
        // Validate and sanitize input
        $params['post_title']     = esc_html(sanitize_text_field( wp_unslash($_POST['smartaipress']['post_title']) ?? '' ));
        $params['focus_keyword']  = esc_html(sanitize_text_field( wp_unslash($_POST['smartaipress']['focus_keyword']) ?? '' ));
        $params['model']          = esc_html(sanitize_text_field( $_POST['smartaipress']['model'] ?? '' ));
        $params['language']       = esc_html(sanitize_text_field( $_POST['smartaipress']['language'] ?? '' ));
        $params['maximum_length'] = esc_html(sanitize_text_field(absint($_POST['smartaipress']['maximum_length']))) ?? '';
        $params['creativity']     = esc_html(sanitize_text_field( $_POST['smartaipress']['creativity'] ?? '' ));
        $params['tone_of_voice']  = esc_html(sanitize_text_field( $_POST['smartaipress']['tone_of_voice'] ?? '' ));
        $params['post_type']      = esc_html(sanitize_text_field( $_POST['smartaipress']['post_type'] ?? '' ));
    
        // If post title is empty, return early.
        if (empty($params['post_title'])) {
            $error = [
                'error' => [
                    'label' => esc_html__( 'Post title is required!', 'smartaipress' ),
                    'message' => esc_html__( 'The post title field is required for article generation.', 'smartaipress' ), 
                ] 
            ];
            wp_send_json_error($error);
        }
    
        // Generate content using OpenAI
        $prompt = $this->generate_prompt($params);

        $model      = $params['model'] ?? $this->openai_model;
        $post_type  = $params['post_type'];

        // Send prompt to OpenAI API server
        $response = smartaipress('openai-client')->prompt($prompt, $model, $post_type);
        
        // Handle errors returned by OpenAI API server
        if (isset($response->error) && !empty($response->error)) {
            
            wp_send_json_error($response);
            
        } else {
            // Format the response content
            $result = $this->format_content($response);

            // Return the generated content as an AJAX response
            wp_send_json_success($result);
        }
    }

    /**
     * Extract and format content from the OpenAI response.
     *
     * @param object $response The response content from OpenAI.
     *
     * @return array Formatted content.
     * @since 1.0.0
     */
    private function format_content( $response ) {
        $output = array();

        if (empty($response)) {
            return ''; // Handle the case of an empty content array.
        }

        // Include the Parsedown class
        require_once SMARTAIPRESS_DIR . 'includes/class-smartaipress-parsedown.php';

        if ("text_completion" === $response->object || "chat.completion" === $response->object) {
            if (!empty($response->warning)) {
                $output['warning'] = sanitize_text_field( $response->warning );
            }
            if ($response->choices) {
                foreach ($response->choices as $result) {
                    $output['content'] = $this->format_markdown(
                        $response->object === "text_completion" ? $result->text : $result->message->content
                    );
                }
            }
        }

        return $output;
    }

    /**
     * Format content as Markdown.
     *
     * @param string $content The content to format.
     *
     * @return string Formatted content in Markdown.
     * @since 1.0.0
     */
    private function format_markdown( $content ) {
        $parsedown = new Smartaipress_Parsedown();

        return $parsedown->text( $content );
    }

    /**
     * Generate an image using the OpenAI API dalle model.
     *
     * This function handles the AJAX request to generate an image based on the input data
     * received from the client-side. It verifies the security nonce, processes the image
     * request, and communicates with the OpenAI API server to generate the desired image.
     *
     * @since 1.0.0
     */
    public function generate_image() {
        $nonce = isset($_POST['nonce']) ? esc_html(sanitize_text_field($_POST['nonce'])) : ''; // phpcs:ignore
        
        if ( ! wp_verify_nonce( $nonce, 'smartaipress_nonce' ) ) {
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

        $image      = esc_html(sanitize_textarea_field($_POST['image'])) ?? '';
        $resolution = esc_html(sanitize_text_field($_POST['resolution'])) ?? '';
        $model      = esc_html(sanitize_text_field($_POST['model'])) ?? '';
        $style      = esc_html(sanitize_text_field($_POST['style'])) ?? '';
        $post_type  = esc_html(sanitize_text_field($_POST['post_type'])) ?? '';

        $args = [
            'image' => $image, 
            'resolution' => $resolution,
            'model' => $model,
            'style' => $style,
            'total' => 1
        ];

        // Send prompt to OpenAI API server
        $response = smartaipress('openai-client')->prompt($args, 'dalle', $post_type);

        // Handle errors returned by OpenAI API server
        if (!empty($response->error)) {
            wp_send_json_error($response);
        } else {
            wp_send_json_success($response);
        }
	}

    /**
     * Handle the storage of an image from a given URL as a featured image for a post.
     *
     * This function performs the following tasks:
     * 1. Validates a security nonce.
     * 2. Retrieves the image URL and uploads it to the WordPress media library.
     * 3. Sets the uploaded image as an attachment and generates metadata.
     * 4. Associates the attachment with a specified post or the last post.
     * 5. Sends a JSON response indicating success or failure.
     *
     * @since 1.0.0
     */
    public function store_image() {
        $nonce = isset($_POST['nonce']) ? esc_html(sanitize_text_field($_POST['nonce'])) : ''; // phpcs:ignore
        
        if ( ! wp_verify_nonce( $nonce, 'smartaipress_nonce' ) ) {
            wp_send_json_error(
                [ 
                    'error' => [
                        'code' => 'invalid_nonce',
                        'label' => esc_html__( 'Invalid nonce!', 'smartaipress_nonce' ),
                        'message' => esc_html__( 'The nonce verification failed.', 'smartaipress' )
                    ]
                ]
            );
        }

        $image_url  = esc_url_raw($_POST["imageurl"]) ?? ''; // Do not escape!!!
        $post_id    = esc_html(sanitize_text_field(absint($_POST["post_id"]))) ?? smartaipress('helper')->get_last_post_id();

        $args = [
            'timeout' => 300
        ];

        // Add Featured Image to Post
        $image_name       = 'smartaipress-image' . '.png';
        $upload_dir       = wp_upload_dir(); // Set upload folder
        $response         = wp_remote_get($image_url, $args); // Get image data

        if (is_wp_error($response)) {
            wp_send_json_error([
                'error' => [
                    'code' => 'error',
                    'message' => $response->get_error_message()
                ]
            ]);
        } else {
            // Retrieve the body of the response
            $image_data = wp_remote_retrieve_body($response);
        }

        $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
        $filename         = basename( $unique_file_name ); // Create image file name

        // Check folder permission and define file location
        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        global $wp_filesystem;

        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

         // Create the image  file on the server
        if ( ! $wp_filesystem->exists( $file ) ) {
            $wp_filesystem->put_contents( $file, $image_data, FS_CHMOD_FILE );
        }

        // Check image file type
        $wp_filetype = wp_check_filetype( $filename, null );

        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( $filename ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file );

        // Include image.php
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

        // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, $attach_data );

        $attachment_url = wp_get_attachment_url($attach_id);

        if($attachment_url) {
            $data = [
                'label'     => esc_html__( 'Featured image', 'smartaipress' ),
                'message'   => esc_html__( 'Featured image set successfully!', 'smartaipress' ),
                'url'       => $attachment_url,
                'image_id'  => $attach_id,
                'post_id'   => $post_id
            ];
            wp_send_json_success($data);
        } else {
            $error = [
                'error' => [
                    'code' => 'error',
                    'message' => esc_html__( 'Error while setting featured image.', 'smartaipress' ), 
                ]
            ];
            wp_send_json_error($error);
        }
    }

}
