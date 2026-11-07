@extends('layouts.app')

@section('title', 'Client Profiles - Quotation Management')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/quotation.css') }}?v={{ filemtime(public_path('assets/css/quotation.css')) }}">
    <style>
        .clients-table thead th {
            padding: 11px 12px !important;
            font-size: 12px;
            white-space: nowrap;
        }
        .clients-table tbody td {
            padding: 10px 12px !important;
            font-size: 13px;
            vertical-align: middle;
        }
        .clients-table .quotation-actions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
        }
        .clients-table .quotation-action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .clients-address-cell {
            display: inline-block;
            max-width: 170px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
<div class="quotations-page">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb quotation-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Clients</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="quotations-header">
        <div>
            <h1 class="quotations-header-title">Clients Directory</h1>
            <p class="quotations-header-sub">Manage client contact details and company profiles for quotations and proposals</p>
        </div>
        <div class="quotations-header-actions d-flex align-items-center flex-wrap">
            <button type="button" class="btn btn-dark quotations-client-btn mr-2" id="btn-open-create-client" data-toggle="modal" data-target="#clientProfileModal">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                <span>Add Client</span>
            </button>
        </div>
    </div>

    {{-- Clients Table Card --}}
    <div class="quotations-card">
        <div class="quotations-card-header">
            <div>
                <h2>All Clients</h2>
                <p>Saved client profiles available for quotations</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="clients-table" class="table table-hover table-bordered nowrap clients-table quotations-table" style="width: 100%" data-ajax-url="{{ route('quotations.clients') }}">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Contact Person</th>
                        <th>Company Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-center" style="width: 95px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Client Profile Modal --}}
    <div class="modal fade quotation-modal" id="clientProfileModal" tabindex="-1" role="dialog" aria-labelledby="clientProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientProfileModalLabel">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span id="clientModalTitleText">Create Client Profile</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="client-profile-form" action="{{ route('quotations.clients.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" id="client_id" name="client_id" value="">

                    <div class="modal-body">
                        <div class="form-row">
                            {{-- Contact Person * --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_contact_person">Contact Person <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="client_contact_person"
                                       name="contact_person"
                                       placeholder="Enter contact person name"
                                       required>
                                <div class="invalid-feedback" id="error-contact_person"></div>
                            </div>

                            {{-- Company Name (Optional / Nullable) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_company_name">Company Name <span class="text-muted font-weight-normal">(Optional)</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="client_company_name"
                                       name="company_name"
                                       placeholder="Enter company name">
                                <div class="invalid-feedback" id="error-company_name"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Phone * (Unique) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_phone">Phone <span class="text-danger">*</span></label>
                                <input type="tel"
                                       class="form-control"
                                       id="client_phone"
                                       name="phone"
                                       placeholder="Enter phone number"
                                       required>
                                <div class="invalid-feedback" id="error-phone"></div>
                            </div>

                            {{-- Email * (Unique) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_email">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control"
                                       id="client_email"
                                       name="email"
                                       placeholder="Enter email address"
                                       required>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Address * --}}
                            <div class="form-group col-12 mb-0">
                                <label for="client_address">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control"
                                          id="client_address"
                                          name="address"
                                          rows="3"
                                          placeholder="Enter complete site address"
                                          required></textarea>
                                <div class="invalid-feedback" id="error-address"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary quotations-primary-btn" id="btn-save-client-profile">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            <span id="btn-save-client-text">Save Client Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            var clientsBaseUrl = @json(route('quotations.clients'));
            var $clientsTable = $('#clients-table');
            var $modal = $('#clientProfileModal');
            var $form = $('#client-profile-form');
            var $submitBtn = $('#btn-save-client-profile');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            function showToast(type, message) {
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

            var clientsDataTable = $clientsTable.DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: clientsBaseUrl,
                    type: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                },
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false, width: '40px', responsivePriority: 2 },
                    { data: 'contact_person_cell', name: 'contact_person', responsivePriority: 1 },
                    { data: 'company_name_badge', name: 'company_name', responsivePriority: 4 },
                    { data: 'phone_link', name: 'phone', responsivePriority: 2 },
                    { data: 'email_link', name: 'email', responsivePriority: 5 },
                    { data: 'address_display', name: 'address', responsivePriority: 6 },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center', width: '95px', responsivePriority: 1 }
                ],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [6] }
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

            function resetClientModal() {
                $form[0].reset();
                $('#client_id').val('');
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').text('').hide();
                $('#clientModalTitleText').text('Create Client Profile');
                $('#btn-save-client-text').text('Save Client Profile');
            }

            $('#btn-open-create-client').on('click', function () {
                resetClientModal();
            });

            // Edit Client Profile Modal
            $(document).on('click', '.js-client-edit', function (e) {
                e.preventDefault();
                resetClientModal();

                var $btn = $(this);
                var id = $btn.data('id');
                $('#client_id').val(id);
                $('#client_contact_person').val($btn.data('contact_person') || '');
                $('#client_company_name').val($btn.data('company_name') || '');
                $('#client_phone').val($btn.data('phone') || '');
                $('#client_email').val($btn.data('email') || '');
                $('#client_address').val($btn.data('address') || '');

                $('#clientModalTitleText').text('Edit Client Profile');
                $('#btn-save-client-text').text('Update Client Profile');

                $modal.modal('show');
            });

            // Submit Form via jQuery AJAX
            $form.on('submit', function (e) {
                e.preventDefault();

                var clientId = $('#client_id').val();
                var isUpdate = Boolean(clientId);
                var targetUrl = isUpdate ? (clientsBaseUrl + '/' + clientId) : clientsBaseUrl;
                var httpMethod = isUpdate ? 'PUT' : 'POST';

                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').text('').hide();
                $submitBtn.prop('disabled', true);

                var payload = {
                    contact_person: $('#client_contact_person').val(),
                    company_name: $('#client_company_name').val(),
                    phone: $('#client_phone').val(),
                    email: $('#client_email').val(),
                    address: $('#client_address').val()
                };

                $.ajax({
                    url: targetUrl,
                    type: httpMethod,
                    data: payload,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (res) {
                        $modal.modal('hide');
                        resetClientModal();
                        showToast('success', res.message || 'Client profile saved successfully.');
                        clientsDataTable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function (field) {
                                var $input = $('#client_' + field);
                                var $feedback = $('#error-' + field);
                                $input.addClass('is-invalid');
                                $feedback.text(errors[field][0]).show();
                            });
                        } else {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to save client profile.';
                            showToast('error', msg);
                        }
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });

            // Delete Client Confirmation via SweetAlert2 & AJAX DELETE
            $(document).on('click', '.js-client-delete', function (e) {
                e.preventDefault();
                var $btn = $(this);
                var clientId = $btn.data('id');
                var clientName = $btn.data('name') || 'this client';
                var targetUrl = $btn.data('url') || (clientsBaseUrl + '/' + clientId);

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
                        url: targetUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function (res) {
                            showToast('success', res.message || 'Client deleted successfully.');
                            clientsDataTable.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to delete client.';
                            showToast('error', msg);
                        },
                        complete: function () {
                            $btn.prop('disabled', false);
                        }
                    });
                });
            });
        });
    </script>
@endpush
