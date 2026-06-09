<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Spreadsheet\Events\CreateSpreadsheet;

class CreateSpreadsheetLis
{
    public function handle(CreateSpreadsheet $event)
    {
        if (Module_is_active('ActivityLog')) {
            $spreadsheet = $event->spreadsheet;

            $activity = new AllActivityLog();
            $activity['module'] = 'Spreadsheet';
            $activity['sub_module'] = 'Spreadsheet';
            $activity['description'] = __('New Spreadsheet created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $spreadsheet->created_by;
            $activity->save();
        }
    }
}
