<?php

namespace Workdo\RepairManagementSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepairTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_no' => 'required|string|max:20',
        ];
    }
}