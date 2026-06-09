<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SupportTicket\Events\CreateContact;

class CreateContactLis
{
    public function handle(CreateContact $event)
    {
        if (Module_is_active('ActivityLog')) {
            $contact = $event->contact;

            $activity = new AllActivityLog();
            $activity['module'] = 'SupportTicket';
            $activity['sub_module'] = 'Contact';
            $activity['description'] = __('Contact form submitted by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $contact->created_by;
            $activity->save();
        }
    }
}
