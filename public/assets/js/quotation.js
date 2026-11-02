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
                },
                drawCallback: function () {
                    var api = this.api();
                    var info = api.page.info();
                    if (info && $('.quotations-card-header p').length) {
                        $('.quotations-card-header p').text(info.recordsTotal.toLocaleString() + ' records found');
                    }
                }
            });
        }

        // Duplicate Quotation via SweetAlert2 → real AJAX POST
        $(document).on('click', '.js-quote-duplicate', function (e) {
            e.preventDefault();
            var $btn        = $(this);
            var quoteId     = $btn.data('id');
            var quoteNumber = $btn.data('number') || 'this quotation';
            var status      = ($btn.data('status') || 'draft').toLowerCase();
            var statusLabel = status === 'invoiced' ? 'Invoiced' : 'Draft';
            var url         = $btn.data('url') || ('/quotations/' + quoteId + '/duplicate');
            var csrfToken   = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: 'Duplicate Quotation?',
                html: 'Are you sure you want to duplicate <strong>' + quoteNumber + '</strong>?<br>' +
                      '<span class="text-muted" style="font-size: 13px;">A new quotation will be created with today\'s date retaining the <strong>' + statusLabel + '</strong> stage.</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0066FF',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-copy mr-1"></i> Yes, duplicate it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#quotations-table')) {
                            $('#quotations-table').DataTable().ajax.reload(null, false);
                        }
                        showNotification('success', response.message || 'Quotation duplicated successfully.');
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Failed to duplicate quotation. Please try again.';
                        showNotification('error', msg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });

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
    // 2. Clients Directory List View (DataTable & Modal CRUD)
    // -------------------------------------------------------------------------
    var $clientsTable = $('#clients-table');

    if ($clientsTable.length && $.fn.DataTable && !$.fn.DataTable.isDataTable('#clients-table')) {
        var clientsAjaxUrl = $clientsTable.data('ajax-url') || '/quotations/clients';

        $clientsTable.DataTable({
            processing: true,
            serverSide: true,
            ajax: clientsAjaxUrl,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[0, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, width: '45px' },
                { data: 'contact_person_cell', name: 'contact_person' },
                { data: 'company_name_badge', name: 'company_name' },
                { data: 'phone_link', name: 'phone' },
                { data: 'email_link', name: 'email' },
                { data: 'address_display', name: 'address' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center', width: '110px' }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search clients...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ clients',
                infoEmpty: 'No clients found',
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    }

    // Reset Client Modal for "Add Client"
    $(document).on('click', '[data-target="#clientProfileModal"]', function () {
        if (!$(this).hasClass('js-client-edit')) {
            $('#client_id, #modal_client_id').val('');
            $('#clientModalTitleText').text('Create Client Profile');
            $('#btn-save-client-text, #btn-modal-save-client-text').text('Save Client Profile');

            var $form = $('#client-profile-form');
            if ($form.length) {
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').text('').hide();
            }
        }
    });

    // Populate Client Modal for Edit
    $(document).on('click', '.js-client-edit', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var id = $btn.data('id');
        var contactPerson = $btn.data('contact_person') || '';
        var companyName = $btn.data('company_name') || '';
        var phone = $btn.data('phone') || '';
        var email = $btn.data('email') || '';
        var address = $btn.data('address') || '';

        $('#client_id, #modal_client_id').val(id);
        $('#client_contact_person, #modal_contact_person').val(contactPerson);
        $('#client_company_name, #modal_company_name').val(companyName);
        $('#client_phone, #modal_phone').val(phone);
        $('#client_email, #modal_email').val(email);
        $('#client_address, #modal_address').val(address);

        $('#clientModalTitleText').text('Edit Client Profile');
        $('#btn-save-client-text, #btn-modal-save-client-text').text('Update Client Profile');

        var $form = $('#client-profile-form');
        if ($form.length) {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('').hide();
        }

        $('#clientProfileModal').modal('show');
    });

    // Submit Client Profile Form (Create & Update) via AJAX
    $(document).on('submit', '#client-profile-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btn-save-client-profile');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var clientId = $('#client_id').val() || $('#modal_client_id').val();
        var isUpdate = Boolean(clientId);

        var url = isUpdate ? '/quotations/clients/' + clientId : '/quotations/clients';
        var method = isUpdate ? 'PUT' : 'POST';

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('').hide();
        $btn.prop('disabled', true);

        var payload = {
            contact_person: ($('#client_contact_person').length ? $('#client_contact_person') : $('#modal_contact_person')).val(),
            company_name: ($('#client_company_name').length ? $('#client_company_name') : $('#modal_company_name')).val(),
            phone: ($('#client_phone').length ? $('#client_phone') : $('#modal_phone')).val(),
            email: ($('#client_email').length ? $('#client_email') : $('#modal_email')).val(),
            address: ($('#client_address').length ? $('#client_address') : $('#modal_address')).val()
        };

        $.ajax({
            url: url,
            type: method,
            data: payload,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                $('#clientProfileModal').modal('hide');
                $form[0].reset();
                $('#client_id, #modal_client_id').val('');

                showNotification('success', res.message || 'Client profile saved successfully.');

                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#clients-table')) {
                    $('#clients-table').DataTable().ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (field) {
                        var $input = $('#client_' + field + ', #modal_' + field);
                        var $feedback = $('#error-' + field + ', #error-modal-' + field);
                        $input.addClass('is-invalid');
                        $feedback.text(errors[field][0]).show();
                    });
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save client profile.';
                    showNotification('error', msg);
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '#btn-save-client-profile', function (e) {
        var $form = $('#client-profile-form');
        if ($form.length) {
            $form.submit();
        }
    });

    // Delete Client Confirmation via SweetAlert2
    $(document).on('click', '.js-client-delete', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var clientId = $btn.data('id');
        var clientName = $btn.data('name') || 'this client';
        var url = $btn.data('url') || ('/quotations/clients/' + clientId);
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Delete Client?',
            text: 'Are you sure you want to delete ' + clientName + '? This action cannot be undone.',
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
                url: url,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    showNotification('success', res.message || 'Client deleted successfully.');
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#clients-table')) {
                        $('#clients-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete client.';
                    showNotification('error', msg);
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });
    });

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
        // Client Phone Search & Autofill (Live AJAX from Database)
        // ---------------------------------------------------------------------
        var $phoneInput = $('#phone');
        var $dropdown = $('#phone-search-dropdown');
        var phoneSearchTimer = null;

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
            // Strictly allow numbers (0-9) and plus (+) only
            var raw = $(this).val();
            var cleaned = raw.replace(/[^\d+]/g, '');
            if (cleaned.indexOf('+') > 0) {
                cleaned = (cleaned[0] === '+' ? '+' : '') + cleaned.replace(/\+/g, '');
            }
            if ($(this).val() !== cleaned) {
                $(this).val(cleaned);
            }

            var term = cleaned.trim();

            if (term.length < 1) {
                $dropdown.hide().empty();
                return;
            }

            clearTimeout(phoneSearchTimer);
            phoneSearchTimer = setTimeout(function () {
                $.ajax({
                    url: '/quotations/clients/search',
                    type: 'GET',
                    data: { q: term },
                    success: function (res) {
                        var clients = res.clients || [];
                        renderClientSuggestions(clients);
                    },
                    error: function () {
                        $dropdown.hide().empty();
                    }
                });
            }, 200);
        });

        // Strictly allow numbers (0-9) and plus (+) only in client profile modal phone fields
        $(document).on('input', '#modal_phone, #client_phone', function () {
            var raw = $(this).val();
            var cleaned = raw.replace(/[^\d+]/g, '');
            if (cleaned.indexOf('+') > 0) {
                cleaned = (cleaned[0] === '+' ? '+' : '') + cleaned.replace(/\+/g, '');
            }
            if ($(this).val() !== cleaned) {
                $(this).val(cleaned);
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

                // showNotification('success', 'Autofilled client details for ' + client.contact_person);
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
