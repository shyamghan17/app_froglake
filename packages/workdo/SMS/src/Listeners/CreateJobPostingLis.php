<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Recruitment\Events\CreateJobPosting;
use Workdo\SMS\Services\SendSMS;

class CreateJobPostingLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateJobPosting $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Job Posting') == 'on') {
            $user = User::find($event->jobposting->created_by);
            $uArr = [
                'job_name' => $event->jobposting->title ?? '-',
                'company_name' => $user->name ?? '-',
            ];
            if ($user) {
                SendSMS::SendMsgs($uArr, 'New Job Posting', $user->mobile_no ?? null, $event->jobposting->created_by);
            }
        }
    }
}
