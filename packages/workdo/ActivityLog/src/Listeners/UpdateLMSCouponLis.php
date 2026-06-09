<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\UpdateCoupon;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLMSCouponLis
{
    public function handle(UpdateCoupon $event)
    {
        if (Module_is_active('ActivityLog')) {
            $coupon = $event->coupon;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Coupon';
            $activity['description'] = __('LMS Coupon updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $coupon->created_by;
            $activity->save();
        }
    }
}