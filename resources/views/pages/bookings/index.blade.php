@extends('layouts.app')

@section('title', 'Bookings')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
@endpush

@section('content')
    <div class="customers-page">
        <div class="customers-header">
            <div>
                <h1>Bookings</h1>
                <p>Manage customer service bookings and schedules.</p>
            </div>
        </div>

        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Booking List</h2>
                    <p>{{ $bookings->count() }} records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="bookings-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Slot</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="customer-avatar-cell">
                                        <img src="{{ $booking->customer_avatar }}" alt="{{ $booking->customer_name }} avatar" class="customer-avatar">
                                        <div class="customer-info-meta">
                                            <span class="customer-name">{{ $booking->customer_name }}</span>
                                            <span class="customer-email">{{ $booking->customer_email }}</span>
                                        </div>
                                    </div>
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
                                        <i class="far fa-clock mr-1 text-primary"></i>{{ $booking->slot }}
                                    </span>
                                </td>
                                <td>
                                    <strong>${{ number_format($booking->amount, 2) }}</strong>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'badge-success';
                                        if ($booking->payment_status === 'pending') {
                                            $statusClass = 'badge-warning text-dark';
                                        } elseif ($booking->payment_status === 'failed') {
                                            $statusClass = 'badge-danger';
                                        } elseif ($booking->payment_status === 'refunded') {
                                            $statusClass = 'badge-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}" style="padding: 6px 10px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="customer-actions justify-content-center">
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-success customer-action-btn" title="View Booking">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-outline-primary customer-action-btn" title="Edit Booking">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger customer-action-btn" title="Delete Booking">
                                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
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
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#bookings-table').DataTable({
                responsive: true,
                order: [[3, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                language: {
                    search: 'Search bookings:',
                    lengthMenu: 'Show _MENU_ entries',
                }
            });
        });
    </script>
@endpush
