@extends('layouts.app')

@section('title', $isEdit ? 'Edit Quotation - ' . $quotation->quote_number : 'Create New Quotation')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/quotation.css') }}">
@endpush

@section('content')
<div class="quotations-page">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb quotation-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit Quotation' : 'Create New Quotation' }}</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="quotations-header">
        <div>
            <h1 class="quotations-header-title">
                {{ $isEdit ? 'Edit Quotation' : 'Create New Quotation' }}
            </h1>
            <p class="quotations-header-sub">
                {{ $isEdit ? 'Modify the details below to update the quotation for your client.' : 'Fill in the details below to create a quotation for your client.' }}
            </p>
        </div>

        <div>
            <a href="{{ route('quotations.index') }}" class="btn-back-quotations">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Back to Quotations</span>
            </a>
        </div>
    </div>

    {{-- Main Quotation Form --}}
    <form id="quotation-form" method="POST" action="{{ $isEdit ? route('quotations.index') : route('quotations.index') }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        {{-- Hidden fields for state management & calculation submissions --}}
        <input type="hidden" id="form-is-edit" value="{{ $isEdit ? '1' : '0' }}">
        <input type="hidden" id="redirect-index-url" value="{{ route('quotations.index') }}">
        <input type="hidden" id="input-download-pdf" name="download_pdf" value="1">
        <input type="hidden" id="input-subtotal" name="subtotal" value="{{ $quotation->subtotal ?? 0.00 }}">
        <input type="hidden" id="input-gst-amount" name="gst_amount" value="{{ $quotation->gst_amount ?? 0.00 }}">
        <input type="hidden" id="input-total-amount" name="total_amount" value="{{ $quotation->total_amount ?? 0.00 }}">

        {{-- Section 1: Quote Information (Full-Width Card) --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                    <span>Quote Information</span>
                </h2>
            </div>
            <div class="quote-section-body">
                <div class="form-row">
                    {{-- Quote Number (~30% width) --}}
                    <div class="form-group col-12 col-md-4 quote-info-col-num">
                        <label for="quote_number">Quote Number</label>
                        <input type="text" 
                               class="form-control font-weight-bold" 
                               id="quote_number" 
                               name="quote_number" 
                               value="{{ $quotation->quote_number }}" 
                               readonly>
                    </div>

                    {{-- Quote Date (~25% width) --}}
                    <div class="form-group col-12 col-md-3 quote-info-col-date">
                        <label for="quote_date">Quote Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="quote_date" 
                               name="quote_date" 
                               value="{{ is_string($quotation->quote_date) ? $quotation->quote_date : $quotation->quote_date->format('Y-m-d') }}" 
                               required>
                    </div>

                    {{-- ABN (~45% width) --}}
                    <div class="form-group col-12 col-md-5 quote-info-col-abn mb-md-0">
                        <label for="abn">ABN <span class="text-muted font-weight-normal">(Optional)</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="abn" 
                               name="abn" 
                               placeholder="e.g. 51 824 753 556" 
                               value="{{ $quotation->abn ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Prepared For (Full-Width Card, 2 Columns) --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                    <span>Prepared For</span>
                </h2>
            </div>
            <div class="quote-section-body">
                <div class="row">
                    {{-- Left Column: Contact Person & Address --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="contact_person">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   placeholder="Enter contact person name" 
                                   value="{{ $quotation->contact_person ?? '' }}" 
                                   required>
                        </div>
                        <div class="form-group mb-md-0">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="5" 
                                      placeholder="Enter complete site address" 
                                      required 
                                      style="height: 124px;">{{ $quotation->address ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- Right Column: Company Name, Phone, Email --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="company_name" 
                                   name="company_name" 
                                   placeholder="Enter company name" 
                                   value="{{ $quotation->company_name ?? '' }}" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   placeholder="Enter phone number" 
                                   value="{{ $quotation->phone ?? '' }}" 
                                   required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter email address" 
                                   value="{{ $quotation->email ?? '' }}" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Service Details (Full-Width Card, 2 Equal Inputs) --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-sliders-h" aria-hidden="true"></i>
                    <span>Service Details</span>
                </h2>
            </div>
            <div class="quote-section-body">
                <div class="form-row">
                    {{-- Frequency (Input Field) --}}
                    <div class="form-group col-12 col-md-6 mb-md-0">
                        <label for="frequency">Frequency <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="frequency" 
                               name="frequency" 
                               placeholder="e.g. Weekly, Daily, Fortnightly" 
                               value="{{ $quotation->frequency ?? '' }}" 
                               required>
                    </div>

                    {{-- Shift / Schedule (Input Field) --}}
                    <div class="form-group col-12 col-md-6 mb-0">
                        <label for="shift_schedule">Shift / Schedule <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="shift_schedule" 
                               name="shift_schedule" 
                               placeholder="e.g. Morning (08:00 - 12:00)" 
                               value="{{ $quotation->shift_schedule ?? '' }}" 
                               required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Service Items (Full-Width Card) --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-list-ul" aria-hidden="true"></i>
                    <span>Service Items</span>
                </h2>

                <button type="button" class="btn btn-primary btn-add-item-solid" id="btn-add-line-item">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span>Add Line Item</span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table quote-items-table" id="line-items-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">#</th>
                            <th>Description</th>
                            <th class="text-right" style="width: 200px;">Amount (ex GST)</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="line-items-tbody">
                        @php
                            $items = $quotation->items ?? collect();
                            if ($items->isEmpty()) {
                                $items = collect([
                                    (object) ['description' => '', 'amount' => 0.00]
                                ]);
                            }
                        @endphp

                        @foreach ($items as $idx => $item)
                            <tr class="line-item-row">
                                <td class="line-item-index text-center align-middle">{{ $idx + 1 }}</td>
                                <td>
                                    <input type="text" 
                                           name="items[{{ $idx }}][description]" 
                                           class="form-control line-item-desc" 
                                           placeholder="Enter description" 
                                           value="{{ $item->description ?? '' }}" 
                                           required>
                                </td>
                                <td style="width: 200px;">
                                    <div class="amount-input-group">
                                        <span class="currency-symbol">$</span>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               name="items[{{ $idx }}][amount]" 
                                               class="form-control line-item-amount text-right" 
                                               placeholder="0.00" 
                                               value="{{ number_format((float) ($item->amount ?? 0), 2, '.', '') }}" 
                                               required>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-trash js-remove-line-item" title="Delete">
                                        <i class="fas fa-trash-alt text-danger" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- Integrated Pricing Summary Table Rows --}}
                    <tfoot>
                        <tr class="table-summary-row border-top">
                            <td colspan="2" class="text-right font-weight-bold text-muted py-2 align-middle border-0">Subtotal:</td>
                            <td class="text-right font-weight-bold py-2 align-middle border-0">
                                <span id="calc-subtotal">${{ number_format((float) ($quotation->subtotal ?? 0), 2) }}</span>
                            </td>
                            <td class="border-0"></td>
                        </tr>
                        <tr class="table-summary-row">
                            <td colspan="2" class="text-right font-weight-bold text-muted py-2 align-middle border-0">GST (10%):</td>
                            <td class="text-right font-weight-bold py-2 align-middle border-0">
                                <span id="calc-gst">${{ number_format((float) ($quotation->gst_amount ?? 0), 2) }}</span>
                            </td>
                            <td class="border-0"></td>
                        </tr>
                        <tr class="table-summary-total">
                            <td colspan="2" class="text-right font-weight-bold py-3 align-middle border-0">Total (inc GST):</td>
                            <td class="text-right font-weight-bold py-3 align-middle border-0" style="font-size: 16px;">
                                <span id="calc-total">${{ number_format((float) ($quotation->total_amount ?? 0), 2) }}</span>
                            </td>
                            <td class="border-0"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Bottom-Right Action Button ("Save & Download PDF" only) --}}
        <div class="quotation-bottom-actions">
            <button type="submit" class="btn btn-primary btn-primary-blue" id="btn-save-pdf">
                <i class="fas fa-file-pdf mr-1" aria-hidden="true"></i>
                <span>{{ $isEdit ? 'Update & Download PDF' : 'Save & Download PDF' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/quotation.js') }}"></script>
@endpush
