/**
 * Quotations Management Module JavaScript
 * Handles DataTable initialization, dynamic line items, and real-time calculation logic
 */

$(document).ready(function () {
    // -------------------------------------------------------------------------
    // Helper: Toast Notifications
    // -------------------------------------------------------------------------
    function showNotification(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }

    // -------------------------------------------------------------------------
    // Helper: Currency Formatter
    // -------------------------------------------------------------------------
    function formatCurrency(number) {
        return '$' + (Number(number) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // -------------------------------------------------------------------------
    // 1. Quotations List View (DataTable & Actions)
    // -------------------------------------------------------------------------
    var $quotationsTable = $('#quotations-table');

    if ($quotationsTable.length) {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#quotations-table')) {
            $quotationsTable.DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[2, 'desc']], // Sort by Quote Date descending by default
                columnDefs: [
                    { orderable: false, targets: [5] } // Action column not orderable
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search quotations...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ quotations',
                    infoEmpty: 'No quotations found',
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });
        }

        // Delete Confirmation via SweetAlert2 → real AJAX DELETE
        $(document).on('click', '.js-quote-delete', function (e) {
            e.preventDefault();
            var quoteId     = $(this).data('id');
            var quoteNumber = $(this).data('number') || 'this quotation';
            var $btn        = $(this);
            var row         = $btn.closest('tr');
            var csrfToken   = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Delete Quotation?',
                html: 'Are you sure you want to delete <strong>' + quoteNumber + '</strong>?<br>This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '/quotations/' + quoteId,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        row.fadeOut(300, function () {
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#quotations-table')) {
                                $('#quotations-table').DataTable().row(row).remove().draw(false);
                            } else {
                                row.remove();
                            }
                        });
                        showNotification('success', response.message || 'Quotation deleted successfully.');
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Failed to delete quotation. Please try again.';
                        showNotification('error', msg);
                    }
                });
            });
        });
    }

    // -------------------------------------------------------------------------
    // 2. Create / Edit Quotation Form (Dynamic Items & Real-Time Calculation)
    // -------------------------------------------------------------------------
    var $quoteForm = $('#quotation-form');

    if ($quoteForm.length) {
        var $itemsTbody = $('#line-items-tbody');

        /**
         * Re-index table rows and form input names
         */
        function reindexRows() {
            $itemsTbody.find('tr.line-item-row').each(function (index) {
                $(this).find('.line-item-index').text(index + 1);
                $(this).find('.line-item-desc').attr('name', 'items[' + index + '][description]');
                $(this).find('.line-item-amount').attr('name', 'items[' + index + '][amount]');
            });
        }

        /**
         * Real-time calculation logic:
         * Subtotal = Sum(Amount ex GST)
         * GST = 10% of Subtotal
         * Total = Subtotal + GST
         */
        function calculateTotals() {
            var subtotal = 0;

            $itemsTbody.find('.line-item-amount').each(function () {
                var val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    subtotal += val;
                }
            });

            // Rounding to 2 decimal places
            var gst = Math.round((subtotal * 0.10) * 100) / 100;
            var total = Math.round((subtotal + gst) * 100) / 100;

            // Update text displays
            $('#calc-subtotal').text(formatCurrency(subtotal));
            $('#calc-gst').text(formatCurrency(gst));
            $('#calc-total').text(formatCurrency(total));

            // Update hidden form inputs
            $('#input-subtotal').val(subtotal.toFixed(2));
            $('#input-gst-amount').val(gst.toFixed(2));
            $('#input-total-amount').val(total.toFixed(2));
        }

        // Trigger calculation on input change
        $(document).on('input change keyup', '.line-item-amount', function () {
            calculateTotals();
        });

        // Add Line Item Button
        $('#btn-add-line-item').on('click', function (e) {
            e.preventDefault();
            var newIndex = $itemsTbody.find('tr.line-item-row').length;

            var newRowHtml = [
                '<tr class="line-item-row">',
                '    <td class="line-item-index text-center align-middle">' + (newIndex + 1) + '</td>',
                '    <td>',
                '        <input type="text" name="items[' + newIndex + '][description]" class="form-control line-item-desc" placeholder="Enter service description or line item" required>',
                '    </td>',
                '    <td style="width: 200px;">',
                '        <div class="amount-input-group">',
                '            <span class="currency-symbol">$</span>',
                '            <input type="number" step="0.01" min="0" name="items[' + newIndex + '][amount]" class="form-control line-item-amount text-right" placeholder="0.00" value="0.00" required>',
                '        </div>',
                '    </td>',
                '    <td class="text-center align-middle" style="width: 60px;">',
                '        <button type="button" class="btn btn-trash js-remove-line-item" title="Delete">',
                '            <i class="fas fa-trash-alt text-danger"></i>',
                '        </button>',
                '    </td>',
                '</tr>'
            ].join('\n');

            var $newRow = $(newRowHtml).hide();
            $itemsTbody.append($newRow);
            $newRow.fadeIn(200);

            reindexRows();
            calculateTotals();

            // Auto-focus the new description input
            $newRow.find('.line-item-desc').focus();
        });

        // Remove Line Item Button
        $(document).on('click', '.js-remove-line-item', function (e) {
            e.preventDefault();
            var rowCount = $itemsTbody.find('tr.line-item-row').length;

            if (rowCount <= 1) {
                showNotification('warning', 'At least one line item is required.');
                return;
            }

            var $row = $(this).closest('tr.line-item-row');
            $row.fadeOut(150, function () {
                $row.remove();
                reindexRows();
                calculateTotals();
            });
        });

        // "Save & Download PDF" / "Update & Download PDF" Button
        // Sync hidden calculation fields then let the form submit natively (full-page POST)
        $('#btn-save-pdf').on('click', function (e) {
            // Ensure latest totals are in the hidden fields before submit
            calculateTotals();

            // Let the browser perform the native form submission (no preventDefault)
        });

        // On form submit: sync totals then allow native browser POST
        $quoteForm.on('submit', function () {
            calculateTotals();
            // Do NOT call e.preventDefault() – allow the real POST to fire
        });

        // Initial Calculation on Load
        calculateTotals();
    }
});
