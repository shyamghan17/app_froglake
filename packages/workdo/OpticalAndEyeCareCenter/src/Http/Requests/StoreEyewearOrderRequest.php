<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEyewearOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_date' => 'required|date',
            'patient_id' => 'required|exists:eye_patients,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'payment_method' => 'nullable|string|max:50',
            'extra_charge' => 'nullable|numeric|min:0',
            'prescription_details' => 'nullable|string',
            'special_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product_service_items,id',
            'items.*.item_type' => 'nullable|in:standard,custom',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.taxes' => 'nullable|array',
            'items.*.taxes.*.tax_name' => 'required|string',
            'items.*.taxes.*.tax_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.product_id.required' => __('The product field is required.'),
            'items.*.product_id.exists' => __('The selected product is invalid.'),
            'items.*.quantity.required' => __('The quantity field is required.'),
            'items.*.quantity.min' => __('The quantity must be at least 1.'),
            'items.*.unit_price.required' => __('The unit price field is required.'),
            'items.*.unit_price.min' => __('The unit price must be at least 0.'),
        ];
    }
}
