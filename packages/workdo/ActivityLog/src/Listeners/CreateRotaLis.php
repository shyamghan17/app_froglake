<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\CreateRota;

class CreateRotaLis
{
    public function handle(CreateRota $event)
    {
        if (Module_is_active('ActivityLog')) {
            $rota = $event->rota;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Rota';
            $activity['description'] = __('New Work Schedule created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $rota->created_by;
            $activity->save();
        }
    }
}