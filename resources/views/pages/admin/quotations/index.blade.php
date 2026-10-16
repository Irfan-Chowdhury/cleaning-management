@extends('layouts.app')

@section('title', 'Quotations Management')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/quotation.css') }}">
@endpush

@section('content')
<div class="quotations-page">
    {{-- Page Header --}}
    <div class="quotations-header">
        <div>
            <h1 class="quotations-header-title">Quotations Management</h1>
            <p class="quotations-header-sub">Create, organize, and monitor cleaning service quotations and client proposals</p>
        </div>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary quotations-primary-btn">
            <i class="fas fa-plus-circle" aria-hidden="true"></i>
            <span>Create New Quotation</span>
        </a>
    </div>

    {{-- Metric Cards (4 Summary Cards) --}}
    <div class="quotations-metric-grid">
        {{-- Card 1: Total Quotations --}}
        <div class="quotations-metric-card metric-blue">
            <span class="quotations-metric-icon" aria-hidden="true">
                <i class="fas fa-clipboard-list"></i>
            </span>
            <div class="quotations-metric-content">
                <span class="quotations-metric-label">Total Quotations</span>
                <span class="quotations-metric-value">{{ number_format($metrics['total_count']) }}</span>
            </div>
        </div>

        {{-- Card 2: Draft --}}
        <div class="quotations-metric-card metric-navy">
            <span class="quotations-metric-icon" aria-hidden="true">
                <i class="fas fa-file-alt"></i>
            </span>
            <div class="quotations-metric-content">
                <span class="quotations-metric-label">Draft</span>
                <span class="quotations-metric-value">{{ number_format($metrics['draft_count']) }}</span>
            </div>
        </div>

        {{-- Card 3: Converted --}}
        <div class="quotations-metric-card metric-green">
            <span class="quotations-metric-icon" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="quotations-metric-content">
                <span class="quotations-metric-label">Converted</span>
                <span class="quotations-metric-value">{{ number_format($metrics['invoiced_count']) }}</span>
            </div>
        </div>

        {{-- Card 4: Total Revenue (Converted Quotes) --}}
        <div class="quotations-metric-card metric-orange">
            <span class="quotations-metric-icon" aria-hidden="true">
                <i class="fas fa-dollar-sign"></i>
            </span>
            <div class="quotations-metric-content">
                <span class="quotations-metric-label">Total Revenue</span>
                <span class="quotations-metric-value">${{ number_format($metrics['total_revenue'], 2) }}</span>
                <span class="quotations-metric-support">From invoiced quotes</span>
            </div>
        </div>

        {{-- Card 5: Current Month Revenue from Converted Quotes --}}
        <div class="quotations-metric-card metric-purple">
            <span class="quotations-metric-icon" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="quotations-metric-content">
                <span class="quotations-metric-label">Total Revenue This {{ $metrics['current_month_name'] }}</span>
                <span class="quotations-metric-value">${{ number_format($metrics['current_month_revenue'], 2) }}</span>
                <span class="quotations-metric-support">From invoiced quotes</span>
            </div>
        </div>
    </div>

    {{-- Quotations Table Card --}}
    <div class="quotations-card">
        <div class="quotations-card-header">
            <div>
                <h2>All Quotations</h2>
                <p>{{ number_format($metrics['total_count']) }} records found</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="quotations-table" class="table table-hover table-bordered nowrap quotations-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>Quote No.</th>
                        <th>Company</th>
                        <th>Quote Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 110px;">Actions</th>
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
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('public/assets/js/quotation.js') }}"></script>
@endpush
