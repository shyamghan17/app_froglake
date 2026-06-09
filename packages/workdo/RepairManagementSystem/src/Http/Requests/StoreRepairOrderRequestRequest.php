<?php

namespace Workdo\RepairManagementSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairOrderRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|max:255',
            'product_quantity' => 'nullable|integer|min:0',
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email',
            'customer_mobile_no' => 'nullable|max:20',
            'date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:date',
            'repair_technician' => 'required|exists:repair_technicians,id',
        ];
    }
}