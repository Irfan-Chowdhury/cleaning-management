<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ReferralController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'can:customer', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');
    Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
    Route::get('/customer-referrals', [ReferralController::class, 'index'])->name('customer.referrals.index');
    Route::get('/customer-profile', [ProfileController::class, 'index'])->name('customer.profile.index');
    Route::post('/customer-profile', [ProfileController::class, 'update'])->name('customer.profile.update');
});
