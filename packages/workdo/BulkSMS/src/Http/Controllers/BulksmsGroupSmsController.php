<?php

namespace Workdo\BulkSMS\Http\Controllers;

use Workdo\BulkSMS\Models\BulkSmsGroup;
use Workdo\BulkSMS\Models\BulksmsSendMessage;
use Workdo\BulkSMS\Models\BulksmsSend;
use Workdo\BulkSMS\Models\BulkSmsContact;
use Workdo\BulkSMS\Models\SendMsg;
use Workdo\BulkSMS\Http\Requests\StoreBulksmsGroupSmsRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BulksmsGroupSmsController extends Controller
{
    private function checkSmsAccess(BulksmsSend $bulksmsgroupsms)
    {
        if (Auth::user()->can('manage-any-bulk-sms-groups-send')) {
            return $bulksmsgroupsms->created_by == creatorId();
        } elseif (Auth::user()->can('manage-own-bulk-sms-groups-send')) {
            return $bulksmsgroupsms->creator_id == Auth::id();
        } else {
            return false;
        }
    }
    public function index()
    {
        if (Auth::user()->can('manage-bulk-sms-groups-send')) {
            $bulksmsgroupsms = BulksmsSend::with('group')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-bulk-sms-groups-send')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-bulk-sms-groups-send')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->where(function ($query) use ($search) {
                        $query->where('sms', 'like', '%' . $search . '%')
                              ->orWhereHas('group', function ($groupQuery) use ($search) {
                                  $groupQuery->where('name', 'like', '%' . $search . '%');
                              });
                    });
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BulkSMS/BulksmsGroupSms/Index', [
                'bulksmsgroupsms' => $bulksmsgroupsms,
                'bulksmsgroups' => BulkSmsGroup::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBulksmsGroupSmsRequest $request)
    {
        if (Auth::user()->can('create-bulk-sms-groups-send')) {
            $validated = $request->validated();
            $settings = getCompanyAllSetting();
            if (module_is_active('BulkSMS') && isset($settings['bulksms_username'], $settings['bulksms_password']) && $settings['bulksms_username'] && $settings['bulksms_password']) {
                $group = BulkSmsGroup::find($validated['group_id']);
                $contacts = is_string($group->contacts) ? json_decode($group->contacts, true) : $group->contacts;
                $contacts = $contacts ?? [];

                $mobileNumbers = [];
                foreach ($contacts as $contactId) {
                    $contact = BulkSmsContact::where('id', $contactId)->where('created_by', creatorId())->first();
                    if ($contact) {
                        $mobileNumbers[] = $contact->mobile_no;
                    }
                }

                $bulksmsSend             = new BulksmsSend();
                $bulksmsSend->group_id   = $validated['group_id'];
                $bulksmsSend->mobile_no  = implode(',', $mobileNumbers);
                $bulksmsSend->sms        = $validated['sms'];
                $bulksmsSend->creator_id = Auth::id();
                $bulksmsSend->created_by = creatorId();
                $bulksmsSend->save();

                foreach ($contacts as $contactId) {
                    $contact = BulkSmsContact::where('id', $contactId)->where('created_by', creatorId())->first();
                    if ($contact) {
                        $bulksmsSendMessage             = new BulksmsSendMessage();
                        $bulksmsSendMessage->name       = $contact->name;
                        $bulksmsSendMessage->group_id   = $group->id;
                        $bulksmsSendMessage->mobile_no  = $contact->mobile_no;
                        $bulksmsSendMessage->sms        = $validated['sms'];
                        $bulksmsSendMessage->creator_id = Auth::id();
                        $bulksmsSendMessage->created_by = creatorId();

                        $uArr = ['user_name' => $contact->name];
                        $response = SendMsg::SendMsgs($contact->mobile_no, $uArr, $validated['sms']);

                        $status = 'failed';
                        if (isset($response['error']) && !$response['error']) {
                            $status = $response['response']['status'] ?? 'pending';
                        }

                        $bulksmsSendMessage->status = $status;
                        $bulksmsSendMessage->save();
                    }
                }

                return redirect()->route('bulk-s-m-s.bulksms-group-sms.index')->with('success', __('The SMS has been sent successfully.'));
            } else {
                return back()->with('error', __('Please configure the credentials before proceeding'));
            }
        } else {
            return redirect()->route('bulk-s-m-s.bulksms-group-sms.index')->with('error', __('Permission denied'));
        }
    }

    public function show(BulksmsSend $bulksmsgroupsms)
    {
        if (Auth::user()->can('view-bulk-sms-groups-send')) {
             if (!$this->checkSmsAccess($bulksmsgroupsms)) {
                return redirect()->route('bulk-s-m-s.bulksms-group-sms.index')->with('error', __('Permission denied'));
            }
            $messages = BulksmsSendMessage::where('group_id', $bulksmsgroupsms->group_id)
                ->where('created_by', creatorId())
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                              ->orWhere('mobile_no', 'like', '%' . $search . '%');
                    });
                })
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BulkSMS/BulksmsGroupSms/View', [
                'messages' => $messages,
                'bulksmsgroupsms' => $bulksmsgroupsms,
                'bulksmsgroups' => BulkSmsGroup::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

   public function destroy(BulksmsSend $bulksmsgroupsms)
    {
        if (Auth::user()->can('delete-bulk-sms-groups-send')) {
            BulksmsSendMessage::where('group_id', $bulksmsgroupsms->group_id)
                ->where('created_by', creatorId())
                ->delete();

            $bulksmsgroupsms->delete();

            return redirect()->back()->with('success', __('The bulk SMS has been deleted.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulksms-group-sms.index')->with('error', __('Permission denied'));
        }
    }

    public function smsdestroy(BulksmsSendMessage $bulksmssendmessage)
    {
        if (Auth::user()->can('delete-bulk-sms-groups-send')) {
            $bulksmssendmessage->delete();
            return redirect()->back()->with('success', __('The message has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
