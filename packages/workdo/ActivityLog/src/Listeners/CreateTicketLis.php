<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SupportTicket\Events\CreateTicket;

class CreateTicketLis
{
    public function handle(CreateTicket $event)
    {
        if (Module_is_active('ActivityLog')) {
            $ticket = $event->ticket;

            $activity = new AllActivityLog();
            $activity['module'] = 'SupportTicket';
            $activity['sub_module'] = 'Ticket';
            $activity['description'] = __('Support Ticket created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $ticket->created_by;
            $activity->save();
        }
    }
}
