<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\UpdateBeautyGiftCard;

class UpdateBeautyGiftCardLis
{
    public function handle(UpdateBeautyGiftCard $event)
    {
        if (Module_is_active('ActivityLog')) {
            $giftCard = $event->giftcard;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Gift Card';
            $activity['description'] = __('Beauty Spa Gift Card updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $giftCard->created_by;
            $activity->save();
        }
    }
}