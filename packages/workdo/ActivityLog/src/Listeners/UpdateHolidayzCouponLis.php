<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\UpdateHolidayzCoupon;

class UpdateHolidayzCouponLis
{
    public function handle(UpdateHolidayzCoupon $event)
    {
        if (Module_is_active('ActivityLog')) {
            $coupon = $event->coupon;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Coupon';
            $activity['description'] = __('Hotel Coupon updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $coupon->created_by;
            $activity->save();
        }
    }
}