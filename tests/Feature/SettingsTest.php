<?php

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function settingsAdmin(): User
{
    return User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'settings-admin@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

function validSettingsPayload(array $overrides = []): array
{
    return array_merge([
        'company_name' => 'Clean Manage Pro',
        'phone' => '+1 555 014 8821',
        'email' => 'support@cleanmanagepro.test',
        'address' => '240 Spring Street, New York, NY 10013',
        'timezone' => 'America/New_York',
        'currency' => 'USD',
        'minimum_booking_amount' => '50.00',
        'maximum_booking_amount' => '1500.00',
        'maximum_advance_booking_days' => 30,
        'cancellation_notice_hours' => 24,
        'welcome_credit' => '20.00',
        'welcome_credit_enabled' => true,
        'referral_reward' => '25.00',
        'referral_reward_enabled' => true,
        'google_review_reward' => '15.00',
        'google_review_enabled' => true,
        'promotion_max_uses' => 500,
        'promotion_max_uses_per_customer' => 1,
    ], $overrides);
}

it('loads the settings page with current settings', function () {
    Setting::create(validSettingsPayload());

    $this->actingAs(settingsAdmin())
        ->get(route('settings.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.settings.index')
        ->assertSee('Clean Manage Pro')
        ->assertSee('Company Information')
        ->assertSee('Booking Configuration')
        ->assertSee('Promotion Configuration');
});

it('creates settings with the full settings structure', function () {
    $this->actingAs(settingsAdmin())
        ->postJson(route('settings.update'), validSettingsPayload([
            'company_name' => 'Sparkle Crew',
            'welcome_credit_enabled' => false,
            'google_review_enabled' => false,
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Company settings updated successfully!',
        ]);

    $this->assertDatabaseHas('settings', [
        'company_name' => 'Sparkle Crew',
        'phone' => '+1 555 014 8821',
        'email' => 'support@cleanmanagepro.test',
        'timezone' => 'America/New_York',
        'currency' => 'USD',
        'minimum_booking_amount' => '50.00',
        'maximum_booking_amount' => '1500.00',
        'maximum_advance_booking_days' => 30,
        'cancellation_notice_hours' => 24,
        'welcome_credit' => '20.00',
        'welcome_credit_enabled' => false,
        'referral_reward' => '25.00',
        'referral_reward_enabled' => true,
        'google_review_reward' => '15.00',
        'google_review_enabled' => false,
        'promotion_max_uses' => 500,
        'promotion_max_uses_per_customer' => 1,
    ]);
});

it('updates the latest settings record instead of creating duplicates', function () {
    Setting::create(validSettingsPayload([
        'company_name' => 'Original Company',
    ]));

    $this->actingAs(settingsAdmin())
        ->postJson(route('settings.update'), validSettingsPayload([
            'company_name' => 'Updated Company',
            'currency' => 'CAD',
        ]))
        ->assertOk();

    expect(Setting::count())->toBe(1)
        ->and(Setting::first()->company_name)->toBe('Updated Company')
        ->and(Setting::first()->currency)->toBe('CAD');
});

it('validates settings fields', function () {
    $this->actingAs(settingsAdmin())
        ->postJson(route('settings.update'), validSettingsPayload([
            'company_name' => '',
            'email' => 'not-an-email',
            'timezone' => 'Not/A_Timezone',
            'currency' => 'usd',
            'minimum_booking_amount' => '200.00',
            'maximum_booking_amount' => '100.00',
            'maximum_advance_booking_days' => 0,
            'promotion_max_uses' => -1,
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'company_name',
            'email',
            'timezone',
            'currency',
            'maximum_booking_amount',
            'maximum_advance_booking_days',
            'promotion_max_uses',
        ]);
});

it('casts numeric and boolean settings values', function () {
    $setting = Setting::create(validSettingsPayload([
        'welcome_credit_enabled' => false,
        'referral_reward_enabled' => true,
        'google_review_enabled' => false,
    ]));

    expect($setting->welcome_credit)->toBe('20.00')
        ->and($setting->minimum_booking_amount)->toBe('50.00')
        ->and($setting->maximum_advance_booking_days)->toBe(30)
        ->and($setting->welcome_credit_enabled)->toBeFalse()
        ->and($setting->referral_reward_enabled)->toBeTrue()
        ->and($setting->google_review_enabled)->toBeFalse();
});

it('seeds demo settings data', function () {
    $this->seed(SettingSeeder::class);

    $this->assertDatabaseHas('settings', [
        'id' => 1,
        'company_name' => 'Clean Manage Pro',
        'currency' => 'USD',
        'minimum_booking_amount' => '50.00',
        'maximum_booking_amount' => '1500.00',
        'welcome_credit_enabled' => true,
        'referral_reward_enabled' => true,
        'google_review_enabled' => true,
        'promotion_max_uses' => 500,
        'promotion_max_uses_per_customer' => 1,
    ]);
});
