<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\CreateHolidayzCoupon;

class CreateHolidayzCouponLis
{
    public function handle(CreateHolidayzCoupon $event)
    {
        if (Module_is_active('ActivityLog')) {
            $coupon = $event->coupon;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Coupon';
            $activity['description'] = __('New Hotel Coupon created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $coupon->created_by;
            $activity->save();
        }
    }
}