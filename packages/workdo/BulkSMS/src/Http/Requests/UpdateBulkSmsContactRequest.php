<?php

namespace Workdo\BulkSMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkSmsContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|max:100',
            'email'     => 'required|email',
            'mobile_no' => 'required|string|max:20',
            'city'      => 'required|max:50',
            'state'     => 'required|max:50',
            'zip_code'  => 'required|max:10'
        ];
    }
}