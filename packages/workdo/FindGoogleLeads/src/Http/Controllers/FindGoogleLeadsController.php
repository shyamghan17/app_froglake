<?php

namespace Workdo\FindGoogleLeads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Workdo\FindGoogleLeads\Http\Requests\StoreFindGoogleLeadsItemRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\FindGoogleLeads\Models\FindGoogleLeadFoundedLead;
use Workdo\FindGoogleLeads\Models\FindGoogleLeadFoundedLeadContact;

class FindGoogleLeadsController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-find-google-leads')){
            $leads = FindGoogleLeadFoundedLead::query()
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-find-google-leads')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-find-google-leads')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->withCount('contacts')
                ->when(request('name'), fn($q) => $q->where('name', 'like', '%' . request('name') . '%'))
                ->when(request('keywords'), fn($q) => $q->where('keywords', 'like', '%' . request('keywords') . '%'))
                ->when(request('address'), fn($q) => $q->where('address', 'like', '%' . request('address') . '%'))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('FindGoogleLeads/Index', [
                'leads' => $leads,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreFindGoogleLeadsItemRequest $request)
    {
        if (Auth::user()->can('create-find-google-leads')) {
            $validated = $request->validated();

            try {
                $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?';
                $url .= http_build_query([
                    'query' => $validated['title'] . ' in ' . $validated['address'],
                    'type' => $validated['keyword'],
                    'radius' => company_setting('finsgoogleleads_radius'),
                    'key' => company_setting('findgoogleleads_api_key'),
                ]);

                $client = new Client();
                $response = $client->get($url);
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $response_data = json_decode($body, true);

                if ($statusCode == 200) {
                    $lead             = new FindGoogleLeadFoundedLead();
                    $lead->name       = $validated['title'];
                    $lead->keywords   = $validated['keyword'];
                    $lead->address    = $validated['address'];
                    $lead->creator_id = Auth::id();
                    $lead->created_by = creatorId();
                    $lead->save();

                    $this->processLocations($response_data, $lead);
                    $count = FindGoogleLeadFoundedLeadContact::where('founded_lead_id', $lead->id)->count();
                    $lead->contact = $count;
                    $lead->save();

                    return redirect()->route('find-google-leads.index')->with('success', __('The google lead has been found successfully.'));
                } else {
                    if (isset($response_data['status']) && $response_data['status'] == 'error') {
                        return redirect()->back()->with('error', $response_data['message']);
                    } else {
                        return redirect()->back()->with('error', __('Something went wrong.'));
                    }
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        } else {
            return redirect()->route('find-google-leads.index')->with('error', __('Permission denied'));
        }
    }

    protected function processLocations($response_data, $lead)
    {
        if (isset($response_data['results'])) {
            $locations = $response_data['results'];
            foreach ($locations as $location) {
                $this->place_detail($location['place_id'], $lead->id);
            }

            if (isset($response_data['next_page_token'])) {
                $this->processNextPage($response_data['next_page_token'], $lead);
            }
        }
    }

    protected function processNextPage($next_page_token, $lead)
    {
        $nextpagedata = $this->next_page_data($next_page_token);
        if ($nextpagedata['status']) {
            $this->processLocations($nextpagedata['data'], $lead);
        }
    }

    public function next_page_data($nextPageToken)
    {
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?';
        $url .= http_build_query([
            'pagetoken' => $nextPageToken,
            'key' => company_setting('findgoogleleads_api_key'),
        ]);
        $client = new Client();
        $response = $client->get($url);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $response_data = json_decode($body, true);

        if ($statusCode == 200) {
            return ['status' => true, 'data' => $response_data];
        } else {
            return ['status' => false];
        }
    }

    public function place_detail($place_id = null, $lead_id = null)
    {
        $place_detail_url = 'https://maps.googleapis.com/maps/api/place/details/json?';
        $place_detail_url .= http_build_query([
            'place_id' => $place_id,
            'fields' => 'name,formatted_phone_number,website,formatted_address,international_phone_number',
            'key' => company_setting('findgoogleleads_api_key')
        ]);
        $place_detail_client = new Client();
        $place_detail_response = $place_detail_client->get($place_detail_url);
        $place_detail_statusCode = $place_detail_response->getStatusCode();
        $place_detail_body = $place_detail_response->getBody()->getContents();
        $place_detail_response_data = json_decode($place_detail_body, true);

        if ($place_detail_statusCode == 200) {
            if (isset($place_detail_response_data['result'])) {
                $contact                  = new FindGoogleLeadFoundedLeadContact();
                $contact->founded_lead_id = $lead_id;
                $contact->name            = isset($place_detail_response_data['result']['name']) ? $place_detail_response_data['result']['name'] : "";
                $contact->email           = isset($place_detail_response_data['result']['email']) ? $place_detail_response_data['result']['email'] : "";
                $contact->mobile_no       = isset($place_detail_response_data['result']['international_phone_number']) ?
                $place_detail_response_data['result']['international_phone_number'] : (isset($place_detail_response_data['result']['formatted_phone_number']) ?
                $place_detail_response_data['result']['formatted_phone_number'] : "");
                $contact->website         = isset($place_detail_response_data['result']['website']) ? $place_detail_response_data['result']['website'] : "";
                $contact->address         = isset($place_detail_response_data['result']['formatted_address']) ? $place_detail_response_data['result']['formatted_address'] : "";
                $contact->creator_id      = Auth::id();
                $contact->created_by      = creatorId();
                $contact->save();
            }
        }
    }

    public function destroy(FindGoogleLeadFoundedLead $lead)
    {
        if (Auth::user()->can('delete-find-google-leads')) {
            // Delete related contacts first
            FindGoogleLeadFoundedLeadContact::where('founded_lead_id', $lead->id)->delete();

            $lead->delete();

            return back()->with('success', __('The google lead has been deleted.'));
        } else {
            return redirect()->route('find-google-leads.index')->with('error', __('Permission denied'));
        }
    }

    public function show(FindGoogleLeadFoundedLead $lead)
    {
        if (Auth::user()->can('view-find-google-leads')  && $lead->created_by == creatorId()) {
            $contacts = FindGoogleLeadFoundedLeadContact::where('founded_lead_id', $lead->id)->get();

            return Inertia::render('FindGoogleLeads/View', [
                'lead' => $lead,
                'contacts' => $contacts,
                'users' => User::where('created_by', creatorId())->emp([], ['vendor'])->get(['id', 'name']),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroyContact($contactId)
    {
        if (Auth::user()->can('delete-find-google-leads')) {
            $contact = FindGoogleLeadFoundedLeadContact::where('id', $contactId)
                ->where('creator_id', creatorId())
                ->first();

            if (!$contact) {
                return back()->with('error', __('Permission denied'));
            }

            $contact->delete();

            return back()->with('success', __('The contact has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
