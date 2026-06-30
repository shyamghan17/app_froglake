<?php

namespace Workdo\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'email' => 'nullable|email',
            'subject' => 'required|max:200',
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn($query) => $query->where('created_by', creatorId())),
            ],
            'pipeline_id' => 'nullable|integer',
            'stage_id' => 'nullable|integer',
            'sources' => 'nullable|max:100',
            'products' => 'nullable|max:100',
            'notes' => 'nullable',
            'phone' => 'nullable|string|regex:/^\+\d{1,3}\d{9,13}$/',
            'date' => 'nullable|date',
        ];
    }
}
