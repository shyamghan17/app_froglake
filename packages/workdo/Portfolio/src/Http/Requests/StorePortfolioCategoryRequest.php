<?php

namespace Workdo\Portfolio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortfolioCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active'   => 'boolean'
        ];
    }
}
