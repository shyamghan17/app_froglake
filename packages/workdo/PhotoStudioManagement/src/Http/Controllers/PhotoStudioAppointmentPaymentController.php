<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointmentPayment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioAppointmentPaymentRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioAppointmentPaymentRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioAppointmentPayment;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioAppointmentPaymentStatus;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioAppointmentPayment;

class PhotoStudioAppointmentPaymentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-appointment-payments')) {
            $payments = PhotoStudioAppointmentPayment::with(['appointment.service'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-appointment-payments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-appointment-payments')) {
                        $teamMemberId = PhotoStudioTeamMember::where('user_id', Auth::id())
                            ->where('created_by', creatorId())
                            ->value('id');
                        $q->where('creator_id', Auth::id())
                            ->when($teamMemberId, fn($q) => $q->orWhereHas(
                                'appointment',
                                fn($aq) =>
                                $aq->whereJsonContains('team_member_ids', (string) $teamMemberId)
                            ));
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->where(function ($query) {
                    $query->where('appointment_number', 'like', '%' . request('search') . '%')
                        ->orWhere('customer_name', 'like', '%' . request('search') . '%');
                }))
                ->when(request('payment_status'), fn($q) => $q->where('payment_status', request('payment_status')))
                ->when(request('service_id'), fn($q) => $q->whereHas('appointment', fn($aq) => $aq->where('service_id', request('service_id'))))
                ->when(request('date_range'), function ($q) {
                    $range = request('date_range');
                    if (str_contains($range, ' - ')) {
                        [$start, $end] = explode(' - ', $range);
                        $q->whereBetween('payment_date', [trim($start), trim($end)]);
                    }
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $services = PhotoStudioService::where('created_by', creatorId())->get(['id', 'name']);

            return Inertia::render('PhotoStudioManagement/AppointmentPayments/Index', [
                'payments' => $payments,
                'services' => $services,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioAppointmentPaymentRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-appointment-payments')) {
            $validated   = $request->validated();
            $appointment = PhotoStudioAppointment::with('service')->findOrFail($validated['appointment_id']);

            $payment                     = new PhotoStudioAppointmentPayment();
            $payment->appointment_id     = $appointment->id;
            $payment->appointment_number = $appointment->appointment_number;
            $payment->customer_name      = $appointment->name;
            $payment->service_name       = $appointment->service->name ?? '-';
            $payment->payment_date       = $validated['payment_date'];
            $payment->amount             = $appointment->price;
            $payment->payment_status     = 'pending';
            $payment->payment_type       = 'offline';
            $payment->description        = $validated['description'] ?? null;
            $payment->creator_id         = Auth::id();
            $payment->created_by         = creatorId();
            $payment->save();

            CreatePhotoStudioAppointmentPayment::dispatch($request, $payment);

            return redirect()->back()->with('success', __('The payment has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateStatus(UpdatePhotoStudioAppointmentPaymentRequest $request, PhotoStudioAppointmentPayment $payment)
    {
        if (Auth::user()->can('edit-photo-studio-appointment-payments')) {
            try {
                if ($request->payment_status === 'cleared') {
                    try {
                        UpdatePhotoStudioAppointmentPaymentStatus::dispatch($request, $payment);
                    } catch (\Throwable $th) {
                        return back()->with('error', $th->getMessage());
                    }
                }

                $payment->payment_status = $request->payment_status;
                $payment->save();

                return back()->with('success', __('The payment status has been updated successfully.'));
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioAppointmentPayment $payment)
    {
        if (Auth::user()->can('delete-photo-studio-appointment-payments')) {
            DestroyPhotoStudioAppointmentPayment::dispatch($payment);

            if ($payment->appointment) {
                $payment->appointment->update(['payment_status' => 'pending']);
            }

            $payment->delete();

            return redirect()->back()->with('success', __('The payment has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
