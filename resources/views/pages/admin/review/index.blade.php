@extends('layouts.app')

@section('title', 'Google Review Reward Requests')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
@endpush

@section('content')
    <div class="customers-page">
        <div class="customers-header">
            <div>
                <h1>Google Review Rewards</h1>
                <p>Manage customer Google review bonus applications</p>
            </div>
            <div class="bg-white border px-3 py-2 rounded-lg shadow-sm">
                <span class="text-muted small">Configured Reward:</span>
                <strong class="text-primary ml-1">${{ number_format($rewardAmount, 2) }}</strong>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Review Requests</h2>
                    <p>List of all customer reward applications</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="reviews-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Reward Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var reviewsTable = $('#reviews-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('reviews.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'customer', name: 'customer' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'reward_amount_formatted', name: 'reward_amount' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: {
                    emptyTable: "No review reward requests found",
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                }
            });

            $(document).on('click', '.js-delete-review', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Delete Review Request?',
                    text: 'Are you sure you want to delete this Google Review Reward request?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Review request deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            reviewsTable.ajax.reload();
                        },
                        error: function(xhr) {
                            var msg = 'Could not delete review request.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errs = Object.values(xhr.responseJSON.errors);
                                if (errs.length > 0) msg = errs[0][0];
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: msg
                            });
                        }
                    });
                });
            });
        });
    </script>
@endpush
