<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingServiceController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    // return view('welcome');
    return view('layouts.app');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/booking-service/create', [BookingServiceController::class, 'create'])->name('booking-service.create');



Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    //Artisan::call('optimize:clear');
    return "Cache cleared successfully!";
});
