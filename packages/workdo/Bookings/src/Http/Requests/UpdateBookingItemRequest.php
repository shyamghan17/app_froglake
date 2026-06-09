<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'tax_ids' => 'required|array',
            'category_id' => 'required|exists:product_service_categories,id',
            'description' => 'nullable|string',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'duration' => 'required|string',
            'total_slots' => 'required|integer|min:1',
            'type' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('The item name is required.'),
            'sku.required' => __('The SKU is required.'),
            'sku.unique' => __('This SKU already exists.'),
            'tax_ids.required' => __('Please select at least one tax.'),
            'category_id.required' => __('Please select a category.'),
            'category_id.exists' => __('The selected category is invalid.'),
            'sale_price.required' => __('The sale price is required.'),
            'sale_price.numeric' => __('The sale price must be a number.'),
            'sale_price.min' => __('The sale price must be at least 0.'),
            'purchase_price.required' => __('The purchase price is required.'),
            'purchase_price.numeric' => __('The purchase price must be a number.'),
            'purchase_price.min' => __('The purchase price must be at least 0.'),
            'unit.required' => __('Please select a unit.'),
            'duration.required' => __('Please select a duration.'),
            'duration.string' => __('Duration must be a valid time format.'),
            'total_slots.required' => __('Total slots is required.'),
            'total_slots.integer' => __('Total slots must be a number.'),
            'total_slots.min' => __('Total slots must be at least 1.'),
        ];
    }
}