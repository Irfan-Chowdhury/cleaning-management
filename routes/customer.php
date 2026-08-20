<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\BookingController;

Route::get('/my-bookings', [BookingController::class, 'index'])->name('customer.bookings.index');
