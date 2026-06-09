<?php

namespace Workdo\MailBox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Workdo\MailBox\Models\MailBoxCredential;
use Workdo\MailBox\Events\SendMailBoxEmail;

class MailBoxController extends Controller
{
    public function index($folder = 'inbox')
    {
        try {
            if(Auth::user()->can('manage-mailbox')){
                // Check if credentials exist
                $credential = MailBoxCredential::where('created_by', creatorId())
                    ->where('is_active', true)
                    ->first();

                if (!$credential) {
                        if (Auth::user()->can('manage-mailbox-settings')) {
                            return redirect()->route('mailbox.credentials.configuration')->with('error', __('The email credentials are not configured. Please set up your email account to access your mailbox.'));
                        }else{
                            return back()->with('error', __('MailBox is not configured. Please contact your company administrator to set up email credentials.'));
                        }
                }

                // Validate credential completeness
                $validationResult = $this->validateCredentials($credential);
                if (!$validationResult['valid']) {
                    return redirect()->route('mailbox.credentials.configuration')
                        ->with('error', $validationResult['message']);
                }

                try {
                    $emails = $this->fetchEmails($credential, strtoupper($folder));
                } catch (\Exception $e) {
                    $emails = new \Illuminate\Pagination\LengthAwarePaginator(
                        collect([]),
                        0,
                        10,
                        1,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                }

                $folders = ['inbox', 'sent', 'drafts', 'trash', 'spam', 'archive', 'starred'];

                return Inertia::render('MailBox/Index', [
                    'emails' => $emails->withQueryString(),
                    'folders' => $folders,
                    'currentFolder' => $folder,
                    'credential' => $credential ? $credential->only(['email', 'from_name']) : null,
                    'filters' => [
                        'search' => request('search'),
                        'sort' => request('sort', 'date'),
                        'direction' => request('direction', 'desc'),
                        'per_page' => (int)request('per_page', 10)
                    ]
                ]);
            }
            return back()->with('error', __('Permission denied'));
        } catch (\Exception $e) {
            return back()->with('error', __('An error occurred while loading the mailbox.'));
        }
    }

    public function compose()
    {
        if (Auth::user()->can('create-email-mailbox')) {
            return Inertia::render('MailBox/Compose');
        }
        return back()->with('error', __('Permission denied'));
    }

    public function send(Request $request)
    {
        if (Auth::user()->can('create-email-mailbox')) {
            $validated = $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string',
                'body' => 'required|string',
                'cc' => 'nullable|string',
                'bcc' => 'nullable|string'
            ]);

            $credential = MailBoxCredential::where('created_by', creatorId())
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return back()->with('error', __('The email configuration was not found.'));
            }

            // Convert 'to' to array format
            $emailData = $validated;
            $emailData['to'] = [$validated['to']];

            $sent = $this->sendEmail($credential, $emailData);

            if ($sent) {
                // Dispatch event for packages to handle email sending
                SendMailBoxEmail::dispatch($request, $validated);

                return redirect()->route('mailbox.inbox')
                    ->with('success', __('The email has been sent successfully.'));
            } else {
                return back()->with('error', __('The email could not be sent. Please check your configuration.'));
            }
        }
        return back()->with('error', __('Permission denied'));
    }

    private function fetchEmails($credential, $folder)
    {
        $perPage = (int)request('per_page', 10);
        $currentPage = (int)request('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        try {
            $this->testConnection($credential);
        } catch (\Exception $e) {
            throw $e;
        }

        // If search is provided, search across all emails
        if (request('search')) {
            $searchResults = $this->searchEmails($credential, $folder, request('search'));
            $totalEmails = count($searchResults);
            
            // Paginate search results
            $paginatedResults = array_slice($searchResults, $offset, $perPage);
            
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect($paginatedResults)->values(),
                $totalEmails,
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
        }

        // Normal pagination without search
        $totalEmails = $this->getTotalEmailCount($credential, $folder);
        $allEmails = $this->getEmails($credential, $folder, $perPage, $offset);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect($allEmails)->values(),
            $totalEmails,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    private function sendEmail($credential, $data)
    {
        try {
            $this->validateCredentials($credential);

            // Set dynamic mail configuration
            $this->setDynamicMailConfig($credential);

            Mail::raw($data['body'], function ($message) use ($data) {
                $message->to($data['to'])->subject($data['subject']);

                if (!empty($data['cc'])) {
                    $message->cc($data['cc']);
                }

                if (!empty($data['bcc'])) {
                    $message->bcc($data['bcc']);
                }
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set dynamic mail configuration from MailBox credentials
     */
    private function setDynamicMailConfig($credential)
    {
        $mailDriver = $credential->mail_driver ?: 'smtp';

        try {
            // Set the default mailer
            Config::set('mail.default', $mailDriver);

            // Configure based on mail driver
            switch ($mailDriver) {
                case 'smtp':
                    Config::set([
                        'mail.mailers.smtp.transport' => 'smtp',
                        'mail.mailers.smtp.host' => $credential->smtp_host,
                        'mail.mailers.smtp.port' => (int) $credential->smtp_port,
                        'mail.mailers.smtp.encryption' => $credential->smtp_encryption === 'none' ? null : $credential->smtp_encryption,
                        'mail.mailers.smtp.username' => $credential->email,
                        'mail.mailers.smtp.password' => $credential->password,
                        'mail.mailers.smtp.timeout' => 60,
                        'mail.mailers.smtp.local_domain' => null,
                        'mail.mailers.smtp.verify_peer' => false,
                        'mail.mailers.smtp.verify_peer_name' => false,
                    ]);
                    break;

                case 'sendmail':
                    Config::set([
                        'mail.mailers.sendmail.transport' => 'sendmail',
                        'mail.mailers.sendmail.path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
                    ]);
                    break;

                case 'log':
                    Config::set([
                        'mail.mailers.log.transport' => 'log',
                        'mail.mailers.log.channel' => env('MAIL_LOG_CHANNEL'),
                    ]);
                    break;

                default:
                    // For any other driver (mailgun, ses, postmark, custom, etc.)
                    // Just set the driver - Laravel will handle it if properly configured
                    Config::set('mail.default', $mailDriver);
                    break;
            }

            // Set global from address
            Config::set([
                'mail.from.address' => $credential->email,
                'mail.from.name' => $credential->from_name ?: config('app.name', 'WorkDo-Dash'),
            ]);
        } catch (\Exception $e) {
            Config::set('mail.default', 'log');
        }
    }

    public function show($id)
    {
        if (Auth::user()->can('view-mailbox-email')) {
            $credential = MailBoxCredential::where('created_by', creatorId())
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return back()->with('error', __('The email configuration was not found.'));
            }

            $email = $this->getEmailById($credential, $id);

            // Always mark email as read when viewing (Gmail behavior)
            if ($email) {
                $this->performAction($credential, [
                    'action' => 'read',
                    'emails' => [$id]
                ]);
                $email['isRead'] = true;
            }

            return Inertia::render('MailBox/Show', [
                'email' => $email
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }

    public function reply($id)
    {
        if (Auth::user()->can('reply-email-mailbox')) {
            $credential = MailBoxCredential::where('created_by', creatorId())
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return back()->with('error', __('The email configuration was not found.'));
            }

            $originalEmail = $this->getEmailById($credential, $id);

            return Inertia::render('MailBox/Reply', [
                'originalEmail' => $originalEmail
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }

    public function replyStore(Request $request, $id)
    {
        if (Auth::user()->can('reply-email-mailbox')) {
            $validated = $request->validate([
                'body' => 'required|string'
            ]);

            $credential = MailBoxCredential::where('created_by', creatorId())
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return back()->with('error', __('The email configuration was not found.'));
            }

            $sent = $this->sendReply($credential, $id, $validated);

            if ($sent) {
                // Dispatch event for packages to handle email sending
                SendMailBoxEmail::dispatch($request, array_merge($validated, ['type' => 'reply', 'original_id' => $id]));

                return redirect()->route('mailbox.inbox', ['refresh' => 'true'])
                    ->with('success', __('The reply has been sent successfully.'));
            } else {
                return back()->with('error', __('The reply could not be sent.'));
            }
        }
        return back()->with('error', __('Permission denied'));
    }

    public function action(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:read,unread,star,unstar,delete,move,archive,spam',
                'emails' => 'required|array',
                'folder' => 'nullable|string'
            ]);

            // Check specific permissions based on action
            if ($validated['action'] === 'delete') {
                if (!Auth::user()->can('delete-email-mailbox')) {
                    return back()->with('error', __('Permission denied'));
                }
            } else {
                if (!Auth::user()->can('action-email-mailbox')) {
                    return back()->with('error', __('Permission denied'));
                }
            }

            $credential = MailBoxCredential::where('created_by', creatorId())
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return back()->with('error', __('Email configuration not found'));
            }

            $success = $this->performAction($credential, $validated);

            if ($success) {
                $successMessages = [
                    'read' => __('The emails have been marked as read successfully.'),
                    'unread' => __('The emails have been marked as unread successfully.'),
                    'star' => __('The emails have been starred successfully.'),
                    'unstar' => __('The emails have been unstarred successfully.'),
                    'delete' => __('The emails have been deleted successfully.'),
                    'move' => __('The emails have been moved successfully.'),
                    'archive' => __('The emails have been archived successfully.'),
                    'spam' => __('The emails have been marked as spam successfully.')
                ];

                $message = $successMessages[$validated['action']] ?? __('Action completed successfully');
                return back()->with('success', $message);
            } else {
                return back()->with('error', __('Action failed. Please try again'));
            }
        } catch (\Exception $e) {
            return back()->with('error', __('An error occurred. Please try again'));
        }
    }

    private function validateCredentials($credential, $returnArray = true)
    {
        if ($returnArray) {
            if (!$credential->email || !$credential->password) {
                return [
                    'valid' => false,
                    'message' => __('Incomplete credentials. Email and password are required to access your mailbox.')
                ];
            }

            if (!$credential->imap_host || !$credential->imap_port) {
                return [
                    'valid' => false,
                    'message' => __('IMAP settings missing. Please configure your IMAP host and port settings.')
                ];
            }

            if (!filter_var($credential->email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'valid' => false,
                    'message' => __('Invalid email format. Please check your email address configuration.')
                ];
            }

            return ['valid' => true];
        } else {
            if (empty($credential->email) || empty($credential->password)) {
                throw new \Exception('Email credentials not configured');
            }

            if (!$credential->is_active) {
                throw new \Exception('Email account is not active');
            }
        }
    }

    // IMAP Connection Methods
    private $socket;
    private $tagCounter = 1;

    private function connect($credential)
    {
        $host = $credential->imap_host;
        $port = $credential->imap_port;
        $encryption = $credential->imap_encryption;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        if (strtolower($encryption) === 'ssl') {
            $this->socket = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        } else {
            $this->socket = stream_socket_client("tcp://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        }

        if (!$this->socket) {
            throw new \Exception("Connection failed: $errstr ($errno)");
        }

        $this->readResponse();
        return true;
    }

    private function sendCommand($command)
    {
        $tag = 'A' . sprintf('%04d', $this->tagCounter++);
        $fullCommand = "$tag $command\r\n";
        fwrite($this->socket, $fullCommand);
        return $tag;
    }

    private function readResponse($expectedTag = null)
    {
        $response = '';
        while (($line = fgets($this->socket)) !== false) {
            $response .= $line;
            if ($expectedTag && strpos($line, $expectedTag) === 0) {
                break;
            }
            if (!$expectedTag && (strpos($line, '* OK') === 0 || strpos($line, '* PREAUTH') === 0)) {
                break;
            }
        }
        return $response;
    }

    private function login($credential)
    {
        $tag = $this->sendCommand('LOGIN "' . $credential->email . '" "' . $credential->password . '"');
        $response = $this->readResponse($tag);
        if (strpos($response, "$tag OK") === false) {
            throw new \Exception("Authentication failed");
        }
        return true;
    }

    private function disconnect()
    {
        if ($this->socket) {
            $this->sendCommand('LOGOUT');
            fclose($this->socket);
            $this->socket = null;
        }
    }

    private function testConnection($credential)
    {
        try {
            $this->connect($credential);
            $this->login($credential);
            $this->disconnect();
            return true;
        } catch (\Exception $e) {
            $this->disconnect();
            return false;
        }
    }

    private function selectFolder($folder)
    {
        // Handle INBOX first
        if ($folder === 'INBOX') {
            $tag = $this->sendCommand('SELECT "INBOX"');
            $response = $this->readResponse($tag);
            if (strpos($response, "$tag OK") !== false) {
                $noopTag = $this->sendCommand('NOOP');
                $this->readResponse($noopTag);
                preg_match('/\* (\d+) EXISTS/', $response, $matches);
                return isset($matches[1]) ? (int)$matches[1] : 0;
            }
        }

        // Get folder variations based on folder type
        $variations = $this->getAvailableFolderVariations($folder);

        // Try each variation until one works
        foreach ($variations as $folderName) {
            $tag = $this->sendCommand("SELECT \"$folderName\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                $noopTag = $this->sendCommand('NOOP');
                $this->readResponse($noopTag);
                preg_match('/\* (\d+) EXISTS/', $response, $matches);
                return isset($matches[1]) ? (int)$matches[1] : 0;
            }
        }

        return 0;
    }

    private function getFolderName($folder)
    {
        $folderMaps = [
            'INBOX' => 'INBOX',
            'SENT' => 'Sent',
            'DRAFTS' => 'Drafts',
            'TRASH' => 'Trash',
            'SPAM' => 'Spam',
            'ARCHIVE' => 'Archive',
            'STARRED' => 'INBOX'
        ];

        return $folderMaps[strtoupper($folder)] ?? 'INBOX';
    }

    private function getAvailableFolderVariations($folderType)
    {
        $variations = [
            'Sent' => ['Sent', 'Sent Mail', '[Gmail]/Sent Mail', 'INBOX.Sent', 'Sent Items'],
            'Trash' => ['Trash', 'Bin', '[Gmail]/Trash', 'INBOX.Trash', 'Deleted Items', 'Deleted Messages'],
            'Spam' => ['Spam', 'Junk', '[Gmail]/Spam', 'INBOX.Spam', 'Junk E-mail', 'Bulk Mail'],
            'Drafts' => ['Drafts', '[Gmail]/Drafts', 'INBOX.Drafts', 'Draft'],
            'Archive' => ['Archive', '[Gmail]/All Mail', 'INBOX.Archive', 'All Mail'],
            'Starred' => ['Starred', 'INBOX']
        ];

        return $variations[$folderType] ?? [$folderType];
    }

    private function getEmails($credential, $folder = 'INBOX', $limit = 10, $offset = 0)
    {
        try {
            $this->validateCredentials($credential, false);
            $this->connect($credential);
            $this->login($credential);

            $folderName = $this->getFolderName($folder);

            $messageCount = $this->selectFolder($folderName);

            if ($messageCount == 0) {
                $this->disconnect();
                return [];
            }

            $emails = [];

            if (strtoupper($folder) === 'STARRED') {
                $tag = $this->sendCommand("SEARCH FLAGGED");
                $response = $this->readResponse($tag);

                $flaggedIds = [];
                if (preg_match('/\* SEARCH (.+)/', $response, $matches)) {
                    $flaggedIds = array_filter(explode(' ', trim($matches[1])));
                }

                if (empty($flaggedIds)) {
                    $this->disconnect();
                    return [];
                }

                rsort($flaggedIds);
                $paginatedIds = array_slice($flaggedIds, $offset, $limit);

                foreach ($paginatedIds as $msgId) {
                    $email = $this->fetchEmailOverview($msgId, $credential);
                    if ($email) {
                        $emails[] = $email;
                    }
                }
            } else {
                // Always fetch from the most recent emails (highest message numbers)
                $startMsg = max(1, $messageCount - $offset - $limit + 1);
                $endMsg = $messageCount - $offset;

                if ($endMsg <= 0 || $startMsg > $messageCount) {
                    $this->disconnect();
                    return [];
                }

                // Fetch from newest to oldest
                for ($i = $endMsg; $i >= $startMsg && count($emails) < $limit; $i--) {
                    $email = $this->fetchEmailOverview($i, $credential);
                    if ($email) {
                        $emails[] = $email;
                    }
                }
            }

            $this->disconnect();
            return $emails;
        } catch (\Exception $e) {
            $this->disconnect();
            return [];
        }
    }

    private function fetchEmailOverview($msgNum, $credential)
    {
        try {
            $headerTag = $this->sendCommand("FETCH $msgNum (FLAGS BODY.PEEK[HEADER.FIELDS (FROM TO SUBJECT DATE)])");
            $headerResponse = $this->readResponse($headerTag);

            $flags = [];
            if (preg_match('/FLAGS \(([^)]+)\)/', $headerResponse, $matches)) {
                $flags = explode(' ', $matches[1]);
            }

            $headers = $this->parseHeaders($headerResponse);

            $bodyTag = $this->sendCommand("FETCH $msgNum (BODY.PEEK[TEXT]<0.300>)");
            $bodyResponse = $this->readResponse($bodyTag);
            $preview = $this->parseBodyPreview($bodyResponse);

            return [
                'id' => (string)$msgNum,
                'subject' => $headers['subject'] ?? 'No Subject',
                'from' => $headers['from'] ?? 'Unknown',
                'to' => [$credential->email],
                'date' => !empty($headers['date']) && $headers['date'] !== '1970-01-01 00:00:00' ? $headers['date'] : now()->format('Y-m-d H:i:s'),
                'body' => $preview,
                'isRead' => in_array('\\Seen', $flags),
                'isStarred' => in_array('\\Flagged', $flags),
                'hasAttachment' => false,
                'attachments' => []
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getTotalEmailCount($credential, $folder = 'INBOX')
    {
        try {
            $this->validateCredentials($credential, false);
            $this->connect($credential);
            $this->login($credential);

            $folderName = $this->getFolderName($folder);

            $messageCount = $this->selectFolder($folderName);

            if (strtoupper($folder) === 'STARRED') {
                $tag = $this->sendCommand("SEARCH FLAGGED");
                $response = $this->readResponse($tag);

                $count = 0;
                if (preg_match('/\* SEARCH (.+)/', $response, $matches)) {
                    $flaggedIds = array_filter(explode(' ', trim($matches[1])));
                    $count = count($flaggedIds);
                }

                $this->disconnect();
                return $count;
            }

            $this->disconnect();
            return $messageCount;
        } catch (\Exception $e) {
            $this->disconnect();
            return 0;
        }
    }

    private function getEmailById($credential, $id)
    {
        try {
            $this->validateCredentials($credential, false);
            $this->connect($credential);
            $this->login($credential);
            $this->selectFolder('INBOX');

            $headerTag = $this->sendCommand("FETCH $id (FLAGS BODY.PEEK[HEADER.FIELDS (FROM TO SUBJECT DATE)])");
            $headerResponse = $this->readResponse($headerTag);

            $flags = [];
            if (preg_match('/FLAGS \(([^)]+)\)/', $headerResponse, $matches)) {
                $flags = explode(' ', $matches[1]);
            }

            $headers = $this->parseHeaders($headerResponse);
            $body = $this->getEmailBody($id);

            $result = [
                'id' => (string)$id,
                'subject' => $headers['subject'] ?? 'No Subject',
                'from' => $headers['from'] ?? 'Unknown',
                'from_email' => $this->extractEmail($headers['from'] ?? ''),
                'from_name' => $this->extractName($headers['from'] ?? ''),
                'reply_to' => $this->extractEmail($headers['from'] ?? ''),
                'to' => [$credential->email],
                'date' => !empty($headers['date']) && $headers['date'] !== '1970-01-01 00:00:00' ? $headers['date'] : now()->format('Y-m-d H:i:s'),
                'body' => $body,
                'isRead' => in_array('\\Seen', $flags),
                'isStarred' => in_array('\\Flagged', $flags),
                'hasAttachment' => false,
                'attachments' => [],
                'headers' => [
                    'message_id' => '',
                    'references' => '',
                    'in_reply_to' => '',
                    'raw_headers' => ''
                ]
            ];

            $this->disconnect();
            return $result;
        } catch (\Exception $e) {
            $this->disconnect();
            return null;
        }
    }

    private function sendReply($credential, $originalId, $data)
    {
        $originalEmail = $this->getEmailById($credential, $originalId);
        if (!$originalEmail) {
            return false;
        }

        $replyData = [
            'to' => [$originalEmail['from_email']],
            'subject' => 'Re: ' . str_replace('Re: ', '', $originalEmail['subject']),
            'body' => $data['body'] . "\n\n" .
                "---------- Original Message ----------\n" .
                "From: {$originalEmail['from']}\n" .
                "Date: {$originalEmail['date']}\n" .
                "Subject: {$originalEmail['subject']}\n\n" .
                strip_tags($originalEmail['body'])
        ];

        return $this->sendEmail($credential, $replyData);
    }

    private function performAction($credential, $data)
    {
        try {
            $this->validateCredentials($credential, false);
            $this->connect($credential);
            $this->login($credential);

            $currentFolder = $data['folder'] ?? 'INBOX';
            $folderName = $this->getFolderName($currentFolder);
            $this->selectFolder($folderName);

            foreach ($data['emails'] as $emailId) {
                switch ($data['action']) {
                    case 'read':
                        $this->setFlag($emailId, '\\Seen');
                        break;
                    case 'unread':
                        $this->clearFlag($emailId, '\\Seen');
                        break;
                    case 'star':
                    case 'starred':
                        $this->setFlag($emailId, '\\Flagged');
                        break;
                    case 'unstar':
                    case 'unstarred':
                        $this->clearFlag($emailId, '\\Flagged');
                        break;
                    case 'delete':
                        if (strtoupper($currentFolder) === 'TRASH') {
                            $this->setFlag($emailId, '\\Deleted');
                        } else {
                            $this->moveToTrash($emailId);
                        }
                        break;
                    case 'archive':
                        $this->moveToArchive($emailId);
                        break;
                    case 'spam':
                        $this->moveToSpam($emailId);
                        break;
                }
            }

            if (in_array($data['action'], ['delete'])) {
                $this->expunge();
            }

            $this->disconnect();
            return true;
        } catch (\Exception $e) {
            $this->disconnect();
            return false;
        }
    }

    // Helper methods for email parsing and processing
    private function parseHeaders($response)
    {
        $headers = [];

        if (preg_match('/\{(\d+)\}\r?\n(.+?)(?=\r?\n\* |\r?\nA\d+|\Z)/s', $response, $matches)) {
            $headerContent = $matches[2];

            $lines = explode("\n", $headerContent);
            $currentHeader = '';
            $currentValue = '';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (preg_match('/^(From|To|Subject|Date):\s*(.+)$/i', $line, $headerMatch)) {
                    if ($currentHeader) {
                        $headers[strtolower($currentHeader)] = $this->decodeHeaderValue($currentValue);
                    }

                    $currentHeader = $headerMatch[1];
                    $currentValue = $headerMatch[2];
                } else {
                    $currentValue .= ' ' . $line;
                }
            }

            if ($currentHeader) {
                $headers[strtolower($currentHeader)] = $this->decodeHeaderValue($currentValue);
            }
        }

        if (isset($headers['date'])) {
            try {
                $timestamp = strtotime($headers['date']);
                if ($timestamp !== false && $timestamp > 0) {
                    $headers['date'] = date('Y-m-d H:i:s', $timestamp);
                } else {
                    $headers['date'] = now()->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                $headers['date'] = now()->format('Y-m-d H:i:s');
            }
        }

        return $headers;
    }

    private function decodeHeaderValue($value)
    {
        $value = trim($value);

        $value = preg_replace_callback('/=\?([^?]+)\?([BQ])\?([^?]+)\?=/i', function ($matches) {
            $charset = $matches[1];
            $encoding = strtoupper($matches[2]);
            $data = $matches[3];

            if ($encoding === 'B') {
                $decoded = base64_decode($data);
            } else {
                $decoded = quoted_printable_decode(str_replace('_', ' ', $data));
            }

            // Convert to UTF-8 if needed
            if (strtolower($charset) !== 'utf-8') {
                $decoded = @iconv($charset, 'UTF-8//IGNORE', $decoded) ?: $decoded;
            }

            return $decoded;
        }, $value);

        // Sanitize UTF-8
        $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        return $value;
    }

    private function parseBodyPreview($response)
    {
        if (preg_match('/BODY\[TEXT\]<0\.300> \{(\d+)\}\r?\n(.+)/s', $response, $matches)) {
            $length = (int)$matches[1];
            $body = substr($matches[2], 0, $length);
            return $this->cleanEmailContent($body, true);
        }
        return 'No preview available';
    }

    private function getEmailBody($id)
    {
        $bodyTag = $this->sendCommand("FETCH $id (BODY.PEEK[])");
        $bodyResponse = $this->readResponse($bodyTag);

        if (preg_match('/BODY\[\] \{(\d+)\}\r?\n(.+)/s', $bodyResponse, $matches)) {
            $length = (int)$matches[1];
            $fullBody = substr($matches[2], 0, $length);
            $processedBody = $this->processEmailBody($fullBody);
            
            // If processed body is empty, try alternative approach
            if (empty(trim(strip_tags($processedBody)))) {
                // Try to get text content directly
                $textTag = $this->sendCommand("FETCH $id (BODY.PEEK[TEXT])");
                $textResponse = $this->readResponse($textTag);
                
                if (preg_match('/BODY\[TEXT\] \{(\d+)\}\r?\n(.+)/s', $textResponse, $textMatches)) {
                    $textLength = (int)$textMatches[1];
                    $textBody = substr($textMatches[2], 0, $textLength);
                    return $this->cleanEmailContent($textBody, false);
                }
            }
            
            return $processedBody;
        }

        return 'Unable to decode email content';
    }

    private function processEmailBody($fullBody)
    {
        if (empty(trim($fullBody))) {
            return 'No content available';
        }

        // Split headers from body
        $parts = explode("\r\n\r\n", $fullBody, 2);
        if (count($parts) < 2) {
            $parts = explode("\n\n", $fullBody, 2);
        }
        
        $headers = $parts[0] ?? '';
        $body = $parts[1] ?? $fullBody;

        // Check if it's multipart
        if (strpos($headers, 'Content-Type: multipart') !== false) {
            return $this->parseMultipartEmail($fullBody);
        }

        // Check if it's HTML
        if (strpos($headers, 'Content-Type: text/html') !== false) {
            if (strpos($headers, 'quoted-printable') !== false) {
                $body = quoted_printable_decode($body);
            }
            return $this->cleanHtmlContent($body);
        }

        // Default to plain text
        if (strpos($headers, 'quoted-printable') !== false) {
            $body = quoted_printable_decode($body);
        }
        
        return $this->cleanEmailContent($body, false);
    }

    private function parseMultipartEmail($fullBody)
    {
        // Extract boundary
        if (preg_match('/boundary="([^"]+)"/', $fullBody, $matches)) {
            $boundary = $matches[1];
        } elseif (preg_match('/boundary=([^\s;\r\n]+)/', $fullBody, $matches)) {
            $boundary = trim($matches[1], '"');
        } else {
            return $this->cleanEmailContent($fullBody, false);
        }

        // Split by boundary
        $parts = preg_split('/--' . preg_quote($boundary, '/') . '/', $fullBody);

        $htmlContent = '';
        $textContent = '';

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part) || $part === '--') continue;

            // Split part headers from content
            $partParts = preg_split('/\r?\n\r?\n/', $part, 2);
            if (count($partParts) < 2) continue;
            
            $partHeaders = $partParts[0];
            $partContent = $partParts[1];

            // Check content type
            if (strpos($partHeaders, 'Content-Type: text/html') !== false) {
                if (strpos($partHeaders, 'quoted-printable') !== false) {
                    $partContent = quoted_printable_decode($partContent);
                }
                $htmlContent = $partContent;
            } elseif (strpos($partHeaders, 'Content-Type: text/plain') !== false && empty($htmlContent)) {
                if (strpos($partHeaders, 'quoted-printable') !== false) {
                    $partContent = quoted_printable_decode($partContent);
                }
                $textContent = $partContent;
            }
        }

        $content = $htmlContent ?: $textContent;

        if ($htmlContent) {
            return $this->cleanHtmlContent($content);
        } else {
            return $this->cleanEmailContent($content, false);
        }
    }

    private function extractContentFromPart($part)
    {
        $lines = explode("\n", $part);
        $inContent = false;
        $content = '';
        $encoding = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$inContent) {
                if (strpos($line, 'Content-Transfer-Encoding:') !== false) {
                    $encoding = trim(str_replace('Content-Transfer-Encoding:', '', $line));
                }

                if (empty($line)) {
                    $inContent = true;
                    continue;
                }

                if (preg_match('/^[A-Za-z-]+:/', $line)) {
                    continue;
                }
            } else {
                $content .= $line . "\n";
            }
        }

        if (strpos($encoding, 'quoted-printable') !== false) {
            $content = quoted_printable_decode($content);
        } elseif (strpos($encoding, 'base64') !== false) {
            $content = base64_decode($content);
        }

        return $content;
    }

    private function extractHtmlFromSinglePart($fullBody)
    {
        $lines = explode("\n", $fullBody);
        $inContent = false;
        $content = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$inContent) {
                if (empty($line)) {
                    $inContent = true;
                    continue;
                }
                if (preg_match('/^(Content-Type|Content-Transfer-Encoding|Content-Disposition|MIME-Version):/i', $line)) {
                    continue;
                }
            }

            if ($inContent) {
                $content .= $line . "\n";
            }
        }

        return $this->cleanHtmlContent($content);
    }

    private function extractTextFromSinglePart($fullBody)
    {
        $lines = explode("\n", $fullBody);
        $inContent = false;
        $content = '';
        $headerEnded = false;

        foreach ($lines as $line) {
            $line = rtrim($line);

            if (!$inContent) {
                // Look for empty line that separates headers from body
                if (empty($line) && !$headerEnded) {
                    $headerEnded = true;
                    $inContent = true;
                    continue;
                }
                // Skip header lines
                if (preg_match('/^[A-Za-z-]+:\s*/', $line)) {
                    continue;
                }
                // If we haven't found headers, assume content starts
                if (!$headerEnded) {
                    $inContent = true;
                }
            }

            if ($inContent) {
                $content .= $line . "\n";
            }
        }

        // If no content found, try to extract everything after first empty line
        if (empty(trim($content))) {
            $parts = explode("\n\n", $fullBody, 2);
            if (count($parts) > 1) {
                $content = $parts[1];
            } else {
                $content = $fullBody;
            }
        }

        return $this->cleanEmailContent($content, false);
    }

    private function cleanHtmlContent($htmlContent)
    {
        $htmlContent = preg_replace('/^(Content-Type|Content-Transfer-Encoding|Content-Disposition|MIME-Version):.*$/m', '', $htmlContent);
        $htmlContent = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $htmlContent);
        $htmlContent = preg_replace('/<link[^>]*>/si', '', $htmlContent);
        $htmlContent = preg_replace('/<meta[^>]*>/si', '', $htmlContent);
        $htmlContent = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $htmlContent);
        $htmlContent = preg_replace('/\*\s*\{[^}]*\}/s', '', $htmlContent);
        $htmlContent = preg_replace('/body\s*\{[^}]*\}/s', '', $htmlContent);
        $htmlContent = preg_replace('/html\s*\{[^}]*\}/s', '', $htmlContent);
        $htmlContent = preg_replace('/<\/?html[^>]*>/si', '', $htmlContent);
        $htmlContent = preg_replace('/<\/?head[^>]*>/si', '', $htmlContent);
        $htmlContent = preg_replace('/<body([^>]*)>/si', '<div$1>', $htmlContent);
        $htmlContent = preg_replace('/<\/body>/si', '</div>', $htmlContent);
        $htmlContent = str_replace('=3D', '=', $htmlContent);
        $htmlContent = str_replace('=0D=0A', '', $htmlContent);
        $htmlContent = str_replace('=\r\n', '', $htmlContent);
        $htmlContent = str_replace('=\n', '', $htmlContent);
        $htmlContent = preg_replace('/\r\n/', "\n", $htmlContent);
        $htmlContent = preg_replace('/\n{3,}/', "\n\n", $htmlContent);
        $htmlContent = ltrim($htmlContent);

        return '<div class="email-content-isolation" style="all: initial; display: block; width: 100%; max-width: 100%; background: #ffffff; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; font-size: 14px; line-height: 1.4; color: #202124; overflow: hidden; position: relative; z-index: 1;"><div style="all: initial; display: block; width: 100%; padding: 20px; margin: 0; background: #ffffff; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; font-size: 14px; line-height: 1.4; color: #202124; word-wrap: break-word; overflow-wrap: break-word; box-sizing: border-box;"><div style="all: initial; display: block; font-family: inherit; font-size: inherit; line-height: inherit; color: inherit;">' . $htmlContent . '</div></div></div><style>.email-content-isolation * { box-sizing: border-box !important; } .email-content-isolation table { border-collapse: collapse !important; width: auto !important; max-width: 100% !important; } .email-content-isolation img { max-width: 100% !important; height: auto !important; } .email-content-isolation a { color: #1a73e8 !important; text-decoration: underline !important; }</style>';
    }

    private function cleanEmailContent($body, $isPreview = true)
    {
        $body = quoted_printable_decode($body);
        
        // Comprehensive UTF-8 sanitization
        $body = @iconv('UTF-8', 'UTF-8//IGNORE', $body);
        $body = filter_var($body, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $body = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $body);

        if ($isPreview) {
            $body = strip_tags($body);
            $body = preg_replace('/\r\n/', "\n", $body);
            $body = preg_replace('/\n{3,}/', "\n\n", $body);
            $body = preg_replace('/[ \t]{2,}/', ' ', $body);

            if (strlen($body) > 200) {
                $body = substr($body, 0, 200) . '...';
            }
        } else {
            // Don't escape HTML if it's already HTML content
            if (strpos($body, '<') !== false && strpos($body, '>') !== false) {
                // It's HTML content, just clean it up
                $body = preg_replace('/\r\n|\r|\n/', "\n", $body);
                $body = '<div style="font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.4; color: #3c4043; word-wrap: break-word; overflow-wrap: break-word;">' . $body . '</div>';
            } else {
                // It's plain text, convert to HTML
                $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
                $body = preg_replace('/\r\n|\r|\n/', '<br>', $body);
                $body = preg_replace('/  +/', '&nbsp;&nbsp;', $body);
                $body = '<div style="font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.4; color: #3c4043; white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word;">' . $body . '</div>';
            }
        }

        return trim($body);
    }

    private function extractEmail($fromField)
    {
        if (preg_match('/<([^>]+)>/', $fromField, $matches)) {
            return $matches[1];
        }
        if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $fromField, $matches)) {
            return $matches[1];
        }
        return 'unknown@example.com';
    }

    private function extractName($fromField)
    {
        if (preg_match('/^(.+?)\s*</', $fromField, $matches)) {
            return trim($matches[1], '"');
        }
        $email = $this->extractEmail($fromField);
        return explode('@', $email)[0];
    }

    private function setFlag($emailId, $flag)
    {
        $tag = $this->sendCommand("STORE $emailId +FLAGS ($flag)");
        $this->readResponse($tag);
    }

    private function clearFlag($emailId, $flag)
    {
        $tag = $this->sendCommand("STORE $emailId -FLAGS ($flag)");
        $this->readResponse($tag);
    }

    private function moveToTrash($emailId)
    {
        $trashFolders = $this->getAvailableFolderVariations('Trash');
        $moved = false;

        foreach ($trashFolders as $trashFolder) {
            $tag = $this->sendCommand("MOVE $emailId \"$trashFolder\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                $moved = true;
                break;
            }
        }

        if (!$moved) {
            foreach ($trashFolders as $trashFolder) {
                $tag = $this->sendCommand("COPY $emailId \"$trashFolder\"");
                $response = $this->readResponse($tag);

                if (strpos($response, "$tag OK") !== false) {
                    $this->setFlag($emailId, '\\Deleted');
                    $moved = true;
                    break;
                }
            }
        }

        if (!$moved) {
            $this->setFlag($emailId, '\\Deleted');
        }
    }

    private function moveToArchive($emailId)
    {
        $archiveFolders = $this->getAvailableFolderVariations('Archive');

        foreach ($archiveFolders as $archiveFolder) {
            $tag = $this->sendCommand("MOVE $emailId \"$archiveFolder\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                return;
            }
        }

        foreach ($archiveFolders as $archiveFolder) {
            $tag = $this->sendCommand("COPY $emailId \"$archiveFolder\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                $this->setFlag($emailId, '\\Deleted');
                return;
            }
        }
    }

    private function moveToSpam($emailId)
    {
        $spamFolders = $this->getAvailableFolderVariations('Spam');

        foreach ($spamFolders as $spamFolder) {
            $tag = $this->sendCommand("MOVE $emailId \"$spamFolder\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                return;
            }
        }

        foreach ($spamFolders as $spamFolder) {
            $tag = $this->sendCommand("COPY $emailId \"$spamFolder\"");
            $response = $this->readResponse($tag);

            if (strpos($response, "$tag OK") !== false) {
                $this->setFlag($emailId, '\\Deleted');
                return;
            }
        }
    }

    private function expunge()
    {
        $tag = $this->sendCommand("EXPUNGE");
        $this->readResponse($tag);
    }

    private function searchEmails($credential, $folder, $searchTerm)
    {
        try {
            $this->validateCredentials($credential, false);
            $this->connect($credential);
            $this->login($credential);

            $folderName = $this->getFolderName($folder);
            $messageCount = $this->selectFolder($folderName);

            if ($messageCount == 0) {
                $this->disconnect();
                return [];
            }

            $searchResults = [];
            $searchTerm = strtolower(trim($searchTerm));

            // Use IMAP SEARCH command for better performance
            $searchCommands = [
                "SEARCH SUBJECT \"$searchTerm\"",
                "SEARCH FROM \"$searchTerm\"",
                "SEARCH BODY \"$searchTerm\""
            ];

            $foundIds = [];
            
            foreach ($searchCommands as $searchCommand) {
                $tag = $this->sendCommand($searchCommand);
                $response = $this->readResponse($tag);
                
                if (preg_match('/\* SEARCH (.+)/', $response, $matches)) {
                    $ids = array_filter(explode(' ', trim($matches[1])));
                    $foundIds = array_merge($foundIds, $ids);
                }
            }

            // Remove duplicates and sort by message ID (newest first)
            $foundIds = array_unique($foundIds);
            rsort($foundIds);

            // If IMAP search didn't work or returned no results, fall back to manual search
            if (empty($foundIds)) {
                $foundIds = $this->manualSearchEmails($credential, $folder, $searchTerm, $messageCount);
            }

            // Fetch email details for found IDs
            foreach ($foundIds as $msgId) {
                $email = $this->fetchEmailOverview($msgId, $credential);
                if ($email) {
                    $searchResults[] = $email;
                }
            }

            $this->disconnect();
            return $searchResults;
        } catch (\Exception $e) {
            $this->disconnect();
            return [];
        }
    }

    private function manualSearchEmails($credential, $folder, $searchTerm, $messageCount)
    {
        $foundIds = [];
        $searchTerm = strtolower($searchTerm);
        
        // Search through all emails manually (from newest to oldest)
        for ($i = $messageCount; $i >= 1; $i--) {
            try {
                $headerTag = $this->sendCommand("FETCH $i (BODY.PEEK[HEADER.FIELDS (FROM SUBJECT)])");
                $headerResponse = $this->readResponse($headerTag);
                
                $headers = $this->parseHeaders($headerResponse);
                
                $subject = strtolower($headers['subject'] ?? '');
                $from = strtolower($headers['from'] ?? '');
                
                if (str_contains($subject, $searchTerm) || str_contains($from, $searchTerm)) {
                    $foundIds[] = $i;
                }
            } catch (\Exception $e) {
                // Continue searching even if one email fails
                continue;
            }
        }
        
        return $foundIds;
    }
}