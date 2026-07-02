<?php

namespace Workdo\PettyCashManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('petty_cash_categories', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
            'amount'        => 'required|numeric|min:0',
            'receipt_path'  => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'description'   => 'nullable',
        ];
    }
}
