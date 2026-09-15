<?php

use App\Models\Service;
use App\Models\User;

function getCustomerUser(): User
{
    return User::where('email', 'customer@gmail.com')->first()
        ?? User::where('role', 2)->first()
        ?? User::first();
}

function getActiveService(): Service
{
    return Service::where('status', 'active')->first()
        ?? Service::first();
}

it('loads step 1 page for logged in customer user from UserSeeder', function () {
    $customer = getCustomerUser();

    $this->actingAs($customer)
        ->get(route('booking-service.create'))
        ->assertOk()
        ->assertViewIs('pages.booking-service.create')
        ->assertSee('Step 1 of 4: Service Details');
});

it('returns questionnaire JSON data for active service', function () {
    $customer = getCustomerUser();
    $service = getActiveService();

    $response = $this->actingAs($customer)
        ->getJson(route('booking-service.questionnaire', $service->id));

    $response->assertOk()
        ->assertJson([
            'service' => [
                'id'   => $service->id,
                'name' => $service->name,
            ],
        ]);
});

it('stores step 1 data in session and redirects to step 2', function () {
    $customer = getCustomerUser();
    $service = getActiveService();

    $response = $this->actingAs($customer)
        ->post(route('booking-service.store-step-1'), [
            'service_id'    => $service->id,
            'service_notes' => 'Please bring eco-friendly supplies.',
        ]);

    $response->assertRedirect(route('booking-service.date-time'));
    $response->assertSessionHas('booking_wizard.step1.service_id', $service->id);
    $response->assertSessionHas('booking_wizard.step1.service_notes', 'Please bring eco-friendly supplies.');
});

it('validates required service_id field on step 1 submission', function () {
    $customer = getCustomerUser();

    $response = $this->actingAs($customer)
        ->post(route('booking-service.store-step-1'), [
            'service_id' => '',
        ]);

    $response->assertSessionHasErrors(['service_id']);
});
