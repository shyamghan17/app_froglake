<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeautyLoyaltyProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|max:100',
            'points_earned' => 'required|integer|min:0',
            'points_redeemed' => 'nullable|integer|min:0',
            'last_updated' => 'required|date'
        ];
    }
}