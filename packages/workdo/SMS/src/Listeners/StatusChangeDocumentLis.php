<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Documents\Events\StatusChangeDocument;
use Workdo\SMS\Services\SendSMS;

class StatusChangeDocumentLis
{
    public function __construct()
    {
        //
    }

    public function handle(StatusChangeDocument $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Update Status Document') == 'on') {
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
                        'status' => $document->status ?? '',
                        'user_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'Update Status Document', $user->mobile_no, $document->created_by ?? null);
                }
            }
        }
    }
}
