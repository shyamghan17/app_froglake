<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingReview;
use Workdo\Bookings\Http\Requests\StoreBookingReviewRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingReviewRequest;
use Workdo\Bookings\Events\CreateBookingReview;
use Workdo\Bookings\Events\UpdateBookingReview;
use Workdo\Bookings\Events\DestroyBookingReview;

class BookingReviewController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-reviews')) {
            $reviews = BookingReview::with('item:id,name')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-booking-reviews')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-reviews')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();
                
            return Inertia::render('Bookings/SystemSetup/ReviewsSettings/Index', [
                'reviews' => $reviews
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingReviewRequest $request)
    {
        if (Auth::user()->can('create-booking-reviews')) {
            $data = $request->validated();
            $data['created_by'] = creatorId();
            $data['creator_id'] = Auth::id();
            
            $review = BookingReview::create($data);
            
            CreateBookingReview::dispatch($request, $review);
            return back()->with('success', __('The review has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingReview $review)
    {
        if (Auth::user()->can('delete-booking-reviews')) {
            DestroyBookingReview::dispatch($review);

            $review->delete();

            return redirect()->route('bookings.reviews-settings.index')->with('success', __('The review has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}