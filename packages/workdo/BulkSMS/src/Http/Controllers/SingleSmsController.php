<?php

namespace Workdo\BulkSMS\Http\Controllers;

use Workdo\BulkSMS\Models\SingleSms;
use Workdo\BulkSMS\Http\Requests\StoreSingleSmsRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BulkSMS\Models\BulkSmsContact;
use Workdo\BulkSMS\Models\SendMsg;

class SingleSmsController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-single-sms')) {
            $singlesms = SingleSms::with('contact')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-single-sms')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-single-sms')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->where(function ($query) use ($search) {
                        $query->where('mobile_no', 'like', '%' . $search . '%')
                              ->orWhereHas('contact', function ($contactQuery) use ($search) {
                                  $contactQuery->where('name', 'like', '%' . $search . '%')
                                               ->orWhere('mobile_no', 'like', '%' . $search . '%');
                              });
                    });
                })
                ->when(request('contact_id'), function ($q) {
                    $q->where('contact_id', request('contact_id'));
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BulkSMS/SingleSms/Index', [
                'singlesms' => $singlesms,
                'bulksmscontacts' => BulkSmsContact::where('created_by', creatorId())->select('id', 'name', 'mobile_no')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreSingleSmsRequest $request)
    {
        if (Auth::user()->can('create-single-sms')) {
            $validated = $request->validated();
            $settings = getCompanyAllSetting();
            if (module_is_active('BulkSMS') && isset($settings['bulksms_username'], $settings['bulksms_password']) && $settings['bulksms_username'] && $settings['bulksms_password']) {
                $singlesms = new SingleSms();
                $singlesms->contact_id = $validated['contact_id'];
                $singlesms->mobile_no = $validated['mobile_number'];
                $singlesms->sms = $validated['sms'];
                $uArr = [
                    'user_name' => !empty($users) ? $users->name : '-',
                ];
                $response =  SendMsg::SendMsgs($singlesms->mobile_no, $uArr, $singlesms->sms);
                $status = 'failed';
                if (!$response['error']) {
                    $status = $response['response']['status'] ?? 'pending';
                }
                $singlesms->status = $status;
                $singlesms->creator_id = Auth::id();
                $singlesms->created_by = creatorId();
                $singlesms->save();

                return redirect()->route('bulk-s-m-s.single-sms.index')->with('success', __('The sms has been created successfully.'));
            } else {
                return back()->with('error', __('Please configure the credentials before proceeding'));
            }
        } else {
            return redirect()->route('bulk-s-m-s.single-sms.index')->with('error', __('Permission denied'));
        }
    }



    public function destroy(SingleSms $singlesms)
    {
        if (Auth::user()->can('delete-single-sms')) {
            $singlesms->delete();

            return redirect()->back()->with('success', __('The single sms has been deleted.'));
        } else {
            return redirect()->route('bulk-s-m-s.single-sms.index')->with('error', __('Permission denied'));
        }
    }
}
