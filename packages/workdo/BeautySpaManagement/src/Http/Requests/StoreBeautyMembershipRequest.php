<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeautyMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'duration' => 'required|integer|min:1',
            'benefits' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|max:500',
            'included_services_id' => 'required|exists:beauty_services,id'
        ];
    }
}