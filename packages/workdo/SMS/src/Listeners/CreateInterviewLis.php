<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Recruitment\Events\CreateInterview;
use Workdo\SMS\Services\SendSMS;

class CreateInterviewLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateInterview $event)
    {

        if (Module_is_active('SMS') && company_setting('SMS Interview Schedule') == 'on') {
            $users = User::whereIn('id', $event->interview->interviewer_ids ?? [])->get();
            foreach ($users ?? [] as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'company_name' => $event->interview->created_by_name ?? '',
                        'user_name' => $event->interview->candidate->name ?? '-',
                        'application' => $event->interview->jobPosting->title ?? '-',
                    ];
                    SendSMS::SendMsgs($uArr, 'Interview Schedule', $user->mobile_no ?? null, $user->created_by);
                }
            }
        }
    }
}
