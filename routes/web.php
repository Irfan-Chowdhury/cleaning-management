<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookingServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WeeklyScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    // return view('welcome');
    return view('layouts.app');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/weekly-schedule', [WeeklyScheduleController::class, 'index'])->name('weekly-schedule.index');
Route::get('/week/{day}', [WeeklyScheduleController::class, 'edit'])->name('weekly-schedule.edit');
Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
Route::prefix('booking-service')->group(function () {
    Route::get('/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
    Route::get('/questionnaire/{service}', [BookingServiceController::class, 'questionnaire'])->name('booking-service.questionnaire');
    Route::get('/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
    Route::get('/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
    Route::get('/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
});
Route::resource('services', ServiceController::class);

Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
Route::get('/wallets/{user}', [WalletController::class, 'show'])->name('wallets.show');


Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    //Artisan::call('optimize:clear');
    return "Cache cleared successfully!";
});

Route::get('/run-migrate', function () {
    Artisan::call('migrate', [
        '--force' => true,
    ]);

    return nl2br(Artisan::output());
});

Route::get('/run-seeder/{class}', function (string $class) {
    $seederClass = str_contains($class, '\\') ? $class : 'Database\\Seeders\\' . $class;

    if (! class_exists($seederClass)) {
        abort(404, 'Seeder class not found.');
    }

    Artisan::call('db:seed', [
        '--class' => $seederClass,
        '--force' => true,
    ]);

    return nl2br(Artisan::output());
})->where('class', '[A-Za-z0-9_\\\\]+');
