<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Performance\Events\CreatePerformanceIndicator;
use Workdo\SMS\Services\SendSMS;

class CreatePerformanceIndicatorLis
{
    public function handle(CreatePerformanceIndicator $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Performance Indicator') == 'on') {
            $indicator = $event->indicator;
            if (($indicator->created_by != $indicator->creator_id) && $indicator->createdBy->mobile_no) {
                $uArr = [
                    'indicator_name' => $indicator->name ?? '',
                    'company_name' => $indicator->createdBy->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Performance Indicator', $indicator->createdBy->mobile_no, $indicator->created_by);
            }
        }
    }
}
