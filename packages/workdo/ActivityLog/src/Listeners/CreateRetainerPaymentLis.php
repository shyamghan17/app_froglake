<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Retainer\Events\CreateRetainerPayment;

class CreateRetainerPaymentLis
{
    public function handle(CreateRetainerPayment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $retainerPayment = $event->retainerPayment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Retainer';
            $activity['sub_module'] = 'Retainer Payment';
            $activity['description'] = __('New Retainer Payment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $retainerPayment->created_by;
            $activity->save();
        }
    }
}
