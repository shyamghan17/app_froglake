<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPhotoStudioTeamMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_member_ids'   => 'nullable|array',
            'team_member_ids.*' => 'integer|exists:photo_studio_team_members,id',
        ];
    }
}
