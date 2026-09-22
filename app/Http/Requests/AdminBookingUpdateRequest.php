<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminBookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'         => ['required', Rule::enum(BookingStatus::class)],
            'service_id'     => ['nullable', 'exists:services,id'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
            'date'           => ['nullable', 'date'],
            'slot'           => ['nullable', 'string'],
            'payment_status' => ['nullable', 'string', 'in:paid,pending,failed,refunded,unpaid'],
            'payment_method' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please select a booking status.',
            'status.enum'     => 'Selected booking status is invalid.',
        ];
    }
}
