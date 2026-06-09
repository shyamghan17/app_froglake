<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                   => 'required|string|max:255',
            'service_category_ids'   => 'required|array|min:1',
            'service_category_ids.*' => 'required|string',
            'description'            => 'required|string',
            'image'                  => 'required|string',
            'price'                  => 'required|numeric|min:0',
            'status'                 => 'required|boolean',
            'camera_kit_ids'         => 'required|array',
            'camera_kit_ids.*'       => 'string',
        ];
    }
}
