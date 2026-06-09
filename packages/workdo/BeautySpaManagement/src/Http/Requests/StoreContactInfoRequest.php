<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'header_title' => 'required|string|max:255',
            'header_description' => 'required|string',
            'location' => 'required|string|max:500',
            'phone_number' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'location_icon' => 'nullable|string|max:255',
            'phone_icon' => 'nullable|string|max:255',
            'email_icon' => 'nullable|string|max:255',
            'map_title' => 'required|string|max:255',
            'map_subtext' => 'required|string',
            'map_iframe' => 'required|string|regex:/<iframe[^>]*>.*<\/iframe>/i',
            'follow_us_description' => 'required|string',
            'cta_title' => 'required|string|max:255',
            'cta_description' => 'required|string',
        ];
    }
}