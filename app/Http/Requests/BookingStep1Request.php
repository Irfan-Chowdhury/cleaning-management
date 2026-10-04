<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStep1Request extends FormRequest
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
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'questions' => ['nullable', 'array'],
            'service_notes' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Please choose a service to continue.',
            'service_id.exists' => 'The selected service is invalid.',
            'service_notes.max' => 'Notes cannot exceed 500 characters.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'Uploaded file must be a valid image.',
            'images.*.mimes' => 'Images must be in jpeg, png, jpg, gif, or webp format.',
            'images.*.max' => 'Each image size must not exceed 5MB.',
        ];
    }
}
