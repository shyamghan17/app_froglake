<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'about_image' => 'nullable|string',
            'main_title' => 'required|string|max:255',
            'content' => 'required|string',
            'sub_text' => 'required|string|max:255',
            'purpose_title' => 'required|string|max:255',
            'purpose_description' => 'required|string',
            'about_stats' => 'required|array',
            'about_stats.*.title' => 'required|string|max:255',
            'about_stats.*.description' => 'required|string',
            'about_stats.*.icon' => 'nullable|string|max:255',
        ];
    }
}