<?php

namespace Workdo\GoogleCaptcha\Http\Controllers;

use Illuminate\Routing\Controller;
use Workdo\GoogleCaptcha\Http\Requests\UpdateGoogleCaptchaSettingsRequest;

class GoogleCaptchaSettingsController extends Controller
{
    public function update(UpdateGoogleCaptchaSettingsRequest $request)
    {
        try {
            $validated = $request->validated();
            $settings = $validated['settings'];

            foreach ($settings as $key => $value) {
                setSetting($key, $value);
            }

            return redirect()->back()->with('success', __('Google reCAPTCHA settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Google reCAPTCHA settings.'));
        }
    }
}
