<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Performance\Events\CreateReviewCycle;
use Workdo\SMS\Services\SendSMS;

class CreateReviewCycleLis
{
    public function handle(CreateReviewCycle $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Review Cycle') == 'on') {
            $cycle = $event->cycle;

            if (($cycle->created_by != $cycle->creator_id) && $cycle->createdBy->mobile_no) {
                $uArr = [
                    'review_cycle_name' => $cycle->name ?? '',
                    'company_name' => $cycle->createdBy->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Review Cycle', $cycle->createdBy->mobile_no, $cycle->created_by);
            }
        }
    }
}
