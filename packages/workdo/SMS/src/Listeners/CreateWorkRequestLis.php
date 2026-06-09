<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreateWorkRequest;
use Workdo\CMMS\Models\CmmsLocation;
use Workdo\SMS\Services\SendSMS;

class CreateWorkRequestLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateWorkRequest $event)
    {
        $cmmsLocation =  CmmsLocation::find($event->workOrder->location_id);
        if (Module_is_active('SMS', $cmmsLocation->created_by) && company_setting('SMS Work Order Request', $cmmsLocation->created_by) == 'on') {
            $uArr = [
                'component_name' => $event->workOrder->component->name ?? '-',
                'user_name' => $event->request->user_name ?? '-',
            ];

            $user = User::find($cmmsLocation->created_by);
            SendSMS::SendMsgs($uArr, 'Work Order Request', $user->mobile_no ?? null, $cmmsLocation->created_by);
        }
    }
}
