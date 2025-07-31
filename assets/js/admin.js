/**
 * Admin JavaScript for Virtual Coupon Usage Tracker
 */

(function($) {
    'use strict';

    // Plugin object
    var VCUT = {
        currentPage: 1,
        totalPages: 1,
        isLoading: false,
        perPage: 100,
        sorting: {
            orderBy: 'order_id',
            order: 'desc'
        },
        filters: {
            search: '',
            status: '',
            parent_coupon: '',
            date_from: '',
            date_to: ''
        },

        // Initialize the plugin
        init: function() {
            // Set default filter to "used" status
            this.filters.status = 'used';
            this.bindEvents();
            this.loadParentCoupons();
            this.updateSortingUI();
            this.loadCoupons();
        },

        // Bind event handlers
        bindEvents: function() {
            var self = this;

            // Filter button click
            $('#vcut-filter-btn').on('click', function() {
                self.applyFilters();
            });

            // Reset button click
            $('#vcut-reset-btn').on('click', function() {
                self.resetFilters();
            });

            // Search on Enter key
            $('#vcut-search').on('keypress', function(e) {
                if (e.which === 13) {
                    self.applyFilters();
                }
            });

            // Pagination buttons
            $('#vcut-prev-page').on('click', function() {
                if (self.currentPage > 1) {
                    self.loadPage(self.currentPage - 1);
                }
            });

            $('#vcut-next-page').on('click', function() {
                if (self.currentPage < self.totalPages) {
                    self.loadPage(self.currentPage + 1);
                }
            });

            // Page number clicks (delegated event)
            $(document).on('click', '.vcut-page-number', function(e) {
                e.preventDefault();
                var page = parseInt($(this).data('page'));
                if (page !== self.currentPage) {
                    self.loadPage(page);
                }
            });

            // Info button clicks (delegated event)
            $(document).on('click', '.vcut-info-btn', function() {
                var virtualCouponId = $(this).data('virtual-coupon-id');
                self.showMissingOrderReasons(virtualCouponId);
            });

            // Modal close events
            $('.vcut-modal-close').on('click', function() {
                self.closeModal();
            });

            $(document).on('click', '.vcut-modal', function(e) {
                if (e.target === this) {
                    self.closeModal();
                }
            });

            // Close modal on Escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) {
                    self.closeModal();
                    self.closeConfirmDialog();
                    self.closeSuccessDialog();
                }
            });

            // Confirmation dialog events
            $('#vcut-confirm-yes').on('click', function() {
                if (self.confirmCallback) {
                    self.confirmCallback();
                    self.confirmCallback = null;
                }
                self.closeConfirmDialog();
            });

            $('#vcut-confirm-no').on('click', function() {
                self.confirmCallback = null;
                self.closeConfirmDialog();
            });

            // Success dialog events
            $('#vcut-success-ok').on('click', function() {
                self.closeSuccessDialog();
            });

            // Notification close events
            $(document).on('click', '.vcut-notification-close', function() {
                self.closeNotification($(this).closest('.vcut-notification'));
            });

            // Column sorting events
            $(document).on('click', '.vcut-sort-link', function(e) {
                e.preventDefault();
                var $header = $(this).closest('.column-sortable');
                var sortField = $header.data('sort');
                self.handleSort(sortField);
            });

            $(document).on('click', '.sorting-indicator', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $header = $(this).closest('.column-sortable');
                var sortField = $header.data('sort');
                var order = $(this).data('order');
                self.setSorting(sortField, order);
            });

            // Per page dropdown change
            $('#vcut-per-page').on('change', function() {
                self.perPage = parseInt($(this).val());
                self.currentPage = 1;
                self.loadCoupons();
            });

            // Error coupons stat card click
            $('.vcut-error-coupons').on('click', function() {
                self.filters.status = $(this).data('filter');
                $('#vcut-status').val(self.filters.status);
                self.currentPage = 1;
                self.loadCoupons();
            });

            // Actions dropdown
            $(document).on('click', '.vcut-actions-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close all other dropdowns
                $('.vcut-actions-menu').removeClass('show');
                
                // Toggle current dropdown
                $(this).siblings('.vcut-actions-menu').toggleClass('show');
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function() {
                $('.vcut-actions-menu').removeClass('show');
            });

            // Change coupon status action
            $(document).on('click', '.vcut-change-status', function(e) {
                e.preventDefault();
                var couponId = $(this).data('coupon-id');
                var newStatus = $(this).data('new-status');
                self.changeCouponStatus(couponId, newStatus);
            });

            // Edit parent coupon action
            $(document).on('click', '.vcut-edit-parent-coupon', function(e) {
                e.preventDefault();
                var couponId = $(this).data('parent-coupon-id');
                self.editParentCoupon(couponId);
            });
        },

        // Apply filters and reload data
        applyFilters: function() {
            this.filters.search = $('#vcut-search').val();
            this.filters.status = $('#vcut-status').val();
            this.filters.parent_coupon = $('#vcut-parent-coupon').val();
            this.filters.date_from = $('#vcut-date-from').val();
            this.filters.date_to = $('#vcut-date-to').val();
            
            this.currentPage = 1;
            this.loadCoupons();
        },

        // Reset filters and reload data
        resetFilters: function() {
            $('#vcut-search').val('');
            $('#vcut-status').val('used');
            $('#vcut-parent-coupon').val('');
            $('#vcut-date-from').val('');
            $('#vcut-date-to').val('');
            
            this.filters = {
                search: '',
                status: 'used',
                parent_coupon: '',
                date_from: '',
                date_to: ''
            };
            
            this.currentPage = 1;
            this.loadCoupons();
        },

        // Load specific page
        loadPage: function(page) {
            this.currentPage = page;
            this.loadCoupons();
        },

        // Load coupons data via AJAX
        loadCoupons: function() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            this.showLoading();

            var self = this;
            var data = {
                action: 'vcut_get_coupons',
                nonce: vcut_ajax.nonce,
                page: this.currentPage,
                per_page: this.perPage,
                search: this.filters.search,
                status: this.filters.status,
                parent_coupon: this.filters.parent_coupon,
                date_from: this.filters.date_from,
                date_to: this.filters.date_to,
                order_by: this.sorting.orderBy,
                order: this.sorting.order
            };

            $.post(vcut_ajax.ajax_url, data, function(response) {
                self.isLoading = false;
                self.hideLoading();

                if (response.success) {
                    self.totalPages = response.data.pages;
                    self.updateTable(response.data.html);
                    self.updatePagination(response.data.total, response.data.current_page, response.data.pages);
                } else {
                    self.showError(response.data.message || vcut_ajax.error_text);
                }
            }).fail(function() {
                self.isLoading = false;
                self.hideLoading();
                self.showError(vcut_ajax.error_text);
            });
        },

        // Show missing order reasons modal
        showMissingOrderReasons: function(virtualCouponId) {
            var self = this;
            
            $('#vcut-modal').show();
            $('#vcut-modal-loading').show();
            $('#vcut-modal-content').empty();

            var data = {
                action: 'vcut_get_missing_order_reasons',
                nonce: vcut_ajax.nonce,
                virtual_coupon_id: virtualCouponId
            };

            $.post(vcut_ajax.ajax_url, data, function(response) {
                $('#vcut-modal-loading').hide();

                if (response.success) {
                    $('#vcut-modal-content').html(response.data.html);
                } else {
                    $('#vcut-modal-content').html('<p class="error">' + (response.data.message || vcut_ajax.error_text) + '</p>');
                }
            }).fail(function() {
                $('#vcut-modal-loading').hide();
                $('#vcut-modal-content').html('<p class="error">' + vcut_ajax.error_text + '</p>');
            });
        },

        // Close modal
        closeModal: function() {
            $('#vcut-modal').hide();
            $('#vcut-modal-content').empty();
        },

        // Update table with new data
        updateTable: function(html) {
            $('#vcut-results-tbody').html(html);
        },

        // Update pagination controls
        updatePagination: function(total, currentPage, totalPages) {
            var self = this;
            
            // Update pagination info
            var start = ((currentPage - 1) * 20) + 1;
            var end = Math.min(currentPage * 20, total);
            var infoText = '';
            
            if (total > 0) {
                infoText = 'Showing ' + start + ' to ' + end + ' of ' + total + ' entries';
            } else {
                infoText = 'No entries found';
            }
            
            $('#vcut-pagination-text').text(infoText);

            // Update pagination buttons
            $('#vcut-prev-page').prop('disabled', currentPage <= 1);
            $('#vcut-next-page').prop('disabled', currentPage >= totalPages);

            // Update page numbers
            this.updatePageNumbers(currentPage, totalPages);
        },

        // Update page number buttons
        updatePageNumbers: function(currentPage, totalPages) {
            var html = '';
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(totalPages, currentPage + 2);

            // Add first page if not in range
            if (startPage > 1) {
                html += '<a href="#" class="vcut-page-number" data-page="1">1</a>';
                if (startPage > 2) {
                    html += '<span class="vcut-page-ellipsis">...</span>';
                }
            }

            // Add page numbers in range
            for (var i = startPage; i <= endPage; i++) {
                var classes = 'vcut-page-number';
                if (i === currentPage) {
                    classes += ' current';
                }
                html += '<a href="#" class="' + classes + '" data-page="' + i + '">' + i + '</a>';
            }

            // Add last page if not in range
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span class="vcut-page-ellipsis">...</span>';
                }
                html += '<a href="#" class="vcut-page-number" data-page="' + totalPages + '">' + totalPages + '</a>';
            }

            $('#vcut-page-numbers').html(html);
        },

        // Show loading indicator
        showLoading: function() {
            $('#vcut-loading').show();
            $('#vcut-results-table').hide();
        },

        // Hide loading indicator
        hideLoading: function() {
            $('#vcut-loading').hide();
            $('#vcut-results-table').show();
        },

        // Show error message
        showError: function(message) {
            var errorHtml = '<tr><td colspan="8" class="vcut-error" style="text-align: center; padding: 40px; color: #d63384;">' + 
                           '<strong>Error:</strong> ' + message + '</td></tr>';
            $('#vcut-results-tbody').html(errorHtml);
        },

        // Load parent coupons for dropdown
        loadParentCoupons: function() {
            var self = this;
            var data = {
                action: 'vcut_get_parent_coupons',
                nonce: vcut_ajax.nonce
            };

            $.post(vcut_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    var options = '<option value="">' + vcut_ajax.all_coupons_text + '</option>';
                    $.each(response.data.coupons, function(index, coupon) {
                        options += '<option value="' + coupon.ID + '">' + coupon.post_title + '</option>';
                    });
                    $('#vcut-parent-coupon').html(options);
                }
            });
        },

        // Change coupon status
        changeCouponStatus: function(couponId, newStatus) {
            var self = this;
            var statusLabels = {
                'pending': vcut_ajax.status_pending || 'Pending',
                'used': vcut_ajax.status_used || 'Used',
                'unlimited': vcut_ajax.status_unlimited || 'Unlimited'
            };
            
            var confirmMessage = vcut_ajax.confirm_status_change_text.replace('%s', statusLabels[newStatus]);
            
            this.showConfirmDialog(
                vcut_ajax.confirm_title || 'Confirm Status Change',
                confirmMessage,
                function() {
                    // User confirmed - proceed with status change
                    var data = {
                        action: 'vcut_change_coupon_status',
                        nonce: vcut_ajax.nonce,
                        coupon_id: couponId,
                        new_status: newStatus
                    };

                    $.post(vcut_ajax.ajax_url, data, function(response) {
                        if (response.success) {
                            // Show success as modal dialog instead of notification
                            self.showSuccessDialog(vcut_ajax.success_title || 'Success', response.data.message);
                            self.loadCoupons(); // Reload the table
                        } else {
                            self.showNotification('error', vcut_ajax.error_title || 'Error', response.data.message || vcut_ajax.error_text);
                        }
                    }).fail(function() {
                        self.showNotification('error', vcut_ajax.error_title || 'Error', vcut_ajax.error_text);
                    });
                }
            );
        },

        // Edit parent coupon (open WP coupon editor)
        editParentCoupon: function(couponId) {
            if (!couponId || couponId === '0') {
                this.showNotification('error', vcut_ajax.error_title || 'Error', vcut_ajax.invalid_coupon_text || 'Invalid coupon ID');
                return;
            }
            
            var editUrl = vcut_ajax.admin_url + 'post.php?action=edit&post=' + couponId;
            window.open(editUrl, '_blank');
        },

        // Show confirmation dialog
        showConfirmDialog: function(title, message, callback) {
            $('#vcut-confirm-title').text(title);
            $('#vcut-confirm-message').text(message);
            this.confirmCallback = callback;
            $('#vcut-confirm-dialog').show();
        },

        // Close confirmation dialog
        closeConfirmDialog: function() {
            $('#vcut-confirm-dialog').hide();
            this.confirmCallback = null;
        },

        // Show success dialog (similar to confirm dialog)
        showSuccessDialog: function(title, message) {
            $('#vcut-success-title').text(title);
            $('#vcut-success-message').text(message);
            $('#vcut-success-dialog').show();
        },

        // Close success dialog
        closeSuccessDialog: function() {
            $('#vcut-success-dialog').hide();
        },

        // Show notification
        showNotification: function(type, title, message, duration) {
            duration = duration || 5000; // Default 5 seconds
            
            var icons = {
                'success': '✓',
                'error': '✕',
                'warning': '⚠',
                'info': 'ℹ'
            };
            
            var notificationId = 'vcut-notification-' + Date.now();
            var notificationHtml = '<div id="' + notificationId + '" class="vcut-notification ' + type + '">' +
                '<div class="vcut-notification-icon">' + (icons[type] || 'ℹ') + '</div>' +
                '<div class="vcut-notification-content">' +
                '<div class="vcut-notification-title">' + title + '</div>' +
                '<div class="vcut-notification-message">' + message + '</div>' +
                '</div>' +
                '<button class="vcut-notification-close" type="button">×</button>' +
                '</div>';
            
            $('#vcut-notifications').append(notificationHtml);
            
            // Auto-remove after duration
            var self = this;
            setTimeout(function() {
                self.closeNotification($('#' + notificationId));
            }, duration);
        },



        // Close notification
        closeNotification: function($notification) {
            if ($notification.length) {
                $notification.addClass('removing');
                setTimeout(function() {
                    $notification.remove();
                }, 300); // Match animation duration
            }
        },

        // Handle column sorting
        handleSort: function(sortField) {
            if (this.sorting.orderBy === sortField) {
                // Toggle order if same field
                this.sorting.order = this.sorting.order === 'asc' ? 'desc' : 'asc';
            } else {
                // New field - default to desc for order_id, asc for others
                this.sorting.orderBy = sortField;
                this.sorting.order = sortField === 'order_id' ? 'desc' : 'asc';
            }
            
            this.updateSortingUI();
            this.currentPage = 1;
            this.loadCoupons();
        },

        // Set specific sorting
        setSorting: function(sortField, order) {
            this.sorting.orderBy = sortField;
            this.sorting.order = order;
            this.updateSortingUI();
            this.currentPage = 1;
            this.loadCoupons();
        },

        // Update sorting UI indicators
        updateSortingUI: function() {
            // Reset all sorting indicators
            $('.column-sortable').removeClass('sorted asc desc');
            $('.sorting-indicator').removeClass('active');
            
            // Set active sorting
            var $activeHeader = $('.column-sortable[data-sort="' + this.sorting.orderBy + '"]');
            if ($activeHeader.length) {
                $activeHeader.addClass('sorted ' + this.sorting.order);
                $activeHeader.find('.sorting-indicator[data-order="' + this.sorting.order + '"]').addClass('active');
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        // Only initialize on the virtual coupon usage page
        if ($('#vcut-results-table').length) {
            VCUT.init();
        }
    });

    // Expose VCUT object globally for debugging
    window.VCUT = VCUT;

})(jQuery); 