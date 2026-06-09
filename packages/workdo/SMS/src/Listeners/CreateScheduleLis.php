<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Appointment\Events\CreateSchedule;
use Workdo\SMS\Services\SendSMS;

class CreateScheduleLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateSchedule $event)
    {
        if (Module_is_active('SMS', $event->schedule->created_by ?? null) && company_setting('SMS Appointment Schedule', $event->schedule->created_by) == 'on' ?? null) {
            $user = User::find($event->schedule->created_by ?? null);
            $uArr = [
                'date' => $event->schedule->date ?? 'Unknown',
                'time' => $event->schedule->start_time ?? 'Unknown',
            ];
            SendSMS::SendMsgs($uArr, 'Appointment Schedule', $user->mobile_no, $event->schedule->created_by ?? null);
        }
    }
}
