<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Taskly\Events\CreateProjectTask;
use Workdo\SMS\Services\SendSMS;

class CreateProjectTaskLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateProjectTask $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Task') == 'on') {
            foreach ($event->task->assignedUsers() ?? [] as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'task_name' => $event->task->title,
                        'project_name' => $event->task->project->name ?? 'Unknown',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Task', $user->mobile_no);
                }
            }
        }
    }
}
