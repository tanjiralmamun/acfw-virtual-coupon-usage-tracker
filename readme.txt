=== ACFW Virtual Coupon Usage Tracker ===
Contributors: tanjiralmamun
Tags: woocommerce, coupons, advanced coupons, virtual coupons, tracking, usage
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced virtual coupon tracking with clickable analytics, status management, column sorting, and comprehensive filtering. Perfect for WooCommerce stores using Advanced Coupons.

== Description ==

ACFW Virtual Coupon Usage Tracker is a powerful WordPress plugin designed to enhance your WooCommerce store's coupon management capabilities. This plugin provides comprehensive tracking and monitoring of virtual coupon usage, specifically designed to work seamlessly with Advanced Coupons for WooCommerce.

= Key Features =

* **Clickable Analytics Dashboard**: Interactive statistics cards that instantly filter results
* **Status Management**: Change coupon status directly from the interface with confirmation dialogs
* **Advanced Column Sorting**: Sort by order ID, usage date, and other important fields
* **Comprehensive Action Menu**: Dropdown actions for status changes and parent coupon editing
* **Smart Filtering**: Filter by status, parent coupon, date range, search terms, and order association
* **Usage Date Intelligence**: Displays actual usage date vs estimated date with visual indicators
* **Interactive Notifications**: Toast notifications and modal confirmations for better UX
* **Customizable Pagination**: Choose items per page (20, 50, 100) for optimal viewing
* **HPOS Compatibility**: Fully compatible with WooCommerce High-Performance Order Storage
* **Real-time Updates**: Live data updates with modern AJAX interface

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher
* Advanced Coupons for WooCommerce (Free or Premium)
* PHP 7.4 or higher

= Admin Interface =

The plugin adds a "Virtual Coupon Usage" page under the WooCommerce menu in your WordPress admin. This interface provides:

* **Interactive Analytics Dashboard**: Clickable statistics cards for instant filtering
* **Advanced Data Management**: Sortable columns, customizable pagination, and status management
* **Comprehensive Search & Filtering**: Filter by status, parent coupon, date range, and order association
* **Intuitive Actions**: Dropdown menus for status changes, parent coupon editing, and more
* **Visual Feedback**: Usage dates with actual vs estimated indicators and error states
* **Modern UX**: Toast notifications, confirmation dialogs, and smooth interactions

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

= 1.1.1 =
* **New**: Clickable statistics cards for instant filtering - all analytics boxes now filter results with visual feedback
* **New**: Column sorting functionality for Order ID and Usage Date with visual indicators
* **New**: Status management system - change coupon status directly from the interface
* **New**: Comprehensive actions dropdown menu with status changes and parent coupon editing
* **New**: Parent coupon filtering dropdown loaded dynamically
* **New**: Usage date intelligence - shows actual usage date vs estimated with visual distinction
* **New**: Interactive notification system with toast notifications and confirmation dialogs
* **New**: Customizable pagination (20, 50, 100 items per page)
* **Enhancement**: Improved UI/UX with hover effects, smooth animations, and better visual feedback
* **Enhancement**: Enhanced error handling and user feedback
* **Enhancement**: Better default state management (starts with "Used" filter active)
* **Enhancement**: Improved responsive design for mobile devices
* **Fix**: Resolved merge conflicts and code consistency issues

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

= 1.1.1 =
Major UI/UX upgrade! All statistics cards are now clickable for instant filtering, added status management, column sorting, and many more interactive features. Highly recommended upgrade for better user experience.

= 1.1.0 =
This version includes important performance improvements and enhanced HPOS compatibility. Recommended for all users.

= 1.0.0 =
Initial release of ACFW Virtual Coupon Usage Tracker.