<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\UpdateCategory;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLMSCategoryLis
{
    public function handle(UpdateCategory $event)
    {
        if (Module_is_active('ActivityLog')) {
            $category = $event->category;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Category';
            $activity['description'] = __('LMS Category updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $category->created_by;
            $activity->save();
        }
    }
}