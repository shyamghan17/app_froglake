<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\CreateSalesAgentCommissionPayment;

class CreateSalesAgentCommissionPaymentLis
{
    public function handle(CreateSalesAgentCommissionPayment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payment = $event->payment;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Commission Payment';
            $activity['description'] = __('New Sales Agent Commission Payment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payment->created_by;
            $activity->save();
        }
    }
}