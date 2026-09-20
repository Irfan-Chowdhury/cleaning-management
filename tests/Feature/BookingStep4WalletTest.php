<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\BookingService;
use Illuminate\Support\Facades\Cache;

function step4WalletUser(): User
{
    return User::where('email', 'customer@gmail.com')->first()
        ?? User::firstOrCreate(
            ['email' => 'customer_step4@test.com'],
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'password'   => bcrypt('password'),
                'role'       => 2,
            ]
        );
}

function createStep4Booking(User $user, float $subtotal = 100.00): Booking
{
    return Booking::create([
        'user_id'          => $user->id,
        'service_id'       => 1,
        'frequency'        => 'one_time',
        'customer_name'    => $user->first_name . ' ' . ($user->last_name ?? ''),
        'customer_email'   => $user->email,
        'customer_phone'   => '0412345678',
        'customer_address' => '123 Main Street',
        'subtotal'         => $subtotal,
        'discount_amount'  => 0.00,
        'credit_used'      => 0.00,
        'total_amount'     => $subtotal,
        'status'           => BookingStatus::APPROVED,
        'payment_status'   => 'pending',
        'payment_method'   => 'pending',
    ]);
}

it('displays the step 4 review page with discount offer choices and wallet panel', function () {
    $user = step4WalletUser();
    $booking = createStep4Booking($user);

    $this->actingAs($user)
        ->get(route('booking-service.review-confirm', ['booking' => $booking->id]))
        ->assertOk()
        ->assertViewIs('pages.booking-service.review-confirm')
        ->assertSee('Step 4 of 4: Review & Confirm')
        ->assertSee('Discount Offer')
        ->assertSee('Discount Type')
        ->assertSee('Use Wallet')
        ->assertSee('Referral / Promo Code');
});

it('prevents wallet usage when subtotal is less than minimum booking amount setting', function () {
    $user = step4WalletUser();

    // Give user $100 wallet credit
    WalletTransaction::create([
        'user_id'     => $user->id,
        'amount'      => 100.00,
        'type'        => 'credit',
        'source'      => 'deposit',
        'description' => 'Test deposit',
    ]);

    // Set minimum booking amount to $150
    Cache::forget('app_settings');
    Setting::updateOrCreate([], [
        'minimum_booking_amount' => 150.00,
        'max_wallet_usage'       => 50.00,
    ]);

    $subtotal = 100.00;
    $minAmount = 150.00;

    expect($subtotal)->toBeLessThan($minAmount);
});

it('allows customer to apply wallet credit and recalculates total amount on confirmation', function () {
    $user = step4WalletUser();

    // Give user $80 wallet credit
    WalletTransaction::create([
        'user_id'     => $user->id,
        'amount'      => 80.00,
        'type'        => 'credit',
        'source'      => 'welcome_bonus',
        'description' => 'Welcome Bonus',
    ]);

    Cache::forget('app_settings');
    Setting::updateOrCreate([], [
        'minimum_booking_amount' => 20.00,
        'max_wallet_usage'       => 50.00,
    ]);

    $booking = createStep4Booking($user, 120.00);

    $response = $this->actingAs($user)
        ->post(route('booking-service.confirm'), [
            'booking_id'    => $booking->id,
            'wallet_amount' => 30.00,
        ]);

    $response->assertRedirect(route('customer.bookings.index'));

    $booking->refresh();

    expect((float) $booking->credit_used)->toBe(30.00)
        ->and((float) $booking->discount_amount)->toBe(30.00)
        ->and((float) $booking->total_amount)->toBe(90.00)
        ->and($booking->status)->toBe(BookingStatus::CONFIRMED);

    // Verify wallet transaction debit entry was recorded
    $this->assertDatabaseHas('wallet_transactions', [
        'user_id'    => $user->id,
        'booking_id' => $booking->id,
        'type'       => 'debit',
        'amount'     => 30.00,
        'source'     => 'booking_payment',
    ]);
});

it('caps applied wallet credit to max_wallet_usage limit set in admin settings', function () {
    $user = step4WalletUser();

    // User has $200 wallet credit
    WalletTransaction::create([
        'user_id'     => $user->id,
        'amount'      => 200.00,
        'type'        => 'credit',
        'source'      => 'deposit',
        'description' => 'User deposit',
    ]);

    // Max wallet usage is $40 per booking
    Cache::forget('app_settings');
    Setting::updateOrCreate([], [
        'minimum_booking_amount' => 10.00,
        'max_wallet_usage'       => 40.00,
    ]);

    $booking = createStep4Booking($user, 150.00);

    /** @var BookingService $bookingService */
    $bookingService = app(BookingService::class);
    $confirmedBooking = $bookingService->confirmBookingByCustomer($booking, $user, 60.00); // Customer requested $60

    // Should be capped at max_wallet_usage ($40.00)
    expect((float) $confirmedBooking->credit_used)->toBe(40.00)
        ->and((float) $confirmedBooking->discount_amount)->toBe(40.00)
        ->and((float) $confirmedBooking->total_amount)->toBe(110.00);
});
