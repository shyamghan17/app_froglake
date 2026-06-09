<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\CreatePropertyInvoice;

class CreatePropertyInvoiceLis
{
    public function handle(CreatePropertyInvoice $event)
    {
        if (Module_is_active('ActivityLog')) {
            $invoice = $event->invoice;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Invoice';
            $activity['description'] = __('New Property Invoice created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $invoice->created_by;
            $activity->save();
        }
    }
}