<?php

namespace Workdo\BulkSMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Workdo\BulkSMS\Http\Requests\StoreBulkSMSSettingsRequest;
use Illuminate\Support\Facades\Auth;

class BulkSMSSettingsController extends Controller
{
    public function index()
    {
        $bulkSMSNotifications = [];
        return response()->json(['bulkSMSNotifications' => $bulkSMSNotifications]);
    }

    public function store(StoreBulkSMSSettingsRequest $request)
    {
        if (Auth::user()->can('edit-bulk-sms')) {
            try {
                $validated = $request->validated();
                $settings = collect($validated)->only(['bulksms_username', 'bulksms_password', 'bulksms_notification_is']);
                
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId(),false);
                }

                return redirect()->back()->with('success', __('Bulk SMS settings save successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Failed to update Bulk SMS settings: ') . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}