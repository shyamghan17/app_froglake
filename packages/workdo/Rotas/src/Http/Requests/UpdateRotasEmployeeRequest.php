<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRotasEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        
        return [
            'user_id' => $employee && !$employee->user_id ? 'required|exists:users,id' : 'nullable',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'shift_id' => 'required|exists:shifts,id',
            'date_of_joining' => 'required|date',
            'employment_type' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_relationship' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'bank_name' => 'required|string',
            'account_holder_name' => 'required|string',
            'account_number' => 'required|string',
            'bank_identifier_code' => 'required|string',
            'bank_branch' => 'required|string',
            'tax_payer_id' => 'nullable|string',
            'basic_salary' => 'required|numeric',
            'hours_per_day' => 'required|numeric',
            'days_per_week' => 'required|numeric',
            'rate_per_hour' => 'required|numeric',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_number.required' => __('Employee number is required.'),
            'employee_number.max' => __('Employee number cannot exceed 50 characters.'),
            'first_name.required' => __('First name is required.'),
            'first_name.max' => __('First name cannot exceed 255 characters.'),
            'last_name.required' => __('Last name is required.'),
            'last_name.max' => __('Last name cannot exceed 255 characters.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.max' => __('Email cannot exceed 255 characters.'),
            'phone.max' => __('Phone cannot exceed 20 characters.'),
            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
            'department_id.required' => __('Department is required.'),
            'department_id.exists' => __('Selected department is invalid.'),
            'designation_id.required' => __('Designation is required.'),
            'designation_id.exists' => __('Selected designation is invalid.'),
            'hire_date.required' => __('Hire date is required.'),
            'hire_date.date' => __('Hire date must be a valid date.'),
            'hourly_rate.required' => __('Hourly rate is required.'),
            'hourly_rate.numeric' => __('Hourly rate must be a number.'),
            'hourly_rate.min' => __('Hourly rate cannot be negative.'),
            'hourly_rate.max' => __('Hourly rate cannot exceed 999999.99.'),
            'is_active.boolean' => __('Status must be true or false.'),
        ];
    }
}