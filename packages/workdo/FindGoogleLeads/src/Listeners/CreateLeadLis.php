<?php

namespace Workdo\FindGoogleLeads\Listeners;

use Workdo\FindGoogleLeads\Models\FindGoogleLeadFoundedLeadContact;
use Workdo\Lead\Events\CreateLead;
use Workdo\Lead\Models\Lead;

class CreateLeadLis
{    
    public function handle(CreateLead $event)
    {
        if(Module_is_active('FindGoogleLeads'))
        {
            if(company_setting('finsgoogleleads_lead_stages') && company_setting('finsgoogleleads_pipelines'))
            {
                if (isset($event->request->google_contact_lead_id)) {

                    $contact = FindGoogleLeadFoundedLeadContact::where('id', $event->request->google_contact_lead_id)->first();
                    $contact->is_lead = 1;
                    $contact->is_sync = 1;
                    $contact->save();
                }
                $lead = Lead::where('id', $event->lead->id)->first();
                if (isset($event->request->website) && !empty($event->request->website) && $event->request->website != '-') {
        
                    $lead->website = $event->request->website;
                }
                $lead->stage_id = company_setting('finsgoogleleads_lead_stages');
                $lead->pipeline_id = company_setting('finsgoogleleads_pipelines');
                $lead->save();
            }
            
        }
    }
}