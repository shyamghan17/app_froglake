<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwardSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'award_page_title'             => 'required|string|max:255',
            'label'                        => 'required|string|max:255',
            'title'                        => 'required|string|max:255',
            'awards'                       => 'required|array|min:1',
            'awards.*.award_title'         => 'required|string|max:255',
            'awards.*.award_name'          => 'required|string|max:255',
            'awards.*.award_icon'          => 'required|string|max:100',
            'awards.*.description'         => 'required|string',
            'awards.*.achievement_name'    => 'required|string|max:255',
            'awards.*.achievement_icon'    => 'required|string|max:100',
        ];
    }
}
