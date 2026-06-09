<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VCard\Events\CreateContact;

class CreateVCardContactLis
{
    public function handle(CreateContact $event)
    {
        if (Module_is_active('ActivityLog')) {
            $contact = $event->contact;

            $activity = new AllActivityLog();
            $activity['module'] = 'VCard';
            $activity['sub_module'] = 'Contact';
            $activity['description'] = __('New VCard Contact created by the ');
            $activity['creator_id'] = $contact->creator_id ?? null;
            $activity['created_by'] = $contact->created_by;
            $activity->save();
        }
    }
}
