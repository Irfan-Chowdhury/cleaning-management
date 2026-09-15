<?php

use App\Models\Booking;
use App\Models\Holiday;
use App\Models\ScheduleSlot;
use App\Models\Service;
use App\Models\User;
use App\Models\WeeklySchedule;
use Carbon\Carbon;

function step2User(): User
{
    return User::where('email', 'customer@gmail.com')->first()
        ?? User::where('role', 2)->first()
        ?? User::first();
}

function getStep2Service(): Service
{
    return Service::where('status', 'active')->first()
        ?? Service::first();
}

it('loads step 2 page for authenticated customer user', function () {
    $user = step2User();

    $this->actingAs($user)
        ->get(route('booking-service.date-time'))
        ->assertOk()
        ->assertViewIs('pages.booking-service.date-time')
        ->assertSee('Step 2 of 4: Date & Time');
});

it('fetches slots for date via API and returns active slots', function () {
    $user = step2User();
    $today = Carbon::today();
    $dayName = $today->format('l');

    $schedule = WeeklySchedule::firstOrCreate(
        ['day_of_week' => $dayName],
        ['is_active' => true]
    );

    if (!$schedule->is_active) {
        $schedule->update(['is_active' => true]);
    }

    ScheduleSlot::firstOrCreate(
        ['weekly_schedule_id' => $schedule->id, 'start_time' => '09:00:00'],
        ['end_time' => '10:00:00', 'sort_order' => 1]
    );

    $response = $this->actingAs($user)
        ->getJson(route('booking-service.slots-for-date', ['date' => $today->format('Y-m-d')]));

    $response->assertOk()
        ->assertJson([
            'date'          => $today->format('Y-m-d'),
            'day_of_week'   => $dayName,
            'is_holiday'    => false,
            'is_day_active' => true,
        ]);

    expect($response->json('slots'))->not()->toBeEmpty();
});

it('identifies holiday dates via slots-for-date API', function () {
    $user = step2User();
    $holidayDate = Carbon::today()->addDays(150)->format('Y-m-d');

    Holiday::firstOrCreate(
        ['start_date' => $holidayDate, 'end_date' => $holidayDate],
        ['title' => 'Test Labor Day', 'is_active' => true]
    );

    $response = $this->actingAs($user)
        ->getJson(route('booking-service.slots-for-date', ['date' => $holidayDate]));

    $response->assertOk()
        ->assertJson([
            'is_holiday'    => true,
            'holiday_title' => 'Test Labor Day',
            'is_day_active' => false,
            'slots'         => [],
        ]);
});

it('marks booked slots as is_booked true in slots-for-date API', function () {
    $user = step2User();
    $service = getStep2Service();
    $today = Carbon::today();
    $dayName = $today->format('l');

    $schedule = WeeklySchedule::firstOrCreate(
        ['day_of_week' => $dayName],
        ['is_active' => true]
    );

    ScheduleSlot::firstOrCreate(
        ['weekly_schedule_id' => $schedule->id, 'start_time' => '09:00:00'],
        ['end_time' => '10:00:00', 'sort_order' => 1]
    );

    $booking = Booking::firstOrCreate(
        [
            'booking_date' => $today->format('Y-m-d'),
            'start_time'   => '09:00:00',
        ],
        [
            'user_id'    => $user->id,
            'service_id' => $service->id,
            'status'     => 'pending',
        ]
    );

    $response = $this->actingAs($user)
        ->getJson(route('booking-service.slots-for-date', ['date' => $today->format('Y-m-d')]));

    $response->assertOk();

    $slots = collect($response->json('slots'));
    $bookedSlot = $slots->firstWhere('start_time', '09:00');

    expect($bookedSlot)->not()->toBeNull()
        ->and($bookedSlot['is_booked'])->toBeTrue();
});

it('successfully stores step 2 date and time into session and redirects to step 3', function () {
    $user = step2User();
    $today = Carbon::today();
    $dayName = $today->format('l');

    $schedule = WeeklySchedule::firstOrCreate(
        ['day_of_week' => $dayName],
        ['is_active' => true]
    );

    ScheduleSlot::firstOrCreate(
        ['weekly_schedule_id' => $schedule->id, 'start_time' => '09:00:00'],
        ['end_time' => '10:00:00', 'sort_order' => 1]
    );

    // Ensure date is not on holiday
    Holiday::where('start_date', '<=', $today->format('Y-m-d'))
        ->where('end_date', '>=', $today->format('Y-m-d'))
        ->delete();

    // Ensure slot not booked
    Booking::where('booking_date', $today->format('Y-m-d'))
        ->where('start_time', '09:00:00')
        ->delete();

    $response = $this->actingAs($user)
        ->post(route('booking-service.store-step-2'), [
            'booking_date' => $today->format('Y-m-d'),
            'start_time'   => '09:00:00',
            'end_time'     => '10:00:00',
        ]);

    $response->assertRedirect(route('booking-service.your-details'));
    $response->assertSessionHas('booking_wizard.step2.booking_date', $today->format('Y-m-d'));
    $response->assertSessionHas('booking_wizard.step2.start_time', '09:00:00');
});

it('validates and rejects past booking dates', function () {
    $user = step2User();
    $yesterday = Carbon::yesterday()->format('Y-m-d');

    $response = $this->actingAs($user)
        ->post(route('booking-service.store-step-2'), [
            'booking_date' => $yesterday,
            'start_time'   => '09:00:00',
        ]);

    $response->assertSessionHasErrors(['booking_date']);
});

it('validates and rejects booking dates on active holidays', function () {
    $user = step2User();
    $holidayDate = Carbon::today()->addDays(200)->format('Y-m-d');

    Holiday::firstOrCreate(
        ['start_date' => $holidayDate, 'end_date' => $holidayDate],
        ['title' => 'Future Test Holiday', 'is_active' => true]
    );

    $response = $this->actingAs($user)
        ->post(route('booking-service.store-step-2'), [
            'booking_date' => $holidayDate,
            'start_time'   => '09:00:00',
        ]);

    $response->assertSessionHasErrors(['booking_date']);
});

it('validates and rejects already booked time slots', function () {
    $user = step2User();
    $service = getStep2Service();
    $futureDate = Carbon::today()->addDays(10)->format('Y-m-d');
    $dayName = Carbon::parse($futureDate)->format('l');

    $schedule = WeeklySchedule::firstOrCreate(
        ['day_of_week' => $dayName],
        ['is_active' => true]
    );

    ScheduleSlot::firstOrCreate(
        ['weekly_schedule_id' => $schedule->id, 'start_time' => '10:00:00'],
        ['end_time' => '11:00:00', 'sort_order' => 2]
    );

    Booking::firstOrCreate(
        [
            'booking_date' => $futureDate,
            'start_time'   => '10:00:00',
        ],
        [
            'user_id'    => $user->id,
            'service_id' => $service->id,
            'status'     => 'pending',
        ]
    );

    $response = $this->actingAs($user)
        ->post(route('booking-service.store-step-2'), [
            'booking_date' => $futureDate,
            'start_time'   => '10:00:00',
        ]);

    $response->assertSessionHasErrors(['start_time']);
});
