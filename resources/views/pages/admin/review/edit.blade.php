@extends('layouts.app')

@section('title', 'Review Reward Request Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .review-details-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(19, 33, 60, 0.04);
            max-width: 900px;
            margin: 0 auto;
        }
        .customer-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f0f4f9;
            margin-bottom: 24px;
        }
        .customer-detail-avatar {
            width: 70px;
            height: 70px;
            min-width: 70px;
            min-height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0866e8;
            flex-shrink: 0;
            display: block;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .meta-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px 20px;
            border: 1px solid #eef2f7;
        }
        .meta-label {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .meta-value {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
        }
        .verification-callout {
            background: #eeb80015;
            border-left: 4px solid #ff9800;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-3 max-w-900 mx-auto d-flex justify-content-between align-items-center">
        <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Reviews List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show max-w-900 mx-auto mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show max-w-900 mx-auto mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="review-details-card">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="h4 font-weight-bold mb-1">Review Reward Application #{{ $review->id }}</h2>
                <p class="text-muted small">Submitted on {{ $review->created_at ? $review->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
            </div>
            <div>
                @php
                    $status = $review->status instanceof \App\Enums\ReviewStatus
                        ? $review->status
                        : \App\Enums\ReviewStatus::tryFrom((string)$review->status) ?? \App\Enums\ReviewStatus::PENDING;
                @endphp
                <span class="badge {{ $status->badgeClass() }}" style="font-size: 14px; padding: 8px 16px; border-radius: 999px;">
                    {{ $status->label() }}
                </span>
            </div>
        </div>

        <div class="customer-profile-header">
            @php
                $user = $review->user;
                $name = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));
                $avatar = $user?->photo_url ?? "https://ui-avatars.com/api/?background=0866e8&color=fff&name=" . urlencode($name ?: 'Customer');
            @endphp
            <img src="{{ $avatar }}" alt="{{ $name }}" class="customer-detail-avatar">
            <div>
                <h3 class="h5 font-weight-bold mb-1">{{ $name ?: 'Customer' }}</h3>
                <p class="text-muted mb-1"><i class="far fa-envelope mr-1"></i> {{ $user?->email }}</p>
                <p class="text-muted small mb-0"><i class="fas fa-phone mr-1"></i> {{ $user?->phone ?: 'No phone provided' }}</p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Customer ID</div>
                <div class="meta-value">#{{ $review->user_id }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Reward Amount</div>
                <div class="meta-value text-success">
                    ${{ number_format((float)($review->reward_amount ?? $configuredReward), 2) }}
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Current Status</div>
                <div class="meta-value">{{ $status->label() }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Request Date</div>
                <div class="meta-value">{{ $review->created_at ? $review->created_at->format('Y-m-d H:i') : 'N/A' }}</div>
            </div>
        </div>

        @if($status === \App\Enums\ReviewStatus::PENDING)
            <div class="verification-callout">
                <h4 class="h6 font-weight-bold text-dark mb-2">
                    <i class="fas fa-search-location text-warning mr-1"></i> Manual Verification Required
                </h4>
                <p class="text-muted small mb-0">
                    Please visit your business Google Review page to verify that <strong>{{ $name }}</strong> ({{ $user?->email }}) has left a review. Upon verification, click <strong>Approve Request</strong> to credit ${{ number_format((float)($review->reward_amount ?? $configuredReward), 2) }} directly to their wallet.
                </p>
            </div>

            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                <form action="{{ route('reviews.cancel', $review->id) }}" method="POST" class="mr-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4" onclick="return confirm('Are you sure you want to cancel this review reward request?')">
                        <i class="fas fa-times mr-1"></i> Cancel Request
                    </button>
                </form>

                <form action="{{ route('reviews.approve', $review->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success px-4" onclick="return confirm('Confirm approval? This will credit ${{ number_format((float)($review->reward_amount ?? $configuredReward), 2) }} to customer wallet.')">
                        <i class="fas fa-check mr-1"></i> Approve & Issue Wallet Credit
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-secondary mb-0">
                <i class="fas fa-info-circle mr-1"></i> This review reward request is <strong>{{ strtolower($status->label()) }}</strong> and cannot be altered.
            </div>
        @endif
    </div>
</div>
@endsection
