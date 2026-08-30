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
                <p><span id="holiday-record-count">0</span> records found</p>
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
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th class="holiday-action-column">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>{{-- /.holiday-page --}}

{{-- =========================================================
     Holiday Add / Edit Modal
     ========================================================= --}}
<div class="modal fade" id="holiday-form-modal" tabindex="-1"
     role="dialog" aria-labelledby="holiday-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content holiday-modal-content">
            <form id="holiday-modal-form" method="POST" action="{{ route('holidays.store') }}">
                @csrf
                <input type="hidden" name="_method" id="holiday-form-method" value="POST">
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
                        <label for="holiday-title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="holiday-title"
                               name="title" placeholder="e.g. Eid-ul-Fitr">
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label for="holiday-description">Description</label>
                        <textarea class="form-control" id="holiday-description"
                                  name="description" rows="3"
                                  placeholder="Optional description about this holiday"></textarea>
                    </div>

                    {{-- Start Date --}}
                    <div class="form-group">
                        <label for="holiday-start">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="holiday-start"
                               name="start_date">
                    </div>

                    {{-- End Date --}}
                    <div class="form-group">
                        <label for="holiday-end">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="holiday-end"
                               name="end_date">
                    </div>

                    {{-- Active --}}
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                   id="holiday-is-active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="holiday-is-active"
                                   style="font-weight:normal;cursor:pointer;">Active</label>
                        </div>
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

        var holidaysIndexUrl = @json(route('holidays.index'));
        var holidaysBaseUrl  = @json(url('/holidays'));
        var csrfToken        = $('meta[name="csrf-token"]').attr('content');

        /* ── Ajax setup ── */
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

        /* ── Helper: Toast / Alert ── */
        function showToast(icon, title) {
            if (typeof Swal === 'undefined') { alert(title); return; }
            Swal.fire({
                toast: true, position: 'top-end', icon: icon, title: title,
                showConfirmButton: false, timer: 2500, timerProgressBar: true
            });
        }

        /* ── DataTable ── */
        var holidaysTable = $('#holidays-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: holidaysIndexUrl,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            columns: [
                { data: 'DT_RowIndex',        name: 'id',                  searchable: false },
                { data: 'title',              name: 'title' },
                { data: 'description_short',  name: 'description',         orderable: false },
                { data: 'start_date_formatted', name: 'start_date' },
                { data: 'end_date_formatted',   name: 'end_date' },
                { data: 'status_badge',       name: 'is_active',           searchable: false },
                { data: 'action',             name: 'action',              searchable: false, orderable: false }
            ],
            language: { search: '', searchPlaceholder: 'Search holidays...' }
        });

        /* ── Reload table and update count ── */
        function reloadTable() {
            holidaysTable.ajax.reload(function (json) {
                $('#holiday-record-count').text(json.recordsTotal || 0);
            }, false);
        }

        /* ── Modal references ── */
        var $modal  = $('#holiday-form-modal');
        var $form   = $('#holiday-modal-form');
        var $submit = $('#holiday-modal-submit');

        function resetForm() {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.holiday-field-error').remove();
            $('#holiday-id').val('');
            $('#holiday-form-method').val('POST');
            $('#holiday-is-active').prop('checked', true);
            $form.attr('action', holidaysBaseUrl);
        }

        /* ── Add Holiday ── */
        $('.js-holiday-add').on('click', function () {
            resetForm();
            $('#holiday-modal-title').text('Add Holiday');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Save Holiday');
        });

        /* ── Edit Holiday ── */
        $(document).on('click', '.js-holiday-edit', function () {
            var $btn = $(this);
            resetForm();

            $('#holiday-modal-title').text('Edit Holiday');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Update Holiday');

            var id = $btn.data('id');
            $('#holiday-id').val(id);
            $('#holiday-title').val($btn.data('title'));
            $('#holiday-description').val($btn.data('description'));
            $('#holiday-start').val($btn.data('start'));
            $('#holiday-end').val($btn.data('end'));
            $('#holiday-is-active').prop('checked', Number($btn.data('is_active')) === 1);

            $('#holiday-form-method').val('PUT');
            $form.attr('action', holidaysBaseUrl + '/' + id);

            $modal.modal('show');
        });

        /* ── Form Submit (store / update via Ajax) ── */
        $form.on('submit', function (e) {
            e.preventDefault();

            var isEdit    = $('#holiday-form-method').val() === 'PUT';
            var formData  = new FormData(this);
            // Ensure is_active sends 0 when unchecked
            if (!$('#holiday-is-active').is(':checked')) {
                formData.set('is_active', '0');
            }

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.holiday-field-error').remove();
            $submit.prop('disabled', true)
                   .html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Saving...');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $modal.modal('hide');
                    showToast('success', response.message || 'Holiday saved successfully!');
                    reloadTable();
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            var $input = $('[name="' + field + '"]');
                            $input.addClass('is-invalid');
                            $input.closest('.form-group').append(
                                '<span class="holiday-field-error text-danger small d-block mt-1">' + messages[0] + '</span>'
                            );
                        });
                        Swal.fire({ icon: 'error', title: 'Validation failed', text: 'Please check the highlighted fields.' });
                        return;
                    }
                    showToast('error', 'Holiday could not be saved.');
                },
                complete: function () {
                    $submit.prop('disabled', false)
                           .html('<i class="fas fa-save" aria-hidden="true"></i> ' + (isEdit ? 'Update Holiday' : 'Save Holiday'));
                }
            });
        });

        /* ── Delete Holiday ── */
        $(document).on('click', '.js-holiday-delete', function () {
            var id   = $(this).data('id');
            var name = $(this).data('title') || 'this holiday';

            if (typeof Swal === 'undefined') {
                if (!confirm('Delete ' + name + '?')) { return; }
                doDelete(id, name);
                return;
            }

            Swal.fire({
                title: 'Delete Holiday?',
                html: 'Are you sure you want to delete <strong>' + name + '</strong>?'
                    + '<br><small class="text-muted">This action cannot be undone.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) { doDelete(id, name); }
            });
        });

        function doDelete(id, name) {
            $.ajax({
                url: holidaysBaseUrl + '/' + id,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function (response) {
                    showToast('success', response.message || name + ' deleted successfully!');
                    reloadTable();
                },
                error: function () {
                    showToast('error', 'Holiday could not be deleted.');
                }
            });
        }

        /* ── Initial count load ── */
        holidaysTable.on('draw', function () {
            var info = holidaysTable.page.info();
            $('#holiday-record-count').text(info.recordsTotal || 0);
        });

    })(jQuery);
    </script>
@endpush
