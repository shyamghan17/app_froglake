<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Requests\Events\CreateRequest;

class CreateRequestLis
{
    public function handle(CreateRequest $event)
    {
        if (Module_is_active('ActivityLog')) {
            $request = $event->requests;

            $activity = new AllActivityLog();
            $activity['module'] = 'Requests';
            $activity['sub_module'] = 'Request';
            $activity['description'] = __('New Request created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $request->created_by;
            $activity->save();
        }
    }
}
