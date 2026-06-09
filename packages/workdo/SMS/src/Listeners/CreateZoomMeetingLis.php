<?php

namespace Workdo\SMS\Listeners;

use Workdo\ZoomMeeting\Events\CreateZoomMeeting;
use Workdo\SMS\Services\SendSMS;

class CreateZoomMeetingLis
{
    public function handle(CreateZoomMeeting $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Zoom Meeting') == 'on') {
            $meeting = $event->meeting;
            if (isset($meeting->host->mobile_no)) {
                $uArr = [
                    'user_name' => $meeting->host->name ?? '',
                    'meeting_name' => $meeting->title ?? '',
                    'date' => $meeting->start_time ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Zoom Meeting', $meeting->host->mobile_no);
            }
        }
    }
}
