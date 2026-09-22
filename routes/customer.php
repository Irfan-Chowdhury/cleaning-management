<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ReferralController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\GoogleReviewController as CustomerGoogleReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\EnsureGoogleReviewEnabled;

Route::middleware(['auth', 'can:customer', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');
    Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
    Route::get('/customer-referrals', [ReferralController::class, 'index'])->name('customer.referrals.index');
    Route::get('/customer-profile', [ProfileController::class, 'index'])->name('customer.profile.index');
    Route::post('/customer-profile', [ProfileController::class, 'update'])->name('customer.profile.update');

    // Google Review Reward Customer Routes
    Route::middleware(EnsureGoogleReviewEnabled::class)->group(function () {
        Route::get('/my-review', [CustomerGoogleReviewController::class, 'index'])->name('customer.review.index');
        Route::post('/my-review', [CustomerGoogleReviewController::class, 'store'])->name('customer.review.store');
        Route::post('/my-review/{review}/cancel', [CustomerGoogleReviewController::class, 'cancel'])->name('customer.review.cancel');
    });
});
