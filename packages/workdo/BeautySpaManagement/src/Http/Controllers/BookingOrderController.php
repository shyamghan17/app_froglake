<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Http\Requests\UpdateBookingStatusRequest;
use Workdo\BeautySpaManagement\Models\BeautyBookingReceipt;

class BookingOrderController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-bookings')){
            $beautybookings = BeautyBooking::query()
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-beauty-bookings')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-beauty-bookings')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function($q) {
                    $q->where(function($query) {
                        $query->where('name', 'like', '%' . request('name') . '%');
                        $query->orWhere('email', 'like', '%' . request('name') . '%');
                        $query->orWhere('phone_number', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('service') && request('service') !== '', fn($q) => $q->where('service', request('service')))
                ->when(request('status') && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->latest()
                ->paginate(request('per_page', 50))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/BookingOrder/Index', [
                'beautybookings' => $beautybookings,
                'beautyservices' => BeautyService::where('created_by', creatorId())->select('id', 'name', 'price')->get(),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(UpdateBookingStatusRequest $request)
    {
        if(Auth::user()->can('edit-beauty-bookings')){
            $validated = $request->validated();

            $booking = BeautyBooking::findOrFail($validated['booking_id']);
            
              // Update booking status
            $booking->stage_id = $validated['stage_id'];
            $booking->save();

              // Create receipt only when stage_id is 2 (completed)
            if ($validated['stage_id'] == 2) {
                $beautyreceipt = BeautyBookingReceipt::firstOrNew(['beauty_booking_id' => $validated['booking_id']]);

                $beautyreceipt->beauty_booking_id = $validated['booking_id'];
                $beautyreceipt->name              = $validated['name'] ?? $booking->name;
                $beautyreceipt->service           = $validated['service'] ?? $booking->beautyService->id ?? '';
                $beautyreceipt->number            = $validated['number'] ?? $booking->phone_number ?? '';
                $beautyreceipt->gender            = $validated['gender'] ?? $booking->gender;
                $beautyreceipt->start_time        = $validated['start_time'] ?? $booking->start_time;
                $beautyreceipt->end_time          = $validated['end_time'] ?? $booking->end_time;
                $beautyreceipt->price             = $validated['price'] ?? $booking->price ?? 0;
                $beautyreceipt->payment_type      = $validated['payment_type'] ?? $booking->payment_option ?? 'Offline';
                $beautyreceipt->creator_id        = Auth::id();
                $beautyreceipt->created_by        = creatorId();
                $beautyreceipt->save();
            }

            return back()->with('success', __('The booking status has been updated successfully.'));
        }
        else{
            return redirect()->route('beauty-spa-management.booking-order.index')->with('error', __('Permission denied'));
        }
    }

      // Beauty Receipt Methods
    public function receiptIndex()
    {
        if(Auth::user()->can('manage-beauty-receipt')){
            $receipts = BeautyBookingReceipt::query()
                ->with(['beautyBooking.beautyService'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-beauty-receipt')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-beauty-receipt')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function($q) {
                    $q->where('name', 'like', '%' . request('name') . '%');
                })
                ->latest()
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/BeautyReceipt/Index', [
                'receipts' => $receipts,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function receiptDownload(BeautyBookingReceipt $receipt)
    {
        if(Auth::user()->can('download-beauty-receipt')){
            $receipt->load(['beautyBooking.beautyService']);
            
            return Inertia::render('BeautySpaManagement/BeautyReceipt/Print', [
                'receipt' => $receipt
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }
}