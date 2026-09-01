@extends('layouts.app')

@section('title', 'Weekly Schedule')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/weekly-schedule.css') }}">
@endpush

@section('content')
    <div class="schedule-page">
        <div class="schedule-header">
            <h1>Weekly Schedule</h1>
            <p>Manage cleaning slots and availability for each day of the week</p>
        </div>

        <div class="schedule-table-card">
            <div class="schedule-table-card-header">
                <h2>Weekly Shifts</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered schedule-table" style="width: 100%">
                    <thead>
                        <tr class="text-center">
                            <th>Day</th>
                            <th>Total Slot</th>
                            <th>Status</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedule as $row)
                            <tr class="text-center">
                                <td><strong>{{ $row->day_of_week }}</strong></td>
                                <td>{{ $row->slots_count }}</td>
                                <td>
                                    @php
                                        $badgeClass = $row->is_active ? 'badge-success' : 'badge-secondary';
                                        $status = $row->is_active ? 'active' : 'inactive';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="padding: 6px 8px; font-weight: 700; border-radius: 999px; min-width: 68px;">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('weekly-schedule.edit', strtolower($row->day_of_week)) }}"
                                       class="schedule-action-btn" title="Edit {{ $row->day_of_week }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
