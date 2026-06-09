<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Internalknowledge\Events\UpdateInternalknowledgeBook;

class UpdateInternalknowledgeBookLis
{
    public function handle(UpdateInternalknowledgeBook $event)
    {
        if (Module_is_active('ActivityLog')) {
            $internalknowledgeBook = $event->internalknowledgeBook;

            $activity = new AllActivityLog();
            $activity['module'] = 'InternalKnowledge';
            $activity['sub_module'] = 'Book';
            $activity['description'] = __('Internal Knowledge Book updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $internalknowledgeBook->created_by;
            $activity->save();
        }
    }
}
