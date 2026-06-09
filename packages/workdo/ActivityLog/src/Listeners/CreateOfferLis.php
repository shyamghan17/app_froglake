<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateOffer;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateOfferLis
{
    public function handle(CreateOffer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $offer = $event->offer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Offer';
            $activity['description'] = __('New Offer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $offer->created_by;
            $activity->save();
        }
    }
}
