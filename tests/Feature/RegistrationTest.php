<?php

use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('registers a customer with address field and dispatches email verification notification', function () {
    Notification::fake();

    $response = $this->post(route('register'), [
        'first_name'            => 'John',
        'last_name'             => 'Doe',
        'email'                 => 'john.doe@example.com',
        'phone'                 => '+1234567890',
        'gender'                => 'male',
        'address'               => '123 Test Street, New York, NY',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'terms'                 => '1',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticated();

    $user = User::where('email', 'john.doe@example.com')->firstOrFail();

    expect($user->first_name)->toBe('John')
        ->and($user->address)->toBe('123 Test Street, New York, NY')
        ->and($user->role)->toBe(2)
        ->and($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('verifies email and allocates welcome bonus credit from settings', function () {
    Setting::create([
        'company_name'           => 'Dust2Glow',
        'welcome_credit_enabled' => true,
        'welcome_credit'         => 50.00,
    ]);

    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();

    $transaction = WalletTransaction::where('user_id', $user->id)->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->type)->toBe('credit')
        ->and($transaction->source)->toBe('welcome_bonus')
        ->and((float)$transaction->amount)->toBe(50.00);
});
