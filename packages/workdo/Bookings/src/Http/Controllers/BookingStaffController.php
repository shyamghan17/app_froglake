<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingStaff;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\Bookings\Http\Requests\StoreBookingStaffRequest;
use Workdo\Bookings\Http\Requests\StoreBookingStaffRequest as UpdateBookingStaffRequest;
use Workdo\Bookings\Events\CreateBookingStaff;
use Workdo\Bookings\Events\UpdateBookingStaff;
use Workdo\Bookings\Events\DestroyBookingStaff;

class BookingStaffController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-staff')) {
            $staff = BookingStaff::with(['staff:id,name,email,mobile_no,avatar'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-booking-staff')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-staff')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->whereHas('staff', fn($q) => $q->where('name', 'like', '%' . request('search') . '%')))
                ->when(request('name'), fn($q) => $q->whereHas('staff', fn($q) => $q->where('name', 'like', '%' . request('name') . '%')))
                ->when(request('staff_id'), fn($q) => $q->where('staff_id', request('staff_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            // Add item names for each staff
            $staff->getCollection()->transform(function ($item) {
                //$item->item_names = $item->item_names;
                return $item;
            });

            $users = User::emp()->where('created_by', creatorId())
                ->get(['id', 'name', 'email']);
            
            $items = ProductServiceItem::where('type', 'bookings')
                ->where('created_by', creatorId())
                ->get(['id', 'name']);

            return Inertia::render('Bookings/Staff/Index', [
                'staff' => $staff,
                'users' => $users,
                'items' => $items,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create-booking-staff')) {
            $users = User::emp()->where('created_by', creatorId())
                ->get(['id', 'name', 'email']);
            
            $items = ProductServiceItem::where('type', 'bookings')
                ->where('created_by', creatorId())
                ->get(['id', 'name']);

            return Inertia::render('Bookings/Staff/Create', [
                'users' => $users,
                'items' => $items,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingStaffRequest $request)
    {
        if (Auth::user()->can('create-booking-staff')) {
            $validated = $request->validated();

            $staff = BookingStaff::create([
                'staff_id' => $validated['staff_id'],
                'item_ids' => implode(',', $validated['item_ids']),
                'creator_id' => Auth::id(),
                'created_by' => creatorId(),
            ]);

            CreateBookingStaff::dispatch($request, $staff);
            return redirect()->route('bookings.staff.index')
                ->with('success', __('The staff assigned to booking items successfully.'));
        } else {
            return redirect()->route('bookings.staff.index')->with('error', __('Permission denied'));
        }
    }

    public function edit(BookingStaff $staff)
    {
        if (Auth::user()->can('edit-booking-staff') && $staff->created_by == creatorId()) {
            $users = User::emp()->where('created_by', creatorId())
                ->get(['id', 'name', 'email']);
            
            $items = ProductServiceItem::where('type', 'bookings')
                ->where('created_by', creatorId())
                ->get(['id', 'name']);

            return Inertia::render('Bookings/Staff/Edit', [
                'staff' => $staff,
                'users' => $users,
                'items' => $items,
            ]);
        } else {
            return redirect()->route('bookings.staff.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingStaffRequest $request, BookingStaff $staff)
    {
        if (Auth::user()->can('edit-booking-staff') && $staff->created_by == creatorId()) {
            $validated = $request->validated();

            $staff->update([
                'staff_id' => $validated['staff_id'],
                'item_ids' => implode(',', $validated['item_ids']),
            ]);

            UpdateBookingStaff::dispatch($request, $staff);
            return redirect()->back()->with('success', __('The staff assignment details are updated successfully.'));
        } else {
            return redirect()->route('bookings.staff.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingStaff $staff)
    {
        if (Auth::user()->can('delete-booking-staff') && $staff->created_by == creatorId()) {
            DestroyBookingStaff::dispatch($staff);
            $staff->delete();
            return back()->with('success', __('The booking staff has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}