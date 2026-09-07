<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('redirects unauthenticated guests to login on protected admin routes', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('audit-logs.index'))->assertRedirect(route('login'));
});

it('allows admin to access admin routes and blocks customer with 403', function () {
    $admin    = User::factory()->create(['role' => 1]);
    $customer = User::factory()->create(['role' => 2]);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
});

it('allows customer to access customer dashboard and blocks admin with 403', function () {
    $admin    = User::factory()->create(['role' => 1]);
    $customer = User::factory()->create(['role' => 2]);

    $this->actingAs($customer)->get(route('dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('dashboard'))->assertForbidden();
});

it('blocks customer from accessing audit logs with 403', function () {
    $admin    = User::factory()->create(['role' => 1]);
    $customer = User::factory()->create(['role' => 2]);

    $this->actingAs($admin)->get(route('audit-logs.index'))->assertOk();
    $this->actingAs($customer)->get(route('audit-logs.index'))->assertForbidden();
});

it('allows both admin and customer to access booking service wizard', function () {
    $admin    = User::factory()->create(['role' => 1]);
    $customer = User::factory()->create(['role' => 2]);

    $this->actingAs($admin)->get(route('booking-service.create'))->assertOk();
    $this->actingAs($customer)->get(route('booking-service.create'))->assertOk();
});

it('blocks unauthenticated access to utility routes and customers get 403', function () {
    // Guests should be redirected to login
    $this->get('/clear-cache')->assertRedirect(route('login'));
    $this->get('/run-migrate')->assertRedirect(route('login'));

    // Customers should get 403
    $customer = User::factory()->create(['role' => 2]);
    $this->actingAs($customer)->get('/clear-cache')->assertForbidden();
    $this->actingAs($customer)->get('/run-migrate')->assertForbidden();

    // Admin should succeed
    $admin = User::factory()->create(['role' => 1]);
    $this->actingAs($admin)->get('/clear-cache')->assertOk();
});
