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
                processing: true,
                serverSide: true,
                ajax: window.location.pathname,
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[2, 'desc']],
                columns: [
                    { data: 'quote_number_link', name: 'quote_number', responsivePriority: 1 },
                    { data: 'company_display', name: 'company_name', responsivePriority: 2 },
                    { data: 'quote_date_formatted', name: 'quote_date', responsivePriority: 4 },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'quote-amount',
                        responsivePriority: 5,
                        render: function (value, type) {
                            if (type !== 'display') return value;
                            return '$' + (Number(value) || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    { data: 'status_display', name: 'status', responsivePriority: 3 },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, responsivePriority: 1 }
                ],
                columnDefs: [
                    { orderable: false, targets: [5] }
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
            var csrfToken   = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Delete Quotation?',
                text: 'Are you sure you want to delete ' + quoteNumber + '? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);

                $.ajax({
                    url: $btn.data('url') || '/quotations/' + quoteId,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#quotations-table')) {
                            $('#quotations-table').DataTable().ajax.reload(null, false);
                        }
                        showNotification('success', response.message || 'Quotation deleted successfully.');
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Failed to delete quotation. Please try again.';
                        showNotification('error', msg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
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

        // ---------------------------------------------------------------------
        // Client Phone Search & Autofill (Temporary Dummy Data)
        // ---------------------------------------------------------------------
        var dummyClients = [
            {
                phone: '0412345678',
                contact_person: 'John Smith',
                company_name: 'Acme Cleaning Co',
                email: 'john@acmecleaning.com.au',
                address: '123 Test Street, Sydney NSW 2000'
            },
            {
                phone: '0423456789',
                contact_person: 'Sarah Jenkins',
                company_name: 'Prime Corporate Office',
                email: 'sarah@primeoffice.com.au',
                address: '45 George Street, Parramatta NSW 2150'
            },
            {
                phone: '0434567890',
                contact_person: 'Michael Wong',
                company_name: 'Harborview Medical Clinic',
                email: 'm.wong@harborview.com.au',
                address: '88 Pacific Highway, North Sydney NSW 2060'
            },
            {
                phone: '0445678901',
                contact_person: 'Emma Davis',
                company_name: '',
                email: 'emma.davis@gmail.com',
                address: '12 Ocean View Road, Manly NSW 2095'
            },
            {
                phone: '0456789012',
                contact_person: 'David Miller',
                company_name: 'Miller Logistics Hub',
                email: 'david@millerlogistics.com.au',
                address: '10 Warehouse Way, Eastern Creek NSW 2766'
            },
            {
                phone: '0426003944',
                contact_person: 'Irfan Chowdhury',
                company_name: 'Dust2Glow Cleaning',
                email: 'info@dust2glowcleaning.com.au',
                address: 'Suite 4, 150 Pitt Street, Sydney NSW 2000'
            }
        ];

        var $phoneInput = $('#phone');
        var $dropdown = $('#phone-search-dropdown');

        function cleanPhone(val) {
            return (val || '').replace(/[^0-9]/g, '');
        }

        function renderClientSuggestions(matches) {
            if (!$dropdown.length) return;

            if (!matches.length) {
                $dropdown.html('<div class="phone-autocomplete-empty"><i class="fas fa-info-circle mr-1"></i> No matching clients found</div>').show();
                return;
            }

            var html = '';
            matches.forEach(function (client) {
                var emailBadge = client.email
                    ? '<span class="client-email"><i class="far fa-envelope mr-1 text-muted"></i>' + $('<div>').text(client.email).html() + '</span>'
                    : '';

                var clientJson = JSON.stringify(client).replace(/"/g, '&quot;');

                html += [
                    '<div class="phone-autocomplete-item" data-client="' + clientJson + '">',
                    '    <div class="phone-autocomplete-phone">',
                    '        <i class="fas fa-phone-alt text-primary"></i>',
                    '        <span>' + $('<div>').text(client.phone).html() + '</span>',
                    '    </div>',
                    '    <div class="phone-autocomplete-meta">',
                    '        <span class="client-name"><i class="far fa-user mr-1 text-muted"></i>' + $('<div>').text(client.contact_person).html() + '</span>',
                    '        ' + emailBadge,
                    '    </div>',
                    '</div>'
                ].join('');
            });

            $dropdown.html(html).show();
        }

        $phoneInput.on('input focus', function () {
            // Remove any spaces from phone input
            var raw = $(this).val().replace(/\s+/g, '');
            if ($(this).val() !== raw) {
                $(this).val(raw);
            }

            var cleaned = cleanPhone(raw);

            if (cleaned.length < 1 && raw.length < 1) {
                $dropdown.hide().empty();
                return;
            }

            var matches = dummyClients.filter(function (c) {
                var cClean = cleanPhone(c.phone);
                var nameMatch = c.contact_person.toLowerCase().indexOf(raw.toLowerCase()) !== -1;
                var emailMatch = (c.email || '').toLowerCase().indexOf(raw.toLowerCase()) !== -1;
                var phoneMatch = cClean.indexOf(cleaned) !== -1 || c.phone.indexOf(raw) !== -1;
                return phoneMatch || nameMatch || emailMatch;
            });

            renderClientSuggestions(matches);
        });

        // Prevent any space entry in client profile modal phone fields
        $(document).on('input', '#modal_phone, #client_phone', function () {
            var raw = $(this).val().replace(/\s+/g, '');
            if ($(this).val() !== raw) {
                $(this).val(raw);
            }
        });

        // Click selection on a client suggestion
        $(document).on('click', '.phone-autocomplete-item', function (e) {
            e.preventDefault();
            var clientData = $(this).data('client');

            if (clientData) {
                var client = typeof clientData === 'string' ? JSON.parse(clientData) : clientData;

                $phoneInput.val(client.phone);
                $('#contact_person').val(client.contact_person);
                $('#company_name').val(client.company_name || '');
                $('#address').val(client.address);
                $('#email').val(client.email);

                // Add visual pulse effect on the autofilled fields
                var $autofilled = $('#contact_person, #company_name, #address, #email');
                $autofilled.addClass('field-autofilled');
                setTimeout(function () {
                    $autofilled.removeClass('field-autofilled');
                }, 1200);

                showNotification('success', 'Autofilled client details for ' + client.contact_person);
            }

            $dropdown.hide().empty();
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.phone-search-wrapper').length) {
                $dropdown.hide().empty();
            }
        });

        // Close dropdown on Escape key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                $dropdown.hide().empty();
            }
        });

        // -------------------------------------------------------------------------
        // Create Page: Status Radio Toggle (Draft vs Invoiced)
        // -------------------------------------------------------------------------
        function updateSubmitButtonLabel() {
            var isEdit = $('#form-is-edit').val() === '1';
            if (isEdit) {
                return;
            }

            var selectedStatus = $('input[name="status"]:checked').val() || 'draft';
            var $btnLabel = $('#btn-save-label');

            if ($btnLabel.length) {
                if (selectedStatus === 'invoiced') {
                    $btnLabel.text('Save & Download Invoiced');
                } else {
                    $btnLabel.text('Save & Download Draft');
                }
            }
        }

        $(document).on('change', 'input[name="status"]', function () {
            updateSubmitButtonLabel();
        });

        // Initialize button label on load
        updateSubmitButtonLabel();

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
