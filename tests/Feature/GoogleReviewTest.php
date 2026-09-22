<?php

use App\Enums\ReviewStatus;
use App\Models\AuditLog;
use App\Models\GoogleReview;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\GoogleReviewApprovedNotification;
use App\Notifications\GoogleReviewRequestedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create([
        'company_name'          => 'Sparkle Clean',
        'google_review_enabled' => true,
        'google_review_reward'  => 15.00,
    ]);
});

function createReviewCustomer(array $overrides = []): User
{
    $user = User::create(array_merge([
        'first_name'        => 'John',
        'last_name'         => 'Customer',
        'email'             => 'customer_' . uniqid() . '@example.com',
        'password'          => bcrypt('password'),
        'role'              => 2,
        'is_active'         => true,
    ], $overrides));

    $user->markEmailAsVerified();

    return $user;
}

function createReviewAdmin(): User
{
    return User::create([
        'first_name'        => 'Admin',
        'last_name'         => 'User',
        'email'             => 'admin_' . uniqid() . '@example.com',
        'password'          => bcrypt('password'),
        'role'              => 1,
        'is_active'         => true,
        'email_verified_at' => now(),
    ]);
}

/*
|--------------------------------------------------------------------------
| Customer Request Tests (1-5)
|--------------------------------------------------------------------------
*/

it('1. customer can submit a Google Review Reward request', function () {
    Notification::fake();
    $customer = createReviewCustomer();

    $response = $this->actingAs($customer)
        ->post(route('customer.review.store'));

    $response->assertRedirect(route('customer.review.index'));

    $this->assertDatabaseHas('google_reviews', [
        'user_id' => $customer->id,
        'status'  => 'pending',
    ]);
});

it('2. new request starts with pending status', function () {
    Notification::fake();
    $customer = createReviewCustomer();

    $this->actingAs($customer)->post(route('customer.review.store'));

    $review = GoogleReview::where('user_id', $customer->id)->first();
    expect($review)->not->toBeNull()
        ->and($review->status)->toBe(ReviewStatus::PENDING);
});

it('3. customer cannot create duplicate pending requests', function () {
    Notification::fake();
    $customer = createReviewCustomer();

    $this->actingAs($customer)->post(route('customer.review.store'));

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertSessionHasErrors(['review']);
    expect(GoogleReview::where('user_id', $customer->id)->count())->toBe(1);
});

it('4. customer cannot request again after receiving an approved reward', function () {
    $customer = createReviewCustomer();
    GoogleReview::create([
        'user_id'       => $customer->id,
        'status'        => ReviewStatus::APPROVED->value,
        'reward_amount' => 15.00,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertSessionHasErrors(['review']);
});

it('5. customer can request again after a request is cancelled', function () {
    Notification::fake();
    $customer = createReviewCustomer();
    GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::CANCELLED->value,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertRedirect(route('customer.review.index'));
    expect(GoogleReview::where('user_id', $customer->id)->count())->toBe(2);
});

/*
|--------------------------------------------------------------------------
| Cancellation Tests (6-8)
|--------------------------------------------------------------------------
*/

it('6. customer can cancel a pending request', function () {
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $response = $this->actingAs($customer)
        ->post(route('customer.review.cancel', $review->id));

    $response->assertRedirect(route('customer.review.index'));
    expect($review->fresh()->status)->toBe(ReviewStatus::CANCELLED);
});

it('7. cancelling a request does not create a wallet credit', function () {
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($customer)->post(route('customer.review.cancel', $review->id));

    $this->assertDatabaseMissing('wallet_transactions', [
        'user_id' => $customer->id,
    ]);
});

it('8. cancelled request can be submitted again', function () {
    Notification::fake();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($customer)->post(route('customer.review.cancel', $review->id));
    expect($review->fresh()->status)->toBe(ReviewStatus::CANCELLED);

    $this->actingAs($customer)->post(route('customer.review.store'));

    $pendingCount = GoogleReview::where('user_id', $customer->id)
        ->where('status', ReviewStatus::PENDING->value)
        ->count();

    expect($pendingCount)->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Approval Tests (9-14)
|--------------------------------------------------------------------------
*/

it('9. admin can approve a pending request', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('reviews.approve', $review->id));

    $response->assertRedirect(route('reviews.edit', $review->id));
    expect($review->fresh()->status)->toBe(ReviewStatus::APPROVED);
});

it('10. approval changes status to approved', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    expect($review->fresh()->status)->toBe(ReviewStatus::APPROVED);
});

it('11. approval creates one wallet credit', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    $this->assertDatabaseHas('wallet_transactions', [
        'user_id' => $customer->id,
        'type'    => 'credit',
        'source'  => 'review_bonus',
    ]);
    expect(WalletTransaction::where('user_id', $customer->id)->count())->toBe(1);
});

it('12. approval uses the configured Google Review Reward amount', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    $transaction = WalletTransaction::where('user_id', $customer->id)->first();
    expect((float) $transaction->amount)->toBe(15.00);
});

it('13. approval sends a customer notification', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    Notification::assertSentTo($customer, GoogleReviewApprovedNotification::class);
});

it('14. the same request cannot generate a second wallet credit', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    // Try approving again
    $response = $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    $response->assertSessionHasErrors(['status']);
    expect(WalletTransaction::where('user_id', $customer->id)->count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Eligibility Tests (15-17)
|--------------------------------------------------------------------------
*/

it('15. customer with an approved reward cannot create another reward request', function () {
    $customer = createReviewCustomer();
    GoogleReview::create([
        'user_id'       => $customer->id,
        'status'        => ReviewStatus::APPROVED->value,
        'reward_amount' => 15.00,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertSessionHasErrors(['review']);
});

it('16. customer with a cancelled request can create a new request', function () {
    Notification::fake();
    $customer = createReviewCustomer();
    GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::CANCELLED->value,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertRedirect(route('customer.review.index'));
});

it('17. customer with a pending request cannot create another request', function () {
    $customer = createReviewCustomer();
    GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $response = $this->actingAs($customer)->post(route('customer.review.store'));

    $response->assertSessionHasErrors(['review']);
});

/*
|--------------------------------------------------------------------------
| Settings Tests (18-19)
|--------------------------------------------------------------------------
*/

it('18. review feature is available when Google Review Reward is enabled', function () {
    $customer = createReviewCustomer();

    $this->actingAs($customer)
        ->get(route('customer.review.index'))
        ->assertOk()
        ->assertSee('Google Review Reward');
});

it('19. review feature is hidden/unavailable when disabled', function () {
    Setting::first()->update(['google_review_enabled' => false]);
    $customer = createReviewCustomer();

    $this->actingAs($customer)
        ->get(route('customer.review.index'))
        ->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| Authorization Tests (20-21)
|--------------------------------------------------------------------------
*/

it('20. customer cannot access another customer review request cancellation', function () {
    $customer1 = createReviewCustomer();
    $customer2 = createReviewCustomer();

    $review = GoogleReview::create([
        'user_id' => $customer1->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($customer2)
        ->post(route('customer.review.cancel', $review->id))
        ->assertStatus(403);
});

it('21. unauthorized users cannot approve or cancel review requests', function () {
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    // Customer trying to access admin approve route
    $this->actingAs($customer)
        ->post(route('reviews.approve', $review->id))
        ->assertStatus(403);
});

/*
|--------------------------------------------------------------------------
| Admin Operations Tests (22-24)
|--------------------------------------------------------------------------
*/

it('22. admin can view review requests list', function () {
    $admin = createReviewAdmin();

    $this->actingAs($admin)
        ->get(route('reviews.index'))
        ->assertOk()
        ->assertSee('Google Review Rewards');
});

it('23. admin can view review request details', function () {
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)
        ->get(route('reviews.edit', $review->id))
        ->assertOk()
        ->assertSee('Review Reward Application #' . $review->id)
        ->assertSee($customer->email);
});

it('24. admin can delete a pending or cancelled review request', function () {
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::CANCELLED->value,
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('reviews.destroy', $review->id));

    $response->assertRedirect(route('reviews.index'));
    $this->assertDatabaseMissing('google_reviews', ['id' => $review->id]);
});

/*
|--------------------------------------------------------------------------
| Audit Logs Tests (25-28)
|--------------------------------------------------------------------------
*/

it('25. request creation is logged in audit logs', function () {
    Notification::fake();
    $customer = createReviewCustomer();

    $this->actingAs($customer)->post(route('customer.review.store'));

    $review = GoogleReview::where('user_id', $customer->id)->first();
    $this->assertDatabaseHas('audit_logs', [
        'auditable_type' => GoogleReview::class,
        'auditable_id'   => $review->id,
        'action'         => 'created',
    ]);
});

it('26. cancellation is logged in audit logs', function () {
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($customer)->post(route('customer.review.cancel', $review->id));

    $this->assertDatabaseHas('audit_logs', [
        'auditable_type' => GoogleReview::class,
        'auditable_id'   => $review->id,
        'action'         => 'updated',
    ]);
});

it('27. approval is logged in audit logs', function () {
    Notification::fake();
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::PENDING->value,
    ]);

    $this->actingAs($admin)->post(route('reviews.approve', $review->id));

    $this->assertDatabaseHas('audit_logs', [
        'auditable_type' => GoogleReview::class,
        'auditable_id'   => $review->id,
        'action'         => 'updated',
    ]);
});

it('28. deletion is logged in audit logs', function () {
    $admin = createReviewAdmin();
    $customer = createReviewCustomer();
    $review = GoogleReview::create([
        'user_id' => $customer->id,
        'status'  => ReviewStatus::CANCELLED->value,
    ]);

    $this->actingAs($admin)->delete(route('reviews.destroy', $review->id));

    $this->assertDatabaseHas('audit_logs', [
        'auditable_type' => GoogleReview::class,
        'auditable_id'   => $review->id,
        'action'         => 'deleted',
    ]);
});
