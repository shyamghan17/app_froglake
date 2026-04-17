<?php

namespace Workdo\SignInWithGoogle\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\SignInWithGoogle\Http\Requests\UpdateGoogleSettingsRequest;

class GoogleSettingsController extends Controller
{
    public function update(UpdateGoogleSettingsRequest $request)
    {
        if (Auth::user()->can('edit-google-signin-settings')) {

            $validated = $request->validated();
            $settings = $validated['settings'];

            if (isset($settings['google_signin_logo']) && $settings['google_signin_logo']) {
                $settings['google_signin_logo'] = basename($settings['google_signin_logo']);
            }
            foreach ($settings as $key => $value) {
                setSetting($key, $value, creatorId(), $key == "google_signin_enabled"  || $key === "google_signin_logo");
            }

            return redirect()->back()->with('success', __('Sign-In With Google settings updated successfully.'));
        }
        return back()->with('error', __('Permission denied'));
    }
}