<?php

namespace Workdo\RepairManagementSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairProductPartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'repair_id' => 'required|integer|exists:repair_order_requests,id',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|integer|min:1|exists:product_service_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'repair_id.required' => __('Repair order is required.'),
            'repair_id.exists' => __('Selected repair order does not exist.'),
            'items.required' => __('At least one item is required.'),
            'items.min' => __('At least one item is required.'),
            'items.*.item.required' => __('Product is required for each item.'),
            'items.*.item.min' => __('Product is required for each item.'),
            'items.*.item.exists' => __('Selected product does not exist.'),
            'items.*.quantity.required' => __('Quantity is required for each item.'),
            'items.*.quantity.min' => __('Quantity must be at least 1.'),
            'items.*.price.required' => __('Price is required for each item.'),
            'items.*.price.min' => __('Price cannot be negative.'),
        ];
    }
}