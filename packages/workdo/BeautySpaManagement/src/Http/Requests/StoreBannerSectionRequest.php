<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'heading' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string|max:1000',
        ];
    }
}