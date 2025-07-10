<?php
/**
 * Admin interface for Virtual Coupon Usage Tracker
 *
 * @package VirtualCouponUsageTracker
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class for handling the admin interface
 */
class VCUT_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'check_requirements'));
    }
    
    /**
     * Check plugin requirements
     */
    public function check_requirements() {
        // Check if virtual coupons table exists
        if (!VCUT_Database::virtual_coupons_table_exists()) {
            add_action('admin_notices', array($this, 'virtual_coupons_table_missing_notice'));
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('Virtual Coupon Usage', 'virtual-coupon-usage-tracker'),
            __('Virtual Coupon Usage', 'virtual-coupon-usage-tracker'),
            'manage_woocommerce',
            'virtual-coupon-usage',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('woocommerce_page_virtual-coupon-usage' !== $hook) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'vcut-admin-css',
            VCUT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            VCUT_PLUGIN_VERSION
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'vcut-admin-js',
            VCUT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            VCUT_PLUGIN_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('vcut-admin-js', 'vcut_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vcut_ajax_nonce'),
            'loading_text' => __('Loading...', 'virtual-coupon-usage-tracker'),
            'error_text' => __('An error occurred. Please try again.', 'virtual-coupon-usage-tracker'),
            'no_data_text' => __('No virtual coupons found.', 'virtual-coupon-usage-tracker')
        ));
    }
    
    /**
     * Virtual coupons table missing notice
     */
    public function virtual_coupons_table_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Virtual Coupon Usage Tracker: The virtual coupons table is missing. Please ensure Advanced Coupons for WooCommerce is properly installed and activated.', 'virtual-coupon-usage-tracker'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        // Get statistics
        $stats = VCUT_Database::get_statistics();
        ?>
        <div class="wrap">
            <h1><?php _e('Virtual Coupon Usage Tracker', 'virtual-coupon-usage-tracker'); ?></h1>
            
            <!-- Statistics Cards -->
            <div class="vcut-stats-cards">
                <div class="vcut-stat-card">
                    <h3><?php _e('Total Virtual Coupons', 'virtual-coupon-usage-tracker'); ?></h3>
                    <span class="vcut-stat-number"><?php echo number_format($stats['total']); ?></span>
                </div>
                <div class="vcut-stat-card">
                    <h3><?php _e('Used', 'virtual-coupon-usage-tracker'); ?></h3>
                    <span class="vcut-stat-number"><?php echo number_format($stats['used']); ?></span>
                </div>
                <div class="vcut-stat-card">
                    <h3><?php _e('Pending', 'virtual-coupon-usage-tracker'); ?></h3>
                    <span class="vcut-stat-number"><?php echo number_format($stats['pending']); ?></span>
                </div>
                <div class="vcut-stat-card">
                    <h3><?php _e('With Orders', 'virtual-coupon-usage-tracker'); ?></h3>
                    <span class="vcut-stat-number"><?php echo number_format($stats['with_orders']); ?></span>
                </div>
                <div class="vcut-stat-card">
                    <h3><?php _e('Without Orders', 'virtual-coupon-usage-tracker'); ?></h3>
                    <span class="vcut-stat-number"><?php echo number_format($stats['without_orders']); ?></span>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="vcut-filters">
                <div class="vcut-filter-row">
                    <div class="vcut-filter-group">
                        <label for="vcut-search"><?php _e('Search:', 'virtual-coupon-usage-tracker'); ?></label>
                        <input type="text" id="vcut-search" placeholder="<?php _e('Search coupons, users, emails...', 'virtual-coupon-usage-tracker'); ?>" />
                    </div>
                    
                    <div class="vcut-filter-group">
                        <label for="vcut-status"><?php _e('Status:', 'virtual-coupon-usage-tracker'); ?></label>
                        <select id="vcut-status">
                            <option value=""><?php _e('All Statuses', 'virtual-coupon-usage-tracker'); ?></option>
                            <option value="pending"><?php _e('Pending', 'virtual-coupon-usage-tracker'); ?></option>
                            <option value="used" selected><?php _e('Used', 'virtual-coupon-usage-tracker'); ?></option>
                            <option value="unlimited"><?php _e('Unlimited', 'virtual-coupon-usage-tracker'); ?></option>
                        </select>
                    </div>
                    
                    <div class="vcut-filter-group">
                        <label for="vcut-date-from"><?php _e('Date From:', 'virtual-coupon-usage-tracker'); ?></label>
                        <input type="date" id="vcut-date-from" />
                    </div>
                    
                    <div class="vcut-filter-group">
                        <label for="vcut-date-to"><?php _e('Date To:', 'virtual-coupon-usage-tracker'); ?></label>
                        <input type="date" id="vcut-date-to" />
                    </div>
                    
                    <div class="vcut-filter-group">
                        <button type="button" id="vcut-filter-btn" class="button button-primary"><?php _e('Filter', 'virtual-coupon-usage-tracker'); ?></button>
                        <button type="button" id="vcut-reset-btn" class="button"><?php _e('Reset', 'virtual-coupon-usage-tracker'); ?></button>
                    </div>
                </div>
            </div>
            
            <!-- Results Table -->
            <div class="vcut-table-container">
                <div id="vcut-loading" class="vcut-loading" style="display: none;">
                    <div class="vcut-spinner"></div>
                    <p><?php _e('Loading virtual coupon data...', 'virtual-coupon-usage-tracker'); ?></p>
                </div>
                
                <table class="wp-list-table widefat fixed striped" id="vcut-results-table">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column"><?php _e('Virtual Coupon Code', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Main Coupon', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Status', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('User', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Order ID', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Order Total', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Created Date', 'virtual-coupon-usage-tracker'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Actions', 'virtual-coupon-usage-tracker'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="vcut-results-tbody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="vcut-pagination">
                    <div class="vcut-pagination-info">
                        <span id="vcut-pagination-text"></span>
                    </div>
                    <div class="vcut-pagination-controls">
                        <button type="button" id="vcut-prev-page" class="button" disabled><?php _e('Previous', 'virtual-coupon-usage-tracker'); ?></button>
                        <span id="vcut-page-numbers"></span>
                        <button type="button" id="vcut-next-page" class="button" disabled><?php _e('Next', 'virtual-coupon-usage-tracker'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal for showing missing order reasons -->
        <div id="vcut-modal" class="vcut-modal" style="display: none;">
            <div class="vcut-modal-content">
                <div class="vcut-modal-header">
                    <h2><?php _e('Missing Order Information', 'virtual-coupon-usage-tracker'); ?></h2>
                    <span class="vcut-modal-close">&times;</span>
                </div>
                <div class="vcut-modal-body">
                    <div id="vcut-modal-loading" class="vcut-loading" style="display: none;">
                        <div class="vcut-spinner"></div>
                        <p><?php _e('Loading reasons...', 'virtual-coupon-usage-tracker'); ?></p>
                    </div>
                    <div id="vcut-modal-content">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
        
        <?php
    }
    
    /**
     * Format virtual coupon data for display
     *
     * @param array $coupon_data Raw coupon data from database
     * @return array Formatted data for display
     */
    public static function format_coupon_data($coupon_data) {
        $formatted = array();
        
        foreach ($coupon_data as $coupon) {
            $formatted[] = array(
                'id' => $coupon['id'],
                'coupon_code' => esc_html($coupon['coupon_code']),
                'main_coupon_title' => esc_html($coupon['main_coupon_title'] ?: __('N/A', 'virtual-coupon-usage-tracker')),
                'status' => esc_html($coupon['coupon_status']),
                'status_label' => self::get_status_label($coupon['coupon_status']),
                'user_name' => esc_html($coupon['user_name'] ?: __('Guest', 'virtual-coupon-usage-tracker')),
                'user_email' => esc_html($coupon['user_email'] ?: ''),
                'order_id' => $coupon['order_id'] ? intval($coupon['order_id']) : null,
                'order_total' => $coupon['order_total'] ? wc_price($coupon['order_total']) : '',
                'order_status' => $coupon['order_status'] ? esc_html($coupon['order_status']) : '',
                'created_date' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($coupon['date_created'])),
                'expire_date' => $coupon['date_expire'] && $coupon['date_expire'] !== '0000-00-00 00:00:00' ? 
                    date_i18n(get_option('date_format'), strtotime($coupon['date_expire'])) : __('Never', 'virtual-coupon-usage-tracker'),
                'has_order' => !empty($coupon['order_id'])
            );
        }
        
        return $formatted;
    }
    
    /**
     * Get status label for display
     *
     * @param string $status Status value
     * @return string Status label
     */
    private static function get_status_label($status) {
        $labels = array(
            'pending' => __('Pending', 'virtual-coupon-usage-tracker'),
            'used' => __('Used', 'virtual-coupon-usage-tracker'),
            'unlimited' => __('Unlimited', 'virtual-coupon-usage-tracker')
        );
        
        return isset($labels[$status]) ? $labels[$status] : ucfirst($status);
    }
} 