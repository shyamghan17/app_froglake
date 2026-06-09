<?php

namespace Workdo\BulkSMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulksmsGroupSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:bulk_sms_groups,id',
            'sms'      => 'required|string'
        ];
    }
}