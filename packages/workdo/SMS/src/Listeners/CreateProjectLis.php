<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Taskly\Events\CreateProject;
use Workdo\SMS\Services\SendSMS;

class CreateProjectLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateProject $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Project') == 'on') {
            $users = User::whereIn('id', $event->request->user_ids ?? [])->get();
            if ($users) {
                foreach ($users as $user) {
                    if (!empty($user->mobile_no)) {
                        $uArr = [
                            'project_name' => $event->project->name,
                        ];
                        SendSMS::SendMsgs($uArr, 'New Project', $user->mobile_no);
                    }
                }
            }
        }
    }
}
