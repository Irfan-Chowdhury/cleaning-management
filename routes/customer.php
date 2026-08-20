<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ReferralController;

Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
Route::get('/customer-referrals', [ReferralController::class, 'index'])->name('customer.referrals.index');
