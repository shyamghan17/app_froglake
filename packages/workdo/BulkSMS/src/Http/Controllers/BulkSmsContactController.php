<?php

namespace Workdo\BulkSMS\Http\Controllers;

use Workdo\BulkSMS\Models\BulkSmsContact;
use Workdo\BulkSMS\Http\Requests\StoreBulkSmsContactRequest;
use Workdo\BulkSMS\Http\Requests\UpdateBulkSmsContactRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BulkSMS\Events\CreateBulkSmsContact;
use Workdo\BulkSMS\Events\DestroyBulkSmsContact;
use Workdo\BulkSMS\Events\UpdateBulkSmsContact;

class BulkSmsContactController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-bulk-sms-contacts')) {
            $bulksmscontacts = BulkSmsContact::query()

                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-bulk-sms-contacts')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-bulk-sms-contacts')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', '%' . request('name') . '%');
                        $query->orWhere('email', 'like', '%' . request('name') . '%');
                        $query->orWhere('mobile_no', 'like', '%' . request('name') . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BulkSMS/BulkSmsContacts/Index', [
                'bulksmscontacts' => $bulksmscontacts,

            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBulkSmsContactRequest $request)
    {
        if (Auth::user()->can('create-bulk-sms-contacts')) {
            $validated = $request->validated();

            $bulksmscontact = new BulkSmsContact();
            $bulksmscontact->name = $validated['name'];
            $bulksmscontact->email = $validated['email'];
            $bulksmscontact->mobile_no = $validated['mobile_no'];
            $bulksmscontact->city = $validated['city'];
            $bulksmscontact->state = $validated['state'];
            $bulksmscontact->zip_code = $validated['zip_code'];

            $bulksmscontact->creator_id = Auth::id();
            $bulksmscontact->created_by = creatorId();
            $bulksmscontact->save();
            CreateBulkSmsContact::dispatch($request, $bulksmscontact);


            return redirect()->route('bulk-s-m-s.bulk-sms-contacts.index')->with('success', __('The contact has been created successfully.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-contacts.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBulkSmsContactRequest $request, BulkSmsContact $bulksmscontact)
    {
        if (Auth::user()->can('edit-bulk-sms-contacts')) {
            $validated = $request->validated();

            $bulksmscontact->name = $validated['name'];
            $bulksmscontact->email = $validated['email'];
            $bulksmscontact->mobile_no = $validated['mobile_no'];
            $bulksmscontact->city = $validated['city'];
            $bulksmscontact->state = $validated['state'];
            $bulksmscontact->zip_code = $validated['zip_code'];

            $bulksmscontact->save();
            UpdateBulkSmsContact::dispatch($request, $bulksmscontact);

            return back()->with('success', __('The contact details are updated successfully.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-contacts.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BulkSmsContact $bulksmscontact)
    {
        if (Auth::user()->can('delete-bulk-sms-contacts')) {
            DestroyBulkSmsContact::dispatch($bulksmscontact);
            $bulksmscontact->delete();
            return back()->with('success', __('The contact has been deleted.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-contacts.index')->with('error', __('Permission denied'));
        }
    }
}
