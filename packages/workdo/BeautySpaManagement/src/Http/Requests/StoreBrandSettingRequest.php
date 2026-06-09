<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'logo' => 'nullable|string',
            'favicon' => 'nullable|string',
            'footer_text' => 'required|string|max:255',
            'footer_description' => 'required|string|max:1000',
            'beauty_spa_store_name' => 'required|string|max:255',
        ];
    }
}