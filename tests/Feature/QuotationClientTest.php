<?php

use App\Models\QuotationClient;
use App\Models\User;
use App\Services\QuotationClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function getQuotationClientAdmin(): User
{
    return User::where('role', 1)->first()
        ?? User::factory()->create(['role' => 1, 'is_active' => true]);
}

function validQuotationClientPayload(array $overrides = []): array
{
    return array_merge([
        'contact_person' => 'Sarah Connor',
        'company_name'   => 'Cyberdyne Systems',
        'phone'          => '0412 345 678',
        'email'          => 'sarah@cyberdyne.com',
        'address'        => '123 Tech Parkway, Sydney NSW 2000',
    ], $overrides);
}

// ─── QuotationClientService Tests ───────────────────────────────────────────

describe('QuotationClientService', function () {

    it('stores a new client profile and strips phone whitespace', function () {
        $service = app(QuotationClientService::class);
        $payload = validQuotationClientPayload();

        $client = $service->store($payload);

        expect($client)->toBeInstanceOf(QuotationClient::class)
            ->and($client->contact_person)->toBe('Sarah Connor')
            ->and($client->company_name)->toBe('Cyberdyne Systems')
            ->and($client->phone)->toBe('0412345678')
            ->and($client->email)->toBe('sarah@cyberdyne.com');
    });

    it('updates an existing client profile', function () {
        $service = app(QuotationClientService::class);
        $client  = $service->store(validQuotationClientPayload());

        $updated = $service->update($client, [
            'contact_person' => 'Sarah J. Connor',
            'company_name'   => 'Resistance HQ',
            'phone'          => '0499888777',
            'email'          => 'sarah.c@resistance.org',
            'address'        => '456 Bunker Rd, Melbourne VIC 3000',
        ]);

        expect($updated->contact_person)->toBe('Sarah J. Connor')
            ->and($updated->company_name)->toBe('Resistance HQ')
            ->and($updated->phone)->toBe('0499888777');
    });

    it('deletes a client profile', function () {
        $service = app(QuotationClientService::class);
        $client  = $service->store(validQuotationClientPayload());

        $service->delete($client);

        expect(QuotationClient::find($client->id))->toBeNull();
    });

    it('searches clients by phone, name, email, or company', function () {
        $service = app(QuotationClientService::class);
        $client  = $service->store(validQuotationClientPayload([
            'phone'          => '0411222333',
            'contact_person' => 'John Wick',
            'company_name'   => 'Continental Hotel',
            'email'          => 'wick@continental.com',
        ]));

        $phoneMatch = $service->search('0411 222 333');
        expect($phoneMatch->pluck('id'))->toContain($client->id);

        $nameMatch = $service->search('John Wick');
        expect($nameMatch->pluck('id'))->toContain($client->id);

        $companyMatch = $service->search('Continental');
        expect($companyMatch->pluck('id'))->toContain($client->id);
    });
});

// ─── QuotationClientController HTTP Tests ────────────────────────────────────

describe('QuotationClientController', function () {

    it('requires authentication for clients directory', function () {
        $response = $this->get('/quotations/clients');

        $response->assertRedirect('/login');
    });

    it('allows admin users to view the clients directory', function () {
        $admin = getQuotationClientAdmin();

        $response = $this->actingAs($admin)->get('/quotations/clients');

        $response->assertOk()
            ->assertViewIs('pages.admin.quotations.clients.index');
    });

    it('creates a client profile with leading plus phone number', function () {
        $admin   = getQuotationClientAdmin();
        $payload = validQuotationClientPayload([
            'phone' => '+61488776655',
            'email' => 'plus.client@test.com',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/quotations/clients', $payload);

        $response->assertCreated()
            ->assertJsonPath('client.phone', '+61488776655');

        $this->assertDatabaseHas('quotation_clients', [
            'email' => 'plus.client@test.com',
            'phone' => '+61488776655',
        ]);
    });

    it('rejects phone numbers containing letters or illegal strings', function () {
        $admin   = getQuotationClientAdmin();
        $payload = validQuotationClientPayload([
            'phone' => '0412abc999',
            'email' => 'invalid.phone@test.com',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/quotations/clients', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    });

    it('creates a client profile via AJAX POST and returns JSON', function () {
        $admin   = getQuotationClientAdmin();
        $payload = validQuotationClientPayload([
            'phone' => '0488776655',
            'email' => 'ajax.client@test.com',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/quotations/clients', $payload);

        $response->assertCreated()
            ->assertJsonPath('client.contact_person', 'Sarah Connor')
            ->assertJsonPath('client.phone', '0488776655');

        $this->assertDatabaseHas('quotation_clients', [
            'email' => 'ajax.client@test.com',
            'phone' => '0488776655',
        ]);
    });

    it('validates unique phone and email on client creation', function () {
        $admin   = getQuotationClientAdmin();
        $payload = validQuotationClientPayload([
            'phone' => '0488776655',
            'email' => 'duplicate@test.com',
        ]);

        $this->actingAs($admin)->postJson('/quotations/clients', $payload)->assertCreated();

        // Duplicate attempt
        $response = $this->actingAs($admin)->postJson('/quotations/clients', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'email']);
    });

    it('returns search results for quotation creation autocomplete', function () {
        $admin = getQuotationClientAdmin();
        $service = app(QuotationClientService::class);
        $service->store(validQuotationClientPayload([
            'phone' => '0455667788',
            'contact_person' => 'Live Search Target',
        ]));

        $response = $this->actingAs($admin)->getJson('/quotations/clients/search?q=0455667788');

        $response->assertOk()
            ->assertJsonStructure(['clients']);
    });
});
