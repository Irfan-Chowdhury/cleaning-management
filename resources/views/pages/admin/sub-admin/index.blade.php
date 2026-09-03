@extends('layouts.app')

@section('title', 'Sub Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/sub-admin.css') }}">
@endpush

@section('content')
    <div class="sub-admin-page">
        <div class="sub-admin-header">
            <div>
                <h1>Sub Admin</h1>
                <p>Manage sub admin users and their administrative access</p>
            </div>
            <button type="button" class="btn btn-primary sub-admin-primary-btn js-sub-admin-add" data-toggle="modal" data-target="#sub-admin-form-modal">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Sub Admin
            </button>
        </div>

        <div class="sub-admin-table-card">
            <div class="sub-admin-table-card-header">
                <div>
                    <h2>Sub Admin List</h2>
                    <p><span id="sub-admin-record-count">{{ $subAdminCount }}</span> records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sub-admin-table" class="table table-hover table-bordered nowrap sub-admin-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sub-admin-form-modal" tabindex="-1" role="dialog" aria-labelledby="sub-admin-form-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content sub-admin-modal-content">
                <form id="sub-admin-modal-form" method="POST" action="{{ route('sub-admin.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="sub-admin-form-method" value="POST">
                    <input type="hidden" name="id" id="sub-admin-id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="sub-admin-form-modal-title">Add Sub Admin</h5>
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
                                <label for="email">Email Address</label>
                                <div class="input-icon">
                                    <i class="far fa-envelope" aria-hidden="true"></i>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="phone">Phone Number</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="photo">Profile Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*">
                                <label class="custom-file-label" for="photo">Choose profile photo...</label>
                            </div>
                            <div class="photo-preview-container d-none" id="photo-preview-box">
                                <img src="" id="photo-preview-img" class="photo-preview-img" alt="Preview">
                                <small class="text-muted">Current / Selected Image</small>
                            </div>
                        </div>

                        <div class="form-row password-fields">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <div class="input-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Create password">
                                    <small class="form-text text-muted password-hint d-none">Leave blank to keep current password.</small>
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

                    <div class="modal-footer sub-admin-modal-actions">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary sub-admin-primary-btn" id="sub-admin-modal-submit">
                            <i class="fas fa-save" aria-hidden="true"></i> Save Sub Admin
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
            var subAdminStoreUrl = @json(route('sub-admin.store'));
            var subAdminIndexUrl = @json(route('sub-admin.index'));
            var subAdminBaseUrl = @json(url('/sub-admins'));
            var $subAdminModal = $('#sub-admin-form-modal');
            var $subAdminForm = $('#sub-admin-modal-form');
            var $subAdminSubmit = $('#sub-admin-modal-submit');
            var subAdminTable = $('#sub-admin-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: subAdminIndexUrl,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                responsive: true,
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', name: 'id', searchable: false },
                    { data: 'image', name: 'photo', orderable: false, searchable: false },
                    { data: 'name', name: 'first_name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { orderable: false, targets: [1, 5] }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search sub admin...'
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

            function resetSubAdminForm() {
                $subAdminForm[0].reset();
                $subAdminForm.find('.is-invalid').removeClass('is-invalid');
                $subAdminForm.find('.sub-admin-field-error').remove();
                $('#sub-admin-form-method').val('POST');
                $('#sub-admin-id').val('');
                $('#is_active').prop('checked', true);
                $('.password-hint').addClass('d-none');
                $('#password').prop('required', true);
                $('#photo-preview-box').addClass('d-none');
                $('#photo-preview-img').attr('src', '');
                $('.custom-file-label').html('Choose profile photo...');
                $subAdminForm.attr('action', subAdminStoreUrl);
            }

            function reloadSubAdminTable() {
                subAdminTable.ajax.reload(function(json) {
                    $('#sub-admin-record-count').text(json.recordsTotal || 0);
                }, false);
            }

            $('#photo').on('change', function() {
                var file = this.files[0];
                if (file) {
                    $('.custom-file-label').html(file.name);
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photo-preview-img').attr('src', e.target.result);
                        $('#photo-preview-box').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.js-sub-admin-add').on('click', function () {
                resetSubAdminForm();
                $('#sub-admin-form-modal-title').text('Add Sub Admin');
                $('#sub-admin-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Save Sub Admin');
            });

            $(document).on('click', '.js-sub-admin-edit', function () {
                var $button = $(this);
                resetSubAdminForm();

                $('#sub-admin-form-modal-title').text('Edit Sub Admin');
                $('#sub-admin-id').val($button.data('id'));
                $('#first_name').val($button.data('first_name'));
                $('#last_name').val($button.data('last_name'));
                $('#email').val($button.data('email'));
                $('#phone').val($button.data('phone'));
                $('#is_active').prop('checked', Number($button.data('is_active')) === 1);
                $('#sub-admin-form-method').val('PUT');
                $('.password-hint').removeClass('d-none');
                $('#password').prop('required', false);

                var photoUrl = $button.data('photo_url');
                if (photoUrl) {
                    $('#photo-preview-img').attr('src', photoUrl);
                    $('#photo-preview-box').removeClass('d-none');
                }

                $subAdminForm.attr('action', subAdminBaseUrl + '/' + $button.data('id'));
                $('#sub-admin-modal-submit').html('<i class="fas fa-save" aria-hidden="true"></i> Update Sub Admin');
            });

            $subAdminForm.on('submit', function (e) {
                e.preventDefault();

                var isEdit = $('#sub-admin-form-method').val() === 'PUT';
                var formData = new FormData(this);

                $subAdminForm.find('.is-invalid').removeClass('is-invalid');
                $subAdminForm.find('.sub-admin-field-error').remove();
                $subAdminSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Saving...');

                $.ajax({
                    url: $subAdminForm.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $subAdminModal.modal('hide');
                        showToast('success', response.message || 'Sub Admin saved successfully!');
                        reloadSubAdminTable();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                var $input = $('[name="' + field + '"]');
                                $input.addClass('is-invalid');
                                $input.closest('.form-group').append('<span class="sub-admin-field-error text-danger small d-block mt-1">' + messages[0] + '</span>');
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation failed',
                                text: 'Please check the highlighted fields.'
                            });
                            return;
                        }

                        showToast('error', 'Sub Admin could not be saved.');
                    },
                    complete: function() {
                        $subAdminSubmit.prop('disabled', false).html('<i class="fas fa-save" aria-hidden="true"></i> ' + (isEdit ? 'Update Sub Admin' : 'Save Sub Admin'));
                    }
                });
            });

            $(document).on('click', '.js-sub-admin-delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this sub admin';

                Swal.fire({
                    title: 'Delete Sub Admin?',
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
                        url: subAdminBaseUrl + '/' + id,
                        method: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            showToast('success', response.message || 'Sub Admin deleted successfully!');
                            reloadSubAdminTable();
                        },
                        error: function() {
                            showToast('error', 'Sub Admin could not be deleted.');
                        }
                    });
                });
            });
        });
    </script>
@endpush
