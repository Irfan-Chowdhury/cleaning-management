<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateWeeklyScheduleRequest extends FormRequest
{
    private const VALID_DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['required', 'string', 'in:' . implode(',', self::VALID_DAYS)],
            'is_active' => ['nullable', 'boolean'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $startTimes = collect($this->input('slots', []))
                    ->pluck('start_time')
                    ->filter()
                    ->values();

                if ($startTimes->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('slots', 'Start time must be unique for the selected day.');
                }

                $routeDay = ucfirst(strtolower((string) $this->route('day')));
                if ($routeDay !== $this->input('day_of_week')) {
                    $validator->errors()->add('day_of_week', 'The selected day does not match the schedule being edited.');
                }

                foreach ($this->input('slots', []) as $index => $slot) {
                    if (
                        ! empty($slot['start_time'])
                        && ! empty($slot['end_time'])
                        && $slot['end_time'] <= $slot['start_time']
                    ) {
                        $validator->errors()->add("slots.$index.end_time", 'End time must be after start time.');
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.in' => 'Please select a valid day name.',
            'slots.*.start_time.required' => 'Start time is required.',
            'slots.*.start_time.date_format' => 'Start time must be a valid time.',
            'slots.*.end_time.date_format' => 'End time must be a valid time.',
        ];
    }
}
