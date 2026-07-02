<?php

namespace Workdo\PettyCashManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePettyCashRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'         => 'required|date|unique:petty_cashes,date,NULL,id,created_by,' . creatorId(),
            'added_amount' => 'required|numeric|min:0',
            'remarks'      => 'nullable|string|max:1000',
            'bank_account_id' => [
                'nullable',
                'integer',
                Rule::exists('bank_accounts', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => __('Date is required.'),
            'date.date' => __('Please enter a valid date.'),
            'date.unique' => __('Petty cash fund already exists for this date.'),
            'added_amount.required' => __('Added amount is required.'),
            'added_amount.numeric' => __('Added amount must be a number.'),
            'added_amount.min' => __('Added amount cannot be negative.'),
        ];
    }
}
