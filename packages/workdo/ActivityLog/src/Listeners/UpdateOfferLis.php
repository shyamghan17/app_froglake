<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateOffer;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateOfferLis
{
    public function handle(UpdateOffer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $offer = $event->offer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Offer';
            $activity['description'] = __('Offer updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $offer->created_by;
            $activity->save();
        }
    }
}
