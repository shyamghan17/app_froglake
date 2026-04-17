<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesDocumentLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesDocument = $event->salesDocument;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Document';
            $activity['description'] = __('Sales Document updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesDocument->created_by;
            $activity->save();
        }
    }
}