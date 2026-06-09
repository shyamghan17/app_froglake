<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingPackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:booking_packages,name,' . $this->route('package')->id . ',id,created_by,' . creatorId(),
            'item_id' => 'required|exists:product_service_items,id',
            'services' => 'nullable|array',
            'services.*' => 'integer|exists:booking_extra_services,id',
            'delivery_time' => 'required|string',
            'delivery_period' => 'required|in:minutes,hours',
            'price' => 'required|numeric|min:0|max:999999.99',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The package name is required.'),
            'name.unique' => __('A package with this name already exists.'),
            'item_id.required' => __('Please select an item for this package.'),
            'item_id.exists' => __('The selected item is invalid.'),
            'delivery_time.required' => __('The delivery time is required.'),
            'delivery_period.required' => __('Please select a delivery period.'),
            'delivery_period.in' => __('Please select a valid delivery period.'),
            'price.required' => __('The package price is required.'),
            'price.numeric' => __('The price must be a valid number.'),
            'price.min' => __('The price cannot be negative.'),
            'price.max' => __('The price cannot exceed 999,999.99.'),
            'services.array' => __('Services must be a valid selection.'),
            'services.*.integer' => __('Each service must be a valid selection.'),
            'services.*.exists' => __('One or more selected services are invalid.'),
        ];
    }
}