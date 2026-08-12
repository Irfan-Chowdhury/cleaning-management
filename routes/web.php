<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;

Route::get('/', function () {
    // return view('welcome');
    return view('layouts.app');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/booking-service/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
Route::get('/booking-service/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
Route::get('/booking-service/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
Route::get('/booking-service/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
Route::resource('services', ServiceController::class)->except(['show']);



Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    //Artisan::call('optimize:clear');
    return "Cache cleared successfully!";
});
