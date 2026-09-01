<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'timezone' => ['nullable', 'timezone', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'minimum_booking_amount' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'maximum_booking_amount' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'maximum_advance_booking_days' => ['nullable', 'integer', 'min:1'],
            'cancellation_notice_hours' => ['nullable', 'integer', 'min:0'],
            'welcome_credit' => ['nullable', 'numeric', 'min:0'],
            'welcome_credit_enabled' => ['nullable', 'boolean'],
            'referral_reward' => ['nullable', 'numeric', 'min:0'],
            'referral_reward_enabled' => ['nullable', 'boolean'],
            'google_review_reward' => ['nullable', 'numeric', 'min:0'],
            'google_review_enabled' => ['nullable', 'boolean'],
            'promotion_max_uses' => ['nullable', 'integer', 'min:0'],
            'promotion_max_uses_per_customer' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $minimum = $this->input('minimum_booking_amount');
                $maximum = $this->input('maximum_booking_amount');

                if ($minimum !== null && $maximum !== null && (float) $maximum < (float) $minimum) {
                    $validator->errors()->add('maximum_booking_amount', 'Maximum booking amount must be greater than or equal to the minimum booking amount.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'currency.size' => 'Currency must be a 3-letter ISO code.',
            'currency.regex' => 'Currency must use uppercase letters, for example USD.',
        ];
    }
}
