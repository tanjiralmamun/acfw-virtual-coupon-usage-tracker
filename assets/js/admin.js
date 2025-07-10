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
        filters: {
            search: '',
            status: '',
            date_from: '',
            date_to: ''
        },

        // Initialize the plugin
        init: function() {
            // Set default filter to "used" status
            this.filters.status = 'used';
            this.bindEvents();
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
                }
            });
        },

        // Apply filters and reload data
        applyFilters: function() {
            this.filters.search = $('#vcut-search').val();
            this.filters.status = $('#vcut-status').val();
            this.filters.date_from = $('#vcut-date-from').val();
            this.filters.date_to = $('#vcut-date-to').val();
            
            this.currentPage = 1;
            this.loadCoupons();
        },

        // Reset filters and reload data
        resetFilters: function() {
            $('#vcut-search').val('');
            $('#vcut-status').val('used');
            $('#vcut-date-from').val('');
            $('#vcut-date-to').val('');
            
            this.filters = {
                search: '',
                status: 'used',
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
                search: this.filters.search,
                status: this.filters.status,
                date_from: this.filters.date_from,
                date_to: this.filters.date_to
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