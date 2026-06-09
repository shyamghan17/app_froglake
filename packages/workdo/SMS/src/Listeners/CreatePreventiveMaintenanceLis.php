<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreatePreventiveMaintenance;
use Workdo\SMS\Services\SendSMS;

class CreatePreventiveMaintenanceLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreatePreventiveMaintenance $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Pms') == 'on') {
            $uArr = [
                'company_name' => $event->preventiveMaintenance->created_by_name ?? '-',
                'part_name' => $event->preventiveMaintenance->name ?? '-',
            ];

            SendSMS::SendMsgs($uArr, 'New Pms', User::find($event->preventiveMaintenance->created_by)->mobile_no ?? null);
        }
    }
}
