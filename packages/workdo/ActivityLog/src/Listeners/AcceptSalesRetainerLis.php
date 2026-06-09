<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Retainer\Events\AcceptSalesRetainer;

class AcceptSalesRetainerLis
{
    public function handle(AcceptSalesRetainer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $retainer = $event->retainer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Retainer';
            $activity['sub_module'] = 'Retainer';
            $activity['description'] = __('Retainer accepted by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $retainer->created_by;
            $activity->save();
        }
    }
}
