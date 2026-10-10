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
    Route::post('/dashboard/send-invite', [DashboardController::class, 'sendInvite'])->name('customer.dashboard.send-invite');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
    Route::get('/my-bookings/{id}', [BookingController::class, 'show'])->name('customer.bookings.show');
    Route::get('/my-bookings/{id}/download-pdf', [BookingController::class, 'downloadPdf'])->name('customer.bookings.downloadPdf');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');
    Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
    Route::get('/my-referrals', [ReferralController::class, 'index'])->name('customer.referrals.index');

    // Google Review Reward Customer Routes
    Route::middleware(EnsureGoogleReviewEnabled::class)->group(function () {
        Route::get('/my-review', [CustomerGoogleReviewController::class, 'index'])->name('customer.review.index');
        Route::post('/my-review', [CustomerGoogleReviewController::class, 'store'])->name('customer.review.store');
        Route::post('/my-review/{review}/cancel', [CustomerGoogleReviewController::class, 'cancel'])->name('customer.review.cancel');
    });
});
