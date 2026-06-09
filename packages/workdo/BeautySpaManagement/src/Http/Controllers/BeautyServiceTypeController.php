<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Http\Requests\StoreServiceTypeRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateServiceTypeRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyServiceType;
use Workdo\BeautySpaManagement\Events\DestroyBeautyServiceType;
use Workdo\BeautySpaManagement\Events\UpdateBeautyServiceType;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;

class BeautyServiceTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-service-types')) {
            $servicetypes = BeautyServiceType::select('id', 'name', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-service-types')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-service-types')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/ServiceTypes/Index', [
                'servicetypes' => $servicetypes,

            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreServiceTypeRequest $request)
    {
        if (Auth::user()->can('create-beauty-service-types')) {
            $validated = $request->validated();

            $servicetype       = new BeautyServiceType();
            $servicetype->name = $validated['name'];

            $servicetype->creator_id = Auth::id();
            $servicetype->created_by = creatorId();
            $servicetype->save();
            CreateBeautyServiceType::dispatch($request, $servicetype);

            return redirect()->route('beauty-spa-management.service-types.index')->with('success', __('The service type has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.service-types.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateServiceTypeRequest $request, BeautyServiceType $servicetype)
    {
        if (Auth::user()->can('edit-beauty-service-types')) {
            $validated = $request->validated();



            $servicetype->name = $validated['name'];

            $servicetype->save();
            UpdateBeautyServiceType::dispatch($request, $servicetype);

            return redirect()->route('beauty-spa-management.service-types.index')->with('success', __('The service type details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.service-types.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyServiceType $servicetype)
    {
        if (Auth::user()->can('delete-beauty-service-types')) {
            DestroyBeautyServiceType::dispatch($servicetype);
            $servicetype->delete();

            return redirect()->route('beauty-spa-management.service-types.index')->with('success', __('The service type has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.service-types.index')->with('error', __('Permission denied'));
        }
    }
}
