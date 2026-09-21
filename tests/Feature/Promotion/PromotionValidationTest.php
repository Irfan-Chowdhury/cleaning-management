<?php

use App\Enums\PromotionStatus;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function validationAdminUser(): User
{
    return User::create([
        'first_name' => 'Admin',
        'last_name' => 'Val',
        'email' => 'adminval' . Str::random(5) . '@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

it('requires code when storing a promotion', function () {
    $admin = validationAdminUser();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Test Promo',
            'code' => '',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'status' => 1,
            'start_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

it('enforces code uniqueness rules on store', function () {
    $admin = validationAdminUser();

    Promotion::create([
        'name' => 'Existing Promo',
        'code' => 'UNIQUE10',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Duplicate Code Promo',
            'code' => 'unique10',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'status' => 1,
            'start_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

it('requires discount_type and restricts to supported values', function () {
    $admin = validationAdminUser();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Invalid Type Promo',
            'code' => 'INVALIDTYPE',
            'discount_type' => 'invalid_type',
            'discount_value' => 10,
            'status' => 1,
            'start_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['discount_type']);
});

it('prevents percentage greater than 100 on promotion creation', function () {
    $admin = validationAdminUser();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Over 100 Promo',
            'code' => 'OVER100PERCENT',
            'discount_type' => 'percentage',
            'discount_value' => 150,
            'status' => 1,
            'start_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['discount_value']);
});

it('requires expires_at to be after start_at', function () {
    $admin = validationAdminUser();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Invalid Date Range Promo',
            'code' => 'BADDAtes',
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'status' => 1,
            'start_at' => Carbon::now()->addDays(10)->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(2)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['expires_at']);
});

it('restricts status to supported values', function () {
    $admin = validationAdminUser();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), [
            'name' => 'Invalid Status Promo',
            'code' => 'BADSTATUS',
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'status' => 999,
            'start_at' => Carbon::now()->format('Y-m-d\TH:i'),
            'expires_at' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});
