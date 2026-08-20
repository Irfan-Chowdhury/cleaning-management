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
                    <p>{{ $customers->count() }} records found</p>
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
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Wallet</th>
                            <th>Referred</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="customer-avatar-cell">
                                        <img src="{{ $customer['avatar'] }}" alt="{{ $customer['name'] }} avatar" class="customer-avatar">
                                        <div class="customer-info-meta">
                                            <span class="customer-name">{{ $customer['name'] }}</span>
                                            <span class="customer-email">{{ $customer['email'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer['phone'] ?: '-' }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $customer['referral_code'] }}</strong>
                                        <a href="#" class="copy-btn js-copy-code" data-code="{{ $customer['referral_code'] }}">[Copy]</a>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-success';
                                        if ($customer['status'] === 'inactive') {
                                            $badgeClass = 'badge-secondary';
                                        } elseif ($customer['status'] === 'suspended') {
                                            $badgeClass = 'badge-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="padding: 6px 8px; font-weight: 700; border-radius: 999px; min-width: 68px;">
                                        {{ ucfirst($customer['status']) }}
                                    </span>
                                </td>
                                <td>{{ $customer['bookings_count'] }}</td>
                                <td>${{ number_format($customer['wallet_balance'], 2) }}</td>
                                <td>{{ $customer['referred_count'] }}</td>
                                <td>
                                    <div class="customer-actions">
                                        <a href="{{ route('booking-service.create') }}" class="btn btn-sm btn-outline-success customer-action-btn" title="Booking">
                                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary customer-action-btn js-customer-edit" title="Edit"
                                                data-toggle="modal"
                                                data-target="#customer-form-modal"
                                                data-id="{{ $customer['id'] }}"
                                                data-first-name="{{ $customer['first_name'] }}"
                                                data-last-name="{{ $customer['last_name'] }}"
                                                data-email="{{ $customer['email'] }}"
                                                data-phone="{{ $customer['phone'] }}"
                                                data-gender="{{ $customer['gender'] }}"
                                                data-status="{{ $customer['status'] }}">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger customer-action-btn js-delete-placeholder" title="Delete" data-name="{{ $customer['name'] }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Modal Form -->
    <div class="modal fade" id="customer-form-modal" tabindex="-1" role="dialog" aria-labelledby="customer-form-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content customer-modal-content">
                <form id="customer-modal-form" method="POST" action="#">
                    @csrf
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
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone">Phone Number</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="gender">Gender</label>
                                <div class="input-icon">
                                    <i class="fas fa-venus-mars" aria-hidden="true"></i>
                                    <select class="form-control" id="gender" name="gender" required>
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
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="active" checked>
                                <label class="custom-control-label" for="status" style="font-weight: normal; cursor: pointer;">Active</label>
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
            var $customersTable = $('#customers-table');

            // Initialize Datatable if not already initialized
            if ($customersTable.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($customersTable)) {
                $customersTable.DataTable({
                    pageLength: 10,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        { orderable: false, targets: -1 }
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search customer...'
                    }
                });
            }

            // Copy referral code to clipboard
            $(document).on('click', '.js-copy-code', function(e) {
                e.preventDefault();
                var code = $(this).data('code');
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(code).select();
                document.execCommand('copy');
                $temp.remove();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Referral code copied: ' + code,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    alert('Referral code copied: ' + code);
                }
            });

            // Modal Form Handlers
            var $customerModal = $('#customer-form-modal');
            var $customerForm = $('#customer-modal-form');

            function resetCustomerForm() {
                if (!$customerForm.length) return;
                $customerForm[0].reset();
                $customerForm.find('.is-invalid').removeClass('is-invalid');
                $customerForm.find('.invalid-feedback').hide();
                $('.password-fields').show();
                $('#status').prop('checked', true);
            }

            $('.js-customer-add').on('click', function () {
                resetCustomerForm();
                $('#customer-form-modal-title').text('Add Customer');
                $('#customer-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Save Customer');
            });

            $(document).on('click', '.js-customer-edit', function () {
                var $button = $(this);
                resetCustomerForm();
                $('#customer-form-modal-title').text('Edit Customer');
                
                // Populate data
                $('#customer-id').val($button.data('id'));
                $('#first_name').val($button.data('first-name'));
                $('#last_name').val($button.data('last-name'));
                $('#email').val($button.data('email'));
                $('#phone').val($button.data('phone'));
                $('#gender').val($button.data('gender'));
                
                // Check if active
                var status = $button.data('status');
                $('#status').prop('checked', status === 'active');
                
                // Hide password fields for editing simulation
                $('.password-fields').hide();

                $('#customer-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Update Customer');
            });

            // Simulate form submission
            $customerForm.on('submit', function (e) {
                e.preventDefault();
                var action = $('#customer-form-modal-title').text();
                $customerModal.modal('hide');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success!',
                        text: action + ' has been simulated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#0866e8'
                    });
                } else {
                    alert(action + ' has been simulated successfully.');
                }
            });

            // Delete action alert simulation
            $(document).on('click', '.js-delete-placeholder', function(e) {
                e.preventDefault();
                var name = $(this).data('name');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete Customer?',
                        text: 'Are you sure you want to delete ' + name + '?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Deleted!', 'Customer has been deleted (Simulation).', 'success');
                        }
                    });
                } else {
                    confirm('Are you sure you want to delete ' + name + '? (Simulation)');
                }
            });
        });
    </script>
@endpush
