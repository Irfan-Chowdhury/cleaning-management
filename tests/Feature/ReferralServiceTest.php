<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Cache;

function createReferralUser(string $email, string $referralCode): User
{
    return User::create([
        'first_name'    => 'Ref',
        'last_name'     => 'User',
        'email'         => $email,
        'password'      => bcrypt('password'),
        'role'          => 2,
        'referral_code' => $referralCode,
    ]);
}

it('returns validation error if code does not exist', function () {
    $user = createReferralUser('user1@test.com', 'REF111');
    $service = new ReferralService();

    $result = $service->validateCode('INVALIDCODE999', $user, 100.00);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toContain('does not exist');
});

it('returns validation error if user tries to refer themselves', function () {
    $user = createReferralUser('selfref@test.com', 'MYOWNCODE');
    $service = new ReferralService();

    $result = $service->validateCode('MYOWNCODE', $user, 100.00);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toBe('You cannot use your own referral code.');
});

it('returns validation error if referrer has zero completed bookings', function () {
    $referrer = createReferralUser('referrer_no_booking@test.com', 'REFERRERCODE123');
    $customer = createReferralUser('customer_ref@test.com', 'CUSTOMERCODE456');

    // Referrer has a booking but status is PENDING (not COMPLETED)
    Booking::create([
        'user_id'          => $referrer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Referrer Name',
        'customer_email'   => $referrer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '123 St',
        'subtotal'         => 100.00,
        'total_amount'     => 100.00,
        'status'           => BookingStatus::PENDING,
    ]);

    $service = new ReferralService();
    $result = $service->validateCode('REFERRERCODE123', $customer, 100.00);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toContain('referrer must have at least 1 completed booking');
});

it('returns second time validation error if customer has already COMPLETED a booking using any referral code', function () {
    $referrer = createReferralUser('referrer_ok@test.com', 'REFOKCODE');
    $customer = createReferralUser('customer_repeat@test.com', 'CUSTREPEAT');

    // Referrer has completed booking
    Booking::create([
        'user_id'          => $referrer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Referrer',
        'customer_email'   => $referrer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '123 St',
        'subtotal'         => 100.00,
        'total_amount'     => 100.00,
        'status'           => BookingStatus::COMPLETED,
    ]);

    // Customer has a previous COMPLETED booking with a referral code in bookings table
    Booking::create([
        'user_id'          => $customer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Customer',
        'customer_email'   => $customer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '456 St',
        'subtotal'         => 100.00,
        'referal_code'     => 'REFOKCODE',
        'total_amount'     => 90.00,
        'status'           => BookingStatus::COMPLETED,
    ]);

    // Creating a next booking for the same customer
    $nextBooking = Booking::create([
        'user_id'          => $customer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Customer Next',
        'customer_email'   => $customer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '456 St',
        'subtotal'         => 100.00,
        'total_amount'     => 100.00,
        'status'           => BookingStatus::APPROVED,
    ]);

    $service = new ReferralService();
    // Validating for next booking
    $result = $service->validateCode('REFOKCODE', $customer, 100.00, $nextBooking->id);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toBe('You can not use any referal code second time.');
});

it('allows customer to use referral code if past booking with referral code was NOT completed', function () {
    $referrer = createReferralUser('referrer_cancel@test.com', 'REFCANCELCODE');
    $customer = createReferralUser('customer_pending@test.com', 'CUSTPENDING');

    // Referrer has completed booking
    Booking::create([
        'user_id'          => $referrer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Referrer',
        'customer_email'   => $referrer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '123 St',
        'subtotal'         => 100.00,
        'total_amount'     => 100.00,
        'status'           => BookingStatus::COMPLETED,
    ]);

    // Customer has CANCELLED/APPROVED (non-COMPLETED) booking with a referral code
    Booking::create([
        'user_id'          => $customer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Customer',
        'customer_email'   => $customer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '456 St',
        'subtotal'         => 100.00,
        'referal_code'     => 'REFCANCELCODE',
        'total_amount'     => 90.00,
        'status'           => BookingStatus::APPROVED,
    ]);

    $service = new ReferralService();
    $result = $service->validateCode('REFCANCELCODE', $customer, 100.00);

    expect($result['valid'])->toBeTrue()
        ->and($result['code'])->toBe('REFCANCELCODE');
});

it('returns validation error if subtotal is less than minimum booking amount', function () {
    $referrer = createReferralUser('referrer_min@test.com', 'REFMINCODE');
    $customer = createReferralUser('customer_min@test.com', 'CUSTMIN');

    Cache::forget('app_settings');
    Setting::updateOrCreate([], [
        'minimum_booking_amount' => 100.00,
        'referral_reward'        => 15.00,
    ]);

    $service = new ReferralService();
    $result = $service->validateCode('REFMINCODE', $customer, 50.00);

    expect($result['valid'])->toBeFalse()
        ->and($result['message'])->toBe('The total amount ($50.00) is less than minimum booking amount ($100.00).');
});

it('validates and applies valid referral code preserving original casing format', function () {
    $referrer = createReferralUser('referrer_success@test.com', 'IRFAN4');
    $customer = createReferralUser('customer_success@test.com', 'VALIDCUST2026');

    // Referrer has completed booking
    Booking::create([
        'user_id'          => $referrer->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => 'Referrer',
        'customer_email'   => $referrer->email,
        'customer_phone'   => '0411223344',
        'customer_address' => '123 St',
        'subtotal'         => 100.00,
        'total_amount'     => 100.00,
        'status'           => BookingStatus::COMPLETED,
    ]);

    Cache::forget('app_settings');
    Setting::updateOrCreate([], [
        'minimum_booking_amount' => 50.00,
        'referral_reward'        => 15.00,
    ]);

    $service = new ReferralService();
    $result = $service->validateCode('IRFAN4', $customer, 120.00);

    expect($result['valid'])->toBeTrue()
        ->and($result['type'])->toBe('referral')
        ->and($result['code'])->toBe('IRFAN4')
        ->and((float) $result['discount_amount'])->toBe(15.00);
});
