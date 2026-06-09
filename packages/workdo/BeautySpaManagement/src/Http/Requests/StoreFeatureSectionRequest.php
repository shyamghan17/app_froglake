<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'why_choose_us_title' => 'nullable|string|max:255',
            'why_choose_us_description' => 'nullable|string',
            'features' => 'required|array',
            'features.*.title' => 'required|string|max:255',
            'features.*.icon' => 'nullable|string|max:255',
            'features.*.description' => 'required|string',
        ];
    }
}