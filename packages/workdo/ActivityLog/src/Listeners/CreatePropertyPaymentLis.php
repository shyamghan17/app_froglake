<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\CreatePropertyPayment;

class CreatePropertyPaymentLis
{
    public function handle(CreatePropertyPayment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payment = $event->payment;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Payment';
            $activity['description'] = __('New Property Payment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payment->created_by;
            $activity->save();
        }
    }
}