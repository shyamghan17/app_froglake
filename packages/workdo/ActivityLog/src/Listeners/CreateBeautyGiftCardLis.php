<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\CreateBeautyGiftCard;

class CreateBeautyGiftCardLis
{
    public function handle(CreateBeautyGiftCard $event)
    {
        if (Module_is_active('ActivityLog')) {
            $giftCard = $event->giftcard;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Gift Card';
            $activity['description'] = __('New Beauty Spa Gift Card created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $giftCard->created_by;
            $activity->save();
        }
    }
}