<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'banners'             => 'array|min:1',
            'banners.*.title'       => 'required|string|max:255',
            'banners.*.sub_title'   => 'required|string|max:255',
            'banners.*.image'       => 'required|string',
            'banners.*.description' => 'required|string|max:1000',
        ];
    }
}
