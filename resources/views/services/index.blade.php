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
            <a href="{{ route('services.create') }}" class="btn btn-primary services-primary-btn">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Service
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

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
                            <th>Base Price</th>
                            <th>Duration</th>
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
                                <td>${{ number_format($service->base_price, 2) }}</td>
                                <td>{{ $service->duration_minutes ? $service->duration_minutes . ' mins' : '-' }}</td>
                                <td>
                                    <span class="badge service-status-badge {{ $service->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>{{ $service->created_at ? $service->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <div class="service-actions">
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-primary service-action-btn" title="Edit">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger service-action-btn delete-service-btn" title="Delete" data-action="{{ route('services.destroy', $service) }}" data-name="{{ $service->name }}">
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

    <div class="modal fade" id="deleteServiceModal" tabindex="-1" role="dialog" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content service-delete-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteServiceModalLabel">Delete Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this service? This action cannot be undone.</p>
                    <strong id="delete-service-name"></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <form id="delete-service-form" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
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
    <script src="{{ asset('public/assets/js/service.js') }}"></script>
@endpush
