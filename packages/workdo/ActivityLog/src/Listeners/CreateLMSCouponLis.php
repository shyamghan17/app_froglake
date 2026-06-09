<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\CreateCoupon;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLMSCouponLis
{
    public function handle(CreateCoupon $event)
    {
        if (Module_is_active('ActivityLog')) {
            $coupon = $event->coupon;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Coupon';
            $activity['description'] = __('New LMS Coupon created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $coupon->created_by;
            $activity->save();
        }
    }
}