<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Hrm\Events\CreateAward;
use Workdo\SMS\Services\SendSMS;

class CreateAwardLis
{
    public function handle(CreateAward $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Award') == 'on') {
            $award = $event->award;
            $user = $event->award->employee;
            if ($user && $user->mobile_no) {
                $uArr = [
                    'user_name' => $user->name ?? '',
                    'date' => $award->award_date->format(company_setting('dateFormat',  $user->id)) ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Award', $user->mobile_no, $award->created_by);
            }
        }
    }
}
