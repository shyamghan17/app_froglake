<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Documents\Events\CreateDocument;
use Workdo\SMS\Services\SendSMS;

class CreateDocumentLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateDocument $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Document') == 'on') {
            $document = $event->document;
            $users = [];
            if ($document->is_private) {
                $users = $document->assignedUsers;
            } else if ($document->creator_id != $document->created_by) {
                $users = User::whereId($document->created_by)->get();
            }
            foreach ($users ?? [] as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'user_name' => $user->name ?? 'User',
                        'name' => $document->title ?? 'Document',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Document', $user->mobile_no, $document->created_by ?? null);
                }
            }
        }
    }
}
