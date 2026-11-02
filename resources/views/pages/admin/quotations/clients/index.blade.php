@extends('layouts.app')

@section('title', 'Client Profiles - Quotation Management')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/quotation.css') }}">
@endpush

@section('content')
<div class="quotations-page">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb quotation-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Clients</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="quotations-header">
        <div>
            <h1 class="quotations-header-title">Clients Directory</h1>
            <p class="quotations-header-sub">Manage client contact details and company profiles for quotations and proposals</p>
        </div>
        <div class="quotations-header-actions d-flex align-items-center flex-wrap">
            <button type="button" class="btn quotations-client-btn mr-2" id="btn-open-create-client" data-toggle="modal" data-target="#clientProfileModal">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                <span>Add Client</span>
            </button>
        </div>
    </div>

    {{-- Clients Table Card --}}
    <div class="quotations-card">
        <div class="quotations-card-header">
            <div>
                <h2>All Clients</h2>
                <p>Saved client profiles available for quotations</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="clients-table" class="table table-hover table-bordered nowrap clients-table quotations-table" style="width: 100%" data-ajax-url="{{ route('quotations.clients') }}">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>Contact Person</th>
                        <th>Company Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-center" style="width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Client Profile Modal --}}
    <div class="modal fade quotation-modal" id="clientProfileModal" tabindex="-1" role="dialog" aria-labelledby="clientProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientProfileModalLabel">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span id="clientModalTitleText">Create Client Profile</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="client-profile-form" action="{{ route('quotations.clients.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" id="client_id" name="client_id" value="">

                    <div class="modal-body">
                        <div class="form-row">
                            {{-- Contact Person * --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_contact_person">Contact Person <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="client_contact_person"
                                       name="contact_person"
                                       placeholder="Enter contact person name"
                                       required>
                                <div class="invalid-feedback" id="error-contact_person"></div>
                            </div>

                            {{-- Company Name (Optional / Nullable) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_company_name">Company Name <span class="text-muted font-weight-normal">(Optional)</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="client_company_name"
                                       name="company_name"
                                       placeholder="Enter company name">
                                <div class="invalid-feedback" id="error-company_name"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Phone * (Unique) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_phone">Phone <span class="text-danger">*</span></label>
                                <input type="tel"
                                       class="form-control"
                                       id="client_phone"
                                       name="phone"
                                       placeholder="Enter phone number"
                                       required>
                                <div class="invalid-feedback" id="error-phone"></div>
                            </div>

                            {{-- Email * (Unique) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_email">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control"
                                       id="client_email"
                                       name="email"
                                       placeholder="Enter email address"
                                       required>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Address * --}}
                            <div class="form-group col-12 mb-0">
                                <label for="client_address">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control"
                                          id="client_address"
                                          name="address"
                                          rows="3"
                                          placeholder="Enter complete site address"
                                          required></textarea>
                                <div class="invalid-feedback" id="error-address"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary quotations-primary-btn" id="btn-save-client-profile">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            <span id="btn-save-client-text">Save Client Profile</span>
                        </button>
                    </div>
                </form>
            </div>
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
