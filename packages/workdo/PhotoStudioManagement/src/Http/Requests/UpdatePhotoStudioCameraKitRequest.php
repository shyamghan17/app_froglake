<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioCameraKitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                         => 'required|string|max:255',
            'image'                        => 'required|string',
            'description'                  => 'required|string',
            'tags'                         => 'required|array|min:1',
            'tags.*'                       => 'required|string',
            'specifications'               => 'required|array|min:1',
            'specifications.*.field_name'  => 'required|string|max:255',
            'specifications.*.description' => 'required|string',
            'equipment_type_id'            => 'required|exists:photo_studio_equipment_types,id',
            'status'                        => 'required|in:available,unavailable',
        ];
    }
}
