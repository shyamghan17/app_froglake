<?php

namespace Workdo\SignInWithGoogle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoogleSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings.google_signin_enabled' => 'required|string|in:on,off',
            'settings.google_client_id' => 'required_if:settings.google_signin_enabled,on|nullable|string',
            'settings.google_client_secret' => 'required_if:settings.google_signin_enabled,on|nullable|string',
            'settings.google_signin_logo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'settings.google_client_id.required_if' => __('Google Client ID is required when Google Sign-In is enabled.'),
            'settings.google_client_secret.required_if' => __('Google Client Secret is required when Google Sign-In is enabled.'),
            'settings.google_signin_enabled.in' => __('Google Sign-In status must be either on or off.'),
        ];
    }
}