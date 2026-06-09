<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyService;
use Workdo\BeautySpaManagement\Http\Requests\StoreServiceRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateServiceRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;
use App\Models\User;
use Workdo\BeautySpaManagement\Events\CreateBeautyService;
use Workdo\BeautySpaManagement\Events\DestroyBeautyService;
use Workdo\BeautySpaManagement\Events\UpdateBeautyService;

class BeautyServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-services')) {
            $services = BeautyService::query()
                ->with(['service_type', 'staff'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-services')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-services')) {
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
                ->when(request('service_type_id') && request('service_type_id') !== 'all', fn($q) => $q->where('service_type_id', request('service_type_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Services/Index', [
                'services'           => $services,
                'beautyservicetypes' => BeautyServiceType::where('created_by', creatorId())->select('id', 'name')->get(),
                'staff'              => User::where('created_by', creatorId())->where('type', 'staff')->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreServiceRequest $request)
    {
        if (Auth::user()->can('create-beauty-services')) {
            $validated = $request->validated();

            $service                       = new BeautyService();
            $service->name                 = $validated['name'];
            $service->max_bookable_persons = $validated['max_bookable_persons'];
            $service->price                = $validated['price'];
            $service->time                 = $validated['time'];
            $service->description          = $validated['description'];
            $service->service_image        = basename($validated['service_image']);
            $service->service_type_id      = $validated['service_type_id'];
            $service->staff_id             = $validated['staff_id'] === '0' ? null : $validated['staff_id'];
            $service->included_services    = isset($validated['included_services']) ? array_filter($validated['included_services']) : null;

            $service->creator_id = Auth::id();
            $service->created_by = creatorId();
            $service->save();
            CreateBeautyService::dispatch($request, $service);

            return redirect()->route('beauty-spa-management.services.index')->with('success', __('The service has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.services.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateServiceRequest $request, BeautyService $service)
    {
        if (Auth::user()->can('edit-beauty-services')) {
            $validated = $request->validated();

            $service->name                 = $validated['name'];
            $service->max_bookable_persons = $validated['max_bookable_persons'];
            $service->price                = $validated['price'];
            $service->time                 = $validated['time'];
            $service->description          = $validated['description'];
            $service->service_image        = $validated['service_image'];
            $service->service_type_id      = $validated['service_type_id'];
            $service->staff_id             = $validated['staff_id'] === '0' ? null : $validated['staff_id'];
            $service->included_services    = isset($validated['included_services']) ? array_filter($validated['included_services']) : null;

            $service->save();
            UpdateBeautyService::dispatch($request, $service);

            return redirect()->back()->with('success', __('The service details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.services.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyService $service)
    {
        if (Auth::user()->can('delete-beauty-services')) {
            DestroyBeautyService::dispatch($service);

            $service->delete();

            return redirect()->back()->with('success', __('The service has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.services.index')->with('error', __('Permission denied'));
        }
    }
}
