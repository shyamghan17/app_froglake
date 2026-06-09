<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Lead\Events\DealAddClient;

class DealAddClientLis
{
    public function handle(DealAddClient $event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealClient = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New Client Add in deal ') . $dealClient->name . __(' by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealClient->created_by;
            $activity->save();
        }
    }
}
