<?php

use App\Services\BookingSessionService;
use Illuminate\Support\Facades\Session;

it('returns empty array when no booking session data exists', function () {
    Session::forget('booking_wizard');
    $service = new BookingSessionService();

    expect($service->getBookingSession())->toBeArray()->toBeEmpty()
        ->and($service->getStep1Data())->toBeArray()->toBeEmpty()
        ->and($service->getStep2Data())->toBeArray()->toBeEmpty();
});

it('stores and retrieves step 1 booking session data', function () {
    Session::forget('booking_wizard');
    $service = new BookingSessionService();

    $step1Input = [
        'service_id' => 1,
        'questions' => [1 => 'Office', 2 => '200 sqm'],
        'service_notes' => 'Please arrive on time.',
    ];

    $service->saveStep1($step1Input);

    $savedStep1 = $service->getStep1Data();

    expect($savedStep1)->toBeArray()
        ->and($savedStep1['service_id'])->toBe(1)
        ->and($savedStep1['questions'])->toHaveCount(2)
        ->and($savedStep1['service_notes'])->toBe('Please arrive on time.');
});

it('stores and retrieves step 2 booking session data', function () {
    Session::forget('booking_wizard');
    $service = new BookingSessionService();

    $step2Input = [
        'booking_date' => '2026-09-20',
        'start_time'   => '09:00:00',
        'end_time'     => '10:00:00',
        'frequency'    => 'weekly',
    ];

    $service->saveStep2($step2Input);

    $savedStep2 = $service->getStep2Data();

    expect($savedStep2)->toBeArray()
        ->and($savedStep2['booking_date'])->toBe('2026-09-20')
        ->and($savedStep2['start_time'])->toBe('09:00:00')
        ->and($savedStep2['end_time'])->toBe('10:00:00')
        ->and($savedStep2['frequency'])->toBe('weekly');
});

it('clears booking session data completely', function () {
    Session::forget('booking_wizard');
    $service = new BookingSessionService();

    $service->saveStep1(['service_id' => 2]);
    $service->saveStep2(['booking_date' => '2026-09-25']);

    expect($service->getBookingSession())->not()->toBeEmpty();

    $service->clearSession();

    expect($service->getBookingSession())->toBeEmpty()
        ->and($service->getStep1Data())->toBeEmpty()
        ->and($service->getStep2Data())->toBeEmpty();
});

it('stores, retrieves, and removes offer booking session data', function () {
    Session::forget('booking_wizard');
    $service = new BookingSessionService();

    expect($service->getOfferData())->toBeArray()->toBeEmpty();

    $offerData = [
        'type' => 'promo',
        'code' => 'SAVE10',
        'discount_amount' => 10.00,
    ];

    $service->saveOffer($offerData);

    expect($service->getOfferData())->toBeArray()
        ->and($service->getOfferData()['code'])->toBe('SAVE10');

    $service->removeOffer();

    expect($service->getOfferData())->toBeArray()->toBeEmpty();
});
