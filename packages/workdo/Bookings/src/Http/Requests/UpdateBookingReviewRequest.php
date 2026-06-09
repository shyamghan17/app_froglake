<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBookingReviewRequest extends FormRequest
{
    public function authorize()
    {        
        return true;
    }

    public function rules()
    {
        return [
            'item_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The reviewer name is required.'),
            'email.required' => __('The email address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'comment.required' => __('The review comment is required.'),
            'rating.required' => __('The rating is required.'),
            'rating.min' => __('The rating must be at least 1.'),
            'rating.max' => __('The rating may not be greater than 5.')
        ];
    }
}