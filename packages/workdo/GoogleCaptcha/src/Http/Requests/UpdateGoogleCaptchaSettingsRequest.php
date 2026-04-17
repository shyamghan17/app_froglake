<?php

namespace Workdo\GoogleCaptcha\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleCaptchaSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings.recaptcha_enabled' => 'required|in:on,off',
            'settings.recaptcha_version' => 'required|in:v2,v3',
            'settings.recaptcha_site_key' => 'required_if:settings.recaptcha_enabled,on|nullable|string|max:255',
            'settings.recaptcha_secret_key' => 'required_if:settings.recaptcha_enabled,on|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'settings.recaptcha_enabled.required' => __('reCAPTCHA status is required.'),
            'settings.recaptcha_enabled.in' => __('reCAPTCHA status must be either on or off.'),
            'settings.recaptcha_version.required' => __('reCAPTCHA version is required.'),
            'settings.recaptcha_version.in' => __('reCAPTCHA version must be either v2 or v3.'),
            'settings.recaptcha_site_key.required_if' => __('Site key is required when reCAPTCHA is enabled.'),
            'settings.recaptcha_site_key.max' => __('Site key cannot exceed 255 characters.'),
            'settings.recaptcha_secret_key.required_if' => __('Secret key is required when reCAPTCHA is enabled.'),
            'settings.recaptcha_secret_key.max' => __('Secret key cannot exceed 255 characters.'),
        ];
    }
}