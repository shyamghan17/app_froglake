<?php

namespace Workdo\BulkSMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkSMSSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('manage-bulk-sms');
    }

    public function rules(): array
    {
        return [
            'bulksms_username' => 'required_if:bulksms_notification_is,on|string',
            'bulksms_password' => 'required_if:bulksms_notification_is,on|string',
            'bulksms_notification_is' => 'nullable|string|in:on,off',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('settings')) {
            $this->merge($this->input('settings'));
        }
    }
}