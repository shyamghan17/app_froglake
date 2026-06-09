<?php

namespace Workdo\BulkSMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkSmsGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|max:100',
            'contacts' => 'nullable|array'
        ];
    }
}