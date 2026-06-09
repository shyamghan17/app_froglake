<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyBookingRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateBeautyBookingRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;
use Workdo\BeautySpaManagement\Events\DestroyBeautyBooking;
use Workdo\BeautySpaManagement\Events\UpdateBeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Events\CreateBeautyBookingPayment;
use Workdo\BeautySpaManagement\Events\MarkBeautyBookingPaymentPaid;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyBookingPaymentRequest;
use Workdo\BeautySpaManagement\Models\BeautyBookingPayment;

class BeautyBookingController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-bookings')) {
            $beautybookings = BeautyBooking::query()
                ->withExists('payments as has_payment')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-bookings')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-bookings')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', '%' . request('name') . '%');
                        $query->orWhere('email', 'like', '%' . request('name') . '%');
                        $query->orWhere('phone_number', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('service') && request('service') !== '', fn($q) => $q->where('service', request('service')))
                ->when(request('gender') !== null && request('gender') !== '', fn($q) => $q->where('gender', request('gender')))
                ->when(request('reference') !== null && request('reference') !== '', fn($q) => $q->where('reference', request('reference')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();
            return Inertia::render('BeautySpaManagement/BeautyBookings/Index', [
                'beautybookings'    => $beautybookings,
                'beautyservices'    => BeautyService::where('created_by', creatorId())->select('id', 'name', 'price')->get(),
                'reference_options' => BeautyBooking::$reference,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBeautyBookingRequest $request)
    {
        if (Auth::user()->can('create-beauty-bookings')) {
            $validated = $request->validated();

            $service = BeautyService::find($validated['service']);
            $price   = $service->price;

            $offer = BeautyServiceOffer::where('beauty_service_id', $service->id)
                ->where('start_date', '<=', $validated['date'])
                ->where('end_date', '>=', $validated['date'])
                ->where('created_by', creatorId())
                ->first();

            if ($offer) {
                $price = $offer->offer_price;
            }

            $servicePrice = BeautyBooking::total_amount($validated['person'], $price);

            $times = explode('-', $validated['time_slot']);

            $beautybooking                 = new BeautyBooking();
            $beautybooking->name           = $validated['name'];
            $beautybooking->email          = $validated['email'];
            $beautybooking->service        = $validated['service'];
            $beautybooking->date           = $validated['date'];
            $beautybooking->start_time     = $times[0];
            $beautybooking->end_time       = $times[1];
            $beautybooking->person         = $validated['person'];
            $beautybooking->price          = $servicePrice;
            $beautybooking->phone_number   = $validated['phone_number'];
            $beautybooking->gender         = $validated['gender'];
            $beautybooking->reference      = $validated['reference'];
            $beautybooking->notes          = $validated['additional_notes'];
            $beautybooking->payment_option = 'Offline';
            $beautybooking->payment_status = 'pending';
            $beautybooking->stage_id       = 0;

            $beautybooking->creator_id = Auth::id();
            $beautybooking->created_by = creatorId();
            $beautybooking->save();

            CreateBeautyBooking::dispatch($request, $beautybooking);

            return redirect()->route('beauty-spa-management.beauty-bookings.index')->with('success', __('The booking has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-bookings.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBeautyBookingRequest $request, BeautyBooking $beautybooking)
    {
        if (Auth::user()->can('edit-beauty-bookings')) {
            $validated = $request->validated();

            $service = BeautyService::find($validated['service']);
            $price   = $service->price;

            $offer = BeautyServiceOffer::where('beauty_service_id', $service->id)
                ->where('start_date', '<=', $validated['date'])
                ->where('end_date', '>=', $validated['date'])
                ->where('created_by', creatorId())
                ->first();

            if ($offer) {
                $price = $offer->offer_price;
            }

            $servicePrice = BeautyBooking::total_amount($validated['person'], $price);
            $times        = explode('-', $validated['time_slot']);

            $beautybooking->name         = $validated['name'];
            $beautybooking->email        = $validated['email'];
            $beautybooking->service      = $validated['service'];
            $beautybooking->date         = $validated['date'];
            $beautybooking->start_time   = $times[0];
            $beautybooking->end_time     = $times[1];
            $beautybooking->person       = $validated['person'];
            $beautybooking->price        = $servicePrice;
            $beautybooking->phone_number = $validated['phone_number'];
            $beautybooking->gender       = $validated['gender'];
            $beautybooking->reference    = $validated['reference'];
            $beautybooking->notes        = $validated['additional_notes'];

            $beautybooking->save();
            UpdateBeautyBooking::dispatch($request, $beautybooking);

            return redirect()->back()->with('success', __('The booking details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-bookings.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyBooking $beautybooking)
    {
        if (Auth::user()->can('delete-beauty-bookings')) {
            DestroyBeautyBooking::dispatch($beautybooking);

            $beautybooking->delete();

            return redirect()->back()->with('success', __('The booking has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-bookings.index')->with('error', __('Permission denied'));
        }
    }

    public function storePayment(StoreBeautyBookingPaymentRequest $request)
    {
        if (Auth::user()->can('create-beauty-bookings')) {
            $validated = $request->validated();

            $booking = BeautyBooking::find($validated['booking_id']);

            if (!$booking) {
                return redirect()->back()->with('error', __('Booking not found'));
            }

            $payment                   = new BeautyBookingPayment();
            $payment->booking_id       = $booking->id;
            $payment->total_person     = $validated['total_person'];
            $payment->payment_amount   = $validated['payment_amount'];
            $payment->description      = $validated['description'];
            $payment->service          = $validated['service'];
            $payment->payment_date     = $validated['payment_date'];
            $payment->customer_name    = $validated['customer_name'];
            $payment->reference_number = $validated['reference_number'];
            $payment->creator_id       = Auth::id();
            $payment->created_by       = creatorId();
            $payment->save();

            CreateBeautyBookingPayment::dispatch($request, $payment);
            
            return redirect()->back()->with('success', __('Payment added successfully'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-bookings.index')->with('error', __('Permission denied'));
        }
    }

    public function paymentsIndex()
    {
        if (Auth::user()->can('manage-beauty-bookings-payment')) {
            $payments = BeautyBookingPayment::with(['booking', 'beautyService:id,name'])
                ->where('created_by', creatorId())
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('customer_name', 'like', '%' . request('search') . '%')
                              ->orWhere('reference_number', 'like', '%' . request('search') . '%');
                    });
                })
                ->latest()
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/BeautyBookingPayments/Index', [
                'payments' => $payments,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroyPayment(BeautyBookingPayment $payment)
    {
        if (Auth::user()->can('delete-beauty-bookings-payment')) {
            $payment->delete();
            return redirect()->back()->with('success', __('Payment deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function markPaid(BeautyBookingPayment $payment)
    {
        if (Auth::user()->can('beauty-bookings-payments-paid')) {
            $booking = $payment->booking;            
            if ($booking->payment_status === 'paid') {
                return back()->with('error', __('Payment is already marked as paid.'));
            }

            try {
                MarkBeautyBookingPaymentPaid::dispatch($payment, $booking);

                $booking->payment_status = 'paid';
                $booking->save();

                return redirect()->back()->with('success', __('Payment marked as paid successfully.'));
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
