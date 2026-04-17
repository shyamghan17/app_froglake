<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateTemplateLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $template = $event->template;

            $activity = new AllActivityLog();
            $activity['module'] = 'Feedback';
            $activity['sub_module'] = 'Template';
            $activity['description'] = __('Template updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $template->created_by;
            $activity->save();
        }
    }
}