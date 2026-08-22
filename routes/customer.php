<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ReferralController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
    Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
    Route::get('/customer-referrals', [ReferralController::class, 'index'])->name('customer.referrals.index');
    Route::get('/customer-profile', [ProfileController::class, 'index'])->name('customer.profile.index');
});
