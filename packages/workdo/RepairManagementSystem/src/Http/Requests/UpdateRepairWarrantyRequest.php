<?php

namespace Workdo\RepairManagementSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairWarrantyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $repairWarrantyId = $this->route('repair_warranty');
        
        return [
            'repair_order_id' => 'required|exists:repair_order_requests,id',
            'part_id' => 'required|exists:repair_parts,id',
            'warranty_number' => 'required|string',
            'warranty_period' => 'required|string',
            'warranty_terms' => 'required|string|max:1000',
            'claim_status' => 'required|in:0,1,2,3'
        ];
    }
}