<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:2048'],
            'welcome_credit' => ['nullable', 'numeric', 'min:0'],
            'referral_reward' => ['nullable', 'numeric', 'min:0'],
            'google_review_reward' => ['nullable', 'numeric', 'min:0'],
            'maximum_advance_booking_days' => ['nullable', 'integer', 'min:1'],
            'cancellation_notice_hours' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
