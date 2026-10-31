<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action'         => ['sometimes', 'in:update,convert'],
            'quote_date'     => ['required', 'date'],
            'abn'            => ['nullable', 'string', 'max:20'],
            'contact_person' => ['required', 'string', 'max:255'],
            'company_name'   => ['nullable', 'string', 'max:255'],
            'address'        => ['required', 'string', 'max:500'],
            'phone'          => ['required', 'string', 'max:30'],
            'email'          => ['required', 'email', 'max:255'],
            'frequency'      => ['required', 'string', 'max:100'],
            'shift_schedule' => ['required', 'string', 'max:100'],
            'terms_notes'    => ['nullable', 'string', 'max:5000'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.description'    => ['required', 'string', 'max:255'],
            'items.*.amount'         => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'               => 'At least one service line item is required.',
            'items.min'                    => 'At least one service line item is required.',
            'items.*.description.required' => 'Each line item must have a description.',
            'items.*.amount.required'      => 'Each line item must have an amount.',
        ];
    }
}
