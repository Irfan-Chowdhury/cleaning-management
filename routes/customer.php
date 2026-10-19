<?php

use App\Http\Controllers\BookingServiceController;
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

    // Booking Wizard Routes (Accessible by both Admin & Customer)
    Route::prefix('booking-service')->group(function () {
        Route::get('/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
        Route::post('/step-1', [BookingServiceController::class, 'storeStep1'])->name('booking-service.store-step-1');
        Route::get('/questionnaire/{service}', [BookingServiceController::class, 'questionnaire'])->name('booking-service.questionnaire');
        Route::get('/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
        Route::post('/step-2', [BookingServiceController::class, 'storeStep2'])->name('booking-service.store-step-2');
        Route::get('/slots-for-date', [BookingServiceController::class, 'slotsForDate'])->name('booking-service.slots-for-date');
        Route::get('/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
        Route::post('/step-3', [BookingServiceController::class, 'storeStep3'])->name('booking-service.store-step-3');
        Route::get('/review-confirm', [BookingServiceController::class, 'step4ReviewConfirm'])->name('booking-service.review-confirm');
        Route::post('/confirm', [BookingServiceController::class, 'confirmBooking'])->name('booking-service.confirm');
        Route::post('/apply-promo', [BookingServiceController::class, 'applyPromo'])->name('booking-service.apply-promo');
        Route::post('/remove-promo', [BookingServiceController::class, 'removePromo'])->name('booking-service.remove-promo');
    });

    // Google Review Reward Customer Routes
    Route::middleware(EnsureGoogleReviewEnabled::class)->group(function () {
        Route::get('/my-review', [CustomerGoogleReviewController::class, 'index'])->name('customer.review.index');
        Route::post('/my-review', [CustomerGoogleReviewController::class, 'store'])->name('customer.review.store');
        Route::post('/my-review/{review}/cancel', [CustomerGoogleReviewController::class, 'cancel'])->name('customer.review.cancel');
    });
});
