<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'services_title' => 'nullable|string|max:255',
            'services_description' => 'nullable|string',
            'offers_title' => 'nullable|string|max:255',
            'offers_description' => 'nullable|string',
        ];
    }
}