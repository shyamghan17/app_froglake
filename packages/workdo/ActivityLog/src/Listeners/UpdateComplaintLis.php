<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateComplaint;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateComplaintLis
{
    public function handle(UpdateComplaint $event)
    {
        if (Module_is_active('ActivityLog')) {
            $complaint = $event->complaint;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Complaint';
            $activity['description'] = __('Complaint updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $complaint->created_by;
            $activity->save();
        }
    }
}
