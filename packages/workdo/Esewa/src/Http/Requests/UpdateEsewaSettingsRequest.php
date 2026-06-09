<?php

namespace Workdo\Esewa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEsewaSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'settings' => 'required|array',
            'settings.esewa_enabled' => 'required|string|in:on,off',
            'settings.esewa_mode' => 'required_if:settings.esewa_enabled,on|string|in:sandbox,live',
            'settings.esewa_merchant_id' => 'required_if:settings.esewa_enabled,on|string|max:255',
            'settings.esewa_secret_key' => 'required_if:settings.esewa_enabled,on|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'settings.esewa_enabled.required' => __('The esewa enabled field is required.'),
            'settings.esewa_mode.required_if' => __('The esewa mode field is required.'),
            'settings.esewa_merchant_id.required_if' => __('The esewa merchant ID field is required.'),
            'settings.esewa_secret_key.required_if' => __('The esewa secret key field is required.'),
            'settings.esewa_mode.in' => __('The esewa mode must be either sandbox or live.'),
        ];
    }
}