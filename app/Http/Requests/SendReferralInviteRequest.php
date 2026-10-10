<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendReferralInviteRequest extends FormRequest
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
            'email'   => ['required', 'email', 'max:255', 'unique:users,email'],
            'message' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom message for validation errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your friend\'s email address.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'This email address is already registered as an existing account.',
            'message.max'    => 'Personal message cannot exceed 500 characters.',
        ];
    }
}
