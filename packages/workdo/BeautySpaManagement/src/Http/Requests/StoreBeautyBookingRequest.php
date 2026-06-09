<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeautyBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'service' => 'required|exists:beauty_services,id',
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'person' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,other',
            'reference' => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string|max:500'
        ];
    }
}