<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Http\Requests\StoreBookingCustomerRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingCustomerRequest;
use Workdo\Bookings\Events\CreateBookingCustomer;
use Workdo\Bookings\Events\UpdateBookingCustomer;
use Workdo\Bookings\Events\DestroyBookingCustomer;

class BookingCustomerController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-customers')) {
            $customers = BookingCustomer::where(function($q) {
                    if(Auth::user()->can('manage-any-booking-customers')) {
                        $q->where('created_by', creatorId())->orWhereNull('created_by');
                    } elseif(Auth::user()->can('manage-own-booking-customers')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->where(function($query) {
                    $search = request('search');
                    $query->where('first_name', 'like', '%' . $search . '%')
                          ->orWhere('last_name', 'like', '%' . $search . '%')
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                          ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ['%' . $search . '%'])
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('mobile_number', 'like', '%' . $search . '%');
                }))
                ->when(request('date_from'), fn($q) => $q->whereDate('created_at', '>=', request('date_from')))
                ->when(request('date_to'), fn($q) => $q->whereDate('created_at', '<=', request('date_to')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Bookings/Customers/Index', [
                'customers' => $customers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingCustomerRequest $request)
    {
        if (Auth::user()->can('create-booking-customers')) {
            $validated = $request->validated();

            $customer                = new BookingCustomer();
            $customer->first_name    = $validated['first_name'];
            $customer->last_name     = $validated['last_name'];
            $customer->email         = $validated['email'];
            $customer->mobile_number = $validated['mobile_number'];
            $customer->description   = $validated['description'] ?? null;
            $customer->creator_id    = Auth::id();
            $customer->created_by    = creatorId();
            $customer->save();

            CreateBookingCustomer::dispatch($request, $customer);
            return redirect()->route('bookings.customers.index')
                ->with('success', __('The customer has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingCustomerRequest $request, BookingCustomer $customer)
    {
        if (Auth::user()->can('edit-booking-customers') && ($customer->created_by == creatorId() || is_null($customer->created_by))) {
            $validated = $request->validated();

            $customer->first_name    = $validated['first_name'];
            $customer->last_name     = $validated['last_name'];
            $customer->email         = $validated['email'];
            $customer->mobile_number = $validated['mobile_number'];
            $customer->description   = $validated['description'] ?? null;
            $customer->save();

            UpdateBookingCustomer::dispatch($request, $customer);
            return redirect()->back()->with('success', __('The customer details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingCustomer $customer)
    {
        if (Auth::user()->can('delete-booking-customers') && ($customer->created_by == creatorId() || is_null($customer->created_by))) {
            DestroyBookingCustomer::dispatch($customer);
            $customer->delete();
            return back()->with('success', __('The customer has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}