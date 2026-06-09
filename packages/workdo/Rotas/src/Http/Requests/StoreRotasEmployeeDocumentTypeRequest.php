<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasEmployeeDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_name' => 'required|max:100',
            'description' => 'nullable|max:500',
            'is_required' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'document_name.required' => __('Document name is required.'),
            'document_name.max' => __('Document name cannot exceed 100 characters.'),
            'description.max' => __('Description cannot exceed 500 characters.'),
            'is_required.boolean' => __('Required status must be true or false.'),
        ];
    }
}