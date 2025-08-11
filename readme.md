# ACFW Virtual Coupon Usage Tracker

A powerful WordPress plugin for tracking virtual coupon usage with Advanced Coupons for WooCommerce. Features clickable analytics, status management, column sorting, and comprehensive filtering with modern UX.

## Description

ACFW Virtual Coupon Usage Tracker enhances your WooCommerce store's coupon management by providing detailed tracking and monitoring of virtual coupon usage. Designed specifically to work with Advanced Coupons for WooCommerce, this plugin offers a comprehensive solution for monitoring coupon performance and usage patterns.

## Key Features

### 🎯 Interactive Analytics Dashboard
- **Clickable Statistics Cards**: All analytics cards filter results instantly with visual feedback
- **Smart Default State**: Starts with "Used" coupons view for most common use case
- **Real-time Data**: Live updates as you interact with filters

### ⚙️ Advanced Management Tools
- **Status Management**: Change coupon status directly from the interface
- **Action Dropdown Menus**: Comprehensive actions for each coupon
- **Parent Coupon Editing**: Direct access to edit parent coupons
- **Bulk Operations**: Efficient management of multiple coupons

### 📊 Enhanced Data Viewing
- **Column Sorting**: Sort by Order ID, Usage Date, and more with visual indicators
- **Usage Date Intelligence**: Shows actual vs estimated usage dates
- **Customizable Pagination**: Choose 20, 50, or 100 items per page
- **Advanced Filtering**: Filter by status, parent coupon, date range, and order association

### 💫 Modern User Experience
- **Interactive Notifications**: Toast notifications and confirmation dialogs
- **Smooth Animations**: Hover effects and transitions throughout
- **Responsive Design**: Optimized for all device sizes
- **Visual Feedback**: Clear indicators for all user actions

### 🔧 Technical Excellence
- **HPOS Compatibility**: Fully compatible with WooCommerce High-Performance Order Storage
- **AJAX Powered**: Fast, responsive interface without page reloads
- **Secure Operations**: Proper nonce verification and permission checks
- **Clean Code**: Well-documented, WordPress standards compliant

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

#### 📊 Interactive Dashboard
- **Clickable Analytics Cards**: Instant filtering by clicking Total, Used, Pending, With Orders, or Without Orders
- **Visual Active States**: Clear indication of which filter is currently applied
- **Smart Defaults**: Starts with "Used" coupons for immediate productivity

#### 🔍 Advanced Filtering & Sorting
- **Multi-layered Filtering**: Search, status, parent coupon, date range, and order association
- **Column Sorting**: Click headers to sort by Order ID, Usage Date with visual sort indicators
- **Parent Coupon Dropdown**: Dynamically loaded list of all parent coupons
- **Date Intelligence**: Usage dates show actual vs estimated with visual distinction

#### ⚡ Enhanced Actions & Management
- **Status Management**: Change coupon status with confirmation dialogs
- **Action Menus**: Dropdown actions for each coupon (status changes, parent editing)
- **Direct Parent Editing**: Quick access to parent coupon editor in new tab
- **Bulk Status Updates**: Efficient coupon management workflow

#### 🎨 Modern UX Features
- **Toast Notifications**: Non-intrusive success/error feedback
- **Confirmation Dialogs**: Secure actions with user confirmation
- **Smooth Animations**: Hover effects and transitions throughout
- **Responsive Design**: Optimized for desktop, tablet, and mobile

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

## Changelog

### Version 1.1.1 - Major UI/UX Enhancement Release 🎉

#### 🆕 New Features
- **Clickable Statistics Cards**: All analytics boxes now filter results instantly with visual feedback
- **Column Sorting**: Sort by Order ID and Usage Date with visual indicators
- **Status Management System**: Change coupon status directly from the interface with confirmation dialogs
- **Comprehensive Actions Menu**: Dropdown menus with multiple actions per coupon
- **Parent Coupon Filtering**: Dynamic dropdown to filter by specific parent coupons
- **Usage Date Intelligence**: Shows actual usage date vs estimated with visual distinction
- **Interactive Notifications**: Toast notifications and modal confirmations for better UX
- **Customizable Pagination**: Choose 20, 50, or 100 items per page

#### ✨ Enhancements
- **Improved UI/UX**: Hover effects, smooth animations, and better visual feedback throughout
- **Enhanced Error Handling**: Better user feedback and error management
- **Smart Default State**: Starts with "Used" filter active for immediate productivity
- **Better Responsive Design**: Improved mobile and tablet experience
- **Visual Active States**: Clear indication of current filters and sort orders

#### 🔧 Technical Improvements
- **Code Consistency**: Resolved merge conflicts and improved code organization
- **Enhanced Security**: Better nonce verification and permission checks
- **Performance Optimizations**: More efficient database queries and AJAX operations
- **Modern JavaScript**: Updated admin.js with better event handling and state management

### Version 1.1.0 - Performance & Compatibility Release
- Enhanced HPOS compatibility
- Improved AJAX pagination performance
- Added advanced filtering options
- Better error handling for missing order IDs
- UI/UX improvements
- Code optimization and security enhancements

### Version 1.0.0 - Initial Release
- Basic virtual coupon usage tracking
- Order ID association
- Admin interface
- HPOS compatibility
- Search and filter functionality

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