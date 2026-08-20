@extends('layouts.app')

@section('title', 'My Bookings')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .filter-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
        }
        .filter-controls-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .filter-left-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-item label {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #475467;
            white-space: nowrap;
        }
        .filter-item .form-control {
            height: 38px;
            font-size: 13px;
            border-color: #d0d5dd;
            border-radius: 8px;
        }
        .filter-item .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }
        .btn-filter-apply {
            background-color: #0866e8;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            height: 38px;
            padding: 0 18px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-filter-apply:hover {
            background-color: #006ce5;
            color: #ffffff;
        }
        .btn-filter-reset {
            background-color: #f2f4f7;
            color: #344054;
            font-weight: 600;
            font-size: 13px;
            height: 38px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-filter-reset:hover {
            background-color: #eaecf0;
            color: #1d2939;
        }
        .badge-status-confirmed {
            background-color: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #b7e1cd;
        }
        .badge-status-pending {
            background-color: #fff8e6;
            color: #b7791f;
            border: 1px solid #fce8b2;
        }
        .badge-status-completed {
            background-color: #e8f4fd;
            color: #1a73e8;
            border: 1px solid #c2e0ff;
        }
        .badge-status-cancelled {
            background-color: #fce8e6;
            color: #c5221f;
            border: 1px solid #fad2cf;
        }
        .badge-payment-paid {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .badge-payment-unpaid {
            background-color: #fff3cd;
            color: #664d03;
        }
        .badge-payment-refunded {
            background-color: #e2e3e5;
            color: #41464b;
        }
        .booking-id-tag {
            font-family: monospace;
            font-weight: 700;
            font-size: 13px;
            color: #0866e8;
            background: #f0f6fe;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .font-size-13 {
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header">
            <div>
                <h1>My Bookings</h1>
                <p>View, track, and manage all your scheduled cleaning services.</p>
            </div>
            <div>
                <a href="#" class="btn customers-primary-btn">
                    <i class="fas fa-plus mr-1"></i> Book a Service
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card -->
        <div class="filter-card">
            <div class="filter-controls-wrapper">
                <div class="filter-left-group">
                    <!-- Status Filter -->
                    <div class="filter-item">
                        <label for="filter-status"><i class="fas fa-filter text-muted mr-1"></i> Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="filter-item">
                        <label for="filter-date"><i class="far fa-calendar-alt text-muted mr-1"></i> Date</label>
                        <input type="date" id="filter-date" class="form-control">
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <button type="button" id="btn-apply-filter" class="btn btn-filter-apply">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <button type="button" id="btn-reset-filter" class="btn btn-filter-reset">
                        <i class="fas fa-redo-alt"></i> Reset
                    </button>
                </div>

                <!-- Custom Search Bar Wrapper -->
                <div class="filter-item">
                    <label for="custom-search-input"><i class="fas fa-search text-muted mr-1"></i> Search</label>
                    <input type="text" id="custom-search-input" class="form-control" placeholder="Search bookings...">
                </div>
            </div>
        </div>

        <!-- Bookings Datatable Card -->
        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Bookings List</h2>
                    <p>{{ count($bookings) }} total records available</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="customer-bookings-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            @php
                                $statusLower = strtolower($booking->status);
                                $paymentLower = strtolower($booking->payment_status);

                                if ($statusLower === 'confirmed') {
                                    $statusBadgeClass = 'badge-status-confirmed';
                                } elseif ($statusLower === 'pending') {
                                    $statusBadgeClass = 'badge-status-pending';
                                } elseif ($statusLower === 'completed') {
                                    $statusBadgeClass = 'badge-status-completed';
                                } else {
                                    $statusBadgeClass = 'badge-status-cancelled';
                                }

                                if ($paymentLower === 'paid') {
                                    $paymentBadgeClass = 'badge-payment-paid';
                                } elseif ($paymentLower === 'unpaid') {
                                    $paymentBadgeClass = 'badge-payment-unpaid';
                                } else {
                                    $paymentBadgeClass = 'badge-payment-refunded';
                                }
                            @endphp
                            <tr data-status="{{ $statusLower }}" data-date="{{ $booking->date }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="booking-id-tag">{{ $booking->booking_id }}</span>
                                </td>
                                <td>
                                    <strong>{{ $booking->service_name }}</strong>
                                </td>
                                <td>
                                    <span class="text-dark">
                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light border px-2 py-1" style="font-size: 12px; font-weight: 600; color: #17233c;">
                                        <i class="far fa-clock mr-1 text-primary"></i>{{ $booking->time }}
                                    </span>
                                </td>
                                <td>
                                    <strong>${{ number_format($booking->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadgeClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $paymentBadgeClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="customer-actions justify-content-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary customer-action-btn view-booking-btn" 
                                                title="View Details"
                                                data-booking="{{ json_encode($booking) }}">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="far fa-calendar-times fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No bookings found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Redesigned Booking Details Modal according to Wireframe Line 369-397 -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content customer-modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <!-- Modal Header -->
                <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 18px 24px;">
                    <h5 class="modal-title font-weight-bold" id="bookingDetailModalLabel" style="color: #0f172a; font-size: 18px;">
                        <i class="fas fa-receipt text-primary mr-2"></i>Booking #<span id="modal-booking-id" style="color: #0866e8;"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="padding: 24px;">
                    <!-- Two Column Section: Booking Information & Payment Information -->
                    <div class="row">
                        <!-- Left Column: Booking Information -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 border-0 p-3" style="border-radius: 10px; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                                <h6 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #1e293b; font-size: 13.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="fas fa-calendar-alt text-primary mr-2"></i>Booking Information
                                </h6>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Service:</span>
                                    <span id="modal-service" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Date:</span>
                                    <span id="modal-date" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Time:</span>
                                    <span id="modal-time" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Booking Status:</span>
                                    <span id="modal-status-badge"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Payment Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 p-3" style="border-radius: 10px; background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                                <h6 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #1e293b; font-size: 13.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="fas fa-credit-card text-primary mr-2"></i>Payment Information
                                </h6>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Amount:</span>
                                    <span id="modal-amount" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Payment Method:</span>
                                    <span id="modal-payment-method" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Payment Status:</span>
                                    <span id="modal-payment-badge"></span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Paid Amount:</span>
                                    <span id="modal-paid-amount" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-size-13">Wallet Used:</span>
                                    <span id="modal-wallet-used" class="font-weight-bold text-dark font-size-13"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Information / Answers Section -->
                    <div class="mt-4">
                        <div class="card border-0 p-3" style="border-radius: 10px; background-color: #ffffff; border: 1px solid #e2e8f0 !important;">
                            <h6 class="font-weight-bold mb-3 pb-2 border-bottom" style="color: #1e293b; font-size: 13.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="far fa-list-alt text-primary mr-2"></i>Service Information / Answers
                            </h6>

                            <div id="modal-questionnaire-container">
                                <!-- Dynamic Question & Answer items -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Action Bar -->
                <div class="modal-footer d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
                    <div id="modal-cancel-action">
                        <!-- Cancel Booking Action -->
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div id="modal-payment-action" class="mr-2">
                            <!-- Make Payment Action -->
                        </div>
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; font-size: 13px;">Close</button>
                    </div>
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
            // Register DataTables Custom Filter Plugin
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var selectedStatus = $('#filter-status').val().toLowerCase();
                    var selectedDate = $('#filter-date').val();

                    var rowNode = settings.aoData[dataIndex].nTr;
                    var rowStatus = $(rowNode).attr('data-status') || '';
                    var rowDate = $(rowNode).attr('data-date') || '';

                    if (selectedStatus && selectedStatus !== 'all' && rowStatus !== selectedStatus) {
                        return false;
                    }

                    if (selectedDate && rowDate !== selectedDate) {
                        return false;
                    }

                    return true;
                }
            );

            // Initialize DataTable
            var table = $('#customer-bookings-table').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                dom: "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                language: {
                    zeroRecords: 'No matching bookings found.',
                    info: 'Showing _START_ to _END_ of _TOTAL_ bookings',
                    infoEmpty: 'Showing 0 to 0 of 0 bookings',
                    infoFiltered: '(filtered from _MAX_ total bookings)',
                }
            });

            // Connect Custom Search Input
            $('#custom-search-input').on('keyup change clear', function () {
                table.search($(this).val()).draw();
            });

            // Apply Filter Button Event
            $('#btn-apply-filter').on('click', function () {
                table.draw();
            });

            // Reset Filter Button Event
            $('#btn-reset-filter').on('click', function () {
                $('#filter-status').val('all');
                $('#filter-date').val('');
                $('#custom-search-input').val('');
                table.search('').draw();
            });

            // View Details Modal Trigger & Dynamic Population
            $('.view-booking-btn').on('click', function () {
                var bookingData = $(this).attr('data-booking');
                var booking = typeof bookingData === 'string' ? JSON.parse(bookingData) : bookingData;

                $('#modal-booking-id').text(booking.booking_id);
                $('#modal-service').text(booking.service_name);

                // Format Date nicely
                var parsedDate = new Date(booking.date);
                var formattedDate = parsedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                $('#modal-date').text(formattedDate !== 'Invalid Date' ? formattedDate : booking.date);
                $('#modal-time').text(booking.time);

                // Booking Status Badge
                var statusLower = (booking.status || '').toLowerCase();
                var statusBadgeClass = 'badge-status-confirmed';
                if (statusLower === 'pending') statusBadgeClass = 'badge-status-pending';
                else if (statusLower === 'completed') statusBadgeClass = 'badge-status-completed';
                else if (statusLower === 'cancelled') statusBadgeClass = 'badge-status-cancelled';

                $('#modal-status-badge').html(
                    '<span class="badge ' + statusBadgeClass + '" style="padding: 5px 11px; font-weight: 700; border-radius: 999px;">' +
                    booking.status +
                    '</span>'
                );

                // Payment Information
                $('#modal-amount').text('$' + parseFloat(booking.amount || 0).toFixed(2));
                $('#modal-payment-method').text(booking.payment_method || 'N/A');

                var paymentLower = (booking.payment_status || '').toLowerCase();
                var paymentBadgeClass = 'badge-payment-paid';
                if (paymentLower === 'unpaid') paymentBadgeClass = 'badge-payment-unpaid';
                else if (paymentLower === 'refunded') paymentBadgeClass = 'badge-payment-refunded';

                $('#modal-payment-badge').html(
                    '<span class="badge ' + paymentBadgeClass + '" style="padding: 5px 11px; font-weight: 700; border-radius: 999px;">' +
                    booking.payment_status +
                    '</span>'
                );

                $('#modal-paid-amount').text('$' + parseFloat(booking.paid_amount || 0).toFixed(2));
                $('#modal-wallet-used').text('$' + parseFloat(booking.wallet_used || 0).toFixed(2));

                // Service Information / Answers (Questionnaires)
                var qContainer = $('#modal-questionnaire-container');
                qContainer.empty();

                if (booking.questionnaires && booking.questionnaires.length > 0) {
                    var html = '<div class="qa-wrapper">';
                    $.each(booking.questionnaires, function (index, item) {
                        html += '<div class="qa-item p-3 mb-2 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">' +
                                    '<div class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">' +
                                        '<span class="badge badge-primary mr-2" style="font-size: 11px; padding: 3px 6px;">Q' + (index + 1) + '</span>' +
                                        item.question +
                                    '</div>' +
                                    '<div class="text-secondary pl-4" style="font-size: 13px; line-height: 1.4;">' +
                                        '<strong class="text-dark">Answer:</strong> ' + item.answer +
                                    '</div>' +
                                '</div>';
                    });
                    html += '</div>';
                    qContainer.html(html);
                } else {
                    qContainer.html(
                        '<div class="text-center py-3 text-muted" style="font-size: 13px;">' +
                            '<i class="far fa-question-circle mr-1"></i> No questionnaire answers recorded.' +
                        '</div>'
                    );
                }

                // Cancel Booking Action
                var cancelContainer = $('#modal-cancel-action');
                cancelContainer.empty();
                if (booking.cancellation_eligible && statusLower !== 'cancelled' && statusLower !== 'completed') {
                    cancelContainer.html(
                        '<button type="button" class="btn btn-outline-danger px-3 btn-cancel-booking" style="border-radius: 8px; font-weight: 600; font-size: 13px;">' +
                            '<i class="fas fa-times-circle mr-1"></i> Cancel Booking' +
                        '</button>'
                    );
                }

                // Make Payment Action
                var paymentContainer = $('#modal-payment-action');
                paymentContainer.empty();
                if (paymentLower === 'unpaid') {
                    paymentContainer.html(
                        '<button type="button" class="btn btn-success px-3 btn-make-payment" style="border-radius: 8px; font-weight: 600; font-size: 13px;">' +
                            '<i class="fas fa-credit-card mr-1"></i> Make Payment' +
                        '</button>'
                    );
                }

                $('#bookingDetailModal').modal('show');
            });

            // Action Handlers
            $(document).on('click', '.btn-cancel-booking', function () {
                if (confirm('Are you sure you want to cancel this booking?')) {
                    alert('Booking cancellation request submitted successfully.');
                    $('#bookingDetailModal').modal('hide');
                }
            });

            $(document).on('click', '.btn-make-payment', function () {
                alert('Redirecting to payment gateway...');
                $('#bookingDetailModal').modal('hide');
            });
        });
    </script>
@endpush
