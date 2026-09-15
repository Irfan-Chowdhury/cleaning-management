<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'detail_mode'          => ['required', 'in:account,new'],
            'customer_name'        => ['required', 'string', 'max:255'],
            'customer_email'       => ['required', 'email', 'max:255'],
            'customer_phone'       => ['required', 'string', 'max:50'],
            'customer_address'     => ['required', 'string', 'max:500'],
            'unit_suite_floor'     => ['nullable', 'string', 'max:255'],
            'suburb'               => ['required', 'string', 'max:255'],
            'postcode'             => ['required', 'string', 'max:20'],
            'special_instructions' => ['nullable', 'string', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'    => 'Please enter your full name.',
            'customer_email.required'   => 'Please enter your email address.',
            'customer_phone.required'   => 'Please enter your phone number.',
            'customer_address.required' => 'Please enter your service address.',
            'suburb.required'           => 'Please enter your suburb.',
            'postcode.required'         => 'Please enter your postcode.',
        ];
    }
}
