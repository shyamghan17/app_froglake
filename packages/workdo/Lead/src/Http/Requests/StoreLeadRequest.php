<?php

namespace Workdo\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|max:200',            
            'phone' => 'nullable|string|regex:/^\+\d{1,3}\d{9,13}$/',
            'date' => 'nullable|date',
            'website' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:5000',
            'is_live' => 'nullable|boolean',
            'company_pan' => 'nullable|string|max:255',
            'lead_status' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
