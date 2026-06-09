<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Taskly\Events\CreateProjectBug;
use Workdo\SMS\Services\SendSMS;

class CreateProjectBugLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateProjectBug $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Bug') == 'on') {
            foreach ($event->bug->assignedUsers() ?? [] as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'bug_name' => $event->bug->title,
                        'project_name' => $event->bug->project->name ?? 'Unknown',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Bug', $user->mobile_no);
                }
            }
        }
    }
}
