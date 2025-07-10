<?php
/**
 * AJAX handler for Virtual Coupon Usage Tracker
 *
 * @package VirtualCouponUsageTracker
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX class for handling AJAX requests
 */
class VCUT_Ajax {
    
    /**
     * Constructor
     */
    public function __construct() {
        // AJAX actions for logged-in users
        add_action('wp_ajax_vcut_get_coupons', array($this, 'get_coupons'));
        add_action('wp_ajax_vcut_get_missing_order_reasons', array($this, 'get_missing_order_reasons'));
        
        // AJAX actions for non-logged-in users (if needed)
        // add_action('wp_ajax_nopriv_vcut_get_coupons', array($this, 'get_coupons'));
    }
    
    /**
     * Get virtual coupons data via AJAX
     */
    public function get_coupons() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'vcut_ajax_nonce')) {
            wp_die(__('Security check failed', 'virtual-coupon-usage-tracker'));
        }
        
        // Check user permissions
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have permission to access this data', 'virtual-coupon-usage-tracker'));
        }
        
        // Get request parameters
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';
        $order_by = isset($_POST['order_by']) ? sanitize_text_field($_POST['order_by']) : 'date_created';
        $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
        
        // Validate and sanitize date inputs
        if ($date_from && !$this->validate_date($date_from)) {
            $date_from = '';
        }
        if ($date_to && !$this->validate_date($date_to)) {
            $date_to = '';
        }
        
        // Build query arguments
        $args = array(
            'page' => $page,
            'per_page' => 20,
            'search' => $search,
            'status' => $status,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'order_by' => $order_by,
            'order' => $order
        );
        
        try {
            // Get data from database
            $results = VCUT_Database::get_virtual_coupon_usage($args);
            
            if (empty($results['data'])) {
                wp_send_json_success(array(
                    'data' => array(),
                    'total' => 0,
                    'pages' => 0,
                    'current_page' => $page,
                    'html' => $this->get_no_data_html()
                ));
            }
            
            // Format data for display
            $formatted_data = VCUT_Admin::format_coupon_data($results['data']);
            
            // Generate HTML for table rows
            $html = $this->generate_table_rows_html($formatted_data);
            
            // Send response
            wp_send_json_success(array(
                'data' => $formatted_data,
                'total' => $results['total'],
                'pages' => $results['pages'],
                'current_page' => $page,
                'html' => $html
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Failed to retrieve virtual coupon data', 'virtual-coupon-usage-tracker'),
                'error' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Get missing order reasons via AJAX
     */
    public function get_missing_order_reasons() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'vcut_ajax_nonce')) {
            wp_die(__('Security check failed', 'virtual-coupon-usage-tracker'));
        }
        
        // Check user permissions
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have permission to access this data', 'virtual-coupon-usage-tracker'));
        }
        
        // Get virtual coupon ID
        $virtual_coupon_id = isset($_POST['virtual_coupon_id']) ? intval($_POST['virtual_coupon_id']) : 0;
        
        if (!$virtual_coupon_id) {
            wp_send_json_error(array(
                'message' => __('Invalid virtual coupon ID', 'virtual-coupon-usage-tracker')
            ));
        }
        
        try {
            // Get reasons from database
            $reasons = VCUT_Database::get_missing_order_reasons($virtual_coupon_id);
            
            if (empty($reasons)) {
                $reasons = array(__('No specific reason found', 'virtual-coupon-usage-tracker'));
            }
            
            // Generate HTML for reasons
            $html = $this->generate_reasons_html($reasons);
            
            // Send response
            wp_send_json_success(array(
                'reasons' => $reasons,
                'html' => $html
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Failed to retrieve missing order reasons', 'virtual-coupon-usage-tracker'),
                'error' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Generate HTML for table rows
     *
     * @param array $data Formatted coupon data
     * @return string HTML for table rows
     */
    private function generate_table_rows_html($data) {
        if (empty($data)) {
            return $this->get_no_data_html();
        }
        
        $html = '';
        
        foreach ($data as $coupon) {
            $status_class = 'vcut-status-' . $coupon['status'];
            $order_cell = '';
            
            if ($coupon['has_order']) {
                $order_link = $this->get_order_edit_link($coupon['order_id']);
                $order_cell = sprintf(
                    '<a href="%s" target="_blank">#%d</a>',
                    esc_url($order_link),
                    $coupon['order_id']
                );
            } else {
                $order_cell = sprintf(
                    '<span class="vcut-no-order">%s</span>',
                    __('No Order', 'virtual-coupon-usage-tracker')
                );
            }
            
            $actions = '';
            if (!$coupon['has_order']) {
                $actions = sprintf(
                    '<button type="button" class="button vcut-info-btn" data-virtual-coupon-id="%d" title="%s">
                        <span class="dashicons dashicons-info"></span>
                    </button>',
                    $coupon['id'],
                    __('View reasons for missing order', 'virtual-coupon-usage-tracker')
                );
            }
            
            $html .= sprintf(
                '<tr>
                    <td><code>%s</code></td>
                    <td>%s</td>
                    <td><span class="vcut-status %s">%s</span></td>
                    <td>%s%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>',
                $coupon['coupon_code'],
                $coupon['main_coupon_title'],
                $status_class,
                $coupon['status_label'],
                $coupon['user_name'],
                $coupon['user_email'] ? '<br><small>' . $coupon['user_email'] . '</small>' : '',
                $order_cell,
                $coupon['order_total'],
                $coupon['created_date'],
                $actions
            );
        }
        
        return $html;
    }
    
    /**
     * Generate HTML for missing order reasons
     *
     * @param array $reasons Array of reasons
     * @return string HTML for reasons
     */
    private function generate_reasons_html($reasons) {
        if (empty($reasons)) {
            return '<p>' . __('No reasons found.', 'virtual-coupon-usage-tracker') . '</p>';
        }
        
        $html = '<div class="vcut-reasons-list">';
        $html .= '<p>' . __('Possible reasons why this virtual coupon does not have an associated order:', 'virtual-coupon-usage-tracker') . '</p>';
        $html .= '<ul>';
        
        foreach ($reasons as $reason) {
            $html .= '<li>' . esc_html($reason) . '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get HTML for no data message
     *
     * @return string HTML for no data message
     */
    private function get_no_data_html() {
        return sprintf(
            '<tr><td colspan="8" class="vcut-no-data">%s</td></tr>',
            __('No virtual coupons found matching your criteria.', 'virtual-coupon-usage-tracker')
        );
    }
    
    /**
     * Get order edit link
     *
     * @param int $order_id Order ID
     * @return string Order edit URL
     */
    private function get_order_edit_link($order_id) {
        // Check if HPOS is enabled
        $hpos_enabled = class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && 
                       \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
        
        if ($hpos_enabled) {
            // HPOS order edit link
            return admin_url('admin.php?page=wc-orders&action=edit&id=' . $order_id);
        } else {
            // Legacy order edit link
            return admin_url('post.php?post=' . $order_id . '&action=edit');
        }
    }
    
    /**
     * Validate date format
     *
     * @param string $date Date string
     * @return bool True if valid date format
     */
    private function validate_date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
} 