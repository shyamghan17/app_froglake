<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\CreateBeautyServiceOffer;

class CreateBeautyServiceOfferLis
{
    public function handle(CreateBeautyServiceOffer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $serviceOffer = $event->beautyserviceoffer;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Service Offer';
            $activity['description'] = __('New Beauty Spa Service Offer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $serviceOffer->created_by;
            $activity->save();
        }
    }
}
