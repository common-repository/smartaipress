<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin menus.
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin
 * @author     Majestic Code <kdonet@gmail.com>
 */
class Smartaipress_Admin_Menu {

    /**
     * Singleton instance variable for the Smartaipress_Admin_Menu class.
     *
     * This variable holds the single instance of the Smartaipress_Admin_Menu class, ensuring that
     * only one instance is created and used throughout the application. It follows the Singleton design pattern.
     *
     * @var Smartaipress_Admin_Menu|null The single instance of Smartaipress_Admin_Menu.
     */
    private static $instance;

    /**
     * Get a single instance of the Smartaipress_Admin_Menu class.
     *
     * This method implements the Singleton design pattern, ensuring that only one instance
     * of the Smartaipress_Admin_Menu class is created and returned. If an instance does not exist,
     * a new one is created, and if it does exist, the existing instance is returned.
     *
     * @return Smartaipress_Admin_Menu The single instance of Smartaipress_Admin_Menu.
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register the admin menu.
     * 
     * @since   1.0.0
     */
    public function register_admin_menu() {
        add_menu_page(
            esc_html__( 'SmartAIPress', 'smartaipress' ),
            esc_html__( 'SmartAIPress', 'smartaipress' ),
            'manage_options',
            'smartaipress',
            array( $this, 'render_pages' ),
            esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/smartaipress-logo-small.png' ),
            5
        );

        add_submenu_page(
            'smartaipress',
            esc_html__( 'Dashboard', 'smartaipress' ),
            esc_html__( 'Dashboard', 'smartaipress' ),
            'manage_options',
            'smartaipress',
            array( $this, 'render_pages' ),
        );

        add_submenu_page( 
            'smartaipress', 
            esc_html__( 'Settings', 'smartaipress' ), 
            esc_html__( 'Settings', 'smartaipress' ), 
            'manage_options', 
            'smartaipress-settings', 
            array( $this, 'render_pages' ),
        );
    }

    /**
     * Render the admin menu pages.
     * 
     * @since   1.0.0
     */
    public function render_pages() {
        $screen_id = get_current_screen()->id;

        $page_map = [
            'toplevel_page_smartaipress' => 'dashboard',
            'smartaipress_page_smartaipress-settings' => 'settings',
        ];
        
        if (array_key_exists($screen_id, $page_map)) {
            $page = $page_map[$screen_id];
            require_once plugin_dir_path(dirname(__FILE__)) . "/admin/partials/smartaipress-admin-$page.php";
        }
    }

}
