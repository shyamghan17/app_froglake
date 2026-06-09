<?php

namespace Workdo\SMS\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SMSSettingsController extends Controller
{
    public function index()
    {
        $smsProviders = [
            'aws' => 'AWS SNS',
            'twilio' => 'Twilio',
            'clockwork' => 'Clockwork',
            'melipayamak' => 'Melipayamak',
            'kavenegar' => 'Kavenegar',
            'sms_gateway_me' => 'SMS Gateway Me'
        ];

        $smsNotifications = Notification::where('type', 'SMS')->get()->groupBy('module');

        return response()->json([
            'smsProviders' => $smsProviders,
            'smsNotifications' => $smsNotifications
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('edit-sms-settings')) {
            $provider = $request->input('settings.sms_provider');
            
            $rules = [
                'settings.sms_provider' => 'required|string|in:aws,twilio,clockwork,melipayamak,kavenegar,sms_gateway_me',
                'settings.sms_notification_is' => 'required|string|in:on,off',
            ];

            // Add provider-specific validation rules
            switch ($provider) {
                case 'aws':
                    $rules['settings.aws_access_key_id'] = 'required|string';
                    $rules['settings.aws_secret_access_key'] = 'required|string';
                    $rules['settings.aws_default_region'] = 'required|string';
                    $rules['settings.aws_sender_id'] = 'required|string|max:11';
                    $rules['settings.aws_message_type'] = 'required|string|max:50';
                    break;
                case 'twilio':
                    $rules['settings.twilio_account_sid'] = 'required|string';
                    $rules['settings.twilio_auth_token'] = 'required|string';
                    $rules['settings.twilio_from_number'] = 'required|string';
                    break;
                case 'clockwork':
                    $rules['settings.clockwork_api_key'] = 'required|string';
                    $rules['settings.clockwork_from_name'] = 'required|string';
                    break;
                case 'melipayamak':
                    $rules['settings.melipayamak_username'] = 'required|string';
                    $rules['settings.melipayamak_password'] = 'required|string';
                    $rules['settings.melipayamak_from_number'] = 'required|string';
                    break;
                case 'kavenegar':
                    $rules['settings.kavenegar_api_key'] = 'required|string';
                    $rules['settings.kavenegar_sender'] = 'required|string';
                    break;
                case 'sms_gateway_me':
                    $rules['settings.sms_gateway_me_device_id'] = 'required|string';
                    $rules['settings.sms_gateway_me_token'] = 'required|string';
                    break;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', __('Validation failed'));
            }

            $settings = $request->input('settings', []);
            try {
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId(), false);
                }

                return redirect()->back()->with('success', __('SMS settings saved successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Failed to update SMS settings: ') . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}