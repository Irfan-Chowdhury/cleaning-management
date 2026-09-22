<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Holiday;
use App\Models\ScheduleSlot;
use App\Models\WeeklySchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookingStep2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time'   => ['required', 'string'],
            'end_time'     => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'booking_date.required'           => 'Please select a booking date.',
            'booking_date.date_format'        => 'Invalid booking date format.',
            'booking_date.after_or_equal'     => 'Past dates cannot be selected.',
            'start_time.required'             => 'Please select a time slot.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $dateStr = $this->input('booking_date');
            $startTime = $this->input('start_time');

            if (!$dateStr || $validator->errors()->has('booking_date')) {
                return;
            }

            try {
                $date = Carbon::parse($dateStr);
            } catch (\Exception $e) {
                return;
            }

            // 1. Check if date is a holiday
            $holiday = Holiday::where('is_active', true)
                ->where('start_date', '<=', $dateStr)
                ->where('end_date', '>=', $dateStr)
                ->first();

            if ($holiday) {
                $validator->errors()->add('booking_date', "The selected date falls on a holiday ({$holiday->title}) and cannot be booked.");
                return;
            }

            // 2. Check if weekly schedule day is active
            $dayName = $date->format('l'); // e.g. 'Monday'
            $weeklySchedule = WeeklySchedule::where('day_of_week', $dayName)->first();

            if (!$weeklySchedule || !$weeklySchedule->is_active) {
                $validator->errors()->add('booking_date', "Cleaning services are not available on {$dayName}s.");
                return;
            }

            if (!$startTime || $validator->errors()->has('start_time')) {
                return;
            }

            // Normalize time string (e.g. '09:00:00' or '09:00' or '9:00 AM')
            $formattedStartTime = $this->normalizeTime($startTime);

            // 3. Check if start time slot exists in active schedule
            $validSlot = ScheduleSlot::where('weekly_schedule_id', $weeklySchedule->id)
                ->where(function ($query) use ($startTime, $formattedStartTime) {
                    $query->where('start_time', $startTime)
                        ->orWhere('start_time', $formattedStartTime)
                        ->orWhere('start_time', 'LIKE', substr($formattedStartTime, 0, 5) . '%');
                })
                ->first();

            if (!$validSlot) {
                $validator->errors()->add('start_time', "The selected time slot is not available for {$dayName}.");
                return;
            }

            // 4. Check if slot is already booked for this date
            $isBooked = Booking::where('booking_date', $dateStr)
                ->where(function ($query) use ($startTime, $formattedStartTime) {
                    $query->where('start_time', $startTime)
                        ->orWhere('start_time', $formattedStartTime)
                        ->orWhere('start_time', 'LIKE', substr($formattedStartTime, 0, 5) . '%');
                })
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($isBooked) {
                $validator->errors()->add('start_time', "The selected time slot has already been booked. Please choose another time.");
            }
        });
    }

    /**
     * Helper to normalize time string to H:i:s.
     */
    protected function normalizeTime(string $time): string
    {
        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Exception $e) {
            return $time;
        }
    }
}
