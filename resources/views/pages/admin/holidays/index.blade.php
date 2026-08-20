@extends('layouts.app')

@section('title', 'Holidays')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/holiday.css') }}">
@endpush

@section('content')
<div class="holiday-page">

    {{-- Page Header --}}
    <div class="holiday-header">
        <div>
            <h1>Holidays</h1>
            <p>Manage public holidays and business closures</p>
        </div>
        <button type="button" class="btn btn-primary holiday-primary-btn js-holiday-add"
                data-toggle="modal" data-target="#holiday-form-modal">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Holiday
        </button>
    </div>

    {{-- Table Card --}}
    <div class="holiday-table-card">
        <div class="holiday-table-card-header">
            <div>
                <h2>Holiday List</h2>
                <p>{{ $holidays->count() }} records found</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="holidays-table"
                   class="table table-hover table-bordered nowrap holiday-table"
                   style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th class="holiday-action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($holidays as $holiday)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $holiday['title'] }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($holiday['start_date'])->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($holiday['end_date'])->format('d M Y') }}</td>
                        <td>
                            <div class="holiday-actions">
                                {{-- Edit --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary holiday-action-btn js-holiday-edit"
                                        title="Edit"
                                        data-toggle="modal"
                                        data-target="#holiday-form-modal"
                                        data-id="{{ $holiday['id'] }}"
                                        data-title="{{ $holiday['title'] }}"
                                        data-start="{{ $holiday['start_date'] }}"
                                        data-end="{{ $holiday['end_date'] }}">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                {{-- Delete --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger holiday-action-btn js-holiday-delete"
                                        title="Delete"
                                        data-id="{{ $holiday['id'] }}"
                                        data-title="{{ $holiday['title'] }}">
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

</div>{{-- /.holiday-page --}}

{{-- Global delete form (UI prototype) --}}
<form id="global-delete-form" method="POST" action="#" class="d-none">
    @csrf
    @method('DELETE')
</form>

{{-- =========================================================
     Holiday Add / Edit Modal
     ========================================================= --}}
<div class="modal fade" id="holiday-form-modal" tabindex="-1"
     role="dialog" aria-labelledby="holiday-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content holiday-modal-content">
            <form id="holiday-modal-form" method="POST" action="#">
                @csrf
                <input type="hidden" name="id" id="holiday-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="holiday-modal-title">Add Holiday</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- Title --}}
                    <div class="form-group">
                        <label for="holiday-title">Title</label>
                        <input type="text" class="form-control" id="holiday-title"
                               name="title" placeholder="e.g. Christmas Day" required>
                    </div>

                    {{-- Start Date --}}
                    <div class="form-group">
                        <label for="holiday-start">Start Date</label>
                        <input type="date" class="form-control" id="holiday-start"
                               name="start_date" required>
                    </div>

                    {{-- End Date --}}
                    <div class="form-group mb-0">
                        <label for="holiday-end">End Date</label>
                        <input type="date" class="form-control" id="holiday-end"
                               name="end_date" required>
                    </div>
                </div>

                <div class="modal-footer holiday-modal-actions">
                    <button type="button" class="btn btn-outline-secondary"
                            data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary holiday-primary-btn"
                            id="holiday-modal-submit">
                        <i class="fas fa-save" aria-hidden="true"></i> Save Holiday
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
    (function ($) {
        'use strict';

        /* ── DataTable ── */
        var $table = $('#holidays-table');
        if ($table.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($table)) {
            $table.DataTable({
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                responsive: true,
                autoWidth: false,
                columnDefs: [{ orderable: false, targets: -1 }],
                language: { search: '', searchPlaceholder: 'Search holidays...' }
            });
        }

        /* ── Modal helpers ── */
        var $modal  = $('#holiday-form-modal');
        var $form   = $('#holiday-modal-form');
        var $submit = $('#holiday-modal-submit');

        function resetForm() {
            $form[0].reset();
            $('#holiday-id').val('');
        }

        /* Add Holiday */
        $('.js-holiday-add').on('click', function () {
            resetForm();
            $('#holiday-modal-title').text('Add Holiday');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Save Holiday');
        });

        /* Edit Holiday – populate from data attributes */
        $(document).on('click', '.js-holiday-edit', function () {
            var $btn = $(this);
            resetForm();
            $('#holiday-modal-title').text('Edit Holiday');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Update Holiday');

            $('#holiday-id').val($btn.data('id'));
            $('#holiday-title').val($btn.data('title'));
            $('#holiday-start').val($btn.data('start'));
            $('#holiday-end').val($btn.data('end'));
        });

        /* Form submit simulation */
        $form.on('submit', function (e) {
            e.preventDefault();
            var action = $('#holiday-modal-title').text();
            $modal.modal('hide');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: action + ' saved (simulation)',
                    showConfirmButton: false, timer: 2500, timerProgressBar: true
                });
            } else {
                alert(action + ' saved (simulation).');
            }
        });

        /* Delete with SweetAlert2 confirmation */
        $(document).on('click', '.js-holiday-delete', function () {
            var name = $(this).data('title');
            var $row = $(this).closest('tr');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Holiday?',
                    html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small class="text-muted">This action cannot be undone.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        /* Remove row from DataTable (UI prototype) */
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().row($row).remove().draw();
                        } else {
                            $row.remove();
                        }
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success',
                            title: name + ' deleted (simulation)',
                            showConfirmButton: false, timer: 2000, timerProgressBar: true
                        });
                    }
                });
            } else {
                if (confirm('Delete ' + name + '?')) {
                    $row.remove();
                }
            }
        });

    })(jQuery);
    </script>
@endpush
