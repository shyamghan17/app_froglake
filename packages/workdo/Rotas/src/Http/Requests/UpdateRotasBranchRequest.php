<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRotasBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_name.required' => __('Branch name is required.'),
            'branch_name.string' => __('Branch name must be a string.'),
            'branch_name.max' => __('Branch name cannot exceed 255 characters.'),
        ];
    }
}