<?php

namespace Workdo\BulkSMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSingleSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_id'    => 'required|integer|exists:bulk_sms_contacts,id',
            'mobile_number' => 'required|string',
            'sms'           => 'required|max:160'
        ];
    }
    public function messages(): array
    {
        return [
            'contact_id.required' => __('The contact field is required.'),
        ];
    }
}
