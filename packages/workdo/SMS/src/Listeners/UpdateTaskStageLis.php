<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Taskly\Events\UpdateTaskStage;
use Workdo\SMS\Services\SendSMS;

class UpdateTaskStageLis
{
    public function __construct()
    {
        //
    }

    public function handle(UpdateTaskStage $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Task Stage Updated') == 'on') {
            $user = User::find($event->taskStage->creator_id ?? null);
            $uArr = [
                'old_status' => $event->taskStage->getOriginal('name') ?? 'Unknown',
                'new_status' => $event->taskStage->name ?? 'Unknown',
            ];
            SendSMS::SendMsgs($uArr, 'Task Stage Updated', $user->mobile_no);
        }
    }
}
