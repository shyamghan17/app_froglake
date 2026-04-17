<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateInternalknowledgeBookLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $internalknowledgeBook = $event->internalknowledgeBook;

            $activity = new AllActivityLog();
            $activity['module'] = 'InternalKnowledge';
            $activity['sub_module'] = 'Book';
            $activity['description'] = __('Internal Knowledge Book created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $internalknowledgeBook->created_by;
            $activity->save();
        }
    }
}