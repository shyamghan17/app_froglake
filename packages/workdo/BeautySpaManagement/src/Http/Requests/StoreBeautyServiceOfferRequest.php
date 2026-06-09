<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeautyServiceOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:100',
            'name' => 'required|max:100',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount' => 'required|numeric|min:0',
            'offer_price' => 'required|numeric|min:0',
            'description' => 'nullable|max:500',
            'beauty_service_id' => 'required|exists:beauty_services,id'
        ];
    }
}