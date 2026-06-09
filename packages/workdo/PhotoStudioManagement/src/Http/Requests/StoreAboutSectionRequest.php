<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                => 'required|string|max:255',
            'sub_title'            => 'required|string|max:255',
            'content'              => 'required|string',
            'description'          => 'required|string',
            'about_us_image'       => 'required|string',
            'tips'                 => 'required|array|min:1',
            'tips.*.description'   => 'required|string|max:500',
        ];
    }
}
