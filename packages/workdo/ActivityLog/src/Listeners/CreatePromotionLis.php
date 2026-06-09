<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreatePromotion;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePromotionLis
{
    public function handle(CreatePromotion $event)
    {
        if (Module_is_active('ActivityLog')) {
            $promotion = $event->promotion;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Promotion';
            $activity['description'] = __('New Promotion created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $promotion->created_by;
            $activity->save();
        }
    }
}
