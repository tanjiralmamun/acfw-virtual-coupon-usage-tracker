=== ACFW Virtual Coupon Usage Tracker ===
Contributors: tanjiralmamun
Tags: woocommerce, coupons, advanced coupons, virtual coupons, tracking, usage
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track virtual coupon usage with order IDs, show reasons for missing order IDs, and provide AJAX pagination. Compatible with HPOS.

== Description ==

ACFW Virtual Coupon Usage Tracker is a powerful WordPress plugin designed to enhance your WooCommerce store's coupon management capabilities. This plugin provides comprehensive tracking and monitoring of virtual coupon usage, specifically designed to work seamlessly with Advanced Coupons for WooCommerce.

= Key Features =

* **Virtual Coupon Tracking**: Monitor all virtual coupon usage with detailed information
* **Order ID Association**: Track which orders used specific virtual coupons
* **Missing Order ID Detection**: Identify and display reasons why some coupons may not have associated order IDs
* **AJAX Pagination**: Fast, responsive pagination for large datasets without page reloads
* **HPOS Compatibility**: Fully compatible with WooCommerce High-Performance Order Storage (HPOS)
* **Advanced Filtering**: Filter coupons by status, parent coupon, date range, and search terms
* **User Information**: Display user details associated with coupon usage
* **Real-time Updates**: Live data updates with modern AJAX interface

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher
* Advanced Coupons for WooCommerce (Free or Premium)
* PHP 7.4 or higher

= Admin Interface =

The plugin adds a "Virtual Coupon Usage" page under the WooCommerce menu in your WordPress admin. This interface provides:

* Comprehensive coupon usage dashboard
* Search and filter functionality
* Sortable columns for easy data management
* Export capabilities for reporting
* Detailed coupon information display

= HPOS Support =

This plugin is fully compatible with WooCommerce's High-Performance Order Storage (HPOS), ensuring optimal performance and future compatibility with WooCommerce updates.

= Developer Friendly =

The plugin is built with clean, well-documented code and follows WordPress coding standards. It includes proper hooks and filters for customization.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/acfw-virtual-coupon-usage-tracker` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Make sure WooCommerce and Advanced Coupons for WooCommerce are installed and active.
4. Navigate to WooCommerce > Virtual Coupon Usage to access the tracking interface.

== Frequently Asked Questions ==

= Does this plugin work without Advanced Coupons for WooCommerce? =

No, this plugin requires Advanced Coupons for WooCommerce to be installed and active as it specifically tracks virtual coupons created by that plugin.

= Is this plugin compatible with HPOS? =

Yes, this plugin is fully compatible with WooCommerce High-Performance Order Storage (HPOS) and will automatically detect and work with both traditional and HPOS order storage systems.

= Can I export the coupon usage data? =

The plugin provides comprehensive data display and filtering. Export functionality may be available depending on your specific needs.

= Will this plugin slow down my website? =

No, the plugin is optimized for performance with AJAX-based pagination and efficient database queries. It only loads admin resources when needed.

== Screenshots ==

1. Main virtual coupon usage dashboard showing all tracked coupons
2. Advanced filtering options for searching and sorting coupon data
3. Detailed coupon information with order associations
4. AJAX pagination for smooth navigation through large datasets

== Changelog ==

= 1.1.0 =
* Enhanced HPOS compatibility
* Improved AJAX pagination performance
* Added advanced filtering options
* Better error handling for missing order IDs
* UI/UX improvements
* Code optimization and security enhancements

= 1.0.0 =
* Initial release
* Basic virtual coupon usage tracking
* Order ID association
* Admin interface
* HPOS compatibility
* Search and filter functionality

== Upgrade Notice ==

= 1.1.0 =
This version includes important performance improvements and enhanced HPOS compatibility. Recommended for all users.

= 1.0.0 =
Initial release of ACFW Virtual Coupon Usage Tracker.