<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Retainer\Events\CreateRetainer;

class CreateRetainerLis
{
    public function handle(CreateRetainer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $retainer = $event->retainer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Retainer';
            $activity['sub_module'] = 'Retainer';
            $activity['description'] = __('New Retainer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $retainer->created_by;
            $activity->save();
        }
    }
}
