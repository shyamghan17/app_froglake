<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Recruitment\Events\SubmitApplication;
use Workdo\SMS\Services\SendSMS;

class SubmitApplicationLis
{
    public function __construct()
    {
        //
    }

    public function handle(SubmitApplication $event)
    {
        if (Module_is_active('SMS',  $event->candidate->job_posting->created_by ?? null) && company_setting('SMS New Job Application', $event->candidate->job_posting->created_by ?? null) == 'on') {
            $user = User::find($event->candidate->job_posting->creator_id ?? null);
            if (isset($user->mobile_no)) {
                $uArr = [
                    'job_name' => $event->candidate->job_posting->title ?? '-',
                    'user_name' => $event->candidate->name ?? '-',
                ];
                SendSMS::SendMsgs($uArr, 'New Job Application', $user->mobile_no ?? null, $event->candidate->job_posting->created_by ?? null);
            }
        }
    }
}
