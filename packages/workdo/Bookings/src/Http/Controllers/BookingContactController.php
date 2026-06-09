<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingContact;
use Workdo\Bookings\Http\Requests\StoreBookingContactRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingContactRequest;
use Workdo\Bookings\Events\CreateBookingContact;
use Workdo\Bookings\Events\UpdateBookingContact;
use Workdo\Bookings\Events\DestroyBookingContact;

class BookingContactController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-contacts')) {
            $contacts = BookingContact::where(function($q) {
                    if(Auth::user()->can('manage-any-booking-contacts')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-contacts')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();
                
            return Inertia::render('Bookings/SystemSetup/ContactsSettings/Index', [
                'contacts' => $contacts
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingContactRequest $request)
    {
        if (Auth::user()->can('create-booking-contacts')) {
            $data = $request->validated();
            $data['created_by'] = creatorId();
            $data['creator_id'] = Auth::id();
            
            $contact = BookingContact::create($data);

            CreateBookingContact::dispatch($request, $contact);
            
            return back()->with('success', __('The contact has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingContact $contact)
    {
        if (Auth::user()->can('delete-booking-contacts')) {
            DestroyBookingContact::dispatch($contact);
            
            $contact->delete();

            return redirect()->route('bookings.contacts-settings.index')->with('success', __('The contact has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}