<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Taskly\Events\CreateProjectMilestone;
use Workdo\SMS\Services\SendSMS;

class CreateProjectMilestoneLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateProjectMilestone $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Milestone') == 'on') {
            if (isset($event->milestone->project->teamMembers)) {
                foreach ($event->milestone->project->teamMembers ?? [] as $user) {
                    if (!empty($user->mobile_no)) {
                        $uArr = [
                            'milestone_name' => $event->milestone->name,
                            'project_name' => $event->milestone->project->name ?? 'Unknown',
                        ];
                        SendSMS::SendMsgs($uArr, 'New Milestone', $user->mobile_no);
                    }
                }
            }
        }
    }
}
