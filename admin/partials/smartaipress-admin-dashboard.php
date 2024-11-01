<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display admin panel dashboard page.
 * 
 * @link       https://smartaipress.com
 * @since      1.0.0
 *
 * @package    Smartaipress
 * @subpackage Smartaipress/admin/partials
 */

require_once  plugin_dir_path( dirname( __FILE__ ) ) . '/partials/smartaipress-admin-header.php'; ?>

<div class="sap-container-fluid">
    <div class="smartaipress-row">
        <div class="smartaipress-col-md-6 sap-mb-20">
            <div class="sap-card">
                <div class="sap-card-header sap-display-flex sap-align-center">
                    <div class="sap-icon sap-icon-shape sap-icon-lg sap-bg-gradient-primary sap-shadow sap-text-center">
                        <i class="dashicons dashicons-external"></i>
                    </div>
                </div>
                <div class="sap-card-body sap-text-center sap-padding-top-0">
                    <h3 class="sap-margin-0"><?php esc_html_e( 'Requests sent', 'smartaipress' ); ?></h3>
                    <span class="sap-text-xs"><?php esc_html_e( 'Total api requests sent', 'smartaipress' ); ?></span>
                    <hr class="sap-horizontal sap-dark sap-my-3">
                    <h2><?php echo esc_html( smartaipress('helper')->get_openai_send_requests_count() ); ?></h2>
                </div>
            </div>
        </div>
        <div class="smartaipress-col-md-6 sap-mb-20">
            <div class="sap-card">
                <div class="sap-card-header sap-display-flex sap-align-center">
                    <div class="sap-icon sap-icon-shape sap-icon-lg sap-bg-gradient-primary sap-shadow sap-text-center">
                        <i class="dashicons dashicons-clock"></i>
                    </div>
                </div>
                <div class="sap-card-body sap-text-center sap-padding-top-0">
                    <h3 class="sap-margin-0"><?php esc_html_e( 'Average response', 'smartaipress' ); ?></h3>
                    <span class="sap-text-xs"><?php esc_html_e( 'The average openai api response time', 'smartaipress' ); ?></span>
                    <hr class="sap-horizontal sap-dark sap-my-3">
                    <h2>
                        <?php
                            if(smartaipress('helper')->get_average_openai_api_response_time()) {
                                echo esc_html( smartaipress('helper')->get_average_openai_api_response_time() ); 
                                echo " " . esc_html__( 'seconds', 'smartaipress' );
                            } else {
                                echo '0';
                            }
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="smartaipress-row">
        <div class="smartaipress-col-12">
            <div class="sap-card">
                <div class="sap-card-header">
                    <h5><?php esc_html_e( 'Charts', 'smartaipress' ); ?></h5>
                    <p><?php esc_html_e( 'The OpenAI usage charts.', 'smartaipress' ); ?></p>
                    <div id="api-usage-filters-container">
                        <div class="filter-by-days-section">
                            <p><?php esc_html_e( 'Filter by days', 'smartaipress' ); ?></p>
                            <div>
                                <input type="number" id="total-days-filter" min="1" value="5">
                                <button id="filter-by-day-btn"><?php esc_html_e( 'Show Usage Data', 'smartaipress' ); ?></button>
                            </div>
                            <div id="total-days-error" style="color: red;"></div>
                        </div>
                        <div class="filter-by-dates-section">
                            <p><?php esc_html_e( 'Filter by dates', 'smartaipress' ); ?></p>
                            <div class="chart-filters-wrapper">
                                <div class="chart-filter-wrapper">
                                    <span><?php esc_html_e( 'From date:', 'smartaipress' ); ?></span>
                                    <input type="date" id="from-date-fragment">
                                </div>
                                <div class="chart-filter-wrapper">
                                    <span><?php esc_html_e( 'To date:', 'smartaipress' ); ?></span>
                                    <input type="date" id="to-date-fragment">
                                </div>
                                <button id="filter-by-dates-btn"><?php esc_html_e( 'Show Usage Data', 'smartaipress' ); ?></button>
                            </div>
                            <div id="dates-error-box" style="color: red;"></div>
                        </div>
                    </div>
                </div>
                <div class="sap-card-body">
                    <?php smartaipress()->loader(esc_html__('Loading...', 'smartaipress')); ?>
                    <div class="smartaipress-openai-chart" id="smartaipress-openai-chart-container">
                        <canvas id="openAiApiUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once  plugin_dir_path( dirname( __FILE__ ) ) . '/partials/smartaipress-admin-footer.php';  ?>
