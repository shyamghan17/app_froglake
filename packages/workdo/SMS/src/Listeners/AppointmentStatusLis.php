<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Appointment\Events\AppointmentStatus;
use Workdo\SMS\Services\SendSMS;

class AppointmentStatusLis
{
    public function __construct()
    {
        //
    }

    public function handle(AppointmentStatus $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Appointment Status') == 'on') {
            if (!empty($event->schedule->phone)) {
                $uArr = [
                    'status' => $event->schedule->status ?? 'Unknown',
                    'appointment_name' => $event->schedule->name ?? 'Unknown',
                ];
                SendSMS::SendMsgs($uArr, 'Appointment Status', $event->schedule->phone);
            }
        }
    }
}
