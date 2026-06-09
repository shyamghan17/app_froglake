<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'         => 'required|exists:users,id',
            'designation'     => 'required|string|max:255',
            'experience_year' => 'required|integer|min:0|max:100',
            'skills'          => 'required|string|max:255',
            'rate_per_hour'   => 'required|numeric|min:0|max:999999.99',
            'is_active'       => 'boolean',
            'bio'             => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('The user field is required.'),
        ];
    }
}
