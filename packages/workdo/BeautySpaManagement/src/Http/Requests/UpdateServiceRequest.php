<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'max_bookable_persons' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'time' => 'required|numeric|min:0|max:24',
            'description' => 'required|string|max:1000',
            'service_image' => 'nullable|string',
            'service_type_id' => 'required|exists:beauty_service_types,id',
            'staff_id' => 'nullable|integer|exists:users,id',
            'included_services' => 'nullable|array',
            'included_services.*' => 'nullable|string|max:255'
        ];
    }
}