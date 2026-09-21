@extends('layouts.app')

@section('title', 'Google Review Reward')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .review-reward-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 4px 20px rgba(19, 33, 60, 0.04);
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .review-icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(8, 102, 232, 0.1);
            color: #0866e8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .review-icon-circle.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        .review-icon-circle.warning {
            background: rgba(255, 193, 7, 0.15);
            color: #ff9800;
        }
        .review-icon-circle.danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        .review-reward-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 12px;
        }
        .review-reward-text {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 28px;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }
        .reward-badge-amount {
            display: inline-block;
            background: #eef5ff;
            color: #0866e8;
            font-weight: 700;
            font-size: 18px;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show max-w-800 mx-auto mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show max-w-800 mx-auto mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="review-reward-card">
        @if(($eligibility['reason'] ?? '') === 'already_approved')
            {{-- Scenario C: Request Approved --}}
            <div class="review-icon-circle success">
                <i class="fas fa-award"></i>
            </div>
            <h2 class="review-reward-title">Reward Claimed!</h2>
            <div class="reward-badge-amount">
                <i class="fas fa-wallet mr-1"></i> ${{ number_format((float)($eligibility['reward_amount'] ?? 0), 2) }}
            </div>
            <p class="review-reward-text">
                Thank you for sharing your review. You have earned ${{ number_format((float)($eligibility['reward_amount'] ?? 0), 2) }} as a Google Review Reward. The reward has been added to your wallet.
            </p>
        @elseif(($eligibility['reason'] ?? '') === 'pending')
            {{-- Scenario B: Request Pending --}}
            <div class="review-icon-circle warning">
                <i class="fas fa-clock"></i>
            </div>
            <h2 class="review-reward-title">Review Verification Pending</h2>
            <p class="review-reward-text">
                Thank you for your request. Our team will verify your Google review. Once approved, your review reward will be added to your wallet.
            </p>
            <form action="{{ route('customer.review.cancel', $eligibility['pending_review']->id) }}" method="POST" class="d-inline-block">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-lg px-4" onclick="return confirm('Are you sure you want to cancel this review reward request?')">
                    <i class="fas fa-times-circle mr-2"></i> Cancel Request
                </button>
            </form>
        @else
            {{-- Scenario A & D: Customer can submit request --}}
            <div class="review-icon-circle">
                <i class="fab fa-google"></i>
            </div>
            <h2 class="review-reward-title">Google Review Reward</h2>
            @if(isset($eligibility['reward_amount']) && $eligibility['reward_amount'] > 0)
                <div class="reward-badge-amount">
                    Earn ${{ number_format((float)$eligibility['reward_amount'], 2) }} Wallet Credit
                </div>
            @endif

            @if(isset($eligibility['cancelled_review']))
                <div class="alert alert-warning py-2 mb-3 text-left max-w-640 mx-auto" style="font-size: 14px;">
                    <i class="fas fa-info-circle mr-1"></i> Your previous request was cancelled. You may submit a new request after leaving your review.
                </div>
            @endif

            <p class="review-reward-text">
                Go to Google and leave us a review. After submitting your review, apply for your review bonus. Our team will verify your review and approve your request. Once approved, the reward will be added to your wallet.
            </p>

            <form action="{{ route('customer.review.store') }}" method="POST" class="d-inline-block">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm" style="background: #0866e8; border-color: #0866e8;">
                    <i class="fas fa-paper-plane mr-2"></i> Apply for Review Reward
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
