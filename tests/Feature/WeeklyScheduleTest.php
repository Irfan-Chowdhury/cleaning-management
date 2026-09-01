<?php

use App\Models\ScheduleSlot;
use App\Models\User;
use App\Models\WeeklySchedule;
use Database\Seeders\WeeklyScheduleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function weeklyScheduleAdmin(): User
{
    return User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

it('loads the weekly schedule index from database records', function () {
    $this->seed(WeeklyScheduleSeeder::class);

    $this->actingAs(weeklyScheduleAdmin())
        ->get(route('weekly-schedule.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.weekly-schedule.index')
        ->assertSee('Monday')
        ->assertSee('Sunday');
});

it('loads a valid day edit page with eager loaded schedule slots', function () {
    $this->seed(WeeklyScheduleSeeder::class);

    $response = $this->actingAs(weeklyScheduleAdmin())
        ->get(route('weekly-schedule.edit', 'monday'));

    $response->assertOk()
        ->assertViewIs('pages.admin.weekly-schedule.edit')
        ->assertViewHas('weeklySchedule', function (WeeklySchedule $schedule) {
            return $schedule->day_of_week === 'Monday'
                && $schedule->relationLoaded('slots')
                && $schedule->slots->count() === 5;
        });
});

it('rejects invalid day names', function () {
    $this->actingAs(weeklyScheduleAdmin())
        ->get('/weekly-schedule/funday/edit')
        ->assertNotFound();
});

it('updates a weekly schedule and replaces slots', function () {
    $this->seed(WeeklyScheduleSeeder::class);

    $this->actingAs(weeklyScheduleAdmin())
        ->putJson(route('weekly-schedule.update', 'monday'), [
            'day_of_week' => 'Monday',
            'is_active' => false,
            'slots' => [
                ['start_time' => '09:00', 'end_time' => '10:00'],
                ['start_time' => '11:00', 'end_time' => null],
            ],
        ])
        ->assertOk()
        ->assertJson([
            'message' => 'Monday schedule updated successfully!',
        ]);

    $schedule = WeeklySchedule::where('day_of_week', 'Monday')->firstOrFail();

    expect($schedule->is_active)->toBeFalse()
        ->and($schedule->slots)->toHaveCount(2);

    $this->assertDatabaseHas('schedule_slots', [
        'weekly_schedule_id' => $schedule->id,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'sort_order' => 1,
    ]);
});

it('validates duplicate start times for the same day', function () {
    $this->seed(WeeklyScheduleSeeder::class);

    $this->actingAs(weeklyScheduleAdmin())
        ->putJson(route('weekly-schedule.update', 'tuesday'), [
            'day_of_week' => 'Tuesday',
            'is_active' => true,
            'slots' => [
                ['start_time' => '09:00', 'end_time' => '10:00'],
                ['start_time' => '09:00', 'end_time' => '11:00'],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slots']);
});

it('validates required and formatted slot start times', function () {
    $this->seed(WeeklyScheduleSeeder::class);

    $this->actingAs(weeklyScheduleAdmin())
        ->putJson(route('weekly-schedule.update', 'wednesday'), [
            'day_of_week' => 'Wednesday',
            'is_active' => true,
            'slots' => [
                ['start_time' => '', 'end_time' => null],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slots.0.start_time']);
});

it('defines weekly schedule slot relationships', function () {
    $schedule = WeeklySchedule::create([
        'day_of_week' => 'Monday',
        'is_active' => true,
    ]);

    $slot = ScheduleSlot::create([
        'weekly_schedule_id' => $schedule->id,
        'start_time' => '08:00',
        'end_time' => '09:00',
        'sort_order' => 1,
    ]);

    expect($schedule->slots()->first()->is($slot))->toBeTrue()
        ->and($slot->weeklySchedule->is($schedule))->toBeTrue();
});
