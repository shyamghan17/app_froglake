<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingSocialLinkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The social media name is required.'),
            'name.string' => __('The social media name must be a string.'),
            'name.max' => __('The social media name may not be greater than 255 characters.'),
            'icon.required' => __('The icon class is required.'),
            'icon.string' => __('The icon class must be a string.'),
            'icon.max' => __('The icon class may not be greater than 255 characters.'),
            'link.required' => __('The social media link is required.'),
            'link.url' => __('The social media link must be a valid URL.'),
            'link.max' => __('The social media link may not be greater than 255 characters.'),
        ];
    }
}