<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEyewearItemRequest extends FormRequest
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
            'tax_ids' => 'nullable|array',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'sale_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'unit' => 'required',
            'quantity' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'warehouse_id' => 'required',
            'product_type' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'prescription_detail' => 'nullable|string',
            'numbering_status' => 'nullable|in:numbering,non-numbering',
            'customization_details' => 'nullable|string'
        ];
    }
}
