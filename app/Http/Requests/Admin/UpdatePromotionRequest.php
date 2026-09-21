<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePromotionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper((string) $this->input('code')),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promotions', 'code')->ignore($this->route('promotion'))],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required_if:discount_type,fixed', 'nullable', 'numeric', 'min:0.01', 'decimal:0,2'],
            'status' => ['required', Rule::in(['active', 'paused', 'expired'])],
            'start_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:start_at'],
            'new_customers_only' => ['nullable', 'boolean'],
            'existing_customers_only' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ((bool) $this->input('new_customers_only') && (bool) $this->input('existing_customers_only')) {
                    $validator->errors()->add('existing_customers_only', 'Promotion cannot be limited to both new and existing customers.');
                }

                if ($this->input('discount_type') === 'percentage' && (float) $this->input('discount_value') > 100) {
                    $validator->errors()->add('discount_value', 'Percentage discount cannot be greater than 100.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'code.alpha_dash' => 'Promotion code may only contain letters, numbers, dashes, and underscores.',
            'expires_at.after' => 'Expiry date must be after the start date.',
        ];
    }
}
