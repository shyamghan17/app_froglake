<?php

namespace Workdo\SMS\Services;

use App\Models\Notification;
use App\Models\NotificationTemplateLang;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SendSMS
{
    public static function SendMsgs(array $uArr, string $action, string $phoneNumber = null, $id = null)
    {
        // Check if SMS module is active
        if (Module_is_active('SMS', $id)) {

            if (!empty($id)) {
                $usr = User::find($id);
            } else {
                $usr = Auth::user();
            }

            $company_settings = getCompanyAllSetting($id);

            $sms_notification_is = isset($company_settings['sms_notification_is']) ? $company_settings['sms_notification_is'] : 'off';

            $template = Notification::where('action', $action)->where('type', 'SMS')->first();
            if (!$template) {
                return false;
            }

            $content = NotificationTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();
            if ($content == null) {
                $content = NotificationTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', 'en')->first();
            }

            if (!$content) {
                return false;
            }

            $msg = self::replaceVariable($content->content, $uArr, $id);

            $sms_provider = isset($company_settings['sms_provider']) ? $company_settings['sms_provider'] : null;

            if (($sms_notification_is == 'on') && (!empty($sms_provider)) && (!empty($phoneNumber))) {

                return self::sendSMS($phoneNumber, $msg, $id);
            }
        }
        return false;
    }

    public static function sendSMS(string $to, string $message, $userId = null)
    {
        if (!Module_is_active('SMS',  $userId)) {
            return false;
        }

        if (!empty($userId)) {
            $user = User::find($userId);
        } else {
            $user = Auth::user();
        }

        $company_settings = getCompanyAllSetting($userId);
        $sms_notification_is = $company_settings['sms_notification_is'] ?? 'off';
        $sms_provider = $company_settings['sms_provider'] ?? null;

        if ($sms_notification_is !== 'on' || empty($sms_provider)) {
            return false;
        }


        try {
            switch ($sms_provider) {
                case 'aws':
                    return self::sendAWSSMS($to, $message, $company_settings);
                case 'twilio':
                    return self::sendTwilioSMS($to, $message, $company_settings);
                case 'clockwork':
                    return self::sendClockworkSMS($to, $message, $company_settings);
                case 'melipayamak':
                    return self::sendMelipayamakSMS($to, $message, $company_settings);
                case 'kavenegar':
                    return self::sendKavenegarSMS($to, $message, $company_settings);
                case 'sms_gateway_me':
                    return self::sendSMSGatewayMeSMS($to, $message, $company_settings);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function sendAWSSMS($to, $message, $settings)
    {
        $accessKeyId = $settings['aws_access_key_id'] ?? null;
        $secretAccessKey = $settings['aws_secret_access_key'] ?? null;
        $region = $settings['aws_default_region'] ?? null;
        $senderId = $settings['aws_sender_id'] ?? null;
        $messageType = $settings['aws_message_type'] ?? 'Transactional';

        if (!$accessKeyId || !$secretAccessKey || !$region) {
            return false;
        }

        $url = "https://sns.{$region}.amazonaws.com/";
        $params = [
            'Action' => 'Publish',
            'Message' => $message,
            'PhoneNumber' => $to,
            'Version' => '2010-03-31'
        ];

        if ($senderId) {
            $params['MessageAttributes.entry.1.Name'] = 'AWS.SNS.SMS.SenderID';
            $params['MessageAttributes.entry.1.Value.StringValue'] = $senderId;
            $params['MessageAttributes.entry.1.Value.DataType'] = 'String';
        }

        if ($messageType) {
            $params['MessageAttributes.entry.2.Name'] = 'AWS.SNS.SMS.SMSType';
            $params['MessageAttributes.entry.2.Value.StringValue'] = $messageType;
            $params['MessageAttributes.entry.2.Value.DataType'] = 'String';
        }

        $queryString = http_build_query($params);
        $headers = self::getAWSHeaders($queryString, $accessKeyId, $secretAccessKey, $region);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private static function getAWSHeaders($queryString, $accessKeyId, $secretAccessKey, $region)
    {
        $timestamp = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        $service = 'sns';
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = "{$date}/{$region}/{$service}/aws4_request";

        $canonicalHeaders = "content-type:application/x-www-form-urlencoded\nhost:sns.{$region}.amazonaws.com\nx-amz-date:{$timestamp}\n";
        $signedHeaders = 'content-type;host;x-amz-date';
        $payloadHash = hash('sha256', $queryString);

        $canonicalRequest = "POST\n/\n\n{$canonicalHeaders}\n{$signedHeaders}\n{$payloadHash}";
        $stringToSign = "{$algorithm}\n{$timestamp}\n{$credentialScope}\n" . hash('sha256', $canonicalRequest);

        $signingKey = self::getSigningKey($secretAccessKey, $date, $region, $service);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        $authorization = "{$algorithm} Credential={$accessKeyId}/{$credentialScope}, SignedHeaders={$signedHeaders}, Signature={$signature}";

        return [
            'Content-Type: application/x-www-form-urlencoded',
            "Host: sns.{$region}.amazonaws.com",
            "X-Amz-Date: {$timestamp}",
            "Authorization: {$authorization}"
        ];
    }

    private static function getSigningKey($key, $date, $region, $service)
    {
        $kDate = hash_hmac('sha256', $date, 'AWS4' . $key, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        return hash_hmac('sha256', 'aws4_request', $kService, true);
    }

    private static function sendTwilioSMS($to, $message, $settings)
    {
        $accountSid = $settings['twilio_account_sid'] ?? null;
        $authToken = $settings['twilio_auth_token'] ?? null;
        $fromNumber = $settings['twilio_from_number'] ?? null;

        if (!$accountSid || !$authToken || !$fromNumber) {
            return false;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $data = [
            'From' => $fromNumber,
            'To' => $to,
            'Body' => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, $accountSid . ':' . $authToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    private static function sendClockworkSMS($to, $message, $settings)
    {
        $apiKey = $settings['clockwork_api_key'] ?? null;
        $fromName = $settings['clockwork_from_name'] ?? null;

        if (!$apiKey || !$fromName) {
            return false;
        }

        $url = 'https://api.clockworksms.com/http/send.aspx';

        $data = [
            'key' => $apiKey,
            'to' => $to,
            'content' => $message,
            'from' => $fromName
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return strpos($result, 'ID:') === 0;
    }

    private static function sendMelipayamakSMS($to, $message, $settings)
    {
        $username = $settings['melipayamak_username'] ?? null;
        $password = $settings['melipayamak_password'] ?? null;
        $fromNumber = $settings['melipayamak_from_number'] ?? null;

        if (!$username || !$password || !$fromNumber) {
            return false;
        }

        $url = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';

        $data = [
            'username' => $username,
            'password' => $password,
            'to' => $to,
            'from' => $fromNumber,
            'text' => $message,
            'isflash' => false
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        return isset($response['RetStatus']) && $response['RetStatus'] == 1;
    }

    private static function sendKavenegarSMS($to, $message, $settings)
    {
        $apiKey = $settings['kavenegar_api_key'] ?? null;
        $sender = $settings['kavenegar_sender'] ?? null;

        if (!$apiKey || !$sender) {
            return false;
        }

        $url = "https://api.kavenegar.com/v1/{$apiKey}/sms/send.json";

        $data = [
            'receptor' => $to,
            'sender' => $sender,
            'message' => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        return isset($response['return']['status']) && $response['return']['status'] == 200;
    }

    private static function sendSMSGatewayMeSMS($to, $message, $settings)
    {
        $deviceId = $settings['sms_gateway_me_device_id'] ?? null;
        $token = $settings['sms_gateway_me_token'] ?? null;

        if (!$deviceId || !$token) {
            return false;
        }

        $url = 'https://smsgateway.me/api/v4/message/send';

        $data = [
            'phone_number' => $to,
            'message' => $message,
            'device_id' => $deviceId
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $token
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        return isset($response['success']) && $response['success'] === true;
    }

    public static function replaceVariable($content, $obj, $id = null)
    {
        $arrVariable = [
            '{user_name}',
            '{company_name}',
            '{lead_name}',
            '{old_stage}',
            '{new_stage}',
            '{deal_name}',
            '{name}',
            '{purchase_id}',
            '{project_name}',
            '{task_name}',
            '{bug_name}',
            '{old_status}',
            '{new_status}',
            '{appointment_name}',
            '{date}',
            '{time}',
            '{status}',
            '{component_name}',
            '{part_name}',
            '{location_name}',
            '{wo_name}',
            '{pos_number}',
            '{supplier_name}',
            '{contract_number}',
            '{job_name}',
            '{application}',
            '{quotation_id}',
            '{sales_order_id}',
            '{sales_invoice_id}',
            '{meeting_name}',
            '{spreadsheet_name}',
            '{assets}',
            '{asset}',
            '{location}',
            '{module_name}',
            '{submodule_name}',
            '{team_name}',
            '{type}',
            '{status}',
            '{form_name}',
            '{employee_name}',
            '{branch_name}',
            '{start_date}',
            '{end_date}',
            '{article_type}',
            '{book_name}',
            '{challenge}',
            '{position}',
            '{trainer_name}',
            '{indicator_name}',
            '{goal_title}',
            '{reviewer_name}',
            '{review_cycle_name}',
            '{asset_name}',
            '{maintenance_type}',
            '{employee_name}',
            '{location_name}',
        ];
        $arrValue = [
            'user_name' => '-',
            'company_name' => '-',
            'lead_name' => '-',
            'old_stage' => '-',
            'new_stage' => '-',
            'deal_name' => '-',
            'name' => '-',
            'purchase_id' => '-',
            'project_name' => '-',
            'task_name' => '-',
            'bug_name' => '-',
            'old_status' => '-',
            'new_status' => '-',
            'appointment_name' => '-',
            'date' => '-',
            'time' => '-',
            'status' => '-',
            'component_name' => '-',
            'part_name' => '-',
            'location_name' => '-',
            'wo_name' => '-',
            'pos_number' => '-',
            'supplier_name' => '-',
            'contract_number' => '-',
            'job_name' => '-',
            'application' => '-',
            'quotation_id' => '-',
            'sales_order_id' => '-',
            'sales_invoice_id' => '-',
            'meeting_name' => '-',
            'spreadsheet_name' => '-',
            'assets' => '-',
            'asset' => '-',
            'location' => '-',
            'module_name' => '-',
            'submodule_name' => '-',
            'team_name' => '-',
            'type' => '-',
            'status' => '-',
            'form_name' => '-',
            'employee_name' => '-',
            'branch_name' => '-',
            'start_date' => '-',
            'end_date' => '-',
            'article_type' => '-',
            'book_name' => '-',
            'challenge' => '-',
            'position' => '-',
            'trainer_name' => '-',
            'indicator_name' => '-',
            'goal_title' => '-',
            'reviewer_name' => '-',
            'review_cycle_name' => '-',
            'asset_name' => '-',
            'maintenance_type' => '-',
            'employee_name' => '-',
            'location_name' => '-',
        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        if (!empty($id)) {
            $user = User::find($id);
        } else {
            $user = Auth::user();
        }

        $arrValue['company_name'] = $user->name;

        $replacements = [];
        foreach ($arrVariable as $variable) {
            $key = trim($variable, '{}');
            $replacements[$variable] = $arrValue[$key] ?? '-';
        }

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
