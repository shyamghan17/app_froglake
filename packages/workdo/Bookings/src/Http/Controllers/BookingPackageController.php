<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingPackage;
use Workdo\Bookings\Models\BookingExtraService;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\Bookings\Http\Requests\StoreBookingPackageRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingPackageRequest;
use Workdo\Bookings\Events\CreateBookingPackage;
use Workdo\Bookings\Events\UpdateBookingPackage;
use Workdo\Bookings\Events\DestroyBookingPackage;

class BookingPackageController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-packages')) {
            $packages = BookingPackage::with(['item:id,name'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-booking-packages')) {
                        $q->where('booking_packages.created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-packages')) {
                        $q->where('booking_packages.creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), fn($q) => $q->where('booking_packages.name', 'like', '%' . request('name') . '%'))
                ->when(request('item_id'), fn($q) => $q->where('booking_packages.item_id', request('item_id')))
                ->when(request('sort'), function($q) {
                    $sort = request('sort');
                    $direction = request('direction', 'asc');
                    
                    if ($sort === 'item_name') {
                        $q->join('product_service_items', 'booking_packages.item_id', '=', 'product_service_items.id')
                          ->orderBy('product_service_items.name', $direction)
                          ->select('booking_packages.*');
                    } else {
                        $q->orderBy('booking_packages.' . $sort, $direction);
                    }
                }, fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();


                $extraServices = BookingExtraService::where(function($q) {
                    if(Auth::user()->can('manage-any-booking-extra-services')) {
                        $q->where('created_by', creatorId())->orWhereNull('created_by');
                    } elseif(Auth::user()->can('manage-own-booking-extra-services')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();
            $items = ProductServiceItem::where('type', 'bookings')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-booking-items')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-items')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get(['id', 'name', 'sale_price', 'tax_ids']);

            return Inertia::render('Bookings/Packages/Index', [
                'packages' => $packages,
                'items' => $items,
                'extraServices' => $extraServices
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingPackageRequest $request)
    {
        if (Auth::user()->can('create-booking-packages')) {
            $validated = $request->validated();

            $package                  = new BookingPackage();
            $package->name            = $validated['name'];
            $package->item_id         = $validated['item_id'];
            $package->services        = json_encode($validated['services'] ?? []);
            $package->delivery_time   = $validated['delivery_time'];
            $package->delivery_period = $validated['delivery_period'];
            $package->price           = $validated['price'];
            $package->creator_id      = Auth::id();
            $package->created_by      = creatorId();
            $package->save();

            CreateBookingPackage::dispatch($request, $package);
            return redirect()->route('bookings.packages.index')
                ->with('success', __('The booking package has been created successfully.'));
        } else {
            return redirect()->route('bookings.packages.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingPackageRequest $request, BookingPackage $package)
    {
        if (Auth::user()->can('edit-booking-packages') && $package->created_by == creatorId()) {
            $validated = $request->validated();

            $package->name            = $validated['name'];
            $package->item_id         = $validated['item_id'];
            $package->services        = json_encode($validated['services'] ?? []);
            $package->delivery_time   = $validated['delivery_time'];
            $package->delivery_period = $validated['delivery_period'];
            $package->price           = $validated['price'];
            $package->save();

            UpdateBookingPackage::dispatch($request, $package);
            return redirect()->back()->with('success', __('The booking package details are updated successfully.'));
        } else {
            return redirect()->route('bookings.packages.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingPackage $package)
    {
        if (Auth::user()->can('delete-booking-packages') && $package->created_by == creatorId()) {
            DestroyBookingPackage::dispatch($package);
            $package->delete();
            return back()->with('success', __('The booking package has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}