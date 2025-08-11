<?php
/**
 * Database operations for Virtual Coupon Usage Tracker
 *
 * @package VirtualCouponUsageTracker
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database class for handling virtual coupon usage queries
 */
class VCUT_Database {
    
    /**
     * Get virtual coupon usage data with order information
     *
     * @param array $args Query arguments
     * @return array Query results
     */
    public static function get_virtual_coupon_usage($args = array()) {
        global $wpdb;
        
        // Default arguments
        $defaults = array(
            'page' => 1,
            'per_page' => 100,
            'search' => '',
            'status' => '',
            'parent_coupon' => 0,
            'order_by' => 'order_id',
            'order' => 'DESC',
            'date_from' => '',
            'date_to' => '',
            'order_filter' => '',
            'stat_filter' => ''
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Calculate offset
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        // Get virtual coupons table name
        $virtual_coupons_table = $wpdb->prefix . 'acfw_virtual_coupons';
        
        // Check if HPOS is enabled
        $hpos_enabled = class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && 
                       \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
        
        // Simplified query - start with basic virtual coupons and add order info where available
        $base_query = "
            SELECT DISTINCT
                vc.id,
                vc.virtual_coupon as coupon_code,
                vc.coupon_id,
                vc.coupon_status,
                vc.user_id,
                vc.date_created,
                vc.date_expire,
                p.post_title as main_coupon_title,
                COALESCE(o.ID, oh.id) as order_id,
                COALESCE(o.post_status, oh.status) as order_status,
                COALESCE(o.post_date, oh.date_created_gmt) as order_date,
                COALESCE(om_total.meta_value, oh.total_amount) as order_total,
                COALESCE(om_billing_email.meta_value, oh.billing_email) as order_billing_email,
                COALESCE(om_customer_id.meta_value, oh.customer_id) as order_customer_id,
                u.display_name as user_name,
                u.user_email as user_email
            FROM {$virtual_coupons_table} vc
            LEFT JOIN {$wpdb->posts} p ON p.ID = vc.coupon_id AND p.post_type = 'shop_coupon'
            LEFT JOIN {$wpdb->users} u ON u.ID = vc.user_id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
            LEFT JOIN {$wpdb->posts} o ON o.ID = oi.order_id AND o.post_type = 'shop_order'
            LEFT JOIN {$wpdb->postmeta} om_total ON om_total.post_id = o.ID AND om_total.meta_key = '_order_total'
            LEFT JOIN {$wpdb->postmeta} om_billing_email ON om_billing_email.post_id = o.ID AND om_billing_email.meta_key = '_billing_email'
            LEFT JOIN {$wpdb->postmeta} om_customer_id ON om_customer_id.post_id = o.ID AND om_customer_id.meta_key = '_customer_user'
        ";
        
        // Add HPOS support if enabled
        if ($hpos_enabled) {
            $base_query .= "
            LEFT JOIN {$wpdb->prefix}wc_orders oh ON oh.id = oi.order_id
            ";
        } else {
            $base_query .= "
            LEFT JOIN {$wpdb->prefix}wc_orders oh ON 1=0
            ";
        }
        
        // Build WHERE clause
        $where_conditions = array('1=1');
        
        // Search filter
        if (!empty($args['search'])) {
            $search = esc_sql($args['search']);
            $where_conditions[] = "(
                vc.virtual_coupon LIKE '%{$search}%' OR
                p.post_title LIKE '%{$search}%' OR
                u.display_name LIKE '%{$search}%' OR
                u.user_email LIKE '%{$search}%'
            )";
        }
        
        // Status filter
        if (!empty($args['status'])) {
            $status = esc_sql($args['status']);
            if ($status === 'used_without_orders') {
                // Special filter for used coupons without orders
                $where_conditions[] = "vc.coupon_status = 'used'";
                $where_conditions[] = "COALESCE(o.ID, oh.id) IS NULL";
            } else {
                $where_conditions[] = "vc.coupon_status = '{$status}'";
            }
        }
        
        // Parent coupon filter
        if (!empty($args['parent_coupon'])) {
            $parent_coupon = intval($args['parent_coupon']);
            $where_conditions[] = "vc.coupon_id = {$parent_coupon}";
        }
        
        // Date range filter
        if (!empty($args['date_from'])) {
            $date_from = esc_sql($args['date_from']);
            $where_conditions[] = "vc.date_created >= '{$date_from}'";
        }
        
        if (!empty($args['date_to'])) {
            $date_to = esc_sql($args['date_to']);
            $where_conditions[] = "vc.date_created <= '{$date_to}'";
        }
        
        // Order filter (with_orders/without_orders)
        if (!empty($args['order_filter'])) {
            if ($args['order_filter'] === 'with_orders') {
                $where_conditions[] = "(COALESCE(o.ID, oh.id) IS NOT NULL)";
            } elseif ($args['order_filter'] === 'without_orders') {
                $where_conditions[] = "(COALESCE(o.ID, oh.id) IS NULL)";
            }
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Build ORDER BY clause
        $allowed_order_by = array('date_created', 'coupon_code', 'user_name', 'order_date', 'order_id', 'usage_date');
        $order_by = in_array($args['order_by'], $allowed_order_by) ? $args['order_by'] : 'order_id';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        // Map order_by to correct column names
        if ($hpos_enabled) {
            $order_by_mapping = array(
                'date_created' => 'vc.date_created',
                'coupon_code' => 'vc.virtual_coupon',
                'user_name' => 'u.display_name',
                'order_date' => 'oh.date_created_gmt',
                'order_id' => 'COALESCE(o.ID, oh.id)',
                'usage_date' => 'COALESCE(oh.date_created_gmt, o.post_date, vc.date_created)'
            );
        } else {
            $order_by_mapping = array(
                'date_created' => 'vc.date_created',
                'coupon_code' => 'vc.virtual_coupon',
                'user_name' => 'u.display_name',
                'order_date' => 'o.post_date',
                'order_id' => 'COALESCE(o.ID, oh.id)',
                'usage_date' => 'COALESCE(o.post_date, oh.date_created_gmt, vc.date_created)'
            );
        }
        
        $order_column = isset($order_by_mapping[$order_by]) ? $order_by_mapping[$order_by] : 'vc.date_created';
        $order_clause = "ORDER BY {$order_column} {$order}";
        
        // Build LIMIT clause
        $limit_clause = "LIMIT {$args['per_page']} OFFSET {$offset}";
        
        // Execute query
        $query = $base_query . ' ' . $where_clause . ' ' . $order_clause . ' ' . $limit_clause;
        $results = $wpdb->get_results($query, ARRAY_A);
        
        // Get total count - need to include the same joins for order filtering
        $count_base_query = "
            FROM {$virtual_coupons_table} vc
            LEFT JOIN {$wpdb->posts} p ON p.ID = vc.coupon_id AND p.post_type = 'shop_coupon'
            LEFT JOIN {$wpdb->users} u ON u.ID = vc.user_id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
            LEFT JOIN {$wpdb->posts} o ON o.ID = oi.order_id AND o.post_type = 'shop_order'
        ";
        
        // Add HPOS support for count query
        if ($hpos_enabled) {
            $count_base_query .= "
            LEFT JOIN {$wpdb->prefix}wc_orders oh ON oh.id = oi.order_id
            ";
        } else {
            $count_base_query .= "
            LEFT JOIN {$wpdb->prefix}wc_orders oh ON 1=0
            ";
        }
        
        $count_query = "SELECT COUNT(DISTINCT vc.id) " . $count_base_query . ' ' . $where_clause;
        $total_count = $wpdb->get_var($count_query);
        
        return array(
            'data' => $results,
            'total' => intval($total_count),
            'pages' => ceil($total_count / $args['per_page'])
        );
    }
    
    /**
     * Get reasons why a virtual coupon might not have an order ID
     *
     * @param int $virtual_coupon_id Virtual coupon ID
     * @return array Array of reasons
     */
    public static function get_missing_order_reasons($virtual_coupon_id) {
        global $wpdb;
        
        $reasons = array();
        
        // Get virtual coupon data
        $virtual_coupons_table = $wpdb->prefix . 'acfw_virtual_coupons';
        $virtual_coupon = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$virtual_coupons_table} WHERE id = %d", $virtual_coupon_id),
            ARRAY_A
        );
        
        if (!$virtual_coupon) {
            $reasons[] = __('Virtual coupon not found', 'virtual-coupon-usage-tracker');
            return $reasons;
        }
        
        // Check if coupon is still pending
        if ($virtual_coupon['coupon_status'] === 'pending') {
            $reasons[] = __('Coupon has not been used yet', 'virtual-coupon-usage-tracker');
        }
        
        // Check if coupon is expired
        if (!empty($virtual_coupon['date_expire']) && 
            $virtual_coupon['date_expire'] !== '0000-00-00 00:00:00' && 
            strtotime($virtual_coupon['date_expire']) < time()) {
            $reasons[] = __('Coupon has expired', 'virtual-coupon-usage-tracker');
        }
        
        // Check if main coupon exists
        $main_coupon = get_post($virtual_coupon['coupon_id']);
        if (!$main_coupon || $main_coupon->post_type !== 'shop_coupon') {
            $reasons[] = __('Main coupon has been deleted', 'virtual-coupon-usage-tracker');
        }
        
        // Check if user exists
        if ($virtual_coupon['user_id'] > 0) {
            $user = get_user_by('id', $virtual_coupon['user_id']);
            if (!$user) {
                $reasons[] = __('Associated user has been deleted', 'virtual-coupon-usage-tracker');
            }
        }
        
        // Check for failed orders
        $hpos_enabled = class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && 
                       \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
        
        if ($hpos_enabled) {
            // HPOS query for failed orders
            $orders_table = $wpdb->prefix . 'wc_orders';
            $order_items_table = $wpdb->prefix . 'woocommerce_order_items';
            $order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
            
            $failed_orders = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT o.status 
                    FROM {$order_itemmeta_table} oim
                    JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
                    JOIN {$orders_table} o ON o.id = oi.order_id
                    WHERE oim.meta_key = 'acfw_virtual_coupon_id' 
                    AND oim.meta_value = %d
                    AND o.status IN ('wc-failed', 'wc-cancelled')
                ", $virtual_coupon_id)
            );
        } else {
            // Legacy query for failed orders
            $order_items_table = $wpdb->prefix . 'woocommerce_order_items';
            $order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
            
            $failed_orders = $wpdb->get_results(
                $wpdb->prepare("
                    SELECT p.post_status 
                    FROM {$order_itemmeta_table} oim
                    JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
                    JOIN {$wpdb->posts} p ON p.ID = oi.order_id
                    WHERE oim.meta_key = 'acfw_virtual_coupon_id' 
                    AND oim.meta_value = %d
                    AND p.post_status IN ('wc-failed', 'wc-cancelled')
                ", $virtual_coupon_id)
            );
        }
        
        if (!empty($failed_orders)) {
            $reasons[] = __('Coupon was used in failed or cancelled orders', 'virtual-coupon-usage-tracker');
        }
        
        // If no specific reasons found but status is used, it might be a data inconsistency
        if (empty($reasons) && $virtual_coupon['coupon_status'] === 'used') {
            $reasons[] = __('Coupon is marked as used but order data may be missing due to data cleanup or migration', 'virtual-coupon-usage-tracker');
        }
        
        return $reasons;
    }
    
    /**
     * Get virtual coupon statistics
     *
     * @return array Statistics data
     */
    public static function get_statistics() {
        global $wpdb;
        
        $virtual_coupons_table = $wpdb->prefix . 'acfw_virtual_coupons';
        
        $stats = array(
            'total' => 0,
            'used' => 0,
            'pending' => 0,
            'expired' => 0,
            'with_orders' => 0,
            'without_orders' => 0,
            'used_without_orders' => 0
        );
        
        // Get basic counts
        $counts = $wpdb->get_results("
            SELECT 
                coupon_status,
                COUNT(*) as count
            FROM {$virtual_coupons_table}
            GROUP BY coupon_status
        ", ARRAY_A);
        
        foreach ($counts as $count) {
            $stats['total'] += $count['count'];
            $stats[$count['coupon_status']] = $count['count'];
        }
        
        // Get expired count
        $expired_count = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$virtual_coupons_table}
            WHERE date_expire != '0000-00-00 00:00:00' 
            AND date_expire < NOW()
        ");
        $stats['expired'] = intval($expired_count);
        
        // Get counts with/without orders
        $hpos_enabled = class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && 
                       \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
        
        if ($hpos_enabled) {
            $order_items_table = $wpdb->prefix . 'woocommerce_order_items';
            $order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
            $with_orders = $wpdb->get_var("
                SELECT COUNT(DISTINCT vc.id)
                FROM {$virtual_coupons_table} vc
                JOIN {$order_itemmeta_table} oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
                JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
            ");
        } else {
            $order_items_table = $wpdb->prefix . 'woocommerce_order_items';
            $order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
            $with_orders = $wpdb->get_var("
                SELECT COUNT(DISTINCT vc.id)
                FROM {$virtual_coupons_table} vc
                JOIN {$order_itemmeta_table} oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
                JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
            ");
        }
        
        $stats['with_orders'] = intval($with_orders);
        $stats['without_orders'] = $stats['total'] - $stats['with_orders'];
        
        // Get used without orders count
        if ($hpos_enabled) {
            $used_without_orders = $wpdb->get_var("
                SELECT COUNT(DISTINCT vc.id)
                FROM {$virtual_coupons_table} vc
                LEFT JOIN {$order_itemmeta_table} oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
                LEFT JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
                LEFT JOIN {$wpdb->prefix}wc_orders oh ON oh.id = oi.order_id
                WHERE vc.coupon_status = 'used' AND oh.id IS NULL
            ");
        } else {
            $used_without_orders = $wpdb->get_var("
                SELECT COUNT(DISTINCT vc.id)
                FROM {$virtual_coupons_table} vc
                LEFT JOIN {$order_itemmeta_table} oim ON oim.meta_key = 'acfw_virtual_coupon_id' AND oim.meta_value = vc.id
                LEFT JOIN {$order_items_table} oi ON oi.order_item_id = oim.order_item_id AND oi.order_item_type = 'coupon'
                LEFT JOIN {$wpdb->posts} o ON o.ID = oi.order_id AND o.post_type = 'shop_order'
                WHERE vc.coupon_status = 'used' AND o.ID IS NULL
            ");
        }
        
        $stats['used_without_orders'] = intval($used_without_orders);
        
        return $stats;
    }
    
    /**
     * Get parent coupons for filter dropdown
     *
     * @return array Array of parent coupons
     */
    public static function get_parent_coupons() {
        global $wpdb;
        
        $virtual_coupons_table = $wpdb->prefix . 'acfw_virtual_coupons';
        
        $results = $wpdb->get_results("
            SELECT DISTINCT p.ID, p.post_title
            FROM {$virtual_coupons_table} vc
            INNER JOIN {$wpdb->posts} p ON p.ID = vc.coupon_id AND p.post_type = 'shop_coupon'
            WHERE p.post_status = 'publish'
            ORDER BY p.post_title ASC
        ", ARRAY_A);
        
        return $results ?: array();
    }
    
    /**
     * Change virtual coupon status
     *
     * @param int $coupon_id Virtual coupon ID
     * @param string $new_status New status (pending, used, unlimited)
     * @return bool True if successful
     */
    public static function change_virtual_coupon_status($coupon_id, $new_status) {
        global $wpdb;
        
        $virtual_coupons_table = $wpdb->prefix . 'acfw_virtual_coupons';
        
        // Validate status
        $allowed_statuses = array('pending', 'used', 'unlimited');
        if (!in_array($new_status, $allowed_statuses)) {
            return false;
        }
        
        $result = $wpdb->update(
            $virtual_coupons_table,
            array('coupon_status' => $new_status),
            array('id' => $coupon_id),
            array('%s'),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Check if virtual coupons table exists
     *
     * @return bool
     */
    public static function virtual_coupons_table_exists() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'acfw_virtual_coupons';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");
        
        return $table_exists === $table_name;
    }
} 