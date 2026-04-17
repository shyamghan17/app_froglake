<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateSupplierLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $supplier = $event->supplier;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'Supplier';
            $activity['description'] = __('New Supplier created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $supplier->created_by;
            $activity->save();
        }
    }
}