@extends('layouts.app')

@section('title', 'Weekly Schedule – Edit ' . $day)

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
        <div class="schedule-form-card">

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
                           name="is_active" {{ $isActive ? 'checked' : '' }}>
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
                @forelse ($slots as $index => $time)
                    <div class="slot-row" data-slot="{{ $index + 1 }}">
                        <span class="slot-label">Slot {{ $index + 1 }}</span>
                        <input type="text" class="slot-time-input"
                               name="slots[]" value="{{ $time }}"
                               placeholder="hh:mm AM" maxlength="8" autocomplete="off">
                        <button type="button" class="slot-remove-btn js-remove-slot" title="Remove slot">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                @empty
                    {{-- No slots: render one blank starter row --}}
                    <div class="slot-row" data-slot="1">
                        <span class="slot-label">Slot 1</span>
                        <input type="text" class="slot-time-input"
                               name="slots[]" value=""
                               placeholder="hh:mm AM" maxlength="8" autocomplete="off">
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
            <button type="button" class="schedule-update-btn" id="js-update-schedule">
                Update
            </button>

        </div>{{-- /.schedule-form-card --}}
    </div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    /* ── Slot counter: start after pre-filled slots ── */
    var $list = $('#slot-list');

    function getSlotCount() {
        return $list.find('.slot-row').length;
    }

    /* ── Re-number all slot labels after add/remove ── */
    function reNumberSlots() {
        $list.find('.slot-row').each(function (i) {
            $(this).attr('data-slot', i + 1)
                   .find('.slot-label').text('Slot ' + (i + 1));
        });
    }

    /* ── Add More ── */
    $('#js-add-slot').on('click', function () {
        var n = getSlotCount() + 1;
        var $row = $(
            '<div class="slot-row" data-slot="' + n + '">' +
                '<span class="slot-label">Slot ' + n + '</span>' +
                '<input type="text" class="slot-time-input" name="slots[]"' +
                       ' placeholder="hh:mm AM" maxlength="8" autocomplete="off">' +
                '<button type="button" class="slot-remove-btn js-remove-slot" title="Remove slot">' +
                    '<i class="fas fa-times" aria-hidden="true"></i>' +
                '</button>' +
            '</div>'
        );
        $list.append($row);
        $row.find('.slot-time-input').focus();
    });

    /* ── Remove slot (keep at least 1) ── */
    $list.on('click', '.js-remove-slot', function () {
        if (getSlotCount() <= 1) return;          // guard
        $(this).closest('.slot-row').remove();
        reNumberSlots();
    });

    /* ── Lightweight time-mask: auto-inserts colon and AM/PM hint ── */
    $list.on('input', '.slot-time-input', function () {
        var raw = $(this).val().replace(/[^0-9APMapm\s:]/g, '');
        $(this).val(raw);
    });

    /* ── Update button (UI only) ── */
    $('#js-update-schedule').on('click', function () {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ $day }} schedule updated (simulation)',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        } else {
            alert('{{ $day }} schedule updated (simulation).');
        }
    });

})(jQuery);
</script>
@endpush
