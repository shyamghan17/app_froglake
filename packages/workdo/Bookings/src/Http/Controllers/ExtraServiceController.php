<?php

namespace Workdo\Bookings\Http\Controllers;

use Workdo\Bookings\Models\ExtraService;
use Workdo\Bookings\Http\Requests\StoreExtraServiceRequest;
use Workdo\Bookings\Http\Requests\UpdateExtraServiceRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ExtraServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-extra-services')) {
            $extraservices = ExtraService::select('id', 'name', 'amount', 'status', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-extra-services')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-extra-services')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Bookings/SystemSetup/ExtraServices/Index', [
                'extraservices' => $extraservices,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreExtraServiceRequest $request)
    {
        if (Auth::user()->can('create-extra-services')) {
            $validated = $request->validated();

            $extraservice = new ExtraService();
            $extraservice->name = $validated['name'];
            $extraservice->amount = $validated['amount'] ?? 0;
            $extraservice->status = $request->boolean('status', false);

            $extraservice->creator_id = Auth::id();
            $extraservice->created_by = creatorId();
            $extraservice->save();

            return redirect()->route('bookings.extra-services.index')->with('success', __('The extra service has been created successfully.'));
        } else {
            return redirect()->route('bookings.extra-services.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateExtraServiceRequest $request, ExtraService $extraservice)
    {
        if (Auth::user()->can('edit-extra-services')) {
            $validated = $request->validated();

            $extraservice->name = $validated['name'];
            $extraservice->amount = $validated['amount'] ?? 0;
            $extraservice->status = $request->boolean('status', false);

            $extraservice->save();

            return redirect()->route('bookings.extra-services.index')->with('success', __('The extra service details are updated successfully.'));
        } else {
            return redirect()->route('bookings.extra-services.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(ExtraService $extraservice)
    {
        if (Auth::user()->can('delete-extra-services')) {
            $extraservice->delete();
            
            return redirect()->route('bookings.extra-services.index')->with('success', __('The extra service has been deleted.'));
        } else {
            return redirect()->route('bookings.extra-services.index')->with('error', __('Permission denied'));
        }
    }
}