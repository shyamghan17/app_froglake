<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ];
    }

    public function messages(): array
    {
        return [
            'department_name.required' => __('Department name is required.'),
            'department_name.string' => __('Department name must be a string.'),
            'department_name.max' => __('Department name cannot exceed 255 characters.'),
            'branch_id.exists' => __('Selected branch does not exist.'),
        ];
    }
}