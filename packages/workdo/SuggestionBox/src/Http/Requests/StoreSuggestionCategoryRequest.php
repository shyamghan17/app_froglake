<?php

namespace Workdo\SuggestionBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100',
            'color'         => 'required',
            'display_order' => 'required|integer|min:0',
            'description'   => 'nullable|string|max:500',
            'is_active'     => 'boolean',
        ];
    }
}