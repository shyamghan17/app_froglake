<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'                          => 'required|string|max:255',
            'title'                          => 'required|string|max:255',
            'media_items'                    => 'required|array|min:1',
            'media_items.*.media_heading'    => 'required|string|max:255',
            'media_items.*.media_image'      => 'required|string',
            'media_items.*.date'             => 'required|string',
            'media_items.*.content_type'     => 'required|string|max:255',
            'media_items.*.content'          => 'required|string',
        ];
    }
}
