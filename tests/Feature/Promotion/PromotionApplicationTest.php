use App\Enums\PromotionStatus;
use App\Models\Booking;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    Service::firstOrCreate(['id' => 1], [
        'name' => 'General Cleaning',
        'status' => 'active',
    ]);
});

function promoCustomer(): User
{
    return User::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'customer' . Str::random(5) . '@example.com',
        'role' => 2,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

function promoApprovedBooking(User $user, float $subtotal = 100.00): Booking
{
    return Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
        'start_time' => '09:00:00',
        'customer_name' => 'John Doe',
        'customer_email' => $user->email,
        'customer_phone' => '1234567890',
        'customer_address' => '123 Main St',
        'status' => 'approved',
        'subtotal' => $subtotal,
        'total_amount' => $subtotal,
    ]);
}

it('accepts valid promo code via HTTP API', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => 'Spring Sale 15%',
        'code' => 'SPRING15',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $response = $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'SPRING15',
            'type' => 'promo',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'code' => 'SPRING15',
            'discount_amount' => 15.00,
            'total_amount' => 85.00,
        ]);

    $booking->refresh();
    expect($booking->promo_code)->toBe('SPRING15');
    expect($booking->discount_amount)->toEqual(15.00);
    expect($booking->total_amount)->toEqual(85.00);
});

it('rejects invalid or non-existing promo code', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    $response = $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'INVALIDCODE99',
            'type' => 'promo',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

it('handles whitespace in promo code input gracefully', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => 'Spring Sale 15%',
        'code' => 'SPRING15',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $response = $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => '  spring15   ',
            'type' => 'promo',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'code' => 'SPRING15',
        ]);
});

it('rejects inactive or expired promotional codes', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => 'Inactive Code',
        'code' => 'INACTIVE10',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::INACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    Promotion::create([
        'name' => 'Expired Code',
        'code' => 'EXPIRED10',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::EXPIRED,
        'start_at' => Carbon::now()->subDays(20),
        'expires_at' => Carbon::now()->subDays(5),
    ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'INACTIVE10',
            'type' => 'promo',
        ])
        ->assertStatus(422);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'EXPIRED10',
            'type' => 'promo',
        ])
        ->assertStatus(422);
});

it('validates start_at and expires_at date window', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => 'Future Code',
        'code' => 'FUTURE10',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->addDays(2),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'FUTURE10',
            'type' => 'promo',
        ])
        ->assertStatus(422);
});

it('correctly updates totals for 10% and 20% promo codes', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 200.00);

    Promotion::create([
        'name' => '20 Percent Code',
        'code' => 'PROMO20',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'PROMO20',
            'type' => 'promo',
        ])
        ->assertOk()
        ->assertJson([
            'discount_amount' => 40.00,
            'total_amount' => 160.00,
        ]);
});

it('caps fixed discount greater than subtotal to subtotal producing zero total', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 50.00);

    Promotion::create([
        'name' => 'Huge Fixed Discount',
        'code' => 'FIXED100',
        'discount_type' => 'fixed',
        'discount_value' => 100,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'FIXED100',
            'type' => 'promo',
        ])
        ->assertOk()
        ->assertJson([
            'discount_amount' => 50.00,
            'total_amount' => 0.00,
        ]);
});

it('prevents security tampering of discount amount in request', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => '10 Dollar Discount',
        'code' => 'TENOFF',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    // Send forged discount_amount and total_amount fields in request
    $response = $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'TENOFF',
            'discount_amount' => 999.00,
            'total_amount' => 1.00,
        ]);

    $response->assertOk()
        ->assertJson([
            'discount_amount' => 10.00,
            'total_amount' => 90.00,
        ]);
});

it('removes applied promo code and restores subtotal', function () {
    $customer = promoCustomer();
    $booking = promoApprovedBooking($customer, 100.00);

    Promotion::create([
        'name' => '10 Dollar Discount',
        'code' => 'TENOFF',
        'discount_type' => 'fixed',
        'discount_value' => 10,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'TENOFF',
        ]);

    $this->actingAs($customer)
        ->postJson(route('booking-service.remove-promo'), [
            'booking_id' => $booking->id,
        ])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'total_amount' => 100.00,
        ]);

    $booking->refresh();
    expect($booking->promo_code)->toBeNull();
    expect($booking->discount_amount)->toEqual(0.00);
    expect($booking->total_amount)->toEqual(100.00);
});
