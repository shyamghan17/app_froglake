<?php

namespace Workdo\SMS\Listeners;

use Workdo\Sales\Events\CreateSalesMeeting;
use Workdo\SMS\Services\SendSMS;

class CreateSalesMeetingLis
{
    public function handle(CreateSalesMeeting $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Meeting Assigned') == 'on') {
            if (isset($event->meeting->assignedUser->mobile_no)) {
                $uArr = [
                    'meeting_name' => $event->meeting->name,
                ];
                SendSMS::SendMsgs($uArr, 'Meeting Assigned', $event->meeting->assignedUser->mobile_no ?? null, $event->meeting->created_by ?? null);
            }
        }
    }
}
