<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookingServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\WeeklyScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->role === 1
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/sub-admins', [SubAdminController::class, 'index'])->name('sub-admin.index');
    Route::post('/sub-admins', [SubAdminController::class, 'store'])->name('sub-admin.store');
    Route::put('/sub-admins/{sub_admin}', [SubAdminController::class, 'update'])->name('sub-admin.update');
    Route::delete('/sub-admins/{sub_admin}', [SubAdminController::class, 'destroy'])->name('sub-admin.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/weekly-schedule', [WeeklyScheduleController::class, 'index'])->name('weekly-schedule.index');
    Route::get('/weekly-schedule/{day}/edit', [WeeklyScheduleController::class, 'edit'])
        ->name('weekly-schedule.edit')
        ->where('day', 'monday|tuesday|wednesday|thursday|friday|saturday|sunday');
    Route::put('/weekly-schedule/{day}', [WeeklyScheduleController::class, 'update'])
        ->name('weekly-schedule.update')
        ->where('day', 'monday|tuesday|wednesday|thursday|friday|saturday|sunday');
    Route::get('/holidays',              [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays',             [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{holiday}',    [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
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
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

require __DIR__.'/customer.php';


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
