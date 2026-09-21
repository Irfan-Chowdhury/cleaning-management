@extends('layouts.app')

@section('title', 'Promotional Offers')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/promotion.css') }}">
@endpush

@section('content')
<div class="promotion-page">
    <div class="promotion-header">
        <div>
            <h1>Promotional Offers</h1>
            <p>Manage discount campaigns, customer eligibility, and active promotion windows.</p>
        </div>
        <button type="button" class="btn btn-primary promotion-primary-btn js-promotion-add"
                data-toggle="modal" data-target="#promotion-form-modal">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Promotion
        </button>
    </div>

    <div class="promotion-table-card">
        <div class="promotion-table-card-header">
            <div>
                <h2>Promotion List</h2>
                <p><span id="promotion-record-count">0</span> records found</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="promotions-table"
                   class="table table-hover table-bordered nowrap promotion-table"
                   style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        {{-- <th>Description</th> --}}
                        <th>Discount</th>
                        <th>Scope</th>
                        <th>Start</th>
                        <th>Expires</th>
                        <th>Status</th>
                        {{-- <th>Created By</th> --}}
                        <th class="promotion-action-column">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="promotion-form-modal" tabindex="-1"
     role="dialog" aria-labelledby="promotion-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content promotion-modal-content">
            <form id="promotion-modal-form" method="POST" action="{{ route('promotions.store') }}">
                @csrf
                <input type="hidden" name="_method" id="promotion-form-method" value="POST">
                <input type="hidden" name="id" id="promotion-id">

                <div class="modal-header">
                    <h5 class="modal-title" id="promotion-modal-title">Add Promotion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="promotion-name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="promotion-name"
                                   name="name" placeholder="e.g. Spring Deep Clean">
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="promotion-code">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="promotion-code"
                                   name="code" placeholder="SPRING15" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="promotion-description">Description</label>
                        <textarea class="form-control" id="promotion-description"
                                  name="description" rows="3"
                                  placeholder="Optional details about this promotional offer"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="promotion-discount-type">Discount Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="promotion-discount-type" name="discount_type">
                                <option value="">Select type...</option>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group" id="promotion-discount-value-group" style="display: none;">
                            <label for="promotion-discount-value">Discount Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" id="promotion-discount-value"
                                   name="discount_value" placeholder="0.00">
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="promotion-status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="promotion-status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                <option value="2">Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="promotion-start-at">Start At <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="promotion-start-at" name="start_at">
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="promotion-expires-at">Expires At <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="promotion-expires-at" name="expires_at">
                        </div>
                    </div>

                    <div class="promotion-eligibility-row">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                   id="promotion-new-customers-only" name="new_customers_only" value="1">
                            <label class="custom-control-label" for="promotion-new-customers-only">New customers only</label>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                   id="promotion-existing-customers-only" name="existing_customers_only" value="1">
                            <label class="custom-control-label" for="promotion-existing-customers-only">Existing customers only</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer promotion-modal-actions">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary promotion-primary-btn" id="promotion-modal-submit">
                        <i class="fas fa-save" aria-hidden="true"></i> Save Promotion
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

        var promotionsIndexUrl = @json(route('promotions.index'));
        var promotionsBaseUrl = @json(url('/promotions'));
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

        function showToast(icon, title) {
            if (typeof Swal === 'undefined') { alert(title); return; }
            Swal.fire({
                toast: true, position: 'top-end', icon: icon, title: title,
                showConfirmButton: false, timer: 2500, timerProgressBar: true
            });
        }

        var promotionsTable = $('#promotions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: promotionsIndexUrl,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            columns: [
                { data: 'DT_RowIndex', name: 'id', searchable: false },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                // { data: 'description_short', name: 'description', orderable: false },
                { data: 'discount', name: 'discount_value' },
                { data: 'customer_scope', name: 'new_customers_only', searchable: false },
                { data: 'start_at_formatted', name: 'start_at' },
                { data: 'expires_at_formatted', name: 'expires_at' },
                { data: 'status_badge', name: 'status', searchable: false },
                // { data: 'creator_name', name: 'creator_name', searchable: false, orderable: false },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ],
            language: { search: '', searchPlaceholder: 'Search promotions...' }
        });

        function reloadTable() {
            promotionsTable.ajax.reload(function (json) {
                $('#promotion-record-count').text(json.recordsTotal || 0);
            }, false);
        }

        var $modal = $('#promotion-form-modal');
        var $form = $('#promotion-modal-form');
        var $submit = $('#promotion-modal-submit');

        function toggleDiscountValueField() {
            var selectedType = $('#promotion-discount-type').val();
            if (selectedType === 'fixed') {
                $('#promotion-discount-value-group').slideDown(200);
            } else {
                $('#promotion-discount-value-group').slideUp(200);
            }
        }

        $('#promotion-discount-type').on('change', function () {
            toggleDiscountValueField();
        });

        function resetForm() {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.promotion-field-error').remove();
            $('#promotion-id').val('');
            $('#promotion-form-method').val('POST');
            $('#promotion-status').val(1);
            $form.attr('action', promotionsBaseUrl);
            toggleDiscountValueField();
        }

        $('#promotion-code').on('input', function () {
            $(this).val($(this).val().toUpperCase());
        });

        $('.js-promotion-add').on('click', function () {
            resetForm();
            $('#promotion-modal-title').text('Add Promotion');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Save Promotion');
        });

        $(document).on('click', '.js-promotion-edit', function () {
            var $btn = $(this);
            resetForm();

            $('#promotion-modal-title').text('Edit Promotion');
            $submit.html('<i class="fas fa-save" aria-hidden="true"></i> Update Promotion');

            var id = $btn.data('id');
            $('#promotion-id').val(id);
            $('#promotion-name').val($btn.data('name'));
            $('#promotion-code').val($btn.data('code'));
            $('#promotion-description').val($btn.data('description'));
            $('#promotion-discount-type').val($btn.data('discount_type'));
            $('#promotion-discount-value').val($btn.data('discount_value'));
            $('#promotion-status').val($btn.data('status'));
            $('#promotion-start-at').val($btn.data('start_at'));
            $('#promotion-expires-at').val($btn.data('expires_at'));
            $('#promotion-new-customers-only').prop('checked', Number($btn.data('new_customers_only')) === 1);
            $('#promotion-existing-customers-only').prop('checked', Number($btn.data('existing_customers_only')) === 1);

            toggleDiscountValueField();

            $('#promotion-form-method').val('PUT');
            $form.attr('action', promotionsBaseUrl + '/' + id);

            $modal.modal('show');
        });

        $form.on('submit', function (e) {
            e.preventDefault();

            var isEdit = $('#promotion-form-method').val() === 'PUT';
            var formData = new FormData(this);

            if (!$('#promotion-new-customers-only').is(':checked')) {
                formData.set('new_customers_only', '0');
            }
            if (!$('#promotion-existing-customers-only').is(':checked')) {
                formData.set('existing_customers_only', '0');
            }

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.promotion-field-error').remove();
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
                    showToast('success', response.message || 'Promotion saved successfully!');
                    reloadTable();
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            var $input = $('[name="' + field + '"]');
                            $input.addClass('is-invalid');
                            $input.closest('.form-group, .promotion-eligibility-row').append(
                                '<span class="promotion-field-error text-danger small d-block mt-1">' + messages[0] + '</span>'
                            );
                        });
                        Swal.fire({ icon: 'error', title: 'Validation failed', text: 'Please check the highlighted fields.' });
                        return;
                    }
                    showToast('error', 'Promotion could not be saved.');
                },
                complete: function () {
                    $submit.prop('disabled', false)
                           .html('<i class="fas fa-save" aria-hidden="true"></i> ' + (isEdit ? 'Update Promotion' : 'Save Promotion'));
                }
            });
        });

        $(document).on('click', '.js-promotion-delete', function () {
            var id = $(this).data('id');
            var name = $(this).data('name') || 'this promotion';

            if (typeof Swal === 'undefined') {
                if (!confirm('Delete ' + name + '?')) { return; }
                doDelete(id, name);
                return;
            }

            Swal.fire({
                title: 'Delete Promotion?',
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
                url: promotionsBaseUrl + '/' + id,
                method: 'POST',
                data: { _method: 'DELETE' },
                success: function (response) {
                    showToast('success', response.message || name + ' deleted successfully!');
                    reloadTable();
                },
                error: function () {
                    showToast('error', 'Promotion could not be deleted.');
                }
            });
        }

        promotionsTable.on('draw', function () {
            var info = promotionsTable.page.info();
            $('#promotion-record-count').text(info.recordsTotal || 0);
        });

    })(jQuery);
    </script>
@endpush
