# ACFW Virtual Coupon Usage Tracker

A powerful WordPress plugin for tracking virtual coupon usage with Advanced Coupons for WooCommerce. Features comprehensive monitoring, AJAX pagination, and HPOS compatibility.

## Description

ACFW Virtual Coupon Usage Tracker enhances your WooCommerce store's coupon management by providing detailed tracking and monitoring of virtual coupon usage. Designed specifically to work with Advanced Coupons for WooCommerce, this plugin offers a comprehensive solution for monitoring coupon performance and usage patterns.

## Key Features

- **Virtual Coupon Tracking**: Complete monitoring of all virtual coupon usage
- **Order ID Association**: Track which orders used specific virtual coupons
- **Missing Order ID Detection**: Identify and display reasons for missing order associations
- **AJAX Pagination**: Fast, responsive pagination without page reloads
- **HPOS Compatibility**: Fully compatible with WooCommerce High-Performance Order Storage
- **Advanced Filtering**: Filter by status, parent coupon, date range, and search terms
- **User Information**: Display user details associated with coupon usage
- **Real-time Updates**: Live data updates with modern AJAX interface

## Requirements

- WordPress 5.0+
- WooCommerce 5.0+
- Advanced Coupons for WooCommerce (Free or Premium)
- PHP 7.4+

## Installation

1. Upload the plugin files to `/wp-content/plugins/acfw-virtual-coupon-usage-tracker/`
2. Activate the plugin through the WordPress admin
3. Ensure WooCommerce and Advanced Coupons are installed and active
4. Navigate to **WooCommerce > Virtual Coupon Usage** to access the interface

## Usage

### Admin Interface

The plugin adds a "Virtual Coupon Usage" page under the WooCommerce menu, providing:

- Comprehensive coupon usage dashboard
- Search and filter functionality
- Sortable columns for data management
- Detailed coupon information display
- Export capabilities for reporting

### Filtering Options

- **Search**: Find specific coupons by code or user information
- **Status Filter**: Filter by coupon status (active, used, expired, etc.)
- **Date Range**: Filter by creation or usage date
- **Parent Coupon**: Filter by parent coupon relationship
- **Order Association**: View coupons with or without order IDs

## HPOS Compatibility

This plugin is fully compatible with WooCommerce's High-Performance Order Storage (HPOS), automatically detecting and working with both traditional and HPOS order storage systems for optimal performance.

## Developer Information

Built with clean, well-documented code following WordPress coding standards. The plugin includes proper hooks and filters for customization and extends functionality through:

- Object-oriented PHP architecture
- Proper WordPress hooks and filters
- Secure database operations
- AJAX-powered admin interface
- Responsive design principles

## File Structure

```
acfw-virtual-coupon-usage-tracker/
├── acfw-virtual-coupon-usage-tracker.php  # Main plugin file
├── includes/
│   ├── class-vcut-admin.php               # Admin interface
│   ├── class-vcut-ajax.php                # AJAX handlers
│   └── class-vcut-database.php            # Database operations
├── assets/
│   ├── css/
│   │   └── admin.css                      # Admin styles
│   └── js/
│       └── admin.js                       # Admin JavaScript
├── readme.txt                             # WordPress.org readme
└── readme.md                              # GitHub readme
```

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## Support

For support and questions:
- Plugin URI: [https://tanjirsdev.com/plugins/virtual-coupon-usage-tracker](https://tanjirsdev.com/plugins/virtual-coupon-usage-tracker)
- Author: [Tanjir Al Mamun](https://tanjirsdev.com)

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2024 Tanjir Al Mamun

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```