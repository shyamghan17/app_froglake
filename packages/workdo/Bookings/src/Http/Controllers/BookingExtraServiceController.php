<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingExtraService;
use Workdo\Bookings\Http\Requests\StoreBookingExtraServiceRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingExtraServiceRequest;
use Workdo\Bookings\Events\CreateBookingExtraService;
use Workdo\Bookings\Events\UpdateBookingExtraService;
use Workdo\Bookings\Events\DestroyBookingExtraService;

class BookingExtraServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-extra-services')) {
            $extraservices = BookingExtraService::select('id', 'name','amount', 'status', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-extra-services')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-extra-services')) {
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

    public function store(StoreBookingExtraServiceRequest $request)
    {
        if (Auth::user()->can('create-booking-extra-services')) {
            $validated = $request->validated();

            $extraservice = new BookingExtraService();
            $extraservice->name = $validated['name'];
            $extraservice->status = $request->boolean('status', false);

            $extraservice->creator_id = Auth::id();
            $extraservice->created_by = creatorId();
            $extraservice->save();
            CreateBookingExtraService::dispatch($request, $extraservice);

            return redirect()->route('bookings.booking-extra-services.index')->with('success', __('The extra service has been created successfully.'));
        } else {
            return redirect()->route('bookings.booking-extra-services.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingExtraServiceRequest $request, BookingExtraService $extraservice)
    {
        if (Auth::user()->can('edit-booking-extra-services')) {
            $validated = $request->validated();

            $extraservice->name = $validated['name'];
            $extraservice->status = $request->boolean('status', false);

            $extraservice->save();
            UpdateBookingExtraService::dispatch($request, $extraservice);

            return redirect()->route('bookings.booking-extra-services.index')->with('success', __('The extra service details are updated successfully.'));
        } else {
            return redirect()->route('bookings.booking-extra-services.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingExtraService $extraservice)
    {
        if (Auth::user()->can('delete-booking-extra-services')) {
            DestroyBookingExtraService::dispatch($extraservice);
            $extraservice->delete();
            
            return redirect()->route('bookings.booking-extra-services.index')->with('success', __('The extra service has been deleted.'));
        } else {
            return redirect()->route('bookings.booking-extra-services.index')->with('error', __('Permission denied'));
        }
    }
}