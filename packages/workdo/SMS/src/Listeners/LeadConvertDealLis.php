<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Lead\Events\LeadConvertDeal;
use Workdo\SMS\Services\SendSMS;

class LeadConvertDealLis
{
    public function __construct()
    {
        //
    }

    public function handle(LeadConvertDeal $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Lead to Deal Conversion') == 'on') {
            $user = User::find($event->lead->user_id ?? null);
            $uArr = [
                'lead_name' => $event->lead->name ?? 'Unknown',
            ];

            SendSMS::SendMsgs($uArr, 'Lead to Deal Conversion', $user->mobile_no);
        }
    }
}