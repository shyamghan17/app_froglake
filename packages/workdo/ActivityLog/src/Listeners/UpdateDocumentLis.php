<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateDocumentLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $document = $event->document;

            $activity = new AllActivityLog();
            $activity['module'] = 'Documents';
            $activity['sub_module'] = 'Document';
            $activity['description'] = __('Document updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $document->created_by;
            $activity->save();
        }
    }
}