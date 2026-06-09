<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Lead\Events\DealMoved;
use Workdo\SMS\Services\SendSMS;

class DealMovedLis
{
    public function __construct()
    {
        //
    }

    public function handle(DealMoved $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Deal Moved') == 'on') {
            $deal  = $event->deal;
            $request = $event->request;
            $newStage = \Workdo\Lead\Models\DealStage::where('id', $request->stage_id)->first();
            $userDeal = \Workdo\Lead\Models\UserDeal::with('user')->where('deal_id', $deal->id)->get();
            
            foreach ($userDeal as $assignedUser) {
                if (!empty($assignedUser->user->mobile_no)) {
                    $uArr = [
                        'deal_name' => $deal->name,
                        'old_stage' => $deal->stage->name,
                        'new_stage' => $newStage->name,
                    ];
                    SendSMS::SendMsgs($uArr, 'Deal Moved', $assignedUser->user->mobile_no);
                }
            }
        }
    }
}
