<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateComplaint;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateComplaintLis
{
    public function handle(CreateComplaint $event)
    {
        if (Module_is_active('ActivityLog')) {
            $complaint = $event->complaint;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Complaint';
            $activity['description'] = __('New Complaint created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $complaint->created_by;
            $activity->save();
        }
    }
}
