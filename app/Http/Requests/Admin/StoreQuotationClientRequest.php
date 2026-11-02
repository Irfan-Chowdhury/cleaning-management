<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/\s+/', '', (string) $this->input('phone')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'contact_person' => ['required', 'string', 'max:255'],
            'company_name'   => ['nullable', 'string', 'max:255'],
            'phone'          => [
                'required',
                'string',
                'max:30',
                'regex:/^\+?[0-9]+$/',
                'unique:quotation_clients,phone',
            ],
            'email'          => ['required', 'email', 'max:255', 'unique:quotation_clients,email'],
            'address'        => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_person.required' => 'The contact person name is required.',
            'phone.required'          => 'The phone number is required.',
            'phone.regex'             => 'The phone number must only contain digits and an optional leading plus (+) sign.',
            'phone.unique'            => 'This phone number is already registered to a client.',
            'email.required'          => 'The email address is required.',
            'email.email'             => 'Please provide a valid email address.',
            'email.unique'            => 'This email address is already registered to a client.',
            'address.required'        => 'The address is required.',
        ];
    }
}
