<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Feedback\Events\CreateTemplate;

class CreateTemplateLis
{
    public function handle(CreateTemplate $event)
    {
        if (Module_is_active('ActivityLog')) {
            $template = $event->template;

            $activity = new AllActivityLog();
            $activity['module'] = 'Feedback';
            $activity['sub_module'] = 'Template';
            $activity['description'] = __('Template created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $template->created_by;
            $activity->save();
        }
    }
}
