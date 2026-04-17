<?php

namespace Workdo\MailBox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\MailBox\Models\MailBoxCredential;

use Workdo\MailBox\Events\CreateMailBoxCredential;
use Workdo\MailBox\Events\UpdateMailBoxCredential;


class MailBoxCredentialController extends Controller
{
    public function configuration()
    {
        if (Auth::user()->can('manage-mailbox-settings')) {
            $credentials = MailBoxCredential::where('created_by', creatorId())->get();
            $activeCredential = $credentials->where('is_active', true)->first();

            $quickSetupProviders = [
                [
                    'id' => 'gmail',
                    'name' => 'Gmail',
                    'icon' => '📧',
                    'domains' => ['@gmail.com'],
                    'description' => 'Quick setup for Gmail accounts with App Password'
                ],
                [
                    'id' => 'outlook',
                    'name' => 'Outlook',
                    'icon' => '📨',
                    'domains' => ['@outlook.com', '@hotmail.com', '@live.com'],
                    'description' => 'Quick setup for Microsoft Outlook accounts'
                ],
                [
                    'id' => 'yahoo',
                    'name' => 'Yahoo',
                    'icon' => '📩',
                    'domains' => ['@yahoo.com', '@yahoo.co.uk', '@yahoo.in'],
                    'description' => 'Quick setup for Yahoo Mail accounts'
                ],
                [
                    'id' => 'icloud',
                    'name' => 'iCloud',
                    'icon' => '☁️',
                    'domains' => ['@icloud.com', '@me.com', '@mac.com'],
                    'description' => 'Quick setup for Apple iCloud Mail accounts'
                ]
            ];

            return Inertia::render('MailBox/Configuration', [
                'credential' => $activeCredential,
                'credentials' => $credentials,
                'quickSetupProviders' => $quickSetupProviders,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create-mailbox-settings')) {

            try {
                $validated = $request->validate([
                    'mail_username' => 'required|email',
                    'mail_password' => 'required|string',
                    'mail_host' => 'required|string',
                    'incoming_port' => 'required|integer',
                    'outgoing_port' => 'required|integer',
                    'mail_encryption' => 'required|in:ssl,tls',
                    'mail_from_address' => 'required|email',
                    'mail_from_name' => 'nullable|string',
                    'mail_driver' => 'nullable|string|max:50|in:smtp,sendmail,mailgun,ses,postmark,log'
                ]);

                // Test connection before saving
                $tempCredential = new MailBoxCredential([
                    'email' => $validated['mail_username'],
                    'password' => $validated['mail_password'],
                    'imap_host' => $validated['mail_host'],
                    'imap_port' => $validated['incoming_port'],
                    'imap_encryption' => $validated['mail_encryption'],
                    'smtp_host' => $validated['mail_host'],
                    'smtp_port' => $validated['outgoing_port'],
                    'smtp_encryption' => $validated['mail_encryption']
                ]);

                if (!$this->testConnectionCredential($tempCredential)) {
                    return back()->with('error', __('Connection failed. Please check your credentials and settings before saving.'));
                }

                // Deactivate all existing credentials for this user
                MailBoxCredential::where('created_by', creatorId())->update(['is_active' => false]);

                // Create new credential as active
                $credential = MailBoxCredential::create([
                    'email' => $validated['mail_username'],
                    'password' => $validated['mail_password'],
                    'imap_host' => $validated['mail_host'],
                    'imap_port' => $validated['incoming_port'],
                    'imap_encryption' => $validated['mail_encryption'],
                    'smtp_host' => $validated['mail_host'],
                    'smtp_port' => $validated['outgoing_port'],
                    'smtp_encryption' => $validated['mail_encryption'],
                    'from_name' => $validated['mail_from_name'],
                    'mail_driver' => $validated['mail_driver'] ?? 'smtp',
                    'is_active' => true,
                    'created_by' => creatorId()
                ]);

                // Dispatch event for packages to handle their fields
                if ($credential->wasRecentlyCreated) {
                    CreateMailBoxCredential::dispatch($request, $credential);
                } else {
                    UpdateMailBoxCredential::dispatch($request, $credential);
                }

                return redirect()->route('mailbox.inbox')
                    ->with('success', __('Email configuration saved successfully.'));
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'mail_driver')) {
                    return back()->with('error', __('Invalid mail driver. Use: smtp, sendmail, mailgun, ses, postmark, or log'));
                }
                return back()->with('error', __('Configuration Error: :error', ['error' => $e->getMessage()]));
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function testConnection(Request $request)
    {
        if (Auth::user()->can('test-mailbox-connection')) {
            $email = $request->input('mail_username');
            $password = $request->input('mail_password');
            $host = $request->input('mail_host');
            $port = $request->input('incoming_port');
            $encryption = $request->input('mail_encryption');

            if (!$email || !$password || !$host || !$port) {
                return response()->json(['success' => false, 'message' => 'Missing required fields']);
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['success' => false, 'message' => 'Invalid email format']);
            }

            $domain = substr(strrchr($email, '@'), 1);

            // Test connection using socket
            $success = $this->testSocketConnection($host, $port, $encryption, $email, $password);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful! IMAP settings verified.'
                ]);
            }

            // If connection failed, provide specific guidance
            $message = 'Connection failed. ';

            if (strpos($domain, 'gmail.') !== false) {
                $message .= 'For Gmail: Enable 2FA and use App Password instead of regular password. Go to Google Account Settings > Security > App passwords.';
            } elseif (strpos($domain, 'outlook.') !== false || strpos($domain, 'hotmail.') !== false || strpos($domain, 'live.') !== false) {
                $message .= 'For Outlook: Enable IMAP in Outlook settings. If 2FA is enabled, use App Password.';
            } elseif (strpos($domain, 'yahoo.') !== false) {
                $message .= 'For Yahoo: Enable IMAP access and generate App Password. Use App Password instead of regular password.';
            } elseif (strpos($domain, 'icloud.') !== false || strpos($domain, 'me.com') !== false || strpos($domain, 'mac.com') !== false) {
                $message .= 'For iCloud: Enable 2FA and generate App-specific password. Use App-specific password instead of regular password.';
            } else {
                $message .= 'Please verify: 1) IMAP is enabled for your email account, 2) Server settings are correct, 3) Credentials are valid.';
            }

            return response()->json(['success' => false, 'message' => $message]);
        } else {
            return response()->json(['success' => false, 'message' => 'Permission denied']);
        }
    }

    private function testConnectionCredential($credential)
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $host = $credential->imap_host;
            $port = $credential->imap_port;
            $encryption = $credential->imap_encryption;
            $email = $credential->email;
            $password = $credential->password;

            if (strtolower($encryption) === 'ssl') {
                $socket = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            } else {
                $socket = stream_socket_client("tcp://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            }

            if (!$socket) {
                return false;
            }

            // Read greeting
            $response = fgets($socket);
            if (!$response || (strpos($response, '* OK') === false && strpos($response, '* PREAUTH') === false)) {
                fclose($socket);
                return false;
            }

            // Send login command
            fwrite($socket, "A001 LOGIN \"$email\" \"$password\"\r\n");

            // Read response
            $response = '';
            $attempts = 0;
            while ($attempts < 5) {
                $line = fgets($socket);
                if ($line === false) break;
                $response .= $line;
                if (strpos($line, 'A001') === 0) break;
                $attempts++;
            }

            $success = strpos($response, 'A001 OK') !== false;

            // Logout gracefully
            fwrite($socket, "A002 LOGOUT\r\n");
            fgets($socket);

            fclose($socket);
            return $success;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testSocketConnection($host, $port, $encryption, $email, $password)
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            if (strtolower($encryption) === 'ssl') {
                $socket = stream_socket_client("ssl://$host:$port", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
            } else {
                $socket = stream_socket_client("tcp://$host:$port", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
            }

            if (!$socket) {
                return false;
            }

            // Set timeout for socket operations
            stream_set_timeout($socket, 10);

            // Read greeting
            $response = fgets($socket);
            if (!$response || (strpos($response, '* OK') === false && strpos($response, '* PREAUTH') === false)) {
                fclose($socket);
                return false;
            }

            // Send login command
            fwrite($socket, "A001 LOGIN \"$email\" \"$password\"\r\n");

            // Read response (may be multiple lines)
            $response = '';
            $attempts = 0;
            while ($attempts < 5) {
                $line = fgets($socket);
                if ($line === false) break;
                $response .= $line;
                if (strpos($line, 'A001') === 0) break;
                $attempts++;
            }

            // Check for successful login
            $success = strpos($response, 'A001 OK') !== false;



            // Always try to logout gracefully
            fwrite($socket, "A002 LOGOUT\r\n");
            fgets($socket); // Read logout response

            fclose($socket);
            return $success;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function switchAccount(Request $request, $id)
    {
        if (Auth::user()->can('switch-mailbox-settings')) {
            // Deactivate all accounts
            MailBoxCredential::where('created_by', creatorId())->update(['is_active' => false]);

            // Activate selected account
            $credential = MailBoxCredential::where('id', $id)
                ->where('created_by', creatorId())
                ->first();

            if ($credential) {
                $credential->update(['is_active' => true]);
                return back()->with('success', __('Switched to :email', ['email' => $credential->email]));
            }

            return back()->with('error', __('Account not found'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function deleteAccount($id)
    {
        if (Auth::user()->can('delete-mailbox-settings')) {
            $credential = MailBoxCredential::where('id', $id)
                ->where('created_by', creatorId())
                ->first();

            if ($credential) {
                $credential->delete();
                return back()->with('success', __('Email account deleted'));
            }

            return back()->with('error', __('Account not found'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function quickSetup(Request $request)
    {
        if (Auth::user()->can('create-mailbox-settings')) {

            $provider = $request->input('provider');

            switch ($provider) {
                case 'gmail':
                    return $this->setupGmail($request);
                case 'outlook':
                    return $this->setupOutlook($request);
                case 'yahoo':
                    return $this->setupYahoo($request);
                case 'icloud':
                    return $this->setupIcloud($request);
                default:
                    return back()->with('error', __('Unsupported email provider'));
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    private function setupGmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|ends_with:@gmail.com',
            'password' => 'required|string'
        ]);

        return $this->saveQuickSetup($request, $validated, [
            'imap_host' => 'imap.gmail.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ], 'Gmail');
    }

    private function setupOutlook(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|ends_with:@outlook.com,@hotmail.com,@live.com',
            'password' => 'required|string'
        ]);

        return $this->saveQuickSetup($request, $validated, [
            'imap_host' => 'outlook.office365.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp-mail.outlook.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ], 'Outlook');
    }

    private function setupYahoo(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|ends_with:@yahoo.com,@yahoo.co.uk,@yahoo.in',
            'password' => 'required|string'
        ]);

        return $this->saveQuickSetup($request, $validated, [
            'imap_host' => 'imap.mail.yahoo.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.mail.yahoo.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ], 'Yahoo');
    }

    private function setupIcloud(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|ends_with:@icloud.com,@me.com,@mac.com',
            'password' => 'required|string'
        ]);

        return $this->saveQuickSetup($request, $validated, [
            'imap_host' => 'imap.mail.me.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.mail.me.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ], 'iCloud');
    }

    private function saveQuickSetup($request, $validated, $serverConfig, $providerName)
    {
        try {
            $config = array_merge([
                'email' => $validated['email'],
                'password' => $validated['password'],
                'from_name' => explode('@', $validated['email'])[0],
                'mail_driver' => 'smtp',
                'is_active' => true,
                'created_by' => creatorId()
            ], $serverConfig);

            // Test connection using socket
            $tempCredential = new MailBoxCredential($config);
            $success = $this->testConnectionCredential($tempCredential);

            if (!$success) {
                return back()->with('error', __(':provider connection failed. Please check your credentials and ensure IMAP is enabled.', ['provider' => $providerName]));
            }

            // Deactivate all existing credentials for this user
            MailBoxCredential::where('created_by', creatorId())->update(['is_active' => false]);

            $credential = MailBoxCredential::create($config);

            // Dispatch event for packages to handle their fields
            if ($credential->wasRecentlyCreated) {
                CreateMailBoxCredential::dispatch($request, $credential);
            } else {
                UpdateMailBoxCredential::dispatch($request, $credential);
            }

            return redirect()->route('mailbox.inbox')
                ->with('success', __(':provider configured successfully.', ['provider' => $providerName]));
        } catch (\Exception $e) {
            return back()->with('error', __(':provider setup failed: :error', ['provider' => $providerName, 'error' => $e->getMessage()]));
        }
    }
}
