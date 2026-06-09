<?php

namespace Workdo\BulkSMS\Http\Controllers;

use Workdo\BulkSMS\Models\BulkSmsGroup;
use Workdo\BulkSMS\Http\Requests\StoreBulkSmsGroupRequest;
use Workdo\BulkSMS\Http\Requests\UpdateBulkSmsGroupRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BulkSMS\Events\CreateBulkSmsGroup;
use Workdo\BulkSMS\Events\DestroyBulkSmsGroup;
use Workdo\BulkSMS\Events\UpdateBulkSmsGroup;
use Workdo\BulkSMS\Models\BulkSmsContact;

class BulksmsGroupController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-bulk-sms-groups')) {
            $bulksmsgroups = BulkSmsGroup::query()

                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-bulk-sms-groups')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-bulk-sms-groups')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where('name', 'like', '%' . request('name') . '%');
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BulkSMS/BulkSmsGroups/Index', [
                'bulksmsgroups' => $bulksmsgroups,
                'bulksmscontacts' => BulkSmsContact::where('created_by', creatorId())->select('id', 'name','mobile_no')->get(),

            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBulkSmsGroupRequest $request)
    {
        if (Auth::user()->can('create-bulk-sms-groups')) {
            $validated = $request->validated();

            $bulksmsgroup = new BulkSmsGroup();
            $bulksmsgroup->name = $validated['name'];
            $bulksmsgroup->contacts = json_encode($validated['contacts'] ?? []);

            $bulksmsgroup->creator_id = Auth::id();
            $bulksmsgroup->created_by = creatorId();
            $bulksmsgroup->save();
            CreateBulkSmsGroup::dispatch($request, $bulksmsgroup);

            return redirect()->route('bulk-s-m-s.bulk-sms-groups.index')->with('success', __('The group has been created successfully.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-groups.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBulkSmsGroupRequest $request, BulkSmsGroup $bulksmsgroup)
    {
        if (Auth::user()->can('edit-bulk-sms-groups')) {
            $validated = $request->validated();

            $bulksmsgroup->name = $validated['name'];
            $bulksmsgroup->contacts = json_encode($validated['contacts'] ?? []);

            $bulksmsgroup->save();
            UpdateBulkSmsGroup::dispatch($request, $bulksmsgroup);

            return back()->with('success', __('The group details are updated successfully.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-groups.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BulkSmsGroup $bulksmsgroup)
    {
        if (Auth::user()->can('delete-bulk-sms-groups')) {
            DestroyBulkSmsGroup::dispatch($bulksmsgroup);
            $bulksmsgroup->delete();
            return back()->with('success', __('The group has been deleted.'));
        } else {
            return redirect()->route('bulk-s-m-s.bulk-sms-groups.index')->with('error', __('Permission denied'));
        }
    }
}