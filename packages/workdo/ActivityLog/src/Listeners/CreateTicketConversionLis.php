<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SupportTicket\Events\CreateTicketConversion;

class CreateTicketConversionLis
{
    public function handle(CreateTicketConversion $event)
    {
        if (Module_is_active('ActivityLog')) {
            $ticket = $event->ticket;

            $activity = new AllActivityLog();
            $activity['module'] = 'SupportTicket';
            $activity['sub_module'] = 'Ticket Conversation';
            $activity['description'] = __('Ticket reply added by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $ticket->created_by;
            $activity->save();
        }
    }
}
