<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,scheduled,completed,cancelled',
        ];
    }
}
