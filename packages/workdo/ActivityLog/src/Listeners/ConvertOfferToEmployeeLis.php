<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\ConvertOfferToEmployee;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class ConvertOfferToEmployeeLis
{
    public function handle(ConvertOfferToEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $offer = $event->offer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Offer';
            $activity['description'] = __('Offer converted to Employee by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $offer->created_by;
            $activity->save();
        }
    }
}
