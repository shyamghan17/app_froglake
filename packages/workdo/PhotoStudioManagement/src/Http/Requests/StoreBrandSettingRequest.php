<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo'               => 'nullable|string',
            'footer_logo'        => 'nullable|string',
            'favicon'            => 'nullable|string',
            'site_title'         => 'required|string|max:255',
            'footer_text'        => 'required|string|max:255',
            'footer_description' => 'required|string|max:1000',
        ];
    }
}
