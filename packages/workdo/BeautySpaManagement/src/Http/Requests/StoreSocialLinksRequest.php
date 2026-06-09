<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialLinksRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'social_links' => 'required|array',
            'social_links.*.url' => 'required|url|max:500',
            'social_links.*.icon' => 'required|string|max:255',
        ];
    }
}