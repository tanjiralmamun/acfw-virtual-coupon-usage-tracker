<?php
/**
 * Plugin Name: ACFW Virtual Coupon Usage Tracker
 * Plugin URI: https://tanjirsdev.com/plugins/virtual-coupon-usage-tracker
 * Description: Track virtual coupon usage with order IDs, show reasons for missing order IDs, and provide AJAX pagination. Compatible with HPOS.
 * Version: 1.1.2
 * Author: Tanjir Al Mamun
 * Author URI: https://tanjirsdev.com
 * Text Domain: virtual-coupon-usage-tracker
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VCUT_PLUGIN_FILE', __FILE__);
define('VCUT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VCUT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VCUT_PLUGIN_VERSION', '1.1.2');
define('VCUT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class VirtualCouponUsageTracker {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Plugin initialization
     */
    public function __construct() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'init'));
        
        // Declare HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_hpos_compatibility'));
        
        // Plugin activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Get single instance of the plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Check if Advanced Coupons is active
        if (!class_exists('ACFWF')) {
            add_action('admin_notices', array($this, 'advanced_coupons_missing_notice'));
            return;
        }
        
        // Load plugin textdomain
        add_action('init', array($this, 'load_textdomain'));
        
        // Initialize plugin components
        $this->includes();
        $this->hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once VCUT_PLUGIN_DIR . 'includes/class-vcut-database.php';
        require_once VCUT_PLUGIN_DIR . 'includes/class-vcut-admin.php';
        require_once VCUT_PLUGIN_DIR . 'includes/class-vcut-ajax.php';
    }
    
    /**
     * Setup hooks
     */
    private function hooks() {
        // Initialize admin interface
        if (is_admin()) {
            new VCUT_Admin();
        }
        
        // Initialize AJAX handlers
        new VCUT_Ajax();
    }
    
    /**
     * Declare HPOS compatibility
     */
    public function declare_hpos_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                __FILE__,
                true
            );
        }
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'virtual-coupon-usage-tracker',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('Virtual Coupon Usage Tracker requires WooCommerce to be installed and active.', 'virtual-coupon-usage-tracker'));
        }
        
        // Check if Advanced Coupons is active
        if (!class_exists('ACFWF')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('Virtual Coupon Usage Tracker requires Advanced Coupons for WooCommerce to be installed and active.', 'virtual-coupon-usage-tracker'));
        }
        
        // Store activation time
        update_option('vcut_activated_time', time());
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up any temporary data if needed
        delete_option('vcut_activated_time');
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Virtual Coupon Usage Tracker requires WooCommerce to be installed and active.', 'virtual-coupon-usage-tracker'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Advanced Coupons missing notice
     */
    public function advanced_coupons_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('Virtual Coupon Usage Tracker requires Advanced Coupons for WooCommerce to be installed and active.', 'virtual-coupon-usage-tracker'); ?></p>
        </div>
        <?php
    }
}

// Initialize the plugin
VirtualCouponUsageTracker::get_instance(); 