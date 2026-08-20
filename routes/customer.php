<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\WalletController;

Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
Route::get('/my-wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
