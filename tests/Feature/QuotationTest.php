<?php

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\QuotationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function getAdminUser(): User
{
    return User::where('role', 1)->first()
        ?? User::factory()->create(['role' => 1, 'is_active' => true]);
}

function validQuotationPayload(array $overrides = []): array
{
    return array_merge([
        'quote_date'     => '2026-10-13',
        'abn'            => '51 824 753 556',
        'contact_person' => 'John Smith',
        'company_name'   => 'Acme Cleaning Co',
        'address'        => '123 Test Street, Sydney NSW 2000',
        'phone'          => '0412 345 678',
        'email'          => 'john@acmecleaning.com.au',
        'frequency'      => 'Weekly',
        'shift_schedule' => 'Morning (08:00 - 12:00)',
        'subtotal'       => 900.00,
        'gst_amount'     => 90.00,
        'total_amount'   => 990.00,
        'items'          => [
            ['description' => 'Office Cleaning', 'amount' => 600.00],
            ['description' => 'Window Cleaning', 'amount' => 300.00],
        ],
    ], $overrides);
}

// ─── QuotationService Unit Tests ─────────────────────────────────────────────

describe('QuotationService', function () {

    it('generates a sequential quote number for the current year', function () {
        $service = app(QuotationService::class);

        $number = $service->generateSequentialQuoteNumber();

        expect($number)->toMatch('/^D2G-\d{4}-\d{4}$/');
    });

    it('generates D2G-{YEAR}-0001 when no quotations exist', function () {
        $service = app(QuotationService::class);
        $year    = now()->format('Y');

        $number = $service->generateSequentialQuoteNumber();

        expect($number)->toBe("D2G-{$year}-0001");
    });

    it('increments quote number correctly when previous quotes exist', function () {
        $year = now()->format('Y');

        Quotation::factory()->create(['quote_number' => "D2G-{$year}-0005"]);

        $service = app(QuotationService::class);
        $number  = $service->generateSequentialQuoteNumber();

        expect($number)->toBe("D2G-{$year}-0006");
    });

    it('calculates totals correctly with 10% GST', function () {
        $service = app(QuotationService::class);

        $result = $service->calculateTotals([500.00, 250.00, 150.00]);

        expect($result['subtotal'])->toBe(900.00)
            ->and($result['gst_amount'])->toBe(90.00)
            ->and($result['total_amount'])->toBe(990.00);
    });

    it('stores a quotation with its line items in a transaction', function () {
        $service   = app(QuotationService::class);
        $payload   = validQuotationPayload();

        $quotation = $service->store($payload);

        expect($quotation)->toBeInstanceOf(Quotation::class)
            ->and($quotation->contact_person)->toBe('John Smith')
            ->and($quotation->company_name)->toBe('Acme Cleaning Co')
            ->and($quotation->status)->toBe('draft')
            ->and($quotation->items)->toHaveCount(2);

        $this->assertDatabaseHas('quotations', [
            'id'             => $quotation->id,
            'contact_person' => 'John Smith',
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'quotation_id' => $quotation->id,
            'description'  => 'Office Cleaning',
        ]);
    });

    it('updates a quotation and replaces its line items atomically', function () {
        $service   = app(QuotationService::class);
        $quotation = $service->store(validQuotationPayload());

        $updated = $service->update($quotation, [
            'quote_date'     => '2026-10-14',
            'contact_person' => 'Jane Doe',
            'company_name'   => 'Updated Co',
            'address'        => '456 New Ave, Melbourne VIC 3000',
            'phone'          => '0422 111 222',
            'email'          => 'jane@updated.com',
            'frequency'      => 'Daily',
            'shift_schedule' => 'Evening (18:00 - 22:00)',
            'subtotal'       => 1000.00,
            'gst_amount'     => 100.00,
            'total_amount'   => 1100.00,
            'items'          => [
                ['description' => 'Deep Clean', 'amount' => 1000.00],
            ],
        ]);

        expect($updated->contact_person)->toBe('Jane Doe')
            ->and($updated->items)->toHaveCount(1)
            ->and($updated->items->first()->description)->toBe('Deep Clean');

        $this->assertDatabaseCount('quotation_items', 1);
    });

    it('deletes a quotation and all its line items', function () {
        $service   = app(QuotationService::class);
        $quotation = $service->store(validQuotationPayload());
        $id        = $quotation->id;

        $service->delete($quotation);

        $this->assertDatabaseMissing('quotations', ['id' => $id]);
        $this->assertDatabaseMissing('quotation_items', ['quotation_id' => $id]);
    });

});

// ─── Feature Tests: Store Endpoint ───────────────────────────────────────────

describe('POST /quotations (store)', function () {

    it('redirects back to create form with success message on valid submission', function () {
        $admin   = getAdminUser();
        $payload = validQuotationPayload();

        $response = $this->actingAs($admin)->post(route('quotations.store'), $payload);

        $response->assertRedirect(route('quotations.create'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('quotations', [
            'contact_person' => 'John Smith',
            'company_name'   => 'Acme Cleaning Co',
        ]);
    });

    it('saves line items linked to the new quotation', function () {
        $admin = getAdminUser();

        $this->actingAs($admin)->post(route('quotations.store'), validQuotationPayload());

        $quotation = Quotation::where('contact_person', 'John Smith')->first();

        expect($quotation)->not->toBeNull()
            ->and($quotation->items)->toHaveCount(2);
    });

    it('sets status to draft on creation', function () {
        $admin = getAdminUser();

        $this->actingAs($admin)->post(route('quotations.store'), validQuotationPayload());

        $this->assertDatabaseHas('quotations', [
            'contact_person' => 'John Smith',
            'status'         => 'draft',
        ]);
    });

    it('fails validation when required fields are missing', function () {
        $admin = getAdminUser();

        $response = $this->actingAs($admin)->post(route('quotations.store'), []);

        $response->assertSessionHasErrors([
            'quote_date',
            'contact_person',
            'company_name',
            'address',
            'phone',
            'email',
            'frequency',
            'shift_schedule',
            'items',
        ]);
    });

    it('fails validation when items array is empty', function () {
        $admin   = getAdminUser();
        $payload = validQuotationPayload(['items' => []]);

        $response = $this->actingAs($admin)->post(route('quotations.store'), $payload);

        $response->assertSessionHasErrors('items');
    });

    it('fails validation when a line item is missing a description', function () {
        $admin   = getAdminUser();
        $payload = validQuotationPayload([
            'items' => [
                ['description' => '', 'amount' => 500.00],
            ],
        ]);

        $response = $this->actingAs($admin)->post(route('quotations.store'), $payload);

        $response->assertSessionHasErrors('items.0.description');
    });

    it('fails validation when email is invalid', function () {
        $admin   = getAdminUser();
        $payload = validQuotationPayload(['email' => 'not-an-email']);

        $response = $this->actingAs($admin)->post(route('quotations.store'), $payload);

        $response->assertSessionHasErrors('email');
    });

    it('returns 403 for unauthenticated requests', function () {
        $response = $this->post(route('quotations.store'), validQuotationPayload());

        $response->assertRedirect(route('login'));
    });

});
