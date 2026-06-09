<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdatePropertyPaymentStatus;

class UpdatePropertyPaymentStatusLis
{
    public function handle(UpdatePropertyPaymentStatus $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payment = $event->payment;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Payment';
            $activity['description'] = __('Property Payment status updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payment->created_by;
            $activity->save();
        }
    }
}