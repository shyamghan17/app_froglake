<?php

namespace Workdo\Esewa\Http\Controllers;

use App\Http\Controllers\Controller;
use Workdo\Esewa\Http\Requests\UpdateEsewaSettingsRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class EsewaSettingsController extends Controller
{
    public function update(UpdateEsewaSettingsRequest $request)
    {
        if (Auth::user()->can('edit-esewa-settings')) {
            $validated = $request->validated();

            $settings = $validated['settings'];
            try {
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId(), $key == "esewa_enabled");
                }

                return redirect()->back()->with('success', __('Esewa settings saved successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Failed to update Esewa settings: ') . $e->getMessage());
            }
        }
        return back()->with('error', __('Permission denied'));
    }

    public function checkout(Request $request, $data)
    {
        try {
            if (!$data) {
                return back()->with('error', __('Something went wrong.'));
            }

            return Inertia::render('Esewa/EsewaCheckoutForm', [
                'checkoutData' => decrypt($data)
            ]);

           
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
