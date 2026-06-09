<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\CreateOrder;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLMSOrderLis
{
    public function handle(CreateOrder $event)
    {
        if (Module_is_active('ActivityLog')) {
            $order = $event->order;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Order';
            $activity['description'] = __('New LMS Order created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $order->created_by ?? Auth::user()->id;
            $activity->save();
        }
    }
}