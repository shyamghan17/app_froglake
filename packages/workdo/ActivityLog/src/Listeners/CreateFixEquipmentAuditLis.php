<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\FixEquipment\Events\CreateFixEquipmentAudit;

class CreateFixEquipmentAuditLis
{
    public function handle(CreateFixEquipmentAudit $event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentAudit = $event->fixEquipmentAudit;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'Audit';
            $activity['description'] = __('Fix Equipment Audit created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentAudit->created_by;
            $activity->save();
        }
    }
}
