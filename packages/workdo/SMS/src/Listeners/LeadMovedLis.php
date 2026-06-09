<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Lead\Events\LeadMoved;
use Workdo\SMS\Services\SendSMS;

class LeadMovedLis
{
    public function __construct()
    {
        //
    }

    public function handle(LeadMoved $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Lead Moved') == 'on') {
            $lead       = $event->lead;
            $request    = $event->request;
            $newStage   = \Workdo\Lead\Models\LeadStage::where('id', $request->stage_id)->first();
            $user       = User::find($lead->user_id);
            if (!empty($user->mobile_no)) {
                $uArr = [
                    'lead_name' => $lead->name,
                    'old_stage' => $lead->stage->name,
                    'new_stage' => $newStage->name
                ];
                SendSMS::SendMsgs($uArr, 'Lead Moved', $user->mobile_no);
            }
        }
    }
}
