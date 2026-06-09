<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'designation_name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id'
        ];
    }

    public function messages(): array
    {
        return [
            'designation_name.required' => __('Designation name is required.'),
            'designation_name.string' => __('Designation name must be a string.'),
            'designation_name.max' => __('Designation name cannot exceed 255 characters.'),            
            'branch_id.exists' => __('Selected branch does not exist.'),
            'department_id.exists' => __('Selected department does not exist.'),
        ];
    }
}