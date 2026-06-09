<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDashboardWelcomeCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'copy_link_card_title'       => 'required|string|max:255',
            'copy_link_card_description' => 'required|string|max:1000',
            'copy_link_button_text'      => 'required|string|max:100',
            'copy_link_button_icon'      => 'required|string|max:50',
        ];
    }
}
