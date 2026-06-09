<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRotasLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'max_days_per_year' => 'required|integer|min:1',
            'is_paid' => 'boolean',
            'color' => 'required',
            'description' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Name is required.'),
            'name.max' => __('Name cannot exceed 100 characters.'),
            'max_days_per_year.required' => __('Maximum days per year is required.'),
            'max_days_per_year.integer' => __('Maximum days per year must be an integer.'),
            'max_days_per_year.min' => __('Maximum days per year must be at least 1.'),
            'is_paid.boolean' => __('Paid status must be true or false.'),
            'color.required' => __('Color is required.'),
        ];
    }
}