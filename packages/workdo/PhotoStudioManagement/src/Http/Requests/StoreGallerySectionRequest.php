<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGallerySectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery_page_title'     => 'required|string|max:255',
            'gallery_label'          => 'required|string|max:255',
            'gallery_title'          => 'required|string|max:255',
            'gallery_category_label' => 'required|string|max:255',
            'gallery_category_title' => 'required|string|max:255',
            'images'                 => 'required|array|min:1',
            'images.*.image'           => 'required|string',
            'images.*.gallery_type_id' => 'required|string',
        ];
    }
}
