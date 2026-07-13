<?php

namespace Workdo\SuggestionBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'         => 'required|in:new,under_review,accepted,rejected',
            'admin_response' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => __('Status is required.'),
            'status.in'       => __('Invalid status selected.'),
        ];
    }
}