<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Commission\Events\CreateCommissionPayment;

class CreateCommissionPaymentLis
{
    public function handle(CreateCommissionPayment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $commissionPayment = $event->commissionPayment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Commission';
            $activity['sub_module'] = 'Commission Payment';
            $activity['description'] = __('Commission Payment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $commissionPayment->created_by;
            $activity->save();
        }
    }
}
