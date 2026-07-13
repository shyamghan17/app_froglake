<?php

namespace Workdo\SuggestionBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|max:255',
            'category_id'  => 'required|exists:suggestion_categories,id',
            'description'  => 'required|max:500',
            'is_anonymous' => 'nullable|boolean',
        ];
    }

    public function messages(){
        return [
            'category_id.required' => __('The category field is required.'),
        ];
    }
}