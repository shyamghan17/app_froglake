<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyServiceOfferRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateBeautyServiceOfferRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyServiceOffer;
use Workdo\BeautySpaManagement\Events\DestroyBeautyServiceOffer;
use Workdo\BeautySpaManagement\Events\UpdateBeautyServiceOffer;
use Workdo\BeautySpaManagement\Models\BeautyService;

class BeautyServiceOfferController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-service-offers')) {
            $beautyserviceoffers = BeautyServiceOffer::query()
                ->with(['service'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-service-offers')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-service-offers')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('title'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('title', 'like', '%' . request('title') . '%');
                        $query->orWhere('name', 'like', '%' . request('title') . '%');
                        $query->orWhere('description', 'like', '%' . request('title') . '%');
                    });
                })
                ->when(request('beauty_service_id') && request('beauty_service_id') !== 'all', fn($q) => $q->where('beauty_service_id', request('beauty_service_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/ServiceOffers/Index', [
                'beautyserviceoffers' => $beautyserviceoffers,
                'beautyservices'      => BeautyService::where('created_by', creatorId())->select('id', 'name', 'price')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBeautyServiceOfferRequest $request)
    {
        if (Auth::user()->can('create-beauty-service-offers')) {
            $validated = $request->validated();

            $beautyserviceoffer                    = new BeautyServiceOffer();
            $beautyserviceoffer->title             = $validated['title'];
            $beautyserviceoffer->name              = $validated['name'];
            $beautyserviceoffer->price             = $validated['price'];
            $beautyserviceoffer->start_date        = $validated['start_date'];
            $beautyserviceoffer->end_date          = $validated['end_date'];
            $beautyserviceoffer->discount          = $validated['discount'];
            $beautyserviceoffer->offer_price       = $validated['offer_price'];
            $beautyserviceoffer->description       = $validated['description'];
            $beautyserviceoffer->beauty_service_id = $validated['beauty_service_id'];

            $beautyserviceoffer->creator_id = Auth::id();
            $beautyserviceoffer->created_by = creatorId();
            $beautyserviceoffer->save();
            CreateBeautyServiceOffer::dispatch($request, $beautyserviceoffer);

            return redirect()->route('beauty-spa-management.beauty-service-offers.index')->with('success', __('The service offer has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-service-offers.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBeautyServiceOfferRequest $request, BeautyServiceOffer $beautyserviceoffer)
    {
        if (Auth::user()->can('edit-beauty-service-offers')) {
            $validated = $request->validated();

            $beautyserviceoffer->title             = $validated['title'];
            $beautyserviceoffer->name              = $validated['name'];
            $beautyserviceoffer->price             = $validated['price'];
            $beautyserviceoffer->start_date        = $validated['start_date'];
            $beautyserviceoffer->end_date          = $validated['end_date'];
            $beautyserviceoffer->discount          = $validated['discount'];
            $beautyserviceoffer->offer_price       = $validated['offer_price'];
            $beautyserviceoffer->description       = $validated['description'];
            $beautyserviceoffer->beauty_service_id = $validated['beauty_service_id'];

            $beautyserviceoffer->save();
            UpdateBeautyServiceOffer::dispatch($request, $beautyserviceoffer);

            return redirect()->back()->with('success', __('The service offer details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-service-offers.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyServiceOffer $beautyserviceoffer)
    {
        if (Auth::user()->can('delete-beauty-service-offers')) {
            DestroyBeautyServiceOffer::dispatch($beautyserviceoffer);

            $beautyserviceoffer->delete();

            return redirect()->back()->with('success', __('The service offer has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-service-offers.index')->with('error', __('Permission denied'));
        }
    }
}
