<?php

namespace Workdo\Bookings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Workdo\Bookings\Models\BookingPayment;

class BookingPaymentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-payments')) {
            $bookingpayments = BookingPayment::with(['appointment.customer'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-payments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-payments')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('reference_number', 'like', '%' . request('search') . '%')
                            ->orWhereHas('appointment', function ($appointmentQuery) {
                                $appointmentQuery->where('appointment_number', 'like', '%' . request('search') . '%');
                            });
                    });
                })
                ->when(request('payment_date'), fn($q) => $q->whereDate('payment_date', request('payment_date')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return inertia('Bookings/Payments/Index', [
                'bookingpayments' => $bookingpayments,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(Request $request, BookingPayment $payment)
    {
        if (Auth::user()->can('manage-booking-payments') && $payment->created_by == creatorId()) {
            try {
                $appointment = $payment->appointment;
                
                $validated = $request->validate([
                    'status' => 'required|in:pending,cleared,cancelled'
                ]);

                $payment->update(['payment_status' => $validated['status']]);
                
                if ($validated['status'] === 'cleared') {
                    $appointment->payment_status = 'paid';
                    $appointment->save();
                } elseif ($validated['status'] === 'cancelled') {
                    $appointment->payment_status = 'pending';
                    $appointment->save();
                    
                    $payment->delete();
                    return back()->with('success', __('Payment rejected and removed successfully.'));
                }

                $message = $validated['status'] === 'cleared' ? __('Payment approved successfully.') : __('Payment rejected successfully.');
                return back()->with('success', __($message));
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
