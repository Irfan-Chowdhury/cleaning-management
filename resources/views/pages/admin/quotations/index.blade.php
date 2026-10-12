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
            <div class="quotations-metric-header">
                <span class="quotations-metric-label">Total Quotations</span>
                <span class="quotations-metric-icon">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                </span>
            </div>
            <div class="quotations-metric-value">{{ number_format($metrics['total_count']) }}</div>
        </div>

        {{-- Card 2: Draft --}}
        <div class="quotations-metric-card metric-amber">
            <div class="quotations-metric-header">
                <span class="quotations-metric-label">Draft</span>
                <span class="quotations-metric-icon">
                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                </span>
            </div>
            <div class="quotations-metric-value">{{ number_format($metrics['draft_count']) }}</div>
        </div>

        {{-- Card 3: Converted --}}
        <div class="quotations-metric-card metric-green">
            <div class="quotations-metric-header">
                <span class="quotations-metric-label">Converted</span>
                <span class="quotations-metric-icon">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </span>
            </div>
            <div class="quotations-metric-value">{{ number_format($metrics['converted_count']) }}</div>
        </div>

        {{-- Card 4: Total Revenue (Converted Quotes) --}}
        <div class="quotations-metric-card metric-purple">
            <div class="quotations-metric-header">
                <span class="quotations-metric-label">Total Revenue</span>
                <span class="quotations-metric-icon">
                    <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                </span>
            </div>
            <div class="quotations-metric-value">${{ number_format($metrics['total_revenue'], 2) }}</div>
        </div>
    </div>

    {{-- Quotations Table Card --}}
    <div class="quotations-card">
        <div class="quotations-card-header">
            <div>
                <h2>All Quotations</h2>
                <p>{{ $quotations->count() }} records found</p>
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
                <tbody>
                    @forelse ($quotations as $quote)
                        <tr>
                            {{-- Quote No. --}}
                            <td>
                                <a href="{{ route('quotations.edit', $quote->id) }}" class="quote-number-badge" title="View / Edit Quotation">
                                    <i class="far fa-file-alt" aria-hidden="true"></i>
                                    <span>{{ $quote->quote_number }}</span>
                                </a>
                            </td>

                            {{-- Company --}}
                            <td>
                                <div class="company-meta-cell">
                                    <span class="company-name">{{ $quote->company_name }}</span>
                                    <span class="contact-person"><i class="far fa-user mr-1" aria-hidden="true"></i>{{ $quote->contact_person }}</span>
                                </div>
                            </td>

                            {{-- Quote Date --}}
                            <td>
                                <div class="quote-date-cell">
                                    <i class="far fa-calendar-alt text-muted" aria-hidden="true"></i>
                                    <span>{{ is_string($quote->quote_date) ? date('d M Y', strtotime($quote->quote_date)) : $quote->quote_date->format('d M Y') }}</span>
                                </div>
                            </td>

                            {{-- Amount --}}
                            <td>
                                <span class="quote-amount">${{ number_format((float) $quote->total_amount, 2) }}</span>
                            </td>

                            {{-- Status Badges --}}
                            <td>
                                @if (strtolower($quote->status) === 'converted')
                                    <span class="status-badge status-badge-converted">Converted</span>
                                @else
                                    <span class="status-badge status-badge-draft">Draft</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="quotation-actions">
                                    <a href="{{ route('quotations.edit', $quote->id) }}" 
                                       class="btn btn-sm btn-outline-primary quotation-action-btn" 
                                       title="Edit Quotation">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger quotation-action-btn js-quote-delete" 
                                            data-id="{{ $quote->id }}" 
                                            data-number="{{ $quote->quote_number }}" 
                                            title="Delete Quotation">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No quotations available. Click "Create New Quotation" to add your first quote.
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
    <script src="{{ asset('public/assets/js/quotation.js') }}"></script>
@endpush
