<?php

namespace Workdo\EBilling\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\EBilling\Http\Requests\UpdateEBillingSettingsRequest;

class EBillingSettingsController extends Controller
{
    public function update(UpdateEBillingSettingsRequest $request)
    {
        if (!Auth::user()->can('manage-ebilling')) {
            return back()->with('error', __('Permission denied'));
        }

        $settings = $request->validated('settings');

        foreach ($settings as $key => $value) {
            setSetting($key, $value, creatorId(), true);
        }

        return back()->with('success', __('eBilling settings updated successfully.'));
    }
}

