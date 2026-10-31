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
    <form id="quotation-form" method="POST"
          action="{{ $isEdit ? route('quotations.update', $quotation->id) : route('quotations.store') }}"
          novalidate>
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        {{-- Hidden fields for real-time calculation submission --}}
        <input type="hidden" id="form-is-edit"      value="{{ $isEdit ? '1' : '0' }}">
        <input type="hidden" id="redirect-index-url" value="{{ route('quotations.index') }}">
        <input type="hidden" id="input-subtotal"     name="subtotal"      value="{{ old('subtotal',      $quotation->subtotal ?? 0.00) }}">
        <input type="hidden" id="input-gst-amount"   name="gst_amount"    value="{{ old('gst_amount',    $quotation->gst_amount ?? 0.00) }}">
        <input type="hidden" id="input-total-amount" name="total_amount"  value="{{ old('total_amount',  $quotation->total_amount ?? 0.00) }}">

        {{-- ── Section 1: Quote Information ── --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                    <span>Quote Information</span>
                </h2>
            </div>
            <div class="quote-section-body">
                <div class="form-row">
                    {{-- Quote Number (~30%) --}}
                    <div class="form-group col-12 col-md-4 quote-info-col-num">
                        <label for="quote_number">Quote Number</label>
                        <input type="text"
                               class="form-control font-weight-bold"
                               id="quote_number"
                               name="quote_number"
                               value="{{ $quotation->quote_number }}"
                               readonly>
                    </div>

                    {{-- Quote Date (~25%) --}}
                    <div class="form-group col-12 col-md-3 quote-info-col-date">
                        <label for="quote_date">Quote Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('quote_date') is-invalid @enderror"
                               id="quote_date"
                               name="quote_date"
                               value="{{ old('quote_date', is_string($quotation->quote_date) ? $quotation->quote_date : $quotation->quote_date->format('Y-m-d')) }}"
                               required>
                        @error('quote_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ABN (~45%) --}}
                    <div class="form-group col-12 col-md-5 quote-info-col-abn mb-md-0">
                        <label for="abn">ABN <span class="text-muted font-weight-normal">(Optional)</span></label>
                        <input type="text"
                               class="form-control @error('abn') is-invalid @enderror"
                               id="abn"
                               name="abn"
                               placeholder="e.g. 51 824 753 556"
                               value="{{ old('abn', $quotation->abn ?? '') }}">
                        @error('abn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Prepared For ── --}}
        <div class="quote-section-card has-dropdown" style="z-index: 50; overflow: visible;">
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
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   id="contact_person"
                                   name="contact_person"
                                   placeholder="Enter contact person name"
                                   value="{{ old('contact_person', $quotation->contact_person ?? '') }}"
                                   required>
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-md-0">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address"
                                      name="address"
                                      rows="5"
                                      placeholder="Enter complete site address"
                                      required
                                      style="height: 124px;">{{ old('address', $quotation->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Right Column: Company Name, Phone, Email --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="company_name">Company Name <span class="text-muted font-weight-normal">(Optional)</span></label>
                            <input type="text"
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name"
                                   name="company_name"
                                   placeholder="Enter company name"
                                   value="{{ old('company_name', $quotation->company_name ?? '') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <div class="phone-search-wrapper position-relative">
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       placeholder="Enter or search phone number"
                                       value="{{ old('phone', $quotation->phone ?? '') }}"
                                       autocomplete="off"
                                       required>
                                <span class="phone-search-icon"><i class="fas fa-search text-muted"></i></span>
                                <div id="phone-search-dropdown" class="phone-autocomplete-dropdown" style="display: none;"></div>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="Enter email address"
                                   value="{{ old('email', $quotation->email ?? '') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 3: Service Details ── --}}
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-sliders-h" aria-hidden="true"></i>
                    <span>Service Details</span>
                </h2>
            </div>
            <div class="quote-section-body">
                <div class="form-row">
                    {{-- Frequency --}}
                    <div class="form-group col-12 col-md-6 mb-md-0">
                        <label for="frequency">Frequency <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('frequency') is-invalid @enderror"
                               id="frequency"
                               name="frequency"
                               placeholder="e.g. Weekly, Daily, Fortnightly"
                               value="{{ old('frequency', $quotation->frequency ?? '') }}"
                               required>
                        @error('frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Shift / Schedule --}}
                    <div class="form-group col-12 col-md-6 mb-0">
                        <label for="shift_schedule">Shift / Schedule <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('shift_schedule') is-invalid @enderror"
                               id="shift_schedule"
                               name="shift_schedule"
                               placeholder="e.g. Morning (08:00 - 12:00)"
                               value="{{ old('shift_schedule', $quotation->shift_schedule ?? '') }}"
                               required>
                        @error('shift_schedule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 4: Service Items ── --}}
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

            {{-- Items validation error --}}
            @error('items')
                <div class="alert alert-danger mx-3 mt-3 mb-0 py-2" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </div>
            @enderror

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
                            // Use old() input on validation failure; otherwise use model/object items
                            $oldItems = old('items');
                            if ($oldItems) {
                                $items = collect($oldItems)->map(fn($i) => (object) $i);
                            } else {
                                $items = $quotation->items ?? collect();
                                if ($items->isEmpty()) {
                                    $items = collect([(object) ['description' => '', 'amount' => 0.00]]);
                                }
                            }
                        @endphp

                        @foreach ($items as $idx => $item)
                            <tr class="line-item-row">
                                <td class="line-item-index text-center align-middle">{{ $idx + 1 }}</td>
                                <td>
                                    <input type="text"
                                           name="items[{{ $idx }}][description]"
                                           class="form-control line-item-desc @error('items.' . $idx . '.description') is-invalid @enderror"
                                           placeholder="Enter description"
                                           value="{{ $item->description ?? '' }}"
                                           required>
                                    @error('items.' . $idx . '.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td style="width: 200px;">
                                    <div class="amount-input-group">
                                        <span class="currency-symbol">$</span>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="items[{{ $idx }}][amount]"
                                               class="form-control line-item-amount text-right @error('items.' . $idx . '.amount') is-invalid @enderror"
                                               placeholder="0.00"
                                               value="{{ number_format((float) ($item->amount ?? 0), 2, '.', '') }}"
                                               required>
                                        @error('items.' . $idx . '.amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                    {{-- Integrated Pricing Summary --}}
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

        {{-- ── Section 5: Status (Create Page Only) ── --}}
        @if (! $isEdit)
        <div class="quote-section-card">
            <div class="quote-section-header">
                <h2 class="quote-section-title">
                    <i class="fas fa-file-signature" aria-hidden="true"></i>
                    <span>Status</span>
                </h2>
            </div>
            <div class="quote-section-body py-3">
                <div class="d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline mr-4">
                        <input type="radio"
                               class="custom-control-input quote-status-radio"
                               id="status-draft"
                               name="status"
                               value="draft"
                               {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status-draft">
                            Draft
                        </label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio"
                               class="custom-control-input quote-status-radio"
                               id="status-invoiced"
                               name="status"
                               value="invoiced"
                               {{ old('status') === 'invoiced' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status-invoiced">
                            Invoiced
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Bottom Action Button --}}
        <div class="quotation-bottom-actions">
            <button type="submit" class="btn btn-primary btn-primary-blue" id="btn-save-pdf"
                    @if ($isEdit) name="action" value="update" @endif>
                <i class="fas fa-save mr-1" aria-hidden="true"></i>
                <span id="btn-save-label">{{ $isEdit ? 'Update & Download PDF' : (old('status', 'draft') === 'invoiced' ? 'Save & Download Invoiced' : 'Save & Download Draft') }}</span>
            </button>
            @if ($isEdit)
                <button type="submit"
                        class="btn {{ $quotation->status === 'draft' ? 'btn-success' : 'btn-secondary' }}"
                        name="action"
                        value="convert"
                        @disabled($quotation->status !== 'draft')>
                    <i class="fas {{ $quotation->status === 'draft' ? 'fa-file-invoice-dollar' : 'fa-check-circle' }} mr-1" aria-hidden="true"></i>
                    <span>{{ $quotation->status === 'draft' ? 'Convert to Invoice' : 'Invoiced' }}</span>
                </button>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/quotation.js') }}"></script>
    @if (session('quotation_pdf_download'))
        <script>
            window.addEventListener('load', function () {
                var downloadFrame = document.createElement('iframe');
                downloadFrame.hidden = true;
                downloadFrame.src = @json(session('quotation_pdf_download'));
                document.body.appendChild(downloadFrame);
            });
        </script>
    @endif
@endpush
