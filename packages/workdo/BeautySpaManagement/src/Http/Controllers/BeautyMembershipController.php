<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyMembership;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyMembershipRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateBeautyMembershipRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyMembership;
use Workdo\BeautySpaManagement\Events\DestroyBeautyMembership;
use Workdo\BeautySpaManagement\Events\UpdateBeautyMembership;
use Workdo\BeautySpaManagement\Models\BeautyService;

class BeautyMembershipController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-memberships')) {
            $beautymemberships = BeautyMembership::query()
                ->with(['included_services'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-memberships')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-memberships')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', '%' . request('name') . '%');
                        $query->orWhere('description', 'like', '%' . request('name') . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Memberships/Index', [
                'beautymemberships' => $beautymemberships,
                'beautyservices'    => BeautyService::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBeautyMembershipRequest $request)
    {
        if (Auth::user()->can('create-beauty-memberships')) {
            $validated = $request->validated();

            $beautymembership                       = new BeautyMembership();
            $beautymembership->name                 = $validated['name'];
            $beautymembership->duration             = $validated['duration'];
            $beautymembership->benefits             = $validated['benefits'];
            $beautymembership->price                = $validated['price'];
            $beautymembership->description          = $validated['description'];
            $beautymembership->included_services_id = $validated['included_services_id'];

            $beautymembership->creator_id = Auth::id();
            $beautymembership->created_by = creatorId();
            $beautymembership->save();
            CreateBeautyMembership::dispatch($request, $beautymembership);

            return redirect()->route('beauty-spa-management.beauty-memberships.index')->with('success', __('The membership has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-memberships.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBeautyMembershipRequest $request, BeautyMembership $beautymembership)
    {
        if (Auth::user()->can('edit-beauty-memberships')) {
            $validated = $request->validated();

            $beautymembership->name                 = $validated['name'];
            $beautymembership->duration             = $validated['duration'];
            $beautymembership->benefits             = $validated['benefits'];
            $beautymembership->price                = $validated['price'];
            $beautymembership->description          = $validated['description'];
            $beautymembership->included_services_id = $validated['included_services_id'];

            $beautymembership->save();
            UpdateBeautyMembership::dispatch($request, $beautymembership);

            return redirect()->back()->with('success', __('The membership details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-memberships.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyMembership $beautymembership)
    {
        if (Auth::user()->can('delete-beauty-memberships')) {
            DestroyBeautyMembership::dispatch($beautymembership);

            $beautymembership->delete();

            return redirect()->back()->with('success', __('The membership has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-memberships.index')->with('error', __('Permission denied'));
        }
    }
}
