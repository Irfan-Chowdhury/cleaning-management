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

function eligibilityUser(): User
{
    return User::create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'eligibility' . Str::random(5) . '@example.com',
        'role' => 2,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

it('allows new customer to use new-customer-only promo code', function () {
    $user = eligibilityUser();
    $booking = Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
        'start_time' => '09:00:00',
        'customer_name' => 'Jane Smith',
        'customer_email' => $user->email,
        'status' => 'approved',
        'subtotal' => 120.00,
        'total_amount' => 120.00,
    ]);

    Promotion::create([
        'name' => 'Welcome Offer',
        'code' => 'WELCOME20',
        'discount_type' => 'fixed',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'new_customers_only' => true,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'WELCOME20',
            'type' => 'promo',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'discount_amount' => 20.00,
        ]);
});

it('rejects existing customer from using new-customer-only promo code', function () {
    $user = eligibilityUser();

    // Create prior completed booking for this user
    Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::yesterday()->format('Y-m-d'),
        'status' => 'completed',
        'subtotal' => 100.00,
        'total_amount' => 100.00,
    ]);

    $currentBooking = Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
        'start_time' => '09:00:00',
        'customer_name' => 'Jane Smith',
        'customer_email' => $user->email,
        'status' => 'approved',
        'subtotal' => 120.00,
        'total_amount' => 120.00,
    ]);

    Promotion::create([
        'name' => 'Welcome Offer',
        'code' => 'WELCOME20',
        'discount_type' => 'fixed',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'new_customers_only' => true,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $currentBooking->id,
            'code' => 'WELCOME20',
            'type' => 'promo',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'This promotional code is for new customers only.',
        ]);
});

it('allows existing customer to use existing-customer-only promo code', function () {
    $user = eligibilityUser();

    Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::yesterday()->format('Y-m-d'),
        'status' => 'completed',
        'subtotal' => 100.00,
        'total_amount' => 100.00,
    ]);

    $currentBooking = Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
        'start_time' => '09:00:00',
        'customer_name' => 'Jane Smith',
        'customer_email' => $user->email,
        'status' => 'approved',
        'subtotal' => 150.00,
        'total_amount' => 150.00,
    ]);

    Promotion::create([
        'name' => 'Loyalty Reward',
        'code' => 'LOYAL15',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'existing_customers_only' => true,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $currentBooking->id,
            'code' => 'LOYAL15',
            'type' => 'promo',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'discount_amount' => 22.50,
        ]);
});

it('rejects new customer from using existing-customer-only promo code', function () {
    $user = eligibilityUser();

    $booking = Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
        'start_time' => '09:00:00',
        'customer_name' => 'Jane Smith',
        'customer_email' => $user->email,
        'status' => 'approved',
        'subtotal' => 150.00,
        'total_amount' => 150.00,
    ]);

    Promotion::create([
        'name' => 'Loyalty Reward',
        'code' => 'LOYAL15',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'existing_customers_only' => true,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('booking-service.apply-promo'), [
            'booking_id' => $booking->id,
            'code' => 'LOYAL15',
            'type' => 'promo',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'This promotional code is for existing customers only.',
        ]);
});
