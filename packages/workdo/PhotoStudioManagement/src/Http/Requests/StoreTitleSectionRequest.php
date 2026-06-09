<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTitleSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_page_title'       => 'nullable|string|max:255',
            'service_label'            => 'nullable|string|max:255',
            'service_title'            => 'nullable|string|max:255',
            'camera_kit_page_title'    => 'nullable|string|max:255',
            'camera_kit_label'         => 'nullable|string|max:255',
            'camera_kit_title'         => 'nullable|string|max:255',
            'camera_kit_details_label' => 'nullable|string|max:255',
            'camera_kit_details_title' => 'nullable|string|max:255',
            'equipment_label'          => 'nullable|string|max:255',
            'equipment_title'          => 'nullable|string|max:255',
            'booking_page_title'       => 'nullable|string|max:255',
        ];
    }
}
