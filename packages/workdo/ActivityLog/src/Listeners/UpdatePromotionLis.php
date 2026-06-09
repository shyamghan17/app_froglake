<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdatePromotion;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePromotionLis
{
    public function handle(UpdatePromotion $event)
    {
        if (Module_is_active('ActivityLog')) {
            $promotion = $event->promotion;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Promotion';
            $activity['description'] = __('Promotion updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $promotion->created_by;
            $activity->save();
        }
    }
}
