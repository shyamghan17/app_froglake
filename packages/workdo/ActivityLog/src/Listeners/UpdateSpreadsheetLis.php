<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Spreadsheet\Events\UpdateSpreadsheet;

class UpdateSpreadsheetLis
{
    public function handle(UpdateSpreadsheet $event)
    {
        if (Module_is_active('ActivityLog')) {
            $spreadsheet = $event->spreadsheet;

            $activity = new AllActivityLog();
            $activity['module'] = 'Spreadsheet';
            $activity['sub_module'] = 'Spreadsheet';
            $activity['description'] = __('Spreadsheet updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $spreadsheet->created_by;
            $activity->save();
        }
    }
}
