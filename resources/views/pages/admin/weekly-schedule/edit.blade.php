@extends('layouts.app')

@section('title', 'Weekly Schedule - Edit ' . $day)

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/weekly-schedule.css') }}">
@endpush

@section('content')
    <div class="schedule-page">

        {{-- Page header --}}
        <div class="schedule-header">
            <h1>Weekly Schedule</h1>
            <p>
                <a href="{{ route('weekly-schedule.index') }}" class="text-muted" style="font-size:13px;">
                    <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Back to schedule
                </a>
            </p>
        </div>

        {{-- Form card --}}
        <form class="schedule-form-card" id="weekly-schedule-form" method="POST" action="{{ route('weekly-schedule.update', strtolower($day)) }}">
            @csrf
            @method('PUT')

            {{-- Day of Week --}}
            <div class="form-group mb-3">
                <label for="day_of_week">Day of Week</label>
                <input type="text" id="day_of_week" name="day_of_week"
                       class="form-control" value="{{ $day }}" readonly>
            </div>

            {{-- Active checkbox --}}
            <div class="form-group mb-4">
                <label class="d-block">Active</label>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active"
                           name="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active"
                           style="font-weight: normal; cursor: pointer; padding-top: 1px;">
                        Enable this day
                    </label>
                </div>
            </div>

            {{-- Slots section --}}
            <div class="form-group mb-2">
                <label>Time Slots</label>
            </div>

            <div class="slot-list" id="slot-list">
                @forelse ($slots as $index => $slot)
                    <div class="slot-row" data-slot="{{ $index + 1 }}">
                        <span class="slot-label">Slot {{ $index + 1 }}</span>
                        <input type="time" class="slot-time-input" data-slot-field="start_time"
                               name="slots[{{ $index }}][start_time]"
                               value="{{ substr($slot->start_time, 0, 5) }}"
                               autocomplete="off">
                        <input type="time" class="slot-time-input" data-slot-field="end_time"
                               name="slots[{{ $index }}][end_time]"
                               value="{{ $slot->end_time ? substr($slot->end_time, 0, 5) : '' }}"
                               autocomplete="off">
                        <button type="button" class="slot-remove-btn js-remove-slot" title="Remove slot">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                @empty
                    {{-- No slots: render one blank starter row --}}
                    <div class="slot-row" data-slot="1">
                        <span class="slot-label">Slot 1</span>
                        <input type="time" class="slot-time-input" data-slot-field="start_time"
                               name="slots[0][start_time]" value=""
                               autocomplete="off">
                        <input type="time" class="slot-time-input" data-slot-field="end_time"
                               name="slots[0][end_time]" value=""
                               autocomplete="off">
                        <button type="button" class="slot-remove-btn js-remove-slot" title="Remove slot">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Add More --}}
            <button type="button" class="schedule-add-more-btn" id="js-add-slot">
                <i class="fas fa-plus" aria-hidden="true"></i> Add More
            </button>

            {{-- Update --}}
            <button type="submit" class="schedule-update-btn" id="js-update-schedule">
                Update
            </button>

        </form>
        {{-- /.schedule-form-card --}}
    </div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var $list = $('#slot-list');
    var $form = $('#weekly-schedule-form');
    var $submit = $('#js-update-schedule');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    function showToast(icon, title) {
        if (typeof Swal === 'undefined') { alert(title); return; }
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

    function getSlotCount() {
        return $list.find('.slot-row').length;
    }

    function fieldNameFromErrorKey(field) {
        var parts = field.split('.');

        if (parts.length === 3 && parts[0] === 'slots') {
            return 'slots[' + parts[1] + '][' + parts[2] + ']';
        }

        return field;
    }

    /* Re-number all slot labels and input names after add/remove */
    function reNumberSlots() {
        $list.find('.slot-row').each(function (i) {
            $(this).attr('data-slot', i + 1)
                   .find('.slot-label').text('Slot ' + (i + 1));
            $(this).find('[data-slot-field="start_time"]').attr('name', 'slots[' + i + '][start_time]');
            $(this).find('[data-slot-field="end_time"]').attr('name', 'slots[' + i + '][end_time]');
        });
    }

    /* Add More */
    $('#js-add-slot').on('click', function () {
        var n = getSlotCount() + 1;
        var $row = $(
            '<div class="slot-row" data-slot="' + n + '">' +
                '<span class="slot-label">Slot ' + n + '</span>' +
                '<input type="time" class="slot-time-input" data-slot-field="start_time" name="slots[' + (n - 1) + '][start_time]" autocomplete="off">' +
                '<input type="time" class="slot-time-input" data-slot-field="end_time" name="slots[' + (n - 1) + '][end_time]" autocomplete="off">' +
                '<button type="button" class="slot-remove-btn js-remove-slot" title="Remove slot">' +
                    '<i class="fas fa-times" aria-hidden="true"></i>' +
                '</button>' +
            '</div>'
        );
        $list.append($row);
        $row.find('[data-slot-field="start_time"]').focus();
    });

    /* Remove slot (keep at least 1) */
    $list.on('click', '.js-remove-slot', function () {
        if (getSlotCount() <= 1) return;
        $(this).closest('.slot-row').remove();
        reNumberSlots();
    });

    /* Update schedule via Ajax */
    $form.on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        if (!$('#is_active').is(':checked')) {
            formData.set('is_active', '0');
        }

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.schedule-field-error').remove();
        $submit.prop('disabled', true)
               .html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Updating...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                showToast('success', response.message || '{{ $day }} schedule updated successfully!');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        if (field === 'slots') {
                            $list.before('<span class="schedule-field-error text-danger small d-block mb-2">' + messages[0] + '</span>');
                            return;
                        }

                        var $input = $('[name="' + fieldNameFromErrorKey(field) + '"]');
                        $input.addClass('is-invalid');
                        $input.closest('.slot-row, .form-group').append(
                            '<span class="schedule-field-error text-danger small d-block mt-1">' + messages[0] + '</span>'
                        );
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Validation failed', text: 'Please check the highlighted fields.' });
                    }
                    return;
                }

                showToast('error', 'Schedule could not be updated.');
            },
            complete: function () {
                $submit.prop('disabled', false).text('Update');
            }
        });
    });

})(jQuery);
</script>
@endpush
