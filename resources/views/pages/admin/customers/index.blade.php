@extends('layouts.app')

@section('title', 'Customers')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
@endpush

@section('content')
    <div class="customers-page">
        <div class="customers-header">
            <div>
                <h1>Customers</h1>
                <p>Manage registered customers and their booking activity</p>
            </div>
            <button type="button" class="btn btn-primary customers-primary-btn js-customer-add" data-toggle="modal" data-target="#customer-form-modal">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Customer
            </button>
        </div>

        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Customer List</h2>
                    <p><span id="customers-record-count">{{ $customersCount }}</span> records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="customers-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Referral Code</th>
                            <th>Active</th>
                            <th>Bookings</th>
                            <th>Wallet</th>
                            <th>Referred</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customer-form-modal" tabindex="-1" role="dialog" aria-labelledby="customer-form-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content customer-modal-content">
                <form id="customer-modal-form" method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="customer-form-method" value="POST">
                    <input type="hidden" name="id" id="customer-id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="customer-form-modal-title">Add Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name</label>
                                <div class="input-icon">
                                    <i class="far fa-user" aria-hidden="true"></i>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name</label>
                                <div class="input-icon">
                                    <i class="far fa-user" aria-hidden="true"></i>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone">Phone Number</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="gender">Gender</label>
                                <div class="input-icon">
                                    <i class="fas fa-venus-mars" aria-hidden="true"></i>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-icon">
                                <i class="far fa-envelope" aria-hidden="true"></i>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                            </div>
                        </div>

                        <div class="form-row password-fields">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <div class="input-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Create password">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-icon">
                                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                <label class="custom-control-label" for="is_active" style="font-weight: normal; cursor: pointer;">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer customer-modal-actions">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary customers-primary-btn" id="customer-modal-submit">
                            <i class="fas fa-save" aria-hidden="true"></i> Save Customer
                        </button>
                    </div>
                </form>
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
        $(document).ready(function() {
            var customersStoreUrl = @json(route('customers.store'));
            var customersIndexUrl = @json(route('customers.index'));
            var customersBaseUrl = @json(url('/customers'));
            var $customerModal = $('#customer-form-modal');
            var $customerForm = $('#customer-modal-form');
            var $customerSubmit = $('#customer-modal-submit');
            var customersTable = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: customersIndexUrl,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                responsive: true,
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', name: 'id', searchable: false },
                    { data: 'customer', name: 'first_name' },
                    { data: 'contact', name: 'phone' },
                    { data: 'referral', name: 'referral_code' },
                    { data: 'is_active_badge', name: 'is_active' },
                    { data: 'bookings_count', name: 'bookings_count', searchable: false },
                    { data: 'wallet_balance', name: 'wallet_balance', searchable: false },
                    { data: 'referred_count', name: 'referred_count', searchable: false },
                    { data: 'action', name: 'action', searchable: false }
                ],
                columnDefs: [
                    { orderable: false, targets: [5, 6, 7, 8] }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search customer...'
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function showToast(icon, title) {
                if (typeof Swal === 'undefined') {
                    alert(title);
                    return;
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: title,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }

            function resetCustomerForm() {
                $customerForm[0].reset();
                $customerForm.find('.is-invalid').removeClass('is-invalid');
                $customerForm.find('.customer-field-error').remove();
                $('#customer-form-method').val('POST');
                $('#customer-id').val('');
                $('#is_active').prop('checked', true);
                $customerForm.attr('action', customersStoreUrl);
            }

            function reloadCustomerTable() {
                customersTable.ajax.reload(function(json) {
                    $('#customers-record-count').text(json.recordsTotal || 0);
                }, false);
            }

            $(document).on('click', '.js-copy-code', function(e) {
                e.preventDefault();
                var code = $(this).data('code');
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(code).select();
                document.execCommand('copy');
                $temp.remove();
                showToast('success', 'Referral code copied: ' + code);
            });

            $('.js-customer-add').on('click', function () {
                resetCustomerForm();
                $('#customer-form-modal-title').text('Add Customer');
                $('#customer-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Save Customer');
            });

            $(document).on('click', '.js-customer-edit', function () {
                var $button = $(this);
                resetCustomerForm();

                $('#customer-form-modal-title').text('Edit Customer');
                $('#customer-id').val($button.data('id'));
                $('#first_name').val($button.data('first_name'));
                $('#last_name').val($button.data('last_name'));
                $('#email').val($button.data('email'));
                $('#phone').val($button.data('phone'));
                $('#gender').val($button.data('gender'));
                $('#is_active').prop('checked', Number($button.data('is_active')) === 1);
                $('#customer-form-method').val('PUT');
                $customerForm.attr('action', customersBaseUrl + '/' + $button.data('id'));
                $('#customer-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Update Customer');
            });

            $customerForm.on('submit', function (e) {
                e.preventDefault();

                var isEdit = $('#customer-form-method').val() === 'PUT';
                var formData = new FormData(this);

                $customerForm.find('.is-invalid').removeClass('is-invalid');
                $customerForm.find('.customer-field-error').remove();
                $customerSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Saving...');

                $.ajax({
                    url: $customerForm.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $customerModal.modal('hide');
                        showToast('success', response.message || 'Customer saved successfully!');
                        reloadCustomerTable();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                var $input = $('[name="' + field + '"]');
                                $input.addClass('is-invalid');
                                $input.closest('.form-group').append('<span class="customer-field-error text-danger small d-block mt-1">' + messages[0] + '</span>');
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation failed',
                                text: 'Please check the highlighted fields.'
                            });
                            return;
                        }

                        showToast('error', 'Customer could not be saved.');
                    },
                    complete: function() {
                        $customerSubmit.prop('disabled', false).html('<i class="fas fa-save" aria-hidden="true"></i> ' + (isEdit ? 'Update Customer' : 'Save Customer'));
                    }
                });
            });

            $(document).on('click', '.js-customer-delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this customer';

                Swal.fire({
                    title: 'Delete Customer?',
                    text: 'Are you sure you want to delete ' + name + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete!'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: customersBaseUrl + '/' + id,
                        method: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            showToast('success', response.message || 'Customer deleted successfully!');
                            reloadCustomerTable();
                        },
                        error: function() {
                            showToast('error', 'Customer could not be deleted.');
                        }
                    });
                });
            });
        });
    </script>
@endpush
