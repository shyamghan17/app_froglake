<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreateWorkOrder;
use Workdo\SMS\Services\SendSMS;

class CreateWorkOrderLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateWorkOrder $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Work Order Assigned') == 'on') {
            foreach ($event->workOrder->user_ids ?? [] as  $user) {
                $user = User::find($user);
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'user_name' => $user->name ?? '-',
                        'wo_name' => $event->workOrder->workorder_name ?? '-',
                    ];
                    SendSMS::SendMsgs($uArr, 'Work Order Assigned', $user->mobile_no ?? null, $event->workOrder->created_by);
                }
            }
        }
    }
}
