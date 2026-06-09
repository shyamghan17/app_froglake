<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingBusinessHours;
use Workdo\Bookings\Http\Requests\StoreBookingBusinessHoursRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingBusinessHoursRequest;

class BookingBusinessHoursController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-business-hours')) {
            $businessHours = BookingBusinessHours::getBusinessHours();
            return Inertia::render('Bookings/SystemSetup/BusinessHours/Index', [
                'businessHours' => $businessHours
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingBusinessHoursRequest $request)
    {
        if (Auth::user()->can('create-booking-business-hours')) {


            foreach ($request->business_hours as $dayData) {
                BookingBusinessHours::updateOrCreate(
                    [
                        'day_of_week' => $dayData['day_of_week'],
                        'created_by' => creatorId()
                    ],
                    [
                        'is_closed' => $dayData['is_closed'],
                        'time_slots' => $dayData['is_closed'] ? [] : ($dayData['time_slots'] ?? []),
                        'creator_id' => Auth::id()
                    ]
                );
            }

            return response()->json(['success' => __('The business hours has been updated successfully.')]);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function update(UpdateBookingBusinessHoursRequest $request, $day)
    {
        if (Auth::user()->can('edit-booking-business-hours')) {
            BookingBusinessHours::updateOrCreate(
                [
                    'day_of_week' => $day,
                    'created_by' => creatorId()
                ],
                [
                    'is_closed' => $request->is_closed,
                    'time_slots' => $request->is_closed ? [] : ($request->time_slots ?? []),
                    'creator_id' => Auth::id()
                ]
            );

            return back()->with('success', __('The business hours has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}