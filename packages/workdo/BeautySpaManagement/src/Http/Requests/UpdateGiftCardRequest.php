<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_code' => 'required',
            'customer' => 'required',
            'balance' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
            'status' => 'required'
        ];
    }
}