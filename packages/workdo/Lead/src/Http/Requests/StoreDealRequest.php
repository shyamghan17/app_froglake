<?php

namespace Workdo\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'price' => 'required|numeric|min:0',            
            'phone' => 'nullable|string|regex:/^\+\d{1,3}\d{9,13}$/',
            'clients'   => 'required|array|min:1',
            'clients.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
                Rule::exists('customers', 'user_id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
        ];
    }
}
