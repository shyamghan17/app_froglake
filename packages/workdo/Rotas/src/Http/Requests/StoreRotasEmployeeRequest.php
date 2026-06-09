<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|max:50',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'shift_id' => 'required|exists:shifts,id',
            'date_of_joining' => 'required|date',
            'employment_type' => 'required',
            'address_line_1' => 'required|max:255',
            'address_line_2' => 'nullable|max:255',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'country' => 'required|max:100',
            'postal_code' => 'required|max:20',
            'emergency_contact_name' => 'required|max:100',
            'emergency_contact_relationship' => 'required|max:100',
            'emergency_contact_number' => 'required|max:20|regex:/^\+\d{1,3}\d{9,13}$/',
            'bank_name' => 'required|max:100',
            'account_holder_name' => 'required|max:100',
            'account_number' => 'required|max:50',
            'bank_identifier_code' => 'required|max:50',
            'bank_branch' => 'required|max:100',
            'tax_payer_id' => 'nullable|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'hours_per_day' => 'required|numeric|min:0|max:24',
            'days_per_week' => 'required|numeric|min:0|max:7',
            'rate_per_hour' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'documents' => 'required|array|min:1',
            'documents.*.document_type_id' => 'required|exists:employee_document_types,id',
            'documents.*.file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => __('Employee ID is required.'),
            'employee_id.max' => __('Employee ID cannot exceed 50 characters.'),
            'date_of_birth.required' => __('Date of birth is required.'),
            'date_of_birth.date' => __('Date of birth must be a valid date.'),
            'gender.required' => __('Gender is required.'),
            'shift_id.required' => __('Shift is required.'),
            'shift_id.exists' => __('Selected shift is invalid.'),
            'date_of_joining.required' => __('Date of joining is required.'),
            'date_of_joining.date' => __('Date of joining must be a valid date.'),
            'employment_type.required' => __('Employment type is required.'),
            'address_line_1.required' => __('Address line 1 is required.'),
            'address_line_1.max' => __('Address line 1 cannot exceed 255 characters.'),
            'address_line_2.max' => __('Address line 2 cannot exceed 255 characters.'),
            'city.required' => __('City is required.'),
            'city.max' => __('City cannot exceed 100 characters.'),
            'state.required' => __('State is required.'),
            'state.max' => __('State cannot exceed 100 characters.'),
            'country.required' => __('Country is required.'),
            'country.max' => __('Country cannot exceed 100 characters.'),
            'postal_code.required' => __('Postal code is required.'),
            'postal_code.max' => __('Postal code cannot exceed 20 characters.'),
            'emergency_contact_name.required' => __('Emergency contact name is required.'),
            'emergency_contact_name.max' => __('Emergency contact name cannot exceed 100 characters.'),
            'emergency_contact_relationship.required' => __('Emergency contact relationship is required.'),
            'emergency_contact_relationship.max' => __('Emergency contact relationship cannot exceed 100 characters.'),
            'emergency_contact_number.required' => __('Emergency contact number is required.'),
            'emergency_contact_number.max' => __('Emergency contact number cannot exceed 20 characters.'),
            'emergency_contact_number.regex' => __('Emergency contact number must be a valid phone number.'),
            'bank_name.required' => __('Bank name is required.'),
            'bank_name.max' => __('Bank name cannot exceed 100 characters.'),
            'account_holder_name.required' => __('Account holder name is required.'),
            'account_holder_name.max' => __('Account holder name cannot exceed 100 characters.'),
            'account_number.required' => __('Account number is required.'),
            'account_number.max' => __('Account number cannot exceed 50 characters.'),
            'bank_identifier_code.required' => __('Bank identifier code is required.'),
            'bank_identifier_code.max' => __('Bank identifier code cannot exceed 50 characters.'),
            'bank_branch.required' => __('Bank branch is required.'),
            'bank_branch.max' => __('Bank branch cannot exceed 100 characters.'),
            'tax_payer_id.max' => __('Tax payer ID cannot exceed 50 characters.'),
            'basic_salary.required' => __('Basic salary is required.'),
            'basic_salary.numeric' => __('Basic salary must be a number.'),
            'basic_salary.min' => __('Basic salary cannot be negative.'),
            'hours_per_day.required' => __('Hours per day is required.'),
            'hours_per_day.numeric' => __('Hours per day must be a number.'),
            'hours_per_day.min' => __('Hours per day cannot be negative.'),
            'hours_per_day.max' => __('Hours per day cannot exceed 24.'),
            'days_per_week.required' => __('Days per week is required.'),
            'days_per_week.numeric' => __('Days per week must be a number.'),
            'days_per_week.min' => __('Days per week cannot be negative.'),
            'days_per_week.max' => __('Days per week cannot exceed 7.'),
            'rate_per_hour.required' => __('Rate per hour is required.'),
            'rate_per_hour.numeric' => __('Rate per hour must be a number.'),
            'rate_per_hour.min' => __('Rate per hour cannot be negative.'),
            'user_id.required' => __('User is required.'),
            'user_id.exists' => __('Selected user is invalid.'),
            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
            'department_id.required' => __('Department is required.'),
            'department_id.exists' => __('Selected department is invalid.'),
            'designation_id.required' => __('Designation is required.'),
            'designation_id.exists' => __('Selected designation is invalid.'),
            'documents.required' => __('Documents are required.'),
            'documents.array' => __('Documents must be an array.'),
            'documents.min' => __('At least one document is required.'),
            'documents.*.document_type_id.required' => __('Document type is required.'),
            'documents.*.document_type_id.exists' => __('Selected document type is invalid.'),
            'documents.*.file.required' => __('Document file is required.'),
            'documents.*.file.file' => __('Document must be a file.'),
            'documents.*.file.mimes' => __('Document must be a PDF, DOC, DOCX, JPG, JPEG, or PNG file.'),
            'documents.*.file.max' => __('Document size cannot exceed 2MB.'),
        ];
    }
}
