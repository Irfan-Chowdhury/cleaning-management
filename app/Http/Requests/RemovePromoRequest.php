<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemovePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking ID is required.',
            'booking_id.exists'   => 'Booking does not exist.',
        ];
    }
}
