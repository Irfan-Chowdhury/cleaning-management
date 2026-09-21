use App\Enums\PromotionStatus;
use App\Models\Booking;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\User;
use App\Services\PromotionDiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->promotionDiscountService = new PromotionDiscountService();
    Service::firstOrCreate(['id' => 1], [
        'name' => 'General Cleaning',
        'status' => 'active',
    ]);
});

it('calculates 10% discount on 100 dollars', function () {
    $user = User::factory()->create();
    $promotion = Promotion::create([
        'name' => '10 Percent Off',
        'code' => 'TENOFF',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('TENOFF', $user, 100.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(10.00);
    expect($result['total_amount'])->toEqual(90.00);
});

it('calculates 20% discount on 180 dollars', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => '20 Percent Off',
        'code' => 'TWENTYOFF',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('TWENTYOFF', $user, 180.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(36.00);
    expect($result['total_amount'])->toEqual(144.00);
});

it('calculates 100% discount resulting in zero total', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Free Service',
        'code' => 'FREE100',
        'discount_type' => 'percentage',
        'discount_value' => 100,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FREE100', $user, 150.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(150.00);
    expect($result['total_amount'])->toEqual(0.00);
});

it('rejects percentage discount greater than 100', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Invalid High Percentage',
        'code' => 'OVER100',
        'discount_type' => 'percentage',
        'discount_value' => 120,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('OVER100', $user, 100.00);

    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Invalid percentage discount');
});

it('rejects percentage discount equal to 0', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Zero Percentage',
        'code' => 'ZEROOFF',
        'discount_type' => 'percentage',
        'discount_value' => 0,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('ZEROOFF', $user, 100.00);

    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Invalid percentage discount');
});

it('calculates fixed 20 dollar discount on 100 dollars subtotal', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Fixed 20 Off',
        'code' => 'FIXED20',
        'discount_type' => 'fixed',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FIXED20', $user, 100.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(20.00);
    expect($result['total_amount'])->toEqual(80.00);
});

it('calculates fixed 50 dollar discount on 50 dollars subtotal resulting in zero total', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Fixed 50 Off',
        'code' => 'FIXED50',
        'discount_type' => 'fixed',
        'discount_value' => 50,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FIXED50', $user, 50.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(50.00);
    expect($result['total_amount'])->toEqual(0.00);
});

it('caps fixed 100 dollar discount on 50 dollars subtotal at 50 dollars', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Fixed 100 Off',
        'code' => 'FIXED100',
        'discount_type' => 'fixed',
        'discount_value' => 100,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FIXED100', $user, 50.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(50.00);
    expect($result['total_amount'])->toEqual(0.00);
});

it('rejects fixed discount of 0', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Zero Fixed',
        'code' => 'FIXEDZERO',
        'discount_type' => 'fixed',
        'discount_value' => 0,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FIXEDZERO', $user, 100.00);

    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('Invalid fixed discount');
});

it('ensures final total is never negative', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Large Fixed Off',
        'code' => 'HUGE500',
        'discount_type' => 'fixed',
        'discount_value' => 500,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('HUGE500', $user, 75.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(75.00);
    expect($result['total_amount'])->toBeGreaterThanOrEqual(0.00);
    expect($result['total_amount'])->toEqual(0.00);
});

it('returns correct final total after applying discount', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => '15 Percent Off',
        'code' => 'FIFTEEN',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('FIFTEEN', $user, 200.00);

    expect($result['valid'])->toBeTrue();
    expect($result['discount_amount'])->toEqual(30.00);
    expect($result['total_amount'])->toEqual(170.00);
});

it('allows new customer with new-customer-only promotion', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'New Customer Promo',
        'code' => 'NEWONLY',
        'discount_type' => 'fixed',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'new_customers_only' => true,
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('NEWONLY', $user, 100.00);

    expect($result['valid'])->toBeTrue();
});

it('rejects existing customer with new-customer-only promotion', function () {
    $user = User::factory()->create();
    Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'status' => 'completed',
        'subtotal' => 100,
        'total_amount' => 100,
    ]);

    Promotion::create([
        'name' => 'New Customer Promo',
        'code' => 'NEWONLY',
        'discount_type' => 'fixed',
        'discount_value' => 20,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'new_customers_only' => true,
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('NEWONLY', $user, 100.00);

    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('new customers only');
});

it('allows existing customer with existing-customer-only promotion', function () {
    $user = User::factory()->create();
    Booking::create([
        'user_id' => $user->id,
        'service_id' => 1,
        'status' => 'completed',
        'subtotal' => 100,
        'total_amount' => 100,
    ]);

    Promotion::create([
        'name' => 'Existing Customer Promo',
        'code' => 'EXISTINGONLY',
        'discount_type' => 'fixed',
        'discount_value' => 25,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'existing_customers_only' => true,
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('EXISTINGONLY', $user, 100.00);

    expect($result['valid'])->toBeTrue();
});

it('rejects new customer with existing-customer-only promotion', function () {
    $user = User::factory()->create();
    Promotion::create([
        'name' => 'Existing Customer Promo',
        'code' => 'EXISTINGONLY',
        'discount_type' => 'fixed',
        'discount_value' => 25,
        'status' => PromotionStatus::ACTIVE,
        'start_at' => Carbon::now()->subDay(),
        'expires_at' => Carbon::now()->addDays(10),
        'existing_customers_only' => true,
    ]);

    $result = $this->promotionDiscountService->validatePromotionCode('EXISTINGONLY', $user, 100.00);

    expect($result['valid'])->toBeFalse();
    expect($result['message'])->toContain('existing customers only');
});
