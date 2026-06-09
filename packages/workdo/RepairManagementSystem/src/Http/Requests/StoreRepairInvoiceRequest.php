<?php

namespace Workdo\RepairManagementSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|integer',
            'repair_id' => 'nullable|exists:repair_order_requests,id',
            'repair_charge' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required'
        ];
    }
}