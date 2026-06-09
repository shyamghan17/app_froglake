<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingExtraServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The extra service name is required.'),
            'name.string' => __('The extra service name must be a string.'),
            'name.max' => __('The extra service name may not be greater than 255 characters.'),
            'status.boolean' => __('The status must be true or false.')
        ];
    }
}