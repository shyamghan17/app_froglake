<?php

namespace Workdo\SMS\Listeners;

use Workdo\Timesheet\Events\CreateTimesheet;
use Workdo\SMS\Services\SendSMS;

class CreateTimesheetLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateTimesheet $event)
    {
        $timesheet = $event->timesheet;
        if (Module_is_active('SMS') && company_setting('SMS New Timesheet') == 'on') {
            if ($timesheet->user && $timesheet->user->mobile_no) {
                $uArr = [
                    'user_name' =>  $timesheet->user->name ?? '',
                    'type' => $timesheet->type ?? 'Timesheet',
                ];
                SendSMS::SendMsgs($uArr, 'New Timesheet', $timesheet->user->mobile_no, $timesheet->created_by);
            }
        }
    }
}
