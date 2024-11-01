<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$screen = get_current_screen()->id; 
?> 
<div id="smartaipress-panel">
    <div class="smartaipress-dashboard-background"></div>

    <div id="smartaipress-sidenav">
        <div class="sap-sidenav-header">
            <i class="dashicons-before dashicons-no sap-hide-sidenav"></i>
            <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress') ); ?>" class="sap-logo">
                <img src="<?php echo esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . '/img/smartaipress-logo.png' ); ?>" alt="SmartAIpress" />
                <span>SmartAIPress</span>
            </a>
        </div>
        <hr>
        <div id="sap-sidenav-collapse-main">
            <ul>
                <li><h6><?php esc_html_e( 'Pages', 'smartaipress' ); ?></h6></li>
                <li>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress') ); ?>" class="<?php echo $screen === 'toplevel_page_smartaipress' ? 'active' : ''; ?>">
                        <div class="sap-nav-icon">
                            <i class="dashicons-before dashicons-dashboard" style="color:#4b098d;"></i>
                        </div>
                        <?php esc_html_e( 'Dashboard', 'smartaipress' ); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=smartaipress-settings') ); ?>" class="<?php echo $screen === 'smartaipress_page_smartaipress-settings' ? 'active' : ''; ?>">
                        <div class="sap-nav-icon">
                            <i class="dashicons-before dashicons-admin-settings" style="color:#fb6340;"></i>
                        </div>
                        <?php esc_html_e( 'Settings', 'smartaipress' ); ?>
                    </a>
                </li>
            </ul>
            <hr />
            <ul>
                <li><h6><?php esc_html_e( 'Help', 'smartaipress' ); ?></h6></li>
                <li>
                    <a href="https://smartaipress.com/smartaipress-for-wordpress/?utm_source=smartaipress-wordpress-plugin&utm_medium=admin-panel&utm_campaign=sidenav-changelog-link" target="_blank">
                        <div class="sap-nav-icon">
                            <i class="dashicons-before dashicons-media-text" style="color:#2dce89;"></i>
                        </div>
                        <?php esc_html_e( 'Changelog', 'smartaipress' ); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sap-sidenav-footer">
            <div class="sap-sidenav-card">
                <img src="<?php echo esc_url( SMARTAIPRESS_ADMIN_ASSETS_URL . 'img/icon-documentation.svg' ); ?>">
                <div class="sap-sidenav-card-body">
                    <div class="sap-docs-info">
                        <h6><?php esc_html_e( 'More features?', 'smartaipress' ); ?></h6>
                        <p><?php esc_html_e( 'Upgrade to pro.', 'smartaipress' ); ?></p>
                    </div>
                </div>
            </div>
            <a href="https://smartaipress.com/pricing/?utm_source=smartaipress-wordpress-plugin&utm_medium=admin-panel&utm_campaign=sidenav-upgrade-btn" target="_blank" class="sap-sidenav-btn">
                <i class="dashicons dashicons-heart"></i>
                <?php esc_html_e( 'Upgrade', 'smartaipress' ); ?>
                <i class="dashicons dashicons-heart"></i>
            </a>
        </div>
    </div>

    <main id="smartaipress-main-content">
        <div class="sap-container-flex">
            <div></div>
            <nav id="smartaipress-top-navbar" class="sap-top-navbar">
                <div class="sap-container-fluid">
                    <div class="sap-sidenav-toggler">
                        <a href="javascript:;" id="sap-sidenav-toggler-init">
                            <div class="sap-sidenav-toggler-inner">
                                <i class="sap-sidenav-toggler-line"></i>
                                <i class="sap-sidenav-toggler-line"></i>
                                <i class="sap-sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
