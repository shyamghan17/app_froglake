<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateInternalknowledgeArticleLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $internalknowledgeArticle = $event->internalknowledgeArticle;

            $activity = new AllActivityLog();
            $activity['module'] = 'InternalKnowledge';
            $activity['sub_module'] = 'Article';
            $activity['description'] = __('Internal Knowledge Article created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $internalknowledgeArticle->created_by;
            $activity->save();
        }
    }
}