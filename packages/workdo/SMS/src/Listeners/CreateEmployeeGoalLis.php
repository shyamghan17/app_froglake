<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Performance\Events\CreateEmployeeGoal;
use Workdo\SMS\Services\SendSMS;

class CreateEmployeeGoalLis
{
    public function handle(CreateEmployeeGoal $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Employee Goal') == 'on') {
            $goal = $event->goal;
            if ($goal->employee && $goal->employee->mobile_no) {
                $uArr = [
                    'goal_title' => $goal->title ?? '',
                    'employee_name' => $goal->employee->name ?? '',
                    'company_name' => $goal->createdBy->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Employee Goal', $goal->employee->mobile_no, $goal->created_by);
            }
        }
    }
}
