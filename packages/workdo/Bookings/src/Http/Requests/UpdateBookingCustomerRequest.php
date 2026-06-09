<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'mobile_number' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'customer' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('The first name is required.'),
            'first_name.regex' => __('The first name may only contain letters and spaces.'),
            'last_name.required' => __('The last name is required.'),
            'last_name.regex' => __('The last name may only contain letters and spaces.'),
            'email.required' => __('The email address is required.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.unique' => __('This email address is already registered.'),
            'mobile_number.regex' => __('Please provide a valid mobile number format.'),
            'mobile_number.max' => __('The mobile number cannot exceed 20 characters.'),
            'description.max' => __('The description cannot exceed 1000 characters.'),
        ];
    }
}