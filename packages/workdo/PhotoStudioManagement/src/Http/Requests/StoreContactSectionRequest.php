<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_page_title' => 'required|string|max:255',
            'location_title'     => 'required|string|max:255',
            'contact_title'      => 'required|string|max:255',
            'email_title'        => 'required|string|max:255',
            'visit_address'      => 'required|string',
            'call_details'       => 'required|string|max:255',
            'support_email'      => 'required|email|max:255',
            'location_icon'      => 'required|string|max:100',
            'contact_icon'       => 'required|string|max:100',
            'email_icon'         => 'required|string|max:100',
            'google_map_iframe'  => 'required|string',
        ];
    }
}
