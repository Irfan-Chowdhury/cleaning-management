<?php

use App\Models\Promotion;
use App\Models\User;
use Database\Seeders\PromotionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function promotionAdmin(): User
{
    return User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'promotion-admin@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

function validPromotionPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Spring Deep Clean',
        'code' => 'SPRING15',
        'description' => 'Seasonal percentage discount for deep cleaning bookings.',
        'discount_type' => 'percentage',
        'discount_value' => '15.00',
        'status' => 'active',
        'start_at' => now()->format('Y-m-d\TH:i'),
        'expires_at' => now()->addDays(10)->format('Y-m-d\TH:i'),
        'new_customers_only' => false,
        'existing_customers_only' => false,
    ], $overrides);
}

it('loads the promotions page', function () {
    $this->actingAs(promotionAdmin())
        ->get(route('promotions.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.promotion.index')
        ->assertSee('Promotional Offers')
        ->assertSee('Add Promotion');
});

it('returns promotions for yajra datatables ajax requests', function () {
    $admin = promotionAdmin();
    Promotion::create(validPromotionPayload([
        'created_by' => $admin->id,
    ]));

    $this->actingAs($admin)
        ->getJson(route('promotions.index'), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
});

it('stores a promotion', function () {
    $admin = promotionAdmin();

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), validPromotionPayload([
            'code' => 'welcome25',
            'new_customers_only' => true,
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Promotion created successfully!',
        ]);

    $this->assertDatabaseHas('promotions', [
        'name' => 'Spring Deep Clean',
        'code' => 'WELCOME25',
        'discount_type' => 'percentage',
        'discount_value' => '15.00',
        'status' => 'active',
        'new_customers_only' => true,
        'existing_customers_only' => false,
        'created_by' => $admin->id,
    ]);
});

it('updates a promotion while keeping code uniqueness scoped to other rows', function () {
    $admin = promotionAdmin();
    $promotion = Promotion::create(validPromotionPayload([
        'created_by' => $admin->id,
    ]));

    $this->actingAs($admin)
        ->putJson(route('promotions.update', $promotion), validPromotionPayload([
            'name' => 'Updated Offer',
            'code' => 'SPRING15',
            'status' => 'paused',
            'discount_type' => 'fixed',
            'discount_value' => '20.00',
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Promotion updated successfully!',
        ]);

    $this->assertDatabaseHas('promotions', [
        'id' => $promotion->id,
        'name' => 'Updated Offer',
        'code' => 'SPRING15',
        'status' => 'paused',
        'discount_type' => 'fixed',
        'discount_value' => '20.00',
    ]);
});

it('deletes a promotion', function () {
    $admin = promotionAdmin();
    $promotion = Promotion::create(validPromotionPayload([
        'created_by' => $admin->id,
    ]));

    $this->actingAs($admin)
        ->deleteJson(route('promotions.destroy', $promotion))
        ->assertOk()
        ->assertJson([
            'message' => 'Promotion deleted successfully!',
        ]);

    $this->assertDatabaseMissing('promotions', [
        'id' => $promotion->id,
    ]);
});

it('validates promotion fields', function () {
    $admin = promotionAdmin();
    Promotion::create(validPromotionPayload([
        'code' => 'EXISTS',
        'created_by' => $admin->id,
    ]));

    $this->actingAs($admin)
        ->postJson(route('promotions.store'), validPromotionPayload([
            'name' => '',
            'code' => 'EXISTS',
            'discount_type' => 'percentage',
            'discount_value' => '101.00',
            'status' => 'missing',
            'expires_at' => now()->subDay()->format('Y-m-d\TH:i'),
            'new_customers_only' => true,
            'existing_customers_only' => true,
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name',
            'code',
            'discount_value',
            'status',
            'expires_at',
            'existing_customers_only',
        ]);
});

it('casts promotion values', function () {
    $promotion = Promotion::create(validPromotionPayload([
        'discount_value' => '12.50',
        'new_customers_only' => true,
        'existing_customers_only' => false,
    ]));

    expect($promotion->discount_value)->toBe('12.50')
        ->and($promotion->start_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($promotion->expires_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($promotion->new_customers_only)->toBeTrue()
        ->and($promotion->existing_customers_only)->toBeFalse();
});

it('seeds five demo promotions', function () {
    promotionAdmin();

    $this->seed(PromotionSeeder::class);

    expect(Promotion::count())->toBe(5);

    $this->assertDatabaseHas('promotions', [
        'code' => 'WELCOME25',
        'status' => 'active',
    ]);
});
