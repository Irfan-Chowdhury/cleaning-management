<?php

namespace App\Http\Requests;

use App\Models\ServiceQuestion;
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
        $rules = [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'questions' => ['nullable', 'array'],
            'questions.*' => ['nullable'],
            'service_notes' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];

        $serviceId = $this->input('service_id');
        if ($serviceId) {
            try {
                $requiredQuestions = ServiceQuestion::where('service_id', $serviceId)
                    ->where('required', true)
                    ->get();

                foreach ($requiredQuestions as $question) {
                    $rules["questions.{$question->id}"] = ['required'];
                }
            } catch (\Throwable $e) {
                // Ignore DB failure if questions table is not yet migrated/accessible
            }
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        $messages = [
            'service_id.required' => 'Please choose a service to continue.',
            'service_id.exists' => 'The selected service is invalid.',
            'service_notes.max' => 'Notes cannot exceed 500 characters.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'Uploaded file must be a valid image.',
            'images.*.mimes' => 'Images must be in jpeg, png, jpg, gif, or webp format.',
            'images.*.max' => 'Each image size must not exceed 5MB.',
        ];

        $serviceId = $this->input('service_id');
        if ($serviceId) {
            try {
                $requiredQuestions = ServiceQuestion::where('service_id', $serviceId)
                    ->where('required', true)
                    ->get();

                foreach ($requiredQuestions as $question) {
                    $messages["questions.{$question->id}.required"] = "The '{$question->title}' field is required.";
                }
            } catch (\Throwable $e) {
                // Ignore DB failure
            }
        }

        return $messages;
    }
}
