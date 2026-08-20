@extends('layouts.app')

@section('title', 'Services')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/service.css') }}">
@endpush

@section('content')
    <div class="services-page">
        <div class="services-header">
            <div>
                <h1>Services</h1>
                <p>Manage cleaning services, pricing, duration and availability status.</p>
            </div>
            <button type="button" class="btn btn-primary services-primary-btn js-service-add" data-toggle="modal" data-target="#service-form-modal" data-action="{{ route('services.store') }}">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Service
            </button>
        </div>

        <div class="services-table-card">
            <div class="services-table-card-header">
                <div>
                    <h2>Service List</h2>
                    <p>{{ $services->count() }} records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="services-table" class="table table-hover table-bordered nowrap services-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="service-action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $service->name }}</strong>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($service->description, 80) ?: '-' }}</td>
                                <td>
                                    <span class="badge service-status-badge {{ $service->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>{{ $service->created_at ? $service->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <div class="service-actions">
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-success service-action-btn" title="View">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary service-action-btn js-service-edit" title="Edit" data-toggle="modal" data-target="#service-form-modal" data-action="{{ route('services.update', $service) }}" data-name="{{ $service->name }}" data-description="{{ $service->description }}" data-status="{{ $service->status }}">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger service-action-btn js-delete-confirm" title="Delete" data-action="{{ route('services.destroy', $service) }}" data-name="{{ $service->name }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="global-delete-form" method="POST" action="#" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="service-form-modal" tabindex="-1" role="dialog" aria-labelledby="service-form-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content service-modal-content">
                <form id="service-modal-form" method="POST" action="{{ route('services.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="service-form-method" value="PUT" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="service-form-modal-title">Add Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @include('pages.admin.services._form', ['service' => null])
                    </div>

                    <div class="modal-footer service-modal-actions">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary services-primary-btn" id="service-modal-submit">
                            <i class="fas fa-save" aria-hidden="true"></i> Save Service
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
    <script src="{{ asset('public/assets/js/service.js') }}"></script>
@endpush
