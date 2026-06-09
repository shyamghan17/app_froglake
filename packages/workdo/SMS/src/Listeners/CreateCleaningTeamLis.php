<?php

namespace Workdo\SMS\Listeners;

use Workdo\CleaningManagement\Events\CreateCleaningTeam;
use Workdo\SMS\Services\SendSMS;

class CreateCleaningTeamLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateCleaningTeam $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Cleaning Team') == 'on') {
            $team = $event->cleaningTeam;
            foreach ($team->users() ?? [] as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'company_name' => $team->created_by_name ?? '',
                        'team_name' => $team->name ?? 'Team',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Cleaning Team', $user->mobile_no ?? null, $user->created_by);
                }
            }
        }
    }
}
