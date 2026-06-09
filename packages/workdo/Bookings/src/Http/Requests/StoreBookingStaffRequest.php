<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|exists:users,id',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:product_service_items,id',
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => __('Please select a staff member.'),
            'staff_id.exists' => __('The selected staff member is invalid.'),
            'item_ids.required' => __('Please select at least one booking item.'),
            'item_ids.array' => __('Invalid booking items format.'),
            'item_ids.min' => __('Please select at least one booking item.'),
            'item_ids.*.exists' => __('One or more selected booking items are invalid.'),
        ];
    }
}