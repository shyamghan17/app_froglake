<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\UpdateBeautyServiceOffer;

class UpdateBeautyServiceOfferLis
{
    public function handle(UpdateBeautyServiceOffer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $serviceOffer = $event->beautyserviceoffer;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Service Offer';
            $activity['description'] = __('Beauty Spa Service Offer updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $serviceOffer->created_by;
            $activity->save();
        }
    }
}
