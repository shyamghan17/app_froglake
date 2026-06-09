<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Quotation\Events\SentSalesQuotation;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class SentSalesQuotationLis
{
    public function handle(SentSalesQuotation $event)
    {
        if (Module_is_active('ActivityLog')) {
            $quotation = $event->quotation;

            $activity = new AllActivityLog();
            $activity['module'] = 'Quotation';
            $activity['sub_module'] = 'Quotation';
            $activity['description'] = __('Quotation sent by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $quotation->created_by;
            $activity->save();
        }
    }
}
