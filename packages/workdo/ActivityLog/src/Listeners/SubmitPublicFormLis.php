<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Requests\Events\SubmitPublicForm;

class SubmitPublicFormLis
{
    public function handle(SubmitPublicForm $event)
    {
        $formRequest = $event->formRequest;
        if (Module_is_active('ActivityLog',$formRequest->created_by)) {
            $response = $event->response;

            $activity = new AllActivityLog();
            $activity['module'] = 'Requests';
            $activity['sub_module'] = 'Form Submission';
            $activity['description'] = __('Public Form submitted by the ');
            $activity['creator_id'] = $response->creator_id ?? null;
            $activity['created_by'] = $formRequest->created_by;
            $activity->save();
        }
    }
}
