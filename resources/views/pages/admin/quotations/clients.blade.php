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
            <button type="button" class="btn quotations-client-btn mr-2" data-toggle="modal" data-target="#clientProfileModal">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                <span>Add Client</span>
            </button>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary quotations-primary-btn">
                <i class="fas fa-plus-circle" aria-hidden="true"></i>
                <span>Create New Quotation</span>
            </a>
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
            <table id="clients-table" class="table table-hover table-bordered nowrap clients-table quotations-table" style="width: 100%">
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
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold text-dark">John Smith</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border text-dark px-2 py-1 font-weight-bold">
                                <i class="far fa-building mr-1 text-primary"></i> Acme Cleaning Co
                            </span>
                        </td>
                        <td>
                            <a href="tel:0412345678" class="text-muted"><i class="fas fa-phone-alt mr-1 text-muted"></i> 0412 345 678</a>
                        </td>
                        <td>
                            <a href="mailto:john@acmecleaning.com.au" class="text-muted"><i class="far fa-envelope mr-1 text-muted"></i> john@acmecleaning.com.au</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 220px;" title="123 Test Street, Sydney NSW 2000">
                                123 Test Street, Sydney NSW 2000
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="quotation-actions justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn" data-toggle="modal" data-target="#clientProfileModal" title="Edit Client">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn" title="Delete Client">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold text-dark">Sarah Jenkins</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border text-dark px-2 py-1 font-weight-bold">
                                <i class="far fa-building mr-1 text-primary"></i> Prime Corporate Office
                            </span>
                        </td>
                        <td>
                            <a href="tel:0423456789" class="text-muted"><i class="fas fa-phone-alt mr-1 text-muted"></i> 0423 456 789</a>
                        </td>
                        <td>
                            <a href="mailto:sarah@primeoffice.com.au" class="text-muted"><i class="far fa-envelope mr-1 text-muted"></i> sarah@primeoffice.com.au</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 220px;" title="45 George Street, Parramatta NSW 2150">
                                45 George Street, Parramatta NSW 2150
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="quotation-actions justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn" data-toggle="modal" data-target="#clientProfileModal" title="Edit Client">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn" title="Delete Client">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold text-dark">Michael Wong</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border text-dark px-2 py-1 font-weight-bold">
                                <i class="far fa-building mr-1 text-primary"></i> Harborview Medical Clinic
                            </span>
                        </td>
                        <td>
                            <a href="tel:0434567890" class="text-muted"><i class="fas fa-phone-alt mr-1 text-muted"></i> 0434 567 890</a>
                        </td>
                        <td>
                            <a href="mailto:m.wong@harborview.com.au" class="text-muted"><i class="far fa-envelope mr-1 text-muted"></i> m.wong@harborview.com.au</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 220px;" title="88 Pacific Highway, North Sydney NSW 2060">
                                88 Pacific Highway, North Sydney NSW 2060
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="quotation-actions justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn" data-toggle="modal" data-target="#clientProfileModal" title="Edit Client">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn" title="Delete Client">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold text-dark">Emma Davis</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal">
                                <em>None (Individual)</em>
                            </span>
                        </td>
                        <td>
                            <a href="tel:0445678901" class="text-muted"><i class="fas fa-phone-alt mr-1 text-muted"></i> 0445 678 901</a>
                        </td>
                        <td>
                            <a href="mailto:emma.davis@gmail.com" class="text-muted"><i class="far fa-envelope mr-1 text-muted"></i> emma.davis@gmail.com</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 220px;" title="12 Ocean View Road, Manly NSW 2095">
                                12 Ocean View Road, Manly NSW 2095
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="quotation-actions justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn" data-toggle="modal" data-target="#clientProfileModal" title="Edit Client">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn" title="Delete Client">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold text-dark">David Miller</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border text-dark px-2 py-1 font-weight-bold">
                                <i class="far fa-building mr-1 text-primary"></i> Miller Logistics Hub
                            </span>
                        </td>
                        <td>
                            <a href="tel:0456789012" class="text-muted"><i class="fas fa-phone-alt mr-1 text-muted"></i> 0456 789 012</a>
                        </td>
                        <td>
                            <a href="mailto:david@millerlogistics.com.au" class="text-muted"><i class="far fa-envelope mr-1 text-muted"></i> david@millerlogistics.com.au</a>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 220px;" title="10 Warehouse Way, Eastern Creek NSW 2766">
                                10 Warehouse Way, Eastern Creek NSW 2766
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="quotation-actions justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn" data-toggle="modal" data-target="#clientProfileModal" title="Edit Client">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn" title="Delete Client">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Client Profile Modal (UI Only) --}}
    <div class="modal fade quotation-modal" id="clientProfileModal" tabindex="-1" role="dialog" aria-labelledby="clientProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientProfileModalLabel">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span>Client Profile</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="client-profile-form" novalidate>
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
                            </div>

                            {{-- Company Name (Optional / Nullable) --}}
                            <div class="form-group col-12 col-md-6">
                                <label for="client_company_name">Company Name <span class="text-muted font-weight-normal">(Optional)</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="client_company_name"
                                       name="company_name"
                                       placeholder="Enter company name">
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
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary quotations-primary-btn" id="btn-save-client-profile">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            <span>Save Client Profile</span>
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
    <script>
        $(document).ready(function () {
            if ($('#clients-table').length) {
                $('#clients-table').DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [[0, 'asc']],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search clients...',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ clients',
                        infoEmpty: 'No clients found',
                        paginate: {
                            previous: '<i class="fas fa-chevron-left"></i>',
                            next: '<i class="fas fa-chevron-right"></i>'
                        }
                    }
                });
            }
        });
    </script>
@endpush
