<?php

namespace Workdo\FindGoogleLeads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFindGoogleLeadsItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'keyword' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }
}