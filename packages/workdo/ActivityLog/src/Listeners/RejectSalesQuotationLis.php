<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Quotation\Events\RejectSalesQuotation;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class RejectSalesQuotationLis
{
    public function handle(RejectSalesQuotation $event)
    {
        if (Module_is_active('ActivityLog')) {
            $quotation = $event->quotation;

            $activity = new AllActivityLog();
            $activity['module'] = 'Quotation';
            $activity['sub_module'] = 'Quotation';
            $activity['description'] = __('Quotation rejected by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $quotation->created_by;
            $activity->save();
        }
    }
}
