<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFooterSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location'                   => 'required|string|max:255',
            'phone_no'                   => 'required|string|max:255',
            'email'                      => 'required|email|max:255',
            'location_icon'              => 'required|string|max:100',
            'phone_icon'                 => 'required|string|max:100',
            'email_icon'                 => 'required|string|max:100',
            'newsletter_label'           => 'required|string|max:255',
            'newsletter_title'           => 'required|string|max:255',
            'social_links'               => 'required|array|min:1',
            'social_links.*.social_link' => 'required|url',
            'social_links.*.social_icon' => 'required|string|max:100',
        ];
    }
}
