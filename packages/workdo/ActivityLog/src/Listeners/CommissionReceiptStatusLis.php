<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Commission\Events\CommissionReceiptStatus;

class CommissionReceiptStatusLis
{
    public function handle(CommissionReceiptStatus $event)
    {
        if (Module_is_active('ActivityLog')) {
            $commission = $event->commission;

            $activity = new AllActivityLog();
            $activity['module'] = 'Commission';
            $activity['sub_module'] = 'Commission Receipt';
            $activity['description'] = __('Commission Receipt status changed by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $commission->created_by;
            $activity->save();
        }
    }
}
